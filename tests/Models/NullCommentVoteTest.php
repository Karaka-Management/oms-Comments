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

use Modules\Comments\Models\NullCommentVote;

/**
 * @internal
 */
final class NullCommentVoteTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Comments\Models\NullCommentVote
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Comments\Models\CommentVote', new NullCommentVote());
    }

    /**
     * @covers Modules\Comments\Models\NullCommentVote
     * @group module
     */
    public function testId() : void
    {
        $null = new NullCommentVote(2);
        self::assertEquals(2, $null->id);
    }

    /**
     * @covers Modules\Comments\Models\NullCommentVote
     * @group module
     */
    public function testJsonSerialize() : void
    {
        $null = new NullCommentVote(2);
        self::assertEquals(['id' => 2], $null->jsonSerialize());
    }
}
