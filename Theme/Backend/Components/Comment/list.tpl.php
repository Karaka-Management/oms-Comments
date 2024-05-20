<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Template
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Comments\Models\CommentListStatus;
use phpOMS\Uri\UriFactory;

/** @var \Modules\Comments\Theme\Backend\Components\Comment\ListView $this */
/** @var \Modules\Comments\Models\Comment[] $comments */
$comments = $this->commentList?->getComments() ?? [];
?>

<?php if (($this->commentList?->id ?? 0) !== 0) : ?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-body">
                <form id="iCommentListSettings" method="POST" action="<?= UriFactory::build('{/api}comment/list?id=' . $this->commentList->id . '&csrf={$CSRF}'); ?>">
                        <div class="form-group">
                            <div class="input-control">
                                <select name="commentlist_status">
                                    <option value="<?= CommentListStatus::ACTIVE; ?>"<?= $this->commentList->status === CommentListStatus::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml(':lstatus-' . CommentListStatus::ACTIVE); ?>
                                    <option value="<?= CommentListStatus::INACTIVE; ?>"<?= $this->commentList->status === CommentListStatus::INACTIVE ? ' selected' : ''; ?>><?= $this->getHtml(':lstatus-' . CommentListStatus::INACTIVE); ?>
                                    <option value="<?= CommentListStatus::LOCKED; ?>"<?= $this->commentList->status === CommentListStatus::LOCKED ? ' selected' : ''; ?>><?= $this->getHtml(':lstatus-' . CommentListStatus::LOCKED); ?>
                                </select>
                            </div>
                            <div class="input-control">
                                <label class="checkbox" for="iCommentVoting">
                                    <input id="iCommentVoting" type="checkbox" name="allow_voting" value="1"<?= $this->commentList->allowVoting ? ' checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    <?= $this->getHtml('Voting'); ?>
                                </label>
                            </div>
                            <div class="input-control">
                                <label class="checkbox" for="iCommentEdit">
                                    <input id="iCommentEdit" type="checkbox" name="allow_edit" value="1"<?= $this->commentList->allowEdit ? ' checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    <?= $this->getHtml('Edit'); ?>
                                </label>
                            </div>
                            <div class="input-control">
                                <label class="checkbox" for="iCommentFiles">
                                    <input id="iCommentFiles" type="checkbox" name="allow_upload" value="1"<?= $this->commentList->allowFiles ? ' checked' : ''; ?>>
                                    <span class="checkmark"></span>
                                    <?= $this->getHtml('Upload'); ?>
                                </label>
                            </div>
                            <div class="input-control">
                                <input name="saveCommentSettings" type="submit" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                            </div>
                        </div>
                </form>
            </div>
        </section>
    </div>
</div>

<?php
foreach ($comments as $comment) :
    $editPossible = $this->commentList->status === CommentListStatus::ACTIVE
        && $this->commentList->allowEdit
        && $comment->createdBy->id === $this->request->header->account;
?>
    <div class="row">
        <div class="col-xs-12">
            <?php if ($editPossible) : ?>
                <form id="iComment-<?= $comment->id; ?>" method="POST" action="<?= UriFactory::build('{/api}comment/post?id=' . $comment->id . '{?}&csrf={$CSRF}'); ?>"
                    data-ui-container="#iComment-<?= $comment->id; ?>"
                    data-ui-element=".portlet"
                    data-update-tpl="#iComment-<?= $comment->id; ?> .portlet-tpl">
                <template class="portlet-tpl">
                    <section class="portlet">
                        <div class="portlet-body">
                            <div class="form-group">
                            <textarea id="iComment" name="comment"
                                data-tpl-value="/comment" required></textarea>
                            </div>
                        </div>
                        <div class="portlet-foot">
                            <button class="save-form"><?= $this->getHtml('Save', '0', '0'); ?></button>
                            <button class="cancel cancel-form"><?= $this->getHtml('Cancel', '0', '0'); ?></button>
                        </div>
                    </section>
                </template>
            <?php endif; ?>
            <section class="portlet" data-id="<?= $comment->id; ?>">
                <div class="portlet-body">
                    <article id="iCommentRender-<?= $comment->id; ?>"<?php if ($editPossible) : ?>
                        data-tpl-value="/comment"
                        data-value="<?= $this->printTextarea($comment->contentRaw); ?>"<?php endif; ?>><?= $comment->content; ?></article>
                    <?php $files = $comment->files; foreach ($files as $file) : ?>
                         <span><a class="content" href="<?= UriFactory::build('{/base}/media/view?id=' . $file->id);?>"><?= $file->name; ?></a></span>
                    <?php endforeach; ?>
                </div>
                <div class="portlet-foot">
                    <a class="content" href="<?= UriFactory::build('{/base}/profile/view?{?}&id=' . $comment->createdBy->id); ?>">
                    <?= $this->printHtml($this->renderUserName(
                        '%3$s %2$s %1$s',
                        [$comment->createdBy->name1, $comment->createdBy->name2, $comment->createdBy->name3, $comment->createdBy->login ?? '']
                    )); ?>
                    </a>
                    <span><?= $comment->createdAt->format('Y-m-d H:i:s'); ?></span>
                    <?php if ($editPossible) : ?>
                    <div class="end-xs">
                        <button class="update-form"><?= $this->getHtml('Edit', '0', '0'); ?></button>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
            <?php if ($editPossible) : ?>
                </form>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>