<?php
/* @var $options array contains all the options the current block we're ediging contains */
/* @var $controls NewsletterControls */
/* @var $fields NewsletterFields */
?>

<?php $controls->hidden('placeholder') ?>
<?php $fields->media('image', 'Choose an image', array('alt' => false)) ?>
<?php $fields->url('image-url', 'or direct image src URL 
        <br>(like <strong>personalized images</strong> and <strong>countdowns</strong> from <a href="https://niftyimages.com/" target="_blank">niftyimages.com</a>)') ?>
<?php $fields->text('image-alt', 'Alternative text') ?>
<?php $fields->url('url', __('Link URL', 'newsletter')) ?>

<div class="tnp-field-row">
    <div class="tnp-field-col-2">
        <?php $fields->size('width', __('Width', 'newsletter')) ?>
    </div>
    <div class="tnp-field-col-2">
        <?php $fields->align() ?>
    </div>
</div>

<?php $fields->block_commons() ?>

