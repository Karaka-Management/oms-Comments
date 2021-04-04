<?php declare(strict_types=1);

use Modules\Comments\Models\CommentListStatus;

/** @var \Modules\Comments\Models\Comment[] $comments */
$comments = $this->commentList->getComments();
?>

<form method="POST" action="<?= \phpOMS\Uri\UriFactory::build('{/api}comment/list?id=' . $this->commentList->getId() . '{?}&csrf={$CSRF}'); ?>">
    <select name="commentlist_status">
        <option value="<?= CommentListStatus::ACTIVE; ?>"><?= $this->getHtml('lstatus-' . CommentListStatus::ACTIVE); ?>
        <option value="<?= CommentListStatus::INACTIVE; ?>"><?= $this->getHtml('lstatus-' . CommentListStatus::INACTIVE); ?>
        <option value="<?= CommentListStatus::LOCKED; ?>"><?= $this->getHtml('lstatus-' . CommentListStatus::LOCKED); ?>
    </select>

    <label class="checkbox" for="iComment">
        <input id="iComment" type="checkbox" name="allow_voting" value="1">
        <span class="checkmark"></span>
        <?= $this->getHtml('Voting'); ?>
    </label>

    <label class="checkbox" for="iComment">
        <input id="iComment" type="checkbox" name="allow_edit" value="1">
        <span class="checkmark"></span>
        <?= $this->getHtml('Edit'); ?>
    </label>

    <label class="checkbox" for="iComment">
        <input id="iComment" type="checkbox" name="allow_upload" value="1">
        <span class="checkmark"></span>
        <?= $this->getHtml('Upload'); ?>
    </label>
</form>

<?php
foreach ($comments as $comment) : ?>
    <div class="row">
        <div class="col-xs-12">
            <section class="portlet">
            	<div class="portlet-body">
	                <article>
	                    <?= $comment->content; ?>
	                </article>
            	</div>
            	<div class="portlet-foot">
            		<?= $this->printHtml(
                        \sprintf('%3$s %2$s %1$s', $comment->createdBy->name1, $comment->createdBy->name2, $comment->createdBy->name3)
                    ); ?>
            		<span class="floatRight"><?= $comment->createdAt->format('Y-m-d H:i:s'); ?></span>
            	</div>
            </section>
        </div>
    </div>
<?php endforeach; ?>
