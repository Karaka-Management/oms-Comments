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

use phpOMS\Stdlib\Base\Enum;

/**
 * Comment Status enum.
 *
 * @package Modules\Comments\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
abstract class CommentListStatus extends Enum
{
    public const ACTIVE = 1;

    public const LOCKED = 2;

    public const INACTIVE = 3;
}
