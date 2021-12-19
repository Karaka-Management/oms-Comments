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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Mapper class.
 *
 * @package Modules\Comments\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class CommentVoteMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'comments_comment_vote_id'         => ['name' => 'comments_comment_vote_id',          'type' => 'int',               'internal' => 'id'],
        'comments_comment_vote_score'      => ['name' => 'comments_comment_vote_score',       'type' => 'int',               'internal' => 'score'],
        'comments_comment_vote_comment'    => ['name' => 'comments_comment_vote_comment',     'type' => 'int',               'internal' => 'comment', 'readonly' => true],
        'comments_comment_vote_created_by' => ['name' => 'comments_comment_vote_created_by',  'type' => 'int',               'internal' => 'createdBy', 'readonly' => true],
        'comments_comment_vote_created_at' => ['name' => 'comments_comment_vote_created_at',  'type' => 'DateTimeImmutable', 'internal' => 'createdAt', 'readonly' => true],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'comments_comment_vote';

    /**
     * Created at.
     *
     * @var string
     * @since 1.0.0
     */
    public const CREATED_AT = 'comments_comment_vote_created_at';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='comments_comment_vote_id';

    /**
     * Find vote for comment from user
     *
     * @param int $comment Comment id
     * @param int $account Account id
     *
     * @return CommentVote
     *
     * @since 1.0.0
     */
    public static function findVote(int $comment, int $account) : CommentVote
    {
        $results = self::getAll()->where('comment', $comment)->where('createdBy', $account)->execute();

        return empty($results) ? new NullCommentVote() : \reset($results);
    }
}
