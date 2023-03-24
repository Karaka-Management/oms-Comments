<?php
/**
 * Karaka
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

use phpOMS\Stdlib\Base\Enum;

/**
 * Comment Status enum.
 *
 * @package Modules\Comments\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class CommentListStatus extends Enum
{
    public const ACTIVE = 1;

    public const LOCKED = 2;

    public const INACTIVE = 3;
}
