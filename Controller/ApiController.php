<?php

/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Comments
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */

declare(strict_types=1);

namespace Modules\Comments\Controller;

use Modules\Admin\Models\NullAccount;
use Modules\Comments\Models\Comment;
use Modules\Comments\Models\CommentList;
use Modules\Comments\Models\CommentListMapper;
use Modules\Comments\Models\CommentMapper;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Utils\Parser\Markdown\Markdown;

/**
 * Comments controller class.
 *
 * @package Modules\Comments
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Api method to create comment list
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCommentListCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $commentList = $this->createCommentList();
        $this->createModel($request->getHeader()->getAccount(), $commentList, CommentListMapper::class, 'comment_list', $request->getOrigin());
    }

    /**
     * Create a comment list
     *
     * @return CommentList
     *
     * @since 1.0.0
     */
    public function createCommentList() : CommentList
    {
        $list = new CommentList();
        // @todo: allow config

        return $list;
    }

    /**
     * Api method to create comment
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCommentCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateCommentCreate($request))) {
            $response->set('news_create', new FormValidation($val));
            $response->getHeader()->setStatusCode(RequestStatusCode::R_400);

            return;
        }

        $comment = $this->createCommentFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $comment, CommentMapper::class, 'comment', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully created', $comment);
    }

    /**
     * Validate comment create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateCommentCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = empty($request->getData('title')))
            || ($val['plain'] = empty($request->getData('plain')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create comment from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Comment
     *
     * @since 1.0.0
     */
    private function createCommentFromRequest(RequestAbstract $request) : Comment
    {
        $comment = new Comment();
        $comment->setCreatedBy(new NullAccount($request->getHeader()->getAccount()));
        $comment->setTitle((string) ($request->getData('title') ?? ''));
        $comment->setContentRaw($request->getData('plain') ?? '');
        $comment->setContent(Markdown::parse((string) ($request->getData('plain') ?? '')));
        $comment->setRef($request->getData('ref') !== null ? (int) $request->getData('ref') : null);
        $comment->setList((int) ($request->getData('list') ?? 0));

        return $comment;
    }

    /**
     * Api method to create comment
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCommentUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone CommentMapper::get((int) $request->getData('id'));
        $new = $this->updateCommentFromRequest($request);
        $this->updateModel($request->getHeader()->getAccount(), $old, $new, CommentMapper::class, 'comment', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully updated', $new);
    }

    /**
     * Method to update comment from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Comment
     *
     * @since 1.0.0
     */
    private function updateCommentFromRequest(RequestAbstract $request) : Comment
    {
        $comment = CommentMapper::get((int) $request->getData('id'));
        $comment->setTitle($request->getData('title') ?? $comment->getTitle());
        $comment->setContentRaw($request->getData('plain') ?? $comment->getContentRaw());
        $comment->setContent(Markdown::parse((string) ($request->getData('plain') ?? $comment->getPlain())));
        $comment->setRef($request->getData('ref') ?? $comment->getRef());

        return $comment;
    }

    /**
     * Api method to get a comment
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCommentGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $comment = CommentMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully returned', $comment);
    }

    /**
     * Api method to delete comment
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCommentDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $comment = CommentMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->getHeader()->getAccount(), $comment, CommentMapper::class, 'comment', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully deleted', $comment);
    }
}
