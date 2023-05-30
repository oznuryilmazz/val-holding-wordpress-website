<?php // phpcs:ignoreFile ?>
<p><small><?php esc_html_e('Each of these shortcodes below can be used inside the content of a newsletter to be replaced with an appropriate value automatically.', 'wp-mailinglist'); ?></small></p>

<div class="scroll-list" style="max-height:400px;">
    <table class="form-table">
        <thead>
            <tr>
                <th><?php esc_html_e('Code/String', 'wp-mailinglist'); ?></th>
                <th><?php esc_html_e('Description', 'wp-mailinglist'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $class = ''; ?>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[newsletters_post post_id="X"]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpmlpost_insert();"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php esc_html_e('Inserts the excerpt of a single post.', 'wp-mailinglist'); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[newsletters_posts]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_posts]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php esc_html_e('Insert the excerpts of multiple posts.', 'wp-mailinglist'); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[newsletters_post_thumbnail post_id="X"]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpmlpost_thumbnail_insert();"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php esc_html_e('Insert a post featured thumbnail image.', 'wp-mailinglist'); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
            	<td>
            		<code>[newsletters_post_permalink post_id="X"]</code>
            		<?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpmlpost_permalink_insert();"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
            	</td>
            	<td>
            		<?php esc_html_e('Insert the permalink URL of a post.', 'wp-mailinglist'); ?>
            	</td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[newsletters_email]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_email]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Inserts the email address of each user', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[newsletters_subject]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_subject]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Display the title/subject of this newsletter in the content.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[newsletters_historyid]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_historyid]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Display the history ID of this newsletter in the content.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[newsletters_unsubscribe]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_unsubscribe]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Generates an unsubscribe link for the specific list(s).', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                    <code>[newsletters_unsubscribeall]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_unsubscribeall]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Generates an unsubscribe link for all mailing lists subscribed to.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td>
                	<code>[newsletters_blogname]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_blogname]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Inserts the name of your website/blog', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_siteurl]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_siteurl]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Inserts the URL of your website/blog', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_mailinglist]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_mailinglist]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Outputs the name of the mailing list being sent to.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_activate]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_activate]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Generates an activation link for each subscriber', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_manage]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_manage]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Creates a link which takes the subscriber to a management page', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_online]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_online]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Display a link for subscribers to view the newsletter online', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_print]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_print]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Output a link to print the newsletter', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_date {format}]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href='javascript:wpml_tinymcetag("[newsletters_date format=\"%d/%m/%Y\"]");'><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php echo sprintf(__('Output the current date and/or time. Optionally, specify a format parameter. Eg. [newsletters_date format="%s"] . Leave empty the format for the current WordPress date format . Any PHP %s or %s date string/format can be used.', 'wp-mailinglist'), get_option('date_format'), '<a href="http://php.net/manual/en/function.date.php" target="_blank">' . __('date', 'wp-mailinglist') . '</a>', '<a href="http://php.net/manual/en/function.strftime.php" target="_blank">' . __('strftime', 'wp-mailinglist') . '</a>'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_track]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_track]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Inserts a discreet tracking code into each email.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_bouncecount]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_bouncecount]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Output the total email bounces for the subscriber.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_customfields]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_customfields]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Output all custom fields with values in a table for the subscriber.', 'wp-mailinglist'); ?></td>
            </tr>
            <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                <td><code>[newsletters_subscriberscount {list}]</code>
                    <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href="javascript:wpml_tinymcetag('[newsletters_subscriberscount]');"><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                </td>
                <td><?php esc_html_e('Display the total number of subscribers in the database.', 'wp-mailinglist'); ?>
                <?php esc_html_e('Optional, <code>list</code> parameter to specify the mailing list ID', 'wp-mailinglist'); ?></td>
            </tr>
            <?php $Db -> model = $Field -> model; ?>
            <?php $fields = $Db -> find_all(false, array('id', 'title', 'slug'), array('title', "ASC")); ?>
            <?php if (!empty($fields)) : ?>
                <?php foreach ($fields as $field) : ?>
                    <tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                        <td><code>[newsletters_field name=<?php echo esc_html( $field -> slug); ?>]</code>
                            <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href='javascript:wpml_tinymcetag("[newsletters_field name=<?php echo esc_html( $field -> slug); ?>]");'><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                        </td>
                        <td><b><?php esc_html_e('Custom', 'wp-mailinglist'); ?>:</b> <?php echo esc_html($field -> title); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <?php if (class_exists('WooCommerce')) : ?>
            	<tr class="<?php echo $class = (empty($class)) ? 'alternate' : ''; ?>">
                    <td><code>[newsletters_woocommerce_products perrow=3 number=9 order=DESC orderby=post_date featured=0 bestselling=0 showimage=1 imagesize="thumbnail" showtitle=1 showprice=1 showbutton=1 buttontext="Buy Now"]</code>
                        <?php if (empty($noinsert) || $noinsert == false) : ?><br/><small><a href='javascript:wpml_tinymcetag("[newsletters_woocommerce_products perrow=3 number=9 order=DESC orderby=post_date featured=0 bestselling=0 showimage=1 imagesize=\"thumbnail\" showtitle=1 showprice=1 showbutton=1 buttontext=\"Buy Now\"]");'><?php esc_html_e('Insert into Editor', 'wp-mailinglist'); ?></a></small><?php endif; ?>
                    </td>
                    <td><?php esc_html_e('WooCommerce Products', 'wp-mailinglist'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style type="text/css">
.newsletters .form-table td {
	white-space: normal;
}
</style>

<script type="text/javascript">
function wpmlpost_insert() {
	var post_id = prompt('<?php echo esc_html(__('What is the ID of the post you want to insert?', 'wp-mailinglist')); ?>');
	var eftype = prompt('<?php echo esc_html(__('Do you want to insert a full post or excerpt? Use "full" or "excerpt" to specify.', 'wp-mailinglist')); ?>');
	
	if (post_id) {
		wpml_tinymcetag('[newsletters_post post_id="' + post_id + '" eftype="' + eftype + '"]');
	}
}

function wpmlpost_thumbnail_insert() {
	var post_id = prompt('<?php esc_html_e('What is the ID of the post to take the featured image from? \nIf you are sending/queuing this newsletter from a post/page, leave empty to use the current post/page ID automatically.', 'wp-mailinglist'); ?>');
	var size = prompt('<?php esc_html_e('Please fill in a size! Use either thumbnail, medium, large or full.', 'wp-mailinglist'); ?>');
	
	if (post_id) {
		wpml_tinymcetag('[newsletters_post_thumbnail post_id="' + post_id + '" size="' + size + '"]');
	} else {
		wpml_tinymcetag('[newsletters_post_thumbnail size="' + size + ']');
	}
}

function wpmlpost_permalink_insert() {
	var post_id = prompt('<?php esc_html_e('What is the ID of the post to generate a permalink URL for? \nIf you are sending/queuing this newsletter from a post/page, leave empty to use the current post/page ID automatically.', 'wp-mailinglist'); ?>');
	
	if (post_id) {
		wpml_tinymcetag('[newsletters_post_permalink post_id="' + post_id + '"]');
	} else {
		wpml_tinymcetag('[newsletters_post_permalink]');
	}
}
</script>