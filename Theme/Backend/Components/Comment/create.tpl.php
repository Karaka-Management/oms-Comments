<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Template
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/** @var \Modules\Comments\Theme\Backend\Components\Comment\CreateView $this */
?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <form id="commentCreate" class="Comments_create" method="PUT" action="<?= UriFactory::build('{/api}comment/list?id={!#commentCreate [name=comment]}&csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Comment', 'Comments', 'Backend'); ?></div>
                <div class="portlet-body">
                    <textarea name="comment"></textarea>
                </div>
                <div class="portlet-foot"><input type="submit" name="createButton" id="iCreateButton" value="<?= $this->getHtml('Create', '0', '0'); ?>"></div>
            </form>
        </section>
    </div>
</div>