<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Comments\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

use Modules\Admin\Models\Account;
use Modules\Admin\Models\NullAccount;

/**
 * Comment class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Comment implements \JsonSerializable
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
     * @var Account
     * @since 1.0.0
     */
    public Account $createdBy;

    /**
     * Created at
     *
     * @var \DateTimeImmutable
     * @since 1.0.0
     */
    public \DateTimeImmutable $createdAt;

    /**
     * Comment list this comment belongs to
     *
     * @var int
     * @since 1.0.0
     */
    public int $list = 0;

    /**
     * Title
     *
     * @var string
     * @since 1.0.0
     */
    public string $title = '';

    /**
     * Comment status
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = CommentStatus::VISIBLE;

    /**
     * Content
     *
     * @var string
     * @since 1.0.0
     */
    public string $content = '';

    /**
     * Content raw
     *
     * @var string
     * @since 1.0.0
     */
    public string $contentRaw = '';

    /**
     * Comment this is refering to
     *
     * @var null|int|self
     * @since 1.0.0
     */
    public $ref = null;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->createdBy = new NullAccount();
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'content'   => $this->content,
            'list'      => $this->list,
            'ref'       => $this->ref,
            'status'    => $this->status,
            'createdAt' => $this->createdAt,
            'createdBy' => $this->createdBy,
        ];
    }

    use \Modules\Media\Models\MediaListTrait;
}
