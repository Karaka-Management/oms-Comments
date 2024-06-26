<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Comments\tests\Controller;

use Model\CoreSettings;
use Modules\Admin\Models\AccountPermission;
use phpOMS\Account\Account;
use phpOMS\Account\AccountManager;
use phpOMS\Account\PermissionType;
use phpOMS\Application\ApplicationAbstract;
use phpOMS\DataStorage\Session\HttpSession;
use phpOMS\Dispatcher\Dispatcher;
use phpOMS\Event\EventManager;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Module\ModuleAbstract;
use phpOMS\Module\ModuleManager;
use phpOMS\Router\WebRouter;
use phpOMS\System\MimeType;
use phpOMS\Utils\TestUtils;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Comments\Controller\ApiController::class)]
#[\PHPUnit\Framework\Attributes\TestDox('Modules\Comments\tests\Controller\ApiControllerTest: Comments api controller')]
final class ApiControllerTest extends \PHPUnit\Framework\TestCase
{
    protected ApplicationAbstract $app;

    /**
     * @var \Modules\Comments\Controller\ApiController
     */
    protected ModuleAbstract $module;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->app = new class() extends ApplicationAbstract
        {
            protected string $appName = 'Api';
        };

        $this->app->dbPool         = $GLOBALS['dbpool'];
        $this->app->unitId         = 1;
        $this->app->accountManager = new AccountManager($GLOBALS['session']);
        $this->app->appSettings    = new CoreSettings();
        $this->app->moduleManager  = new ModuleManager($this->app, __DIR__ . '/../../../../Modules/');
        $this->app->dispatcher     = new Dispatcher($this->app);
        $this->app->eventManager   = new EventManager($this->app->dispatcher);
        $this->app->eventManager->importFromFile(__DIR__ . '/../../../../Web/Api/Hooks.php');
        $this->app->sessionManager = new HttpSession(36000);
        $this->app->l11nManager    = new L11nManager();

        $account = new Account();
        TestUtils::setMember($account, 'id', 1);

        $permission       = new AccountPermission();
        $permission->unit = 1;
        $permission->app  = 2;
        $permission->setPermission(
            PermissionType::READ
            | PermissionType::CREATE
            | PermissionType::MODIFY
            | PermissionType::DELETE
            | PermissionType::PERMISSION
        );

        $account->addPermission($permission);

        $this->app->accountManager->add($account);
        $this->app->router = new WebRouter();

        $this->module = $this->app->moduleManager->get('Comments');

        TestUtils::setMember($this->module, 'app', $this->app);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiCommentListCU() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;

        $this->module->apiCommentListCreate($request, $response);
        self::assertGreaterThan(0, $lId = $response->getDataArray('')['response']->id);

        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', $lId);
        $request->setData('allow_edit', '1');
        $request->setData('allow_voting', '0');
        $request->setData('allow_upload', '1');
        $request->setData('commentlist_status', '2');

        $this->module->apiCommentListUpdate($request, $response);
        self::assertTrue($response->getDataArray('')['response']->allowEdit);
        self::assertFalse($response->getDataArray('')['response']->allowVoting);
        self::assertTrue($response->getDataArray('')['response']->allowFiles);
        self::assertEquals(2, $response->getDataArray('')['response']->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiCommentCRU() : void
    {
        // create
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;

        $this->module->apiCommentListCreate($request, $response);
        self::assertGreaterThan(0, $lId = $response->getDataArray('')['response']->id);

        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('list', $lId);
        $request->setData('plain', 'Some **text**.');

        if (!\is_file(__DIR__ . '/test_tmp.md')) {
            \copy(__DIR__ . '/test.md', __DIR__ . '/test_tmp.md');
        }

        TestUtils::setMember($request, 'files', [
            'file1' => [
                'name'     => 'test.md',
                'type'     => MimeType::M_TXT,
                'tmp_name' => __DIR__ . '/test_tmp.md',
                'error'    => \UPLOAD_ERR_OK,
                'size'     => \filesize(__DIR__ . '/test_tmp.md'),
            ],
        ]);

        $request->setData('media', \json_encode([1]));

        $this->module->apiCommentCreate($request, $response);
        self::assertGreaterThan(0, $cId = $response->getDataArray('')['response']->id);

        //read
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', $cId);

        $this->module->apiCommentGet($request, $response);
        self::assertEquals('', $response->getDataArray('')['response']->title);
        self::assertEquals('Some **text**.', $response->getDataArray('')['response']->contentRaw);

        // update
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', $cId);
        $request->setData('title', 'New title');
        $request->setData('plain', 'New plain');

        $this->module->apiCommentUpdate($request, $response);
        self::assertEquals('New title', $response->getDataArray('')['response']->title);
        self::assertEquals('New plain', $response->getDataArray('')['response']->contentRaw);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiCommentCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiCommentCreate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiCommentVoteCreateInvalidData() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('invalid', '1');

        $this->module->apiChangeCommentVote($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testApiCommentVoteCreate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', '1');
        $request->setData('type', '1');

        $this->module->apiChangeCommentVote($request, $response);
        self::assertGreaterThan(0, $vId = $response->getDataArray('')['response']->id);

        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $request->setData('id', '1');
        $request->setData('type', '-1');

        $this->module->apiChangeCommentVote($request, $response);
        self::assertEquals($vId, $response->getDataArray('')['response']->id);
    }

    public function testInvalidapiCommentUpdate() : void
    {
        $response = new HttpResponse();
        $request  = new HttpRequest();

        $request->header->account = 1;
        $this->module->apiCommentUpdate($request, $response);
        self::assertEquals(RequestStatusCode::R_400, $response->header->status);
    }
}
