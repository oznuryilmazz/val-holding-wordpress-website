<?php
	
$messages = array();

$messages[1] = __('Preview has been sent', 'wp-mailinglist');
$messages[2] = __('Preview cannot be sent to %s, %s.', 'wp-mailinglist');
$messages[3] = __('%s is an invalid email address', 'wp-mailinglist');
$messages[4] = __('Draft has been successfully saved. It has been saved to your email history.', 'wp-mailinglist');
$messages[5] = __('Draft could not be saved. Please fill in all required fields', 'wp-mailinglist');
$messages[6] = __('Configuration settings successfully updated', 'wp-mailinglist');
$messages[7] = __('Database update done', 'wp-mailinglist');
$messages[8] = __("Subscriber has been saved", 'wp-mailinglist');
$messages[9] = __('Subscribers could not be imported', 'wp-mailinglist');
$messages[10] = __('Snippet has been saved', 'wp-mailinglist');
$messages[11] = __('No action was specified', 'wp-mailinglist');
$messages[12] = __('No bounces were specified', 'wp-mailinglist');
$messages[13] = __('Bounces were deleted', 'wp-mailinglist');
$messages[14] = __('Subscribers were deleted', 'wp-mailinglist');
$messages[15] = __('Defaults have been loaded', 'wp-mailinglist');
$messages[16] = __('No records were selected', 'wp-mailinglist');
$messages[17] = __('No action was selected', 'wp-mailinglist');
$messages[18] = __('Selected records were deleted', 'wp-mailinglist');
$messages[19] = __('Subscribers are currently importing in the background. You will receive an email notification when it is completed. %s %s', 'wp-mailinglist');
$messages[20] = __('Selected newsletters deleted', 'wp-mailinglist');
$messages[21] = __('Subscribers are currently importing in the background. %s %s', 'wp-mailinglist');

$messages = apply_filters('newsletters_messageserrors', $messages);	