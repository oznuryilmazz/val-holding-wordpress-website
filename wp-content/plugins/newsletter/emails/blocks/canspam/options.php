<?php

/* @var $fields NewsletterFields */
?>

<p>
	<?php echo sprintf( __( 'Company data can be globally set on <a href="%s" target="_blank">company info panel</a>.', 'newsletter' ), '?page=newsletter_main_info' ); ?>
</p>

<?php $fields->font( 'font', __( 'Text', 'newsletter' ), [
	'family_default' => true,
	'size_default'   => true,
	'weight_default' => true
] ) ?>
<?php $fields->block_commons() ?>


