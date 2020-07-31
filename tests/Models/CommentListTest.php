<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Comments\tests\Models;

use Modules\Comments\Models\Comment;
use Modules\Comments\Models\CommentList;

/**
 * @internal
 */
class CommentListTest extends \PHPUnit\Framework\TestCase
{
    public function testDefault() : void
    {
        $list = new CommentList();
        self::assertEquals(0, $list->getId());
        self::assertEquals([], $list->getComments());
    }

    public function testGetSet() : void
    {
        $list    = new CommentList();
        $comment = new Comment();
        $comment->setTitle('Test Title');
        $comment->setContentRaw('TestRaw');
        $comment->setContent('Test Content');

        $list->addComment($comment);
        self::assertEquals('Test Title', $list->getComments()[0]->getTitle());
        self::assertEquals('TestRaw', $list->getComments()[0]->getContentRaw());
        self::assertEquals('Test Content', $list->getComments()[0]->getcontent());
    }
}
