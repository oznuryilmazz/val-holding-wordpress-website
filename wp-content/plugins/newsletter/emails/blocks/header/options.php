<?php
/* @var $options array contains all the options the current block we're ediging contains */
/* @var $controls NewsletterControls */
/* @var $fields NewsletterFields */
?>

<p>
    <?php echo sprintf(__('Company data can be globally set on <a href="%s" target="_blank">company info panel</a>.', 'newsletter'), '?page=newsletter_main_info'); ?><br>
    Use an image block to have a single top banner.
</p>

<?php
$fields->select('layout', __('Layout', 'newsletter'), [
    '' => __('Default', 'newsletter'),
    'logo' => __('Only the logo', 'newsletter'),
    'titlemotto' => 'Title and motto'
])
?>

<?php
$fields->font('font', __('Text', 'newsletter'), [
    'family_default' => true,
    'size_default' => true,
    'weight_default' => true
])
?>

<?php $fields->number('logo_width', __('Width')) ?>

<?php $fields->block_commons() ?>
