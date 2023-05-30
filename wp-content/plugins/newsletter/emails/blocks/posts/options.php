<?php
/* @var $options array contains all the options the current block we're ediging contains */
/* @var $controls NewsletterControls */
/* @var $fields NewsletterFields */

$extensions_url = '?page=newsletter_main_extension';
if (class_exists('NewsletterExtensions')) {
    $extensions_url = '?page=newsletter_extensions_index';
}
?>
<p>
    Custom post types can be added using our <a href="<?php echo $extensions_url ?>" target="_blank">Advanced Composer Blocks Addon</a>.
</p>

<?php if ($context['type'] == 'automated') { ?>

    <div class="tnp-field-box">
        <p>
            <strong>AUTOMATED</strong><br>
            While composing all posts are shown while on sending posts are extrated following the rules below. 
            <a href="https://www.thenewsletterplugin.com/documentation/addons/extended-features/automated-extension/#regeneration" target="_blank">Read more</a>.
        </p>
        <?php $fields->select('automated_disabled', '', ['' => 'Use the last newsletter date and...', '1' => 'Do not consider the last newsletter']) ?>

        <div class="tnp-field-row">
            <div class="tnp-field-col-2">
                <?php
                $fields->select('automated_include', __('If there are new posts', 'newsletter'),
                        [
                            'new' => __('Include only new posts', 'newsletter'),
                            'max' => __('Include specified max posts', 'newsletter')
                        ],
                        ['description' => '', 'class' => 'tnp-small'])
                ?>
            </div>
            <div class="tnp-field-col-2">
                <?php
                $fields->select('automated', __('If there are not new posts', 'newsletter'),
                        [
                            '' => 'Show the message below',
                            '1' => 'Do not send the newsletter',
                            '2' => 'Remove this block'
                        ],
                        ['description' => '', 'class' => 'tnp-small'])
                ?>
                <?php $fields->text('automated_no_contents', null, ['placeholder' => 'No new posts message']) ?>
            </div>
        </div>
        <div style="clear: both"></div>
    </div>
<?php } ?>


<?php
$fields->select('layout', __('Layout', 'newsletter'),
        [
            'one' => __('One column', 'newsletter'),
            'one-2' => __('One column variant', 'newsletter'),
            'two' => __('Two columns', 'newsletter'),
            'big-image' => __('One column, big image', 'newsletter'),
            'full-post' => __('Full post', 'newsletter')
])
?>


<div class="tnp-field-row">
    <label class="tnp-row-label"><?php _e('Post info', 'newsletter') ?></label>
    <div class="tnp-field-col-3">
        <?php $fields->checkbox('show_date', __('Show date', 'newsletter')) ?>
    </div>
    <div class="tnp-field-col-3">
        <?php $fields->checkbox('show_author', __('Show author', 'newsletter')) ?>
    </div>
    <div class="tnp-field-col-3">
        <?php $fields->checkbox('show_image', __('Show image', 'newsletter')) ?>
    </div>
    <div style="clear: both"></div>
</div>

<div class="tnp-field-row">
    <div class="tnp-field-col-2">
        <?php $fields->select_number('max', __('Max posts', 'newsletter'), 1, 40); ?>
    </div>
    <div class="tnp-field-col-2">
        <?php $fields->select_number('post_offset', __('Posts offset', 'newsletter'), 0, 20); ?>
    </div>
</div>

<div class="tnp-field-row">
    <div class="tnp-field-col-3">
        <?php $fields->number('excerpt_length', __('Excerpt length', 'newsletter'), array('min' => 0)); ?>
    </div>
    <div class="tnp-field-col-3">
        <?php $fields->select('excerpt_length_type', 'Count', ['' => __('Words', 'newsletter'), 'chars' => __('Chars', 'newsletter')]); ?>
    </div>
    <div class="tnp-field-col-3">
        <?php $fields->yesno('show_read_more_button', 'Show read more button') ?>
    </div>
    <div style="clear: both"></div>
</div>

<?php $fields->language(); ?>

<?php $fields->section(__('Filters', 'newsletter')) ?>
<?php $fields->categories(); ?>
<?php $fields->text('tags', __('Tags', 'newsletter'), ['description' => __('Comma separated')]); ?>

<?php $fields->section(__('Styles', 'newsletter')) ?>
<?php $fields->font('title_font', __('Title font', 'newsletter'), ['family_default' => true, 'size_default' => true, 'weight_default' => true]) ?>
<?php $fields->font('font', __('Excerpt font', 'newsletter'), ['family_default' => true, 'size_default' => true, 'weight_default' => true]) ?>
<?php
$fields->button('button', __('Read more button', 'newsletter'), [
    'url' => false,
    'family_default' => true,
    'size_default' => true,
    'weight_default' => true
])
?>

<?php $fields->block_commons() ?>

