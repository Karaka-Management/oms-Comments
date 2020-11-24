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

use Modules\Admin\Models\NullAccount;
use Modules\Comments\Models\Comment;
use Modules\Comments\Models\CommentList;
use Modules\Comments\Models\CommentListMapper;

/**
 * @internal
 */
class CommentListMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Comments\Models\CommentListMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $list = new CommentList();

        $comment = new Comment();
        $comment->createdBy = new NullAccount(1);
        $comment->title = 'Test Comment';

        $list->addComment($comment);

        $id = CommentListMapper::create($list);
        self::assertGreaterThan(0, $list->getId());
        self::assertEquals($id, $list->getId());

        $listR = CommentListMapper::get($list->getId());
        self::assertEquals($id, $listR->getId());

        $actual = $listR->getComments();
        self::assertEquals($comment->title, \reset($actual)->title);
    }
}
