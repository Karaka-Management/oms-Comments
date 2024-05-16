<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Comments\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

use Modules\Admin\Models\AccountMapper;
use Modules\Media\Models\MediaMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Comment mapper class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Comment
 * @extends DataMapperFactory<T>
 */
final class CommentMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'comments_comment_id'          => ['name' => 'comments_comment_id',          'type' => 'int',               'internal' => 'id'],
        'comments_comment_title'       => ['name' => 'comments_comment_title',       'type' => 'string',            'internal' => 'title'],
        'comments_comment_status'      => ['name' => 'comments_comment_status',      'type' => 'int',               'internal' => 'status'],
        'comments_comment_content'     => ['name' => 'comments_comment_content',     'type' => 'string',            'internal' => 'content'],
        'comments_comment_content_raw' => ['name' => 'comments_comment_content_raw', 'type' => 'string',            'internal' => 'contentRaw'],
        'comments_comment_list'        => ['name' => 'comments_comment_list',        'type' => 'int',               'internal' => 'list'],
        'comments_comment_ref'         => ['name' => 'comments_comment_ref',         'type' => 'int',               'internal' => 'ref'],
        'comments_comment_created_by'  => ['name' => 'comments_comment_created_by',  'type' => 'int',               'internal' => 'createdBy', 'readonly' => true],
        'comments_comment_created_at'  => ['name' => 'comments_comment_created_at',  'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'createdBy' => [
            'mapper'   => AccountMapper::class,
            'external' => 'comments_comment_created_by',
        ],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'files' => [
            'mapper'   => MediaMapper::class,
            'table'    => 'comments_comment_media',
            'external' => 'comments_comment_media_dst',
            'self'     => 'comments_comment_media_src',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'comments_comment';

    /**
     * Created at.
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'comments_comment_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'comments_comment_id';
}
