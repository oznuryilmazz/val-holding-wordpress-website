<?php // phpcs:ignoreFile ?>
    <h3><?php esc_html_e('Other Available Lists', 'wp-mailinglist'); ?></h3>
<p><?php esc_html_e('You can subscribe to our other mailing list/s as well.', 'wp-mailinglist'); ?></p>

<?php if (!empty($success) && $success == true) : ?>
	<div class="ui-state-highlight ui-corner-all">
		<p><i class="fa fa-check"></i> <?php echo $successmessage; ?></p>
	</div>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
<?php endif; ?>

<?php if (!empty($otherlists)) : ?>
    <table>
        <tbody>
            <?php foreach ($otherlists as $list_id) : ?>
                <?php $Db -> model = $Mailinglist -> model; ?>
                <?php if ($mailinglist = $Db -> find(array('id' => $list_id))) : ?>
                    <tr>
                        <td>
							<?php echo esc_html($mailinglist -> title); ?>
                            <?php if ($mailinglist -> paid == "Y") : ?>
                            	<?php $intervals = $this -> get_option('intervals'); ?>
                            	<span class="wpmlcustomfieldcaption"><small>(<?php echo $Html -> currency() . '' . number_format($mailinglist -> price, 2, '.', '') . ' ' . $intervals[$mailinglist -> interval]; ?>)</small></span>
                            <?php endif; ?>
                        </td>
                        <td><span id="subscribenowlink<?php echo esc_html( $list_id); ?>"><a href="javascript:wpmlmanagement_subscribe('<?php echo esc_html( $subscriber -> id); ?>', '<?php echo esc_html( $list_id); ?>');" class="<?php echo esc_html($this -> pre); ?>button subscribebutton ui-button-success"><?php esc_html_e('Subscribe', 'wp-mailinglist'); ?></a></span></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <script type="text/javascript">jQuery(document).ready(function() { if (jQuery.isFunction(jQuery.fn.button)) { jQuery('.<?php echo esc_html($this -> pre); ?>button, .newsletters_button').button(); } });</script>
<?php else : ?>
	<div class="ui-state-error ui-corner-all">
		<p><i class="fa fa-exclamation-triangle"></i> <?php esc_html_e('No other subscriptions are available', 'wp-mailinglist'); ?></p>
	</div>
<?php endif; ?>