<?php // phpcs:ignoreFile ?>
<?php

$user_id = (empty($_POST['user_id'])) ? get_current_user_id() : (int) sanitize_text_field(wp_unslash($_POST['user_id']));
$user = get_userdata($user_id);

$args = array(
	'number'				=>	10,
	'orderby'				=>	'registered',
	'order'					=>	"DESC",
);

$user_query = new WP_User_Query($args);
$latestusers = $user_query -> get_results();

?>

<select name="user_id" id="authorsautocomplete" style="min-width:300px; width:auto;">
	<option selected="selected" value="<?php echo esc_attr(wp_unslash($user_id)); ?>"><?php echo esc_html($user -> display_name); ?></option>
	<?php if (!empty($latestusers)) : ?>
		<?php foreach ($latestusers as $latestuser) : ?>
			<option value="<?php echo esc_html( $latestuser -> ID); ?>"><?php echo esc_html( $latestuser -> display_name); ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery('#authorsautocomplete').select2({
	  placeholder: '<?php esc_html_e('Search users', 'wp-mailinglist'); ?>',
	  ajax: {
	        url: newsletters_ajaxurl + "action=newsletters_autocomplete_users&security=<?php echo esc_html( wp_create_nonce('autocomplete_users')); ?>",
	        dataType: 'json',
	        data: function (params) {
		      return {
		        q: params.term, // search term
		        page: params.page
		      };
		    },
		    processResults: function (data, page) {
		      return {
		        results: data
		      };
		    },
	    },
	  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
	  minimumInputLength: 1,
	  templateResult: formatResult,
	  templateSelection: formatSelection,
    }).next().css('width', "auto").css('min-width', "300px");
});

function formatResult(data) {
    return data.text;
};

function formatSelection(data) {
    return data.text;
};

function filter_value(filtername, filtervalue) {	    			
    if (filtername != "") {
        document.cookie = "<?php echo esc_html($this -> pre); ?>filter_" + filtername + "=" + filtervalue + "; expires=<?php echo esc_html( $Html -> gen_date($this -> get_option('cookieformat'), strtotime("+30 days"))); ?> UTC; path=/";
    }
}
</script>