<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Comments
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Comments\Theme\Backend\Components\Comment;

use Modules\Comments\Models\CommentList;
use phpOMS\Localization\L11nManager;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Component view.
 *
 * @package Modules\Comments
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
class ListView extends View
{
    /**
     * Comment list
     *
     * @var null|CommentList
     * @since 1.0.0
     */
    protected ?CommentList $commentList = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Comments/Theme/Backend/Components/Comment/list');
    }

    /**
     * {@inheritdoc}
     */
    public function render(mixed ...$data) : string
    {
        /** @var array{0:null|CommentList} $data */
        $this->commentList = $data[0];

        return parent::render();
    }
}
