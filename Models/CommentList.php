<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Comments\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

/**
 * Task class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
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
     * @var bool
     * @since 1.0.0
     */
    protected bool $isActive = true;

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

    /**
     * Is active
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function isActive() : bool
    {
        return $this->isActive;
    }

    /**
     * Set list activity
     *
     * @param bool $active Is active
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setActive(bool $active) : void
    {
        $this->isActive = $active;
    }
}
