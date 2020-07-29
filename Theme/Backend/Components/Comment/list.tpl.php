<?php
/** @var \Modules\Comments\Models\Comment[] $comments */
$comments = $this->commentList->getComments();
foreach ($comments as $comment) : ?>
    <div class="row">
        <div class="col-xs-12">
            <section class="portlet">
                <article>
                    <?= $comment->getContent(); ?>
                </article>
            </section>
        </div>
    </div>
<?php endforeach; ?>