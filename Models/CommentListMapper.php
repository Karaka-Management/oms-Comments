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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Mapper class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of CommentList
 * @extends DataMapperFactory<T>
 */
final class CommentListMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'comments_list_id'           => ['name' => 'comments_list_id',           'type' => 'int',  'internal' => 'id'],
        'comments_list_status'       => ['name' => 'comments_list_status',       'type' => 'int',  'internal' => 'status'],
        'comments_list_allow_voting' => ['name' => 'comments_list_allow_voting', 'type' => 'bool', 'internal' => 'allowVoting'],
        'comments_list_allow_edit'   => ['name' => 'comments_list_allow_edit',   'type' => 'bool', 'internal' => 'allowEdit'],
        'comments_list_allow_files'  => ['name' => 'comments_list_allow_files',  'type' => 'bool', 'internal' => 'allowFiles'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'comments' => [
            'mapper'   => CommentMapper::class,
            'table'    => 'comments_comment',
            'self'     => 'comments_comment_list',
            'external' => null,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'comments_list';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'comments_list_id';
}
