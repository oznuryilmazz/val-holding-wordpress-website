<?php
/*
 * @var $options array contains all the options the current block we're ediging contains
 * @var $controls NewsletterControls
 */
/* @var $fields NewsletterFields */

$fields->controls->data['schema'] = '';
?>

<div class="tnp-field-row">
    <div class="tnp-field-col-2">
        <?php $fields->select('layout', __('Layout', 'newsletter'), array('full' => 'Full', 'left' => 'Image left', 'right' => 'Image right')) ?>
    </div>
    <div class="tnp-field-col-2">
        <?php $fields->select('schema', __('Schema', 'newsletter'), array('' => 'Custom', 'bright' => 'Bright', 'dark' => 'Dark'), ['after-rendering' => 'reload']) ?>
    </div>
</div>

<?php $fields->text('title', __('Title', 'newsletter')) ?>

<?php $fields->font('title_font', '', ['family_default'=>true, 'size_default'=>true, 'weight_default'=>true]) ?>

<?php $fields->media('image', __('Image', 'newsletter'), array('alt' => true)) ?>

<?php $fields->textarea('text', __('Text', 'newsletter')) ?>
<?php $fields->font( 'font', '', [ 'family_default' => true, 'size_default' => true, 'weight_default' => true ] ) ?>

<?php $fields->button('button', __('Button', 'newsletter'), [
	'family_default' => true,
	'size_default'   => true,
	'weight_default' => true
]) ?>

<?php $fields->block_commons() ?>

