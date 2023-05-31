<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Comments
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
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
 * @license OMS License 2.0
 * @link    https://jingga.app
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
    public function apiCommentListCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
    public function apiCommentListUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Comments\Models\CommentList $old */
        $old = CommentListMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $old = clone $old;
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
        /** @var \Modules\Comments\Models\CommentList $list */
        $list              = CommentListMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $list->allowEdit   = $request->getDataBool('allow_edit') ?? $list->allowEdit;
        $list->allowVoting = $request->getDataBool('allow_voting') ?? $list->allowVoting;
        $list->allowFiles  = $request->getDataBool('allow_upload') ?? $list->allowFiles;
        $list->status      = $request->getDataInt('commentlist_status') ?? $list->status;

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
    public function apiCommentCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateCommentCreate($request))) {
            $response->data['comment_create'] = new FormValidation($val);
            $response->header->status         = RequestStatusCode::R_400;

            return;
        }

        $comment = $this->createCommentFromRequest($request);
        $this->createModel($request->header->account, $comment, CommentMapper::class, 'comment', $request->getOrigin());

        if (!empty($request->files)
            || !empty($request->getDataJson('media'))
        ) {
            $this->createCommentMedia($comment, $request);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Comment', 'Comment successfully created', $comment);
    }

    /**
     * Create media for comment
     *
     * @param Comment         $comment Comment
     * @param RequestAbstract $request Request data incl. files to upload
     *
     * @return void
     *
     * @since 1.0.0
     */
    private function createCommentMedia(Comment $comment, RequestAbstract $request) : void
    {
        $path = $this->createCommentDir($comment);

        /** @var \Modules\Admin\Models\Account $account */
        $account = AccountMapper::get()->where('id', $request->header->account)->execute();

        if (!empty($uploadedFiles = $request->files)) {
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
                $this->createModelRelation(
                    $request->header->account,
                    $comment->id,
                    $media->id,
                    CommentMapper::class,
                    'media',
                    '',
                    $request->getOrigin()
                );

                $accountPath = '/Accounts/' . $account->id . ' ' . $account->login
                    . '/Comments/'
                    . $comment->createdAt->format('Y/m')
                    . '/' . $comment->id;

                $ref            = new Reference();
                $ref->name      = $media->name;
                $ref->source    = new NullMedia($media->id);
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($accountPath);

                $this->createModel($request->header->account, $ref, ReferenceMapper::class, 'media_reference', $request->getOrigin());

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        $accountPath,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files/Accounts/' . $account->id . '/Comments/' . $comment->createdAt->format('Y/m') . '/' . $comment->id
                    );
                }

                $this->createModelRelation(
                    $request->header->account,
                    $collection->id,
                    $ref->id,
                    CollectionMapper::class,
                    'sources',
                    '',
                    $request->getOrigin()
                );
            }
        }

        if (!empty($mediaFiles = $request->getDataJson('media'))) {
            $collection = null;

            foreach ($mediaFiles as $file) {
                /** @var \Modules\Media\Models\Media $media */
                $media = MediaMapper::get()->where('id', (int) $file)->limit(1)->execute();
                $this->createModelRelation(
                    $request->header->account,
                    $comment->id,
                    $media->id,
                    CommentMapper::class,
                    'media',
                    '',
                    $request->getOrigin()
                );

                $ref            = new Reference();
                $ref->name      = $media->name;
                $ref->source    = new NullMedia($media->id);
                $ref->createdBy = new NullAccount($request->header->account);
                $ref->setVirtualPath($path);

                $this->createModel($request->header->account, $ref, ReferenceMapper::class, 'media_reference', $request->getOrigin());

                if ($collection === null) {
                    $collection = $this->app->moduleManager->get('Media')->createRecursiveMediaCollection(
                        $path,
                        $request->header->account,
                        __DIR__ . '/../../../Modules/Media/Files' . $path
                    );
                }

                $this->createModelRelation(
                    $request->header->account,
                    $collection->id,
                    $ref->id,
                    CollectionMapper::class,
                    'sources',
                    '',
                    $request->getOrigin()
                );
            }
        }
    }

    /**
     * Create media directory path
     *
     * @param Comment $comment Comment
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function createCommentDir(Comment $comment) : string
    {
        return '/Modules/Comments/'
            . $comment->createdAt->format('Y/m/d') . '/'
            . $comment->id;
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
        if (($val['plain'] = !$request->hasData('plain'))
            || ($val['list'] = !$request->hasData('list'))
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
        $comment->title      = $request->getDataString('title') ?? '';
        $comment->contentRaw = $request->getDataString('plain') ?? '';
        $comment->content    = Markdown::parse($request->getDataString('plain') ?? '');
        $comment->ref        = $request->hasData('ref') ? (int) $request->getData('ref') : null;
        $comment->list       = $request->getDataInt('list') ?? 0;

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
    public function apiCommentUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Comments\Models\Comment $old */
        $old = CommentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $old = clone $old;
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
        /** @var \Modules\Comments\Models\Comment $comment */
        $comment             = CommentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $comment->title      = $request->getDataString('title') ?? $comment->title;
        $comment->contentRaw = $request->getDataString('plain') ?? $comment->contentRaw;
        $comment->content    = Markdown::parse((string) ($request->getData('plain') ?? $comment->contentRaw));
        $comment->ref        = $request->getDataInt('ref');

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
    public function apiCommentGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Comments\Models\Comment $comment */
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
    public function apiCommentDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var \Modules\Comments\Models\Comment $comment */
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
    public function apiChangeCommentVote(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateCommentVote($request))) {
            $response->data['qa_answer_vote'] = new FormValidation($val);
            $response->header->status         = RequestStatusCode::R_400;

            return;
        }

        /** @var \Modules\Comments\Models\CommentVote $vote */
        $vote = CommentVoteMapper::findVote((int) $request->getData('id'), $request->header->account);

        if ($vote->id === 0) {
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
        if (($val['id'] = !$request->hasData('id'))
            || ($val['type'] = ($request->getDataInt('type') < -1 || $request->getDataInt('type') > 1))
        ) {
            return $val;
        }

        return [];
    }
}
