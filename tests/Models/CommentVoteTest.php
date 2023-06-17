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

use Modules\Comments\Models\CommentVote;

/**
 * @internal
 */
final class CommentVoteTest extends \PHPUnit\Framework\TestCase
{
    private CommentVote $vote;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->vote = new CommentVote();
    }

    /**
     * @covers Modules\Comments\Models\CommentVote
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->vote->id);
        self::assertEquals(0, $this->vote->score);
        self::assertEquals(0, $this->vote->comment);
        self::assertEquals(0, $this->vote->createdBy);
        self::assertInstanceOf('\DateTimeImmutable', $this->vote->createdAt);
    }

    /**
     * @covers Modules\Comments\Models\CommentVote
     * @group module
     */
    public function testScoreInputOutput() : void
    {
        $this->vote->score = 1;
        self::assertEquals(1, $this->vote->score);
    }

    /**
     * @covers Modules\Comments\Models\CommentVote
     * @group module
     */
    public function testCommentInputOutput() : void
    {
        $this->vote->comment = 1;
        self::assertEquals(1, $this->vote->comment);
    }
}
