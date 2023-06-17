<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Comments\Controller\ApiController;
use Modules\Comments\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/comment(\?.*|$)' => [
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiCommentCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::COMMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiCommentUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::COMMENT,
            ],
        ],
    ],
    '^.*/comment/list(\?.*|$)' => [
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiCommentListUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::LIST,
            ],
        ],
    ],
    '^.*/comment/vote(\?.*|$)' => [
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiChangeCommentVote',
            'verb'       => RouteVerb::PUT | RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::VOTE,
            ],
        ],
    ],
];
