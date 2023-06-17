<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Comments\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

use Modules\Admin\Models\Account;

/**
 * Comment vote class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class CommentVote
{
    /**
     * ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Account.
     *
     * @var int
     * @since 1.0.0
     */
    public int $createdBy = 0;

    /**
     * Created at
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Comment
     *
     * @var int
     * @since 1.0.0
     */
    public int $comment = 0;

    /**
     * Score
     *
     * @var int
     * @since 1.0.0
     */
    public int $score = 0;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

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
}
