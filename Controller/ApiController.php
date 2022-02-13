<?php

/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Comments
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */

declare(strict_types=1);

namespace Modules\Comments\Controller;

use Modules\Admin\Models\AccountMapper;
use Modules\Admin\Models\NullAccount;
use Modules\Comments\Models\Comment;
use Modules\Comments\Models\CommentList;
use Modules\Comments\Models\CommentListMapper;
use Modules\Comments\Models\CommentMapper;
use Modules\Comments\Models\CommentVote;
use Modules\Comments\Models\CommentVoteMapper;
use Modules\Comments\Models\NullCommentVote;
use Modules\Media\Models\CollectionMapper;
use Modules\Media\Models\MediaMapper;
use Modules\Media\Models\NullMedia;
use Modules\Media\Models\Reference;
use Modules\Media\Models\ReferenceMapper;
use phpOMS\Message\Http\RequestStatusCode;
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
        $this->createModel($request->header->account, $commentList, CommentListMapper::class, 'comment_list', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment List', 'Comment list successfully created', $commentList);
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

        return $list;
    }

    /**
     * Api method to update comment list
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
    public function apiCommentListUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $old = clone CommentListMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateCommentListFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, CommentListMapper::class, 'comment_list', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment List', 'Comment list successfully updated', $new);
    }

    /**
     * Method to update comment from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return CommentList
     *
     * @since 1.0.0
     */
    private function updateCommentListFromRequest(RequestAbstract $request) : CommentList
    {
        $list              = CommentListMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $list->allowEdit   = (bool) ($request->getData('allow_edit') ?? $list->allowEdit);
        $list->allowVoting = (bool) ($request->getData('allow_voting') ?? $list->allowVoting);
        $list->allowFiles  = (bool) ($request->getData('allow_upload') ?? $list->allowFiles);
        $list->status      = (int) ($request->getData('commentlist_status') ?? $list->status);

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
            $response->set('comment_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $comment = $this->createCommentFromRequest($request);
        $this->createModel($request->header->account, $comment, CommentMapper::class, 'comment', $request->getOrigin());

        if (!empty($request->getFiles() ?? [])
            || !empty($request->getDataJson('media') ?? [])
        ) {
            $this->createCommentMedia($comment, $request);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully created', $comment);
    }

    private function createCommentMedia(Comment $comment, RequestAbstract $request) : void
    {
        $path = $this->createCommentDir($comment);
        $account = AccountMapper::get()->where('id', $request->header->account)->execute();

        if (!empty($uploadedFiles = $request->getFiles() ?? [])) {
            $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
                [],
                [],
                $uploadedFiles,
                $request->header->account,
                __DIR__ . '/../../../Modules/Media/Files' . $path,
                $path,
            );

            $collection = null;

            foreach ($uploaded as $media) {
                MediaMapper::create()->execute($media);
                CommentMapper::writer()->createRelationTable('media', [$media->getId()], $comment->getId());

                $ref = new Reference();
                $ref->source = new NullMedia($media->getId());
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($accountPath = '/Accounts/' . $account->getId() . ' ' . $account->login . '/Comments/' . $comment->createdAt->format('Y') . '/' . $comment->createdAt->format('m') . '/' . $comment->getId());

                ReferenceMapper::create()->execute($ref);

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        '/Modules/Media/Files',
                        $accountPath,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files/Accounts/' . $account->getId() . '/Comments/' . $comment->createdAt->format('Y') . '/' . $comment->createdAt->format('m') . '/' . $comment->getId()
                    );
                }

                CollectionMapper::writer()->createRelationTable('sources', [$ref->getId()], $collection->getId());
            }
        }

        if (!empty($mediaFiles = $request->getDataJson('media') ?? [])) {
            $collection = null;

            foreach ($mediaFiles as $media) {
                CommentMapper::writer()->createRelationTable('media', [(int) $media], $comment->getId());

                $ref = new Reference();
                $ref->source = new NullMedia((int) $media);
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($path);

                ReferenceMapper::create()->execute($ref);

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        '/Modules/Media/Files',
                        $path,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files' . $path
                    );
                }

                CollectionMapper::writer()->createRelationTable('sources', [$ref->getId()], $collection->getId());
            }
        }
    }

    private function createCommentDir(Comment $comment) : string
    {
        return '/Modules/Comments/'
            . $comment->createdAt->format('Y') . '/'
            . $comment->createdAt->format('m') . '/'
            . $comment->createdAt->format('d') . '/'
            . $comment->getId();
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
        if (($val['plain'] = empty($request->getData('plain')))
            || ($val['list'] = empty($request->getData('list')))
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
        $comment             = new Comment();
        $comment->createdBy  = new NullAccount($request->header->account);
        $comment->title      = (string) ($request->getData('title') ?? '');
        $comment->contentRaw = (string) ($request->getData('plain') ?? '');
        $comment->content    = Markdown::parse((string) ($request->getData('plain') ?? ''));
        $comment->ref        = $request->getData('ref') !== null ? (int) $request->getData('ref') : null;
        $comment->list       = (int) ($request->getData('list') ?? 0);

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
        $old = clone CommentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateCommentFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, CommentMapper::class, 'comment', $request->getOrigin());
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
        $comment             = CommentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $comment->title      = $request->getData('title') ?? $comment->getTitle();
        $comment->contentRaw = $request->getData('plain') ?? $comment->getContentRaw();
        $comment->content    = Markdown::parse((string) ($request->getData('plain') ?? $comment->getPlain()));
        $comment->ref        = $request->getData('ref') ?? $comment->ref;

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
        $comment = CommentMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
        $comment = CommentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $comment, CommentMapper::class, 'comment', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully deleted', $comment);
    }

    /**
     * Api method to change vote
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
    public function apiChangeCommentVote(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateCommentVote($request))) {
            $response->set('qa_answer_vote', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $vote = CommentVoteMapper::findVote((int) $request->getData('id'), $request->header->account);

        if ($vote instanceof NullCommentVote) {
            $new            = new CommentVote();
            $new->score     = (int) $request->getData('type');
            $new->comment   = (int) $request->getData('id');
            $new->createdBy = $request->header->account;

            $this->createModel($request->header->account, $new, CommentVoteMapper::class, 'comment_vote', $request->getOrigin());
            $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Vote', 'Sucessfully voted.', $new);
        } else {
            $new        = clone $vote;
            $new->score = (int) $request->getData('type');

            $this->updateModel($request->header->account, $vote, $new, CommentVoteMapper::class, 'comment_vote', $request->getOrigin());
            $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Vote', 'Vote successfully changed.', $new);
        }
    }

    /**
     * Validate answer vote request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateCommentVote(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = empty($request->getData('id')))
            || ($val['type'] = ($request->getData('type', 'int') < -1 || $request->getData('type', 'int') > 1))
        ) {
            return $val;
        }

        return [];
    }
}
