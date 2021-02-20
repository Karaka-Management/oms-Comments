<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Comments\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

use phpOMS\DataStorage\Database\DataMapperAbstract;

/**
 * Mapper class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class CommentListMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'comments_list_id'            => ['name' => 'comments_list_id', 'type' => 'int', 'internal' => 'id'],
        'comments_list_active'        => ['name' => 'comments_list_active', 'type' => 'bool', 'internal' => 'isActive'],
        'comments_list_allow_comment' => ['name' => 'comments_list_allow_comment', 'type' => 'bool', 'internal' => 'allowComment'],
        'comments_list_allow_voting'  => ['name' => 'comments_list_allow_voting', 'type' => 'bool', 'internal' => 'allowVoting'],
        'comments_list_allow_edit'    => ['name' => 'comments_list_allow_edit', 'type' => 'bool', 'internal' => 'allowEdit'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    protected static array $hasMany = [
        'comments' => [
            'mapper'       => CommentMapper::class,
            'table'        => 'comments_comment',
            'self'         => 'comments_comment_list',
            'external'     => null,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'comments_list';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'comments_list_id';
}
