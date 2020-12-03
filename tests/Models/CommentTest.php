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
use Modules\Comments\Models\NullComment;

/**
 * @internal
 */
class CommentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testDefault() : void
    {
        $comment = new Comment();
        self::assertEquals(0, $comment->getId());

        $date = new \DateTime('now');
        self::assertEquals($date->format('Y-m-d'), $comment->createdAt->format('Y-m-d'));
        self::assertEquals(0, $comment->createdBy->getId());
        self::assertEquals(0, $comment->getList());
        self::assertEquals(0, $comment->getRef());
        self::assertEquals('', $comment->title);
        self::assertEquals('', $comment->content);
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testGetSet() : void
    {
        $comment = new Comment();

        $comment->createdBy = new NullAccount(1);
        self::assertEquals(1, $comment->createdBy->getId());

        $comment->setList(2);
        self::assertEquals(2, $comment->getList());

        $comment->setRef(new NullComment(3));
        self::assertEquals(3, $comment->getRef()->getId());

        $comment->title = 'Test Title';
        self::assertEquals('Test Title', $comment->title);

        $comment->content = 'Test Content';
        self::assertEquals('Test Content', $comment->content);
    }
}
