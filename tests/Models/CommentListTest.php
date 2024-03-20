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

use Modules\Comments\Models\Comment;
use Modules\Comments\Models\CommentList;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Comments\Models\CommentList::class)]
final class CommentListTest extends \PHPUnit\Framework\TestCase
{
    private CommentList $list;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->list = new CommentList();
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->list->id);
        self::assertEquals([], $this->list->getComments());
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testGetSet() : void
    {
        $comment             = new Comment();
        $comment->title      = 'Test Title';
        $comment->contentRaw = 'TestRaw';
        $comment->content    = 'Test Content';

        $this->list->addComment($comment);
        self::assertEquals('Test Title', $this->list->getComments()[0]->title);
        self::assertEquals('TestRaw', $this->list->getComments()[0]->contentRaw);
        self::assertEquals('Test Content', $this->list->getComments()[0]->content);
    }
}
