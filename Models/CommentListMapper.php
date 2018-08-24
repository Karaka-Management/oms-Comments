<?php
/**
 * Orange Management
 *
 * PHP Version 7.2
 *
 * @package    Modules\Comments\Models
 * @copyright  Dennis Eichhorn
 * @license    OMS License 1.0
 * @version    1.0.0
 * @link       http://website.orange-management.de
 */
declare(strict_types=1);

namespace Modules\Comments\Models;

use Modules\Comments\Models\CommentMapper;
use phpOMS\DataStorage\Database\DataMapperAbstract;
use phpOMS\DataStorage\Database\Query\Builder;
use phpOMS\DataStorage\Database\Query\Column;
use phpOMS\DataStorage\Database\RelationType;

/**
 * Mapper class.
 *
 * @package    Modules\Comments\Models
 * @license    OMS License 1.0
 * @link       http://website.orange-management.de
 * @since      1.0.0
 */
class CommentListMapper extends DataMapperAbstract
{

    /**
     * Columns.
     *
     * @var array<string, array<string, string>>
     * @since 1.0.0
     */
    protected static $columns = [
        'comments_list_id' => ['name' => 'comments_list_id', 'type' => 'int', 'internal' => 'id'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array<string, string|null>>
     * @since 1.0.0
     */
    protected static $hasMany = [
        'comments' => [
            'mapper' => CommentMapper::class,
            'table'  => 'comments_comment',
            'dst'    => 'comments_comment_list',
            'src'    => null,
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $table = 'comments_list';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    protected static $primaryField = 'comments_list_id';
}
