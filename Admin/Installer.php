<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Comments\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Comments\Admin;

use phpOMS\Module\InstallerAbstract;

/**
 * Install class.
 *
 * @package Modules\Comments\Admin
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;
}
