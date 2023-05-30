<?php
/* @var $this Newsletter */
defined('ABSPATH') || exit;

include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();

$current_language = $this->get_current_language();

$is_all_languages = $this->is_all_languages();

//if (!$is_all_languages) {
//    $controls->warnings[] = 'You are configuring the language "<strong>' . $current_language . '</strong>". Switch to "all languages" to see every options.';
//}

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_main');
} else {

    if ($controls->is_action('save')) {
        $controls->data['googleplus_url'] = '';
        $this->merge_options($controls->data);
        $this->save_options($controls->data, 'info');
        $controls->add_message_saved();
    }
}

$controls->add_message('Message example with long text');
$controls->errors = 'Errors example with long text';
$controls->warnings[] = 'Warnings example with long text';
?>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">
        <?php $controls->title_help('#')?>

        <h2>Page main title</h2>
        <p>
            Page description with instruction and <a href="#">some links</a> and a <strong>bit of bold text</strong> and, why not, <code>some code</code>.
        </p>
        <ul>
            <li>Item 1</li>
            <li>Item 1</li>
            <li>Item 1</li>
        </ul>

    </div>
    <div id="tnp-body">

        <form method="post" action="">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-example1">Tab example 1</a></li>
                    <li><a href="#tabs-example2">Tab example 2</a></li>
                </ul>

                <div id="tabs-example1">
                    <p>
                        Optional introductory text of a tab <a href="#">with one link</a> and <strong>bold text</strong>.
                    </p>
                    <table class="form-table">
                        <tr>
                            <th>
                                Text field with long label<br>
                            </th>
                            <td>
                                <?php $controls->text('text'); ?>
                                <span class="description">Hidden help text <a href="#">with link</a> and <code>tech value</code></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Select field</th>
                            <td>
                                <?php $controls->select('select', ['1' => 'Option number 1', '2' => 'Option number 2', '3' => 'Option 3']); ?>
                                <p class="description">
                                    Help text under the field <a href="#">with link</a> and <code>tech value</code>.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th>Yes/No field</th>
                            <td>
                                <?php $controls->yesno('yn'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Multi checkbox</th>
                            <td>
                                <div class="tnpc-checkboxes">
                                    <label><input type="checkbox" id="options_sex" name="options[options_sex][]" value="f">&nbsp;Women</label><label><input type="checkbox" id="options_sex" name="options[options_sex][]" value="m">&nbsp;Men</label><label><input type="checkbox" id="options_sex" name="options[options_sex][]" value="n">&nbsp;Not specified</label>
                                    <div style="clear: both"></div>                            
                                </div>
                            </td>
                        </tr>
                    </table>

                    <h3>Option block title</h3>

                    <table class="form-table">
                        <tr>
                            <th>
                                Image selector field<br>
                            </th>
                            <td>
                                <?php $controls->media('image', 'medium'); ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="tabs-example2">

                    <h3>Widefat table</h3>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Parameter 1</td>
                                <td>
                                    This is the value, just text
                                </td>
                            </tr>
                            <tr>
                                <td><code>tecnical parameter</code></td>
                                <td>
                                    This is the value
                                </td>
                            </tr>
                            <tr>
                                <td>Parameter 2</td>
                                <td>
                                    This is the value, just text
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <div id="tabs-example3">
                </div>
            </div>

            <div class="tnp-buttons">
                <?php $controls->button_save(); ?>
            </div>

        </form>
    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>
