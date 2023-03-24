<?php
/**
 * Karaka
 *
 * PHP Version 8.1
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
use Modules\Comments\Models\CommentMapper;

/**
 * @internal
 */
final class CommentMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Comments\Models\CommentMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $comment            = new Comment();
        $comment->createdBy = new NullAccount(1);
        $comment->title     = 'Test Title';
        $comment->content   = 'Test Content';
        $comment->ref       = null;
        $comment->list      = new CommentList();

        $id = CommentMapper::create()->execute($comment);
        self::assertGreaterThan(0, $comment->getId());
        self::assertEquals($id, $comment->getId());

        $commentR = CommentMapper::get()->where('id', $comment->getId())->execute();
        self::assertEquals($id, $commentR->getId());
        self::assertEquals($comment->createdBy->getId(), $commentR->createdBy->getId());
        self::assertEquals($comment->title, $commentR->title);
        self::assertEquals($comment->content, $commentR->content);
        self::assertEquals($comment->ref, $commentR->ref);
        self::assertEquals($comment->list->getId(), $commentR->list->getId());
    }
}
