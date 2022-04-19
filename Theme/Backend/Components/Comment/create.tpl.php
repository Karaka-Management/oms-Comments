<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Template
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <form id="commentCreate" method="PUT" action="<?= UriFactory::build('{/api}comment/list?id={!#commentCreate [name=comment]}&csrf={$CSRF}'); ?>">
                <div class="portlet-head">Create Comment</div>
                <div class="portlet-body">
                    <textarea name="comment">For writing a new comment... of course this is just a placeholder</textarea>
                </div>
                <div class="portlet-foot"><input type="submit" name="createButton" id="iCreateButton" value="<?= $this->getHtml('Create', '0', '0'); ?>"></div>
            </form>
        </section>
    </div>
</div>