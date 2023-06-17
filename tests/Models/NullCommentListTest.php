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

use Modules\Comments\Models\NullCommentList;

/**
 * @internal
 */
final class NullCommentListTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Comments\Models\NullCommentList
     * @group framework
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Comments\Models\CommentList', new NullCommentList());
    }

    /**
     * @covers Modules\Comments\Models\NullCommentList
     * @group framework
     */
    public function testId() : void
    {
        $null = new NullCommentList(2);
        self::assertEquals(2, $null->id);
    }
}
