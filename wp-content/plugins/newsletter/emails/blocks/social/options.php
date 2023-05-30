<?php
/* @var $options array contains all the options the current block we're ediging contains */
/* @var $controls NewsletterControls */
/* @var $fields NewsletterFields */
?>

<p>Social profiles can be configured on company info panel.</p>

<?php $fields->select('type', 'Appearance', ['1'=>'Round colored', '2'=>'Round monochrome']) ?>

<?php $fields->select('width', 'Size', ['16'=>'16 px', '24'=>'24 px', '32'=>'32 px']) ?>

<?php $fields->block_commons() ?>
