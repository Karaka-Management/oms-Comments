<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Comments\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

/**
 * Comment list class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
class CommentList
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Comments
     *
     * @var array
     * @since 1.0.0
     */
    private array $comments = [];

    /**
     * Is active
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = CommentListStatus::ACTIVE;

    /**
     * Allow voting
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $allowVoting = true;

    /**
     * Allow editing
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $allowEdit = true;

    /**
     * Allow files
     *
     * @var bool
     * @since 1.0.0
     */
    public bool $allowFiles = true;

    /**
     * Get id.
     *
     * @return int Model id
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * Get the comments
     *
     * @return int[]|Comment[]
     *
     * @since 1.0.0
     */
    public function getComments() : array
    {
        return $this->comments;
    }

    /**
     * Add a comment
     *
     * @param mixed $comment Comment
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addComment($comment) : void
    {
        $this->comments[] = $comment;
    }
}
