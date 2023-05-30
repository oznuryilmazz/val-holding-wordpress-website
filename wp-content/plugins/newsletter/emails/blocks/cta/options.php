<?php
/* @var $fields NewsletterFields */

// Migration from old option names
if (!empty($controls->data['font_color'])) $controls->data['button_font_color'] = $controls->data['font_color'];
if (!empty($controls->data['url'])) $controls->data['button_url'] = $controls->data['url'];
if (!empty($controls->data['font_family'])) $controls->data['button_font_family'] = $controls->data['font_family'];
if (!empty($controls->data['font_size'])) $controls->data['button_font_size'] = $controls->data['font_size'];
if (!empty($controls->data['font_weight'])) $controls->data['button_font_weight'] = $controls->data['font_weight'];
if (!empty($controls->data['background'])) $controls->data['button_background'] = $controls->data['background'];
if (!empty($controls->data['text'])) $controls->data['button_label'] = $controls->data['text'];
if (!empty($controls->data['width'])) $controls->data['button_width'] = $controls->data['width'];

unset($controls->data['font_color']);
unset($controls->data['url']);
unset($controls->data['font_family']);
unset($controls->data['font_size']);
unset($controls->data['font_weight']);
unset($controls->data['background']);
unset($controls->data['text']);
unset($controls->data['width']);

?>

<?php $fields->select('schema', __('Schema', 'newsletter'), array('' => 'Custom', 'bright' => 'Bright', 'dark' => 'Dark'), ['after-rendering' => 'reload']) ?>

<?php $fields->button( 'button', 'Button layout', [
	'family_default' => true,
	'size_default'   => true,
	'weight_default' => true,
        'media' => true
] ) ?>

<div class="tnp-field-row">
    <div class="tnp-field-col-2">
        <?php $fields->size('button_width', __('Width', 'newsletter')) ?>
    </div>
    <div class="tnp-field-col-2">
        <?php $fields->select('button_align', 'Alignment', ['center' => __('Center'), 'left' => __('Left'), 'right' => __('Right')]) ?>
    </div>

</div>


<?php $fields->block_commons() ?>
