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

namespace Modules\Comments\tests\Models;

use Modules\Admin\Models\NullAccount;
use Modules\Comments\Models\Comment;
use Modules\Comments\Models\CommentList;
use Modules\Comments\Models\CommentListMapper;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Comments\Models\CommentListMapper::class)]
final class CommentListMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testCRUD() : void
    {
        $list = new CommentList();

        $comment            = new Comment();
        $comment->createdBy = new NullAccount(1);
        $comment->title     = 'Test Comment';

        $list->addComment($comment);

        $id = CommentListMapper::create()->execute($list);
        self::assertGreaterThan(0, $list->id);
        self::assertEquals($id, $list->id);

        $listR = CommentListMapper::get()->with('comments')->where('id', $list->id)->execute();
        self::assertEquals($id, $listR->id);

        $actual = $listR->getComments();
        self::assertEquals($comment->title, \reset($actual)->title);
    }
}
