<?php
// Those default options are used ONLY on FIRST setup and on plugin updates but limited to
// new options that may have been added between your and new version.
//
// This is the main language file, too, which is always loaded by Newsletter. Other language
// files are loaded according the WPLANG constant defined in wp-config.php file. Those language
// specific files are "merged" with this one and the language specific configuration
// keys override the ones in this file.
//
// Language specific files only need to override configurations containing texts
// langiage dependant.

$options = array();

$options['noconfirmation'] = 1;

$options['notify_email'] = get_option('admin_email');
$options['multiple'] = 1;
$options['notify'] = 0;

$options['error_text'] = '<p>' . __('This email address is already subscribed, please contact the site administrator.', 'newsletter') . '</p>';

// Subscription page introductory text (befor the subscription form)
$options['subscription_text'] = "{subscription_form}";

// Message show after a subbscription request has made.
$options['confirmation_text'] = '<p>' . __('A confirmation email is on the way. Follow the instructions and check the spam folder. Thank you.') . '</p>';

// Confirmation email subject (double opt-in)
$options['confirmation_subject'] = __("Please confirm your subscription", 'newsletter');

$options['confirmation_tracking'] = '';

// Confirmation email body (double opt-in)
$options['confirmation_message'] = '<p>' . __('Please confirm your subscription <a href="{subscription_confirm_url}">clicking here</a>', 'newsletter') . '</p>';

// Subscription confirmed text (after a user clicked the confirmation link
// on the email he received
$options['confirmed_text'] = '<p>' . __('Your subscription has been confirmed', 'newsletter') . '</p>';

$options['confirmed_subject'] = __('Welcome', 'newsletter');

$options['confirmed_message'] =
"<p>" . __('This message confirms your subscription to our newsletter. Thank you!', 'newsletter') . '</p>' .
'<hr>' . 
'<p><a href="{profile_url}">' . __('Change your profile', 'newsletter') . '</p>';

$options['confirmed_tracking'] = '';
        