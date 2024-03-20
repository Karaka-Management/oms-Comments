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
use Modules\Comments\Models\CommentListMapper;
use Modules\Comments\Models\CommentMapper;
use Modules\Comments\Models\CommentVote;
use Modules\Comments\Models\CommentVoteMapper;

/**
 * @internal
 */
final class CommentVoteMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Modules\Comments\Models\CommentVoteMapper
     * @group module
     */
    public function testCR() : void
    {
        $list = new CommentList();
        $lId  = CommentListMapper::create()->execute($list);

        $comment            = new Comment();
        $comment->title     = 'TestComment';
        $comment->createdBy = new NullAccount(1);
        $comment->list      = $lId;

        $cId = CommentMapper::create()->execute($comment);

        $vote            = new CommentVote();
        $vote->comment   = $cId;
        $vote->score     = 1;
        $vote->createdBy = 1;

        CommentVoteMapper::create()->execute($vote);

        $voteR = CommentvoteMapper::findVote($cId, 1);
        self::assertEquals($vote->comment, $voteR->comment);
        self::assertEquals($vote->createdBy, $voteR->createdBy);
    }
}
