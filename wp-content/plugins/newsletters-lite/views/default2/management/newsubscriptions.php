<?php // phpcs:ignoreFile ?>
    <h3><?php esc_html_e('Other Available Lists', 'wp-mailinglist'); ?></h3>
<p><?php esc_html_e('You can subscribe to our other mailing list/s as well.', 'wp-mailinglist'); ?></p>

<?php if (!empty($success) && $success == true) : ?>
	<div class="alert alert-success">
		<i class="fa fa-check"></i> <?php echo wp_kses_post($success); ?>
	</div>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
	<?php $this -> render('error', array('errors' => $errors), true, 'default'); ?>
<?php endif; ?>

<?php if (!empty($otherlists)) : ?>
    <table class="table table-striped">
        <tbody>
            <?php foreach ($otherlists as $list_id) : ?>
                <?php $Db -> model = $Mailinglist -> model; ?>
                <?php if ($mailinglist = $Db -> find(array('id' => $list_id))) : ?>
                    <tr>
                        <td>
							<label for="mailinglists<?php echo esc_html( $mailinglist -> id); ?>"><?php echo esc_html($mailinglist -> title); ?></label>
                            <?php if ($mailinglist -> paid == "Y") : ?>
                            	<?php $intervals = $this -> get_option('intervals'); ?>
                            	<div>
	                            	<span class="wpmlcustomfieldcaption"><small><?php echo $Html -> currency() . '' . number_format($mailinglist -> price, 2, '.', '') . ' ' . $intervals[$mailinglist -> interval]; ?></small></span>
                            	</div>
                            <?php endif; ?>
                        </td>
                        <td><span id="subscribenowlink<?php echo esc_html( $list_id); ?>"><a href="javascript:wpmlmanagement_subscribe('<?php echo esc_html( $subscriber -> id); ?>', '<?php echo esc_html( $list_id); ?>');" class="<?php echo esc_html($this -> pre); ?>button subscribebutton btn btn-success"><?php esc_html_e('Subscribe', 'wp-mailinglist'); ?></a></span></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
	<div class="alert alert-danger">
		<i class="fa fa-exclamation-triangle"></i> <?php esc_html_e('No other subscriptions are available', 'wp-mailinglist'); ?>
	</div>
<?php endif; ?>