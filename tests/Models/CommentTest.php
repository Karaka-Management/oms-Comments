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
use Modules\Comments\Models\CommentStatus;
use Modules\Comments\Models\NullComment;
use Modules\Media\Models\Media;

/**
 * @internal
 */
final class CommentTest extends \PHPUnit\Framework\TestCase
{
    private Comment $comment;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->comment = new Comment();
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->comment->id);

        $date = new \DateTime('now');
        self::assertEquals($date->format('Y-m-d'), $this->comment->createdAt->format('Y-m-d'));
        self::assertEquals(0, $this->comment->createdBy->id);
        self::assertEquals(0, $this->comment->list);
        self::assertEquals(0, $this->comment->ref);
        self::assertEquals('', $this->comment->title);
        self::assertEquals('', $this->comment->content);
        self::assertEquals([], $this->comment->getMedia());
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testCreatedByInputOutput() : void
    {
        $this->comment->createdBy = new NullAccount(1);
        self::assertEquals(1, $this->comment->createdBy->id);
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testListInputOutput() : void
    {
        $this->comment->list = 3;
        self::assertEquals(3, $this->comment->list);
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testRefInputOutput() : void
    {
        $this->comment->ref = 2;
        self::assertEquals(2, $this->comment->ref);

        $this->comment->ref = new NullComment(3);
        self::assertEquals(3, $this->comment->ref->id);
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testTitleInputOutput() : void
    {
        $this->comment->title = 'Test Title';
        self::assertEquals('Test Title', $this->comment->title);
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testContentInputOutput() : void
    {
        $this->comment->content = 'Test Content';
        self::assertEquals('Test Content', $this->comment->content);
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->comment->setStatus(CommentStatus::INVISIBLE);
        self::assertEquals(CommentStatus::INVISIBLE, $this->comment->getStatus());
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testMediaInputOutput() : void
    {
        $this->comment->addMedia(new Media());
        self::assertCount(1, $this->comment->getMedia());
    }

    /**
     * @covers Modules\Comments\Models\Comment
     * @group module
     */
    public function testSerialize() : void
    {
        $this->comment->title     = 'Title';
        $this->comment->content   = 'Content';
        $this->comment->list      = 2;
        $this->comment->ref       = 1;
        $this->comment->createdBy = new NullAccount(2);

        $serialized = $this->comment->jsonSerialize();
        unset($serialized['createdAt']);
        unset($serialized['createdBy']);

        self::assertEquals(
            [
                'id'           => 0,
                'title'        => 'Title',
                'content'      => 'Content',
                'list'         => 2,
                'ref'          => 1,
                'status'       => CommentStatus::VISIBLE,
            ],
            $serialized
        );
    }
}
