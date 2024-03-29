<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Template
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
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
                <form id="iComentListSettings" method="POST" action="<?= UriFactory::build('{/api}comment/list?id=' . $this->commentList->id . '{?}&csrf={$CSRF}'); ?>">
                        <div class="form-group">
                            <div class="input-control">
                                <select name="commentlist_status">
                                    <option value="<?= CommentListStatus::ACTIVE; ?>"<?= $this->commentList->status === CommentListStatus::ACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('lstatus-' . CommentListStatus::ACTIVE); ?>
                                    <option value="<?= CommentListStatus::INACTIVE; ?>"<?= $this->commentList->status === CommentListStatus::INACTIVE ? ' selected' : ''; ?>><?= $this->getHtml('lstatus-' . CommentListStatus::INACTIVE); ?>
                                    <option value="<?= CommentListStatus::LOCKED; ?>"<?= $this->commentList->status === CommentListStatus::LOCKED ? ' selected' : ''; ?>><?= $this->getHtml('lstatus-' . CommentListStatus::LOCKED); ?>
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
foreach ($comments as $comment) : ?>
    <div class="row">
        <div class="col-xs-12">
            <section class="portlet">
                <div class="portlet-body">
                    <article><?= $comment->content; ?></article>
                    <?php $files = $comment->files; foreach ($files as $file) : ?>
                         <span><a class="content" href="<?= UriFactory::build('{/base}/media/view?id=' . $file->id);?>"><?= $file->name; ?></a></span>
                    <?php endforeach; ?>
                </div>
                <div class="portlet-foot">
                    <a class="content" href="<?= UriFactory::build('profile/view?{?}&id=' . $comment->createdBy->id); ?>">
                    <?= $this->printHtml($this->renderUserName(
                        '%3$s %2$s %1$s',
                        [$comment->createdBy->name1, $comment->createdBy->name2, $comment->createdBy->name3, $comment->createdBy->login ?? '']
                    )); ?>
                    </a>
                    <span class="rf"><?= $comment->createdAt->format('Y-m-d H:i:s'); ?></span>
                </div>
            </section>
        </div>
    </div>
<?php endforeach; ?>
<?php endif; ?>