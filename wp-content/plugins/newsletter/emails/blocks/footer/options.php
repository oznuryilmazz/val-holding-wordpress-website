<?php
/* @var $fields NewsletterFields */
?>


<?php $fields->text_on_off('view', __('View online link','newsletter')) ?>

<?php $fields->text_on_off('profile', __('Subscriber profile link','newsletter')) ?>

<?php $fields->text_on_off('unsubscribe', __('Unsubscribe link','newsletter')) ?>

<?php $fields->font( 'font', __( 'Text', 'newsletter' ), [
	'family_default' => true,
	'size_default'   => true,
	'weight_default' => true
] ) ?>

<?php $fields->block_commons() ?>
