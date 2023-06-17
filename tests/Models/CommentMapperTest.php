<?php
/**
 * Jingga
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
        self::assertGreaterThan(0, $comment->id);
        self::assertEquals($id, $comment->id);

        $commentR = CommentMapper::get()->where('id', $comment->id)->execute();
        self::assertEquals($id, $commentR->id);
        self::assertEquals($comment->createdBy->id, $commentR->createdBy->id);
        self::assertEquals($comment->title, $commentR->title);
        self::assertEquals($comment->content, $commentR->content);
        self::assertEquals($comment->ref, $commentR->ref);
        self::assertEquals($comment->list->id, $commentR->list->id);
    }
}
