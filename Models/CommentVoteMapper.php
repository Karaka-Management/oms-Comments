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
final class CommentVoteMapper extends DataMapperAbstract
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    protected static array $columns = [
        'comments_comment_vote_id'          => ['name' => 'comments_comment_vote_id',          'type' => 'int',      'internal' => 'id'],
        'comments_comment_vote_score'       => ['name' => 'comments_comment_vote_score',  'type' => 'int',      'internal' => 'score'],
        'comments_comment_vote_comment'     => ['name' => 'comments_comment_vote_comment',  'type' => 'int',      'internal' => 'comment', 'readonly' => true],
        'comments_comment_vote_created_by'  => ['name' => 'comments_comment_vote_created_by',  'type' => 'int',      'internal' => 'createdBy', 'readonly' => true],
        'comments_comment_vote_created_at'  => ['name' => 'comments_comment_vote_created_at',  'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $table = 'comments_comment_vote';

    /**
     * Created at.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $createdAt = 'comments_comment_vote_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static string $primaryField = 'comments_comment_vote_id';
}
