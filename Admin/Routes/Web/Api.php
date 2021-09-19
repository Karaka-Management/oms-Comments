<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

use Modules\Comments\Controller\ApiController;
use Modules\Comments\Models\PermissionState;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/comment(\?.*|$)' => [
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiCommentCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::COMMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiCommentUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::COMMENT,
            ],
        ],
    ],
    '^.*/comment/list(\?.*|$)' => [
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiCommentListUpdate',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::LIST,
            ],
        ],
    ],
    '^.*/comment/vote(\?.*|$)' => [
        [
            'dest'       => '\Modules\Comments\Controller\ApiController:apiChangeCommentVote',
            'verb'       => RouteVerb::PUT | RouteVerb::SET,
            'permission' => [
                'module' => ApiController::MODULE_NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionState::VOTE,
            ],
        ],
    ],
];
