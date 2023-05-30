<?php

/* @var $fields NewsletterFields */

//$fields->controls->data['schema'] = '';
?>
<?php //$fields->select('schema', __('Schema', 'newsletter'), array('' => 'Custom', 'bright' => 'Bright', 'dark' => 'Dark', 'red' => 'Red'), ['after-rendering' => 'reload']) ?>

<?php $fields->text('text', __('Text', 'newsletter')) ?>
<?php $fields->font('font', false, ['family_default'=>true, 'size_default'=>true, 'weight_default'=>true]) ?>
<?php $fields->align() ?>


<?php $fields->block_commons() ?>
