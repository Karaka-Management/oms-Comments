<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
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
class CommentMapperTest extends \PHPUnit\Framework\TestCase
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
        $comment->ref = null;
        $comment->list = new CommentList();

        $id = CommentMapper::create($comment);
        self::assertGreaterThan(0, $comment->getId());
        self::assertEquals($id, $comment->getId());

        $commentR = CommentMapper::get($comment->getId());
        self::assertEquals($id, $commentR->getId());
        self::assertEquals($comment->createdBy->getId(), $commentR->createdBy->getId());
        self::assertEquals($comment->title, $commentR->title);
        self::assertEquals($comment->content, $commentR->content);
        self::assertEquals($comment->ref, $commentR->ref);
        self::assertEquals($comment->list->getId(), $commentR->list->getId());
    }
}
