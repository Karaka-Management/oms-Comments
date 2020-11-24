<?php declare(strict_types=1);
/** @var \Modules\Comments\Models\Comment[] $comments */
$comments = $this->commentList->getComments();
foreach ($comments as $comment) : ?>
    <div class="row">
        <div class="col-xs-12">
            <section class="portlet">
                <article>
                    <?= $comment->content; ?>
                </article>
            </section>
        </div>
    </div>
<?php endforeach; ?>