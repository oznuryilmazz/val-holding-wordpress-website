=== Newsletters ===
Contributors: contrid
Donate link: https://tribulant.com
Tags: newsletters, email, bulk email, mailing list, subscribers, newsletter, opt-in, subscribe, marketing, auto newsletter, automatic newsletter, autoresponder, campaign, email, email alerts, email subscription, emailing, follow up, newsletter signup, newsletter widget, newsletters, post notification, subscription, bounce, latest posts, insert posts into newsletter
Requires at least: 3.8
Tested up to: 6.1.1
Stable tag: 4.8.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html 

Newsletter plugin for WordPress to capture subscribers and send beautiful, bulk newsletter emails.


== Description ==

A full-featured WordPress newsletter plugin created by <a href="https://tribulant.com">Tribulant</a> for WordPress which fulfills all subscribers, emails, marketing and newsletter related needs for both personal and business environments.

It has robust, efficient and unique features! This is an all-in-one newsletter tool for your WordPress site can be configured to behave as desired and it will provide the best experience for your email subscribers at the same time.

The software works the way you do so you can focus on creating newsletters and giving your website the necessary exposure!


= Features =

Some of the features in the WordPress Newsletter plugin include (see PRO Version section below to view the limitations in this LITE version):

* Mailing Lists 
* Bounce Email Management 
* Newsletter Queue & Scheduling 
* Newsletter Templates 
* Drag & Drop Newsletter & Template Builder 
* Complete Email History 
* Unlimited Sidebar Widgets 
* Post/Page Opt-In Embedding 
* Subscription Forms Builder 
* Offsite Subscription Forms 
* Publish Newsletter as a Post 
* Send Post as a Newsletter 
* Add Email Attachments 
* SMTP Authentication 
* Ajax Powered Features 
* Import/Export Subscribers 
* Paid Subscriptions (PayPal & 2Checkout) 
* Integrates with the banner rotator plugin 
* WordPress Multisite Compatible
* Email Tracking 
* IP Logging of Subscribers
* Newsletter Themes 
* POP/IMAP Bounce Handling 
* Latest Posts Subscription
* Single/Multiple Posts into Emails 
* Bit.ly click tracking 
* Autoresponders 
* Newsletters by conditions 
* Multilingual (qTranslate & WPML) 
* Custom Post Types 
* Custom Fields 
* Link/click tracking 
* DKIM Signature 
* WordPress Dashboard Widget
* and much more...

See the newsletter subscribe forms builder in action:

https://www.youtube.com/watch?v=ZHbXN72eqmU


= Demo and Support =

See the <a href="https://tribulant.net/newsletter/">online demonstration</a> and view the <a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/31">online documentation</a> for tips, tricks, guides, and more.


= Extensions =

There are many free and paid extension plugins for the WordPress Newsletter plugin. All extensions work with both Newsletters LITE and Newsletters PRO, no problem.

Some extensions include:

* <a href="https://tribulant.com/extensions/view/42/woocommerce-subscribers">WooCommerce Subscribers</a>
* <a href="https://tribulant.com/extensions/view/28/contact-form-7-subscribers">Contact Form 7 Subscribers</a>
* <a href="https://tribulant.com/extensions/view/46/google-analytics">Google Analytics Tracking</a>
* <a href="https://tribulant.com/extensions/view/6/embedded-images">Embedded Images</a>
* <a href="https://tribulant.com/extensions/view/26/total-ms-control">Total MS Control</a>
* <a href="https://tribulant.com/extensions/view/17/gravity-forms-subscribers">Gravity Forms Subscribers</a>
* <a href="https://tribulant.com/extensions/view/16/formidable-subscribers">Formidable Subscribers</a>
* <a href="https://tribulant.com/extensions/view/43/digital-access-pass">Digital Access Pass Subscribers</a>
* <a href="https://tribulant.com/extensions/view/36/total-control">Total Control</a>
* <a href="https://tribulant.com/extensions/view/32/s2member-subscribers">s2Member Subscribers</a>
* <a href="https://tribulant.com/extensions/view/31/wp-emember-subscribers">WP eMember Subscribers</a>

<a href="https://tribulant.com/plugins/extensions/1/wordpress-newsletter-plugin">Visit the Newsletters extensions page</a>


= Email/Newsletter Templates =

Included with the newsletter plugin are several premade email/newsletter templates.

Shop our <a href="https://tribulant.com/emailthemes/" title="newsletter templates">newsletter templates</a> for more variety and high quality, premium, responsive newsletter templates.


= Languages =

All language files and the instructions to use them are in <a href="https://poeditor.com/join/project/b31cab38f30cec409424dc273a131183">POEditor</a>. Anyone can join the project to add languages and contribute translations for strings.

Thank you for these wonderful people who contributed in translating the plugin:

* Afrikaans (af_ZA) by <a href="https://www.contrid.co.za">Antonie Potgieter</a>
* German (de_DE) by Peter Schonmann
* Greek (el_GR) by <a href="https://www.aio.gr">Harris Karanikolas | AiO Systems Information</a>
* Spanish (es_ES) by Juan Llamosas
* French (fr_FR) by Kim Gjerstad
* Hungarian (hu_HU) by <a href="https://www.idsign.hu">iD Sign | Gergely Almasi</a>
* Italian (it_IT) by <a href="https://www.playcodestudio.com">Matteo Galli | Playcode</a>, Johnny
* Lithuanian (lt_LT) by Tomas
* Dutch (nl_NL) by <a href="https://www.webzenz.nl">Ronald de Caluwe | WebZenz</a>
* Brazilian Portuguese (pt_BR) by Vitor Argos
* Portuguese (pt_PT) by wordpress.mowster.net
* Romanian (ro_RO) by <a href="https://richardconsulting.ro">Richard Vencu</a>
* Swedish (sv_SE) by Tomas Lindhoff
* Turkish (tr_TR) by Sersah Namoglu


= Offsite HTML Code =

<script type="text/javascript"> var wpmlAjax = "' . $this -> url() . '/' . $this -> plugin_name . '-ajax.php"; </script>
<script type="text/javascript" src="' . $this -> url() . '/js/wp-mailinglist.js"></script>
<script type="text/javascript" src="' . get_option('siteurl') . '/wp-includes/js/scriptaculous/prototype.js"></script>
<script type="text/javascript" src="' . get_option('siteurl') . '/wp-includes/js/scriptaculous/scriptaculous.js?load=effects"></script>'


= API Example =
<?php
$url = 'http://domain.com/wp-admin/admin-ajax.php?action=newsletters_api';
$data = array(
    'api_method'        =>   'subscriber_add',
    'api_key'           =>   '37C1D6053E817212348E507D29CCCE49',
    'api_data'          =>   array(
        'email'             => "email@domain.com",
        'list_id'           =>   array(1,2,3),
    )
);

$data_string = wp_json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);

$result = json_decode(curl_exec($ch));
curl_close($ch);
?>


= PRO Version =

The Newsletters LITE version has nearly all of the features that the PRO version has but it has some limitations.

You can have one mailing list, 500 subscribers, send 1000 emails per month, mail type is limited to local server and SMTP with no API integration with mail service providers, and the drag & drop newsletter & template builder and the custom dynamic fields are not available. These limits should be sufficient for a personal blogger or a small business.

To remove these limits, you can upgrade to the PRO version and submit your serial key inside the plugin.

In addition to the limits being removed, you will receive <a href="https://tribulant.com/support/">priority support</a> from <a href="https://tribulant.com">Tribulant</a>.

<a href="https://tribulant.com/plugins/view/1/wordpress-newsletter-plugin">Visit the Newsletters PRO page</a>


= 3rd Party Services =

Our plugin makes use of some 3rd party services or APIs to provide you with the latest technology and functionality. Here is a list of the services:

* SpamScore (https://www.spamscore.net) - Fetch the spam score of an email/newsletter
* IPEcho (https://ipecho.net) - Get the current mail exchange IP address
* IPLocate (https://www.iplocate.io) - To get the country of a user by IP address
* HostIP.info (http://www.hostip.info) - To get the country of a user by IP address
* geoPlugin (https://www.geoplugin.com) - To get the country of a user by IP address


== Installation ==

Installing the WordPress Newsletter plugin is simple. Follow these steps:

= Automatic Installation =

1. Go to **Plugins > Add New** in your WordPress dashboard.
2. Search for `newsletters` to find this plugin, by Tribulant.
3. Click **Install Now** to install it and then activate it after the installation.

= Manual Installation =

1. Extract the `zip` file to obtain the plugin folder.
2. Upload the plugin folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the **Plugins** menu in WordPress.


== Screenshots ==

1. Premade newsletter themes included
2. Detailed statistics for emails, subscribers, etc.
3. Flexible configuration settings
4. Easy, WYSIWYG newsletter creation
5. Complete history of newsletters with stats
6. Import subscribers from CSV or Mac OS X vCard
7. Export subscribers to CSV file
8. Email queue with scheduling
9. Many extensions and integrations available
10. Dashboard widget for quick overview


== Changelog ==

= 4.8.8 =
* FIX: AWS signature v4 issue.
* FIX: Bounce management error on PHP 8.1.
* FIX: Activation error with WPML and PHP 8.x.
* FIX: WPML conflict with sending emails.

= 4.8.7 =
* ADD: Debug log page wrapper. To fix a recurring issue and for added security, debug logs now show up on a page in the admin area rather than in a downloadable .txt file. You can still copy the content to a document.
* ADD: 'Remove from all' button on the unsubscribe page. This helps your subscribers remove their subscriptions from all mailing lists by clicking one button rather than doing so one by one.
* IMPROVE: More PHP 8.1 compatibility.
* FIX: Themes/Templates drag and drop builder CSS code showing in the old templates.
* FIX: Subscription Forms custom HTML before/after escaping issue.
* FIX: Subscription Forms before and after scripts were not removable.
* FIX: Saving the settings of Subscription Forms caused the title and other values of the form to become empty.
* FIX: Caption text for the email custom field was not getting removed in Subscription Forms.
* FIX: Subscribe Forms 'Continue editing' under Save Form was missing.
* FIX: Warnings due to null check in Subscription Forms settings.
* FIX: Modified date was not getting updated when editing Subscription Forms.
* FIX: Multilingual save issue when saving Subscription Forms.
* FIX: PHP 8 issue with newsletters_woocommerce_products shortcode.
* FIX: Spam Score issue.
* FIX: AJAX (e.g., Show Progress) now works correctly.
* FIX: Custom Fields were not getting saved when editing a Subscription Form.
* FIX: Font Awesome icons conflict with some third party plugins.
* FIX: Opt in function backward compatibility issue with PHP 8.0.
* FIX: Panel option description showed HTML in Gutenberg editor.
* FIX: Missing Label For that caused accessibility scan notice.

= 4.8.6 =
* FIX: Latest Posts Subscriptions duplication and non-stop emails issue.
* FIX: Minor queue problem.

= 4.8.5 =
* FIX: Send email issue.
* FIX: Versioning causing database inconsistency.
* FIX: Various bugs.

= 4.8.4 =
* FIX: Some PHP 8 compatibility.
* FIX: Latest Posts Subscriptions subject and content missing.
* FIX: Queue duplication for Latest Posts Subscriptions.
* FIX: Latest Post Subscriptions preview HTML escape.
* FIX: Gravatar display in different locations.
* FIX: Send email preview.

= 4.8.3 =
* This release also includes versions 4.8, 4.8.1, and 4.8.2.
* ADD: (Paid version only) Drag & drop newsletter & template builder is back! This is still in beta for now. It can be found when drafting a newsletter and when editing a theme/template.
* ADD: Disable Drag & Drop Builder When Creating Newsletters setting in Configuration > General > General Mail Settings in case of issues.
* ADD: Display or hide the thumbnail for post(s) in the [newsletters_posts] and [newsletters_post] shortcodes. E.g.:
- [newsletters_posts hidethumbnail="N"] will show the thumbnails in multiple posts.
- [newsletters_post hidethumbnail="Y"] will hide the thumbnail in a single post.
- Usage information: https://tribulant.com/docs/wordpress-mailing-list-plugin/95/wp-newsletters-shortcodes/
* ADD: Delete WordPress User on Unsubscribe setting in Configuration > Subscribers > Unsubscribe Behaviour. Make sure to click Advanced next to Admin Mode at the top.
* IMPROVE: Full PHP 8.0 compatibility.
* IMPROVE: WordPress 6.0 compatibility.
* IMPROVE: Updated CKEditor to 4.19.
* IMPROVE: Upload button graphics.
* IMPROVE: Changed 'Mandrill' name to Mailchimp Transactional Email (Mandrill).
* IMPROVE: Text updates for clarity.
* IMPROVE: Reduced plugin size.
* FIX: Exported CSV cache busting.
* FIX: Newsletter queue issue background process.
* FIX: Duplicated newsletters for users who have multiple user roles.
* FIX: Updated Bootstrap library in views/default2/ to 5.1.3.
* FIX: Prevent duplicated newsletter emails for subscribers that are registered as a user.
* FIX: Disabling global debugging when plugin debug is set to false.
* FIX: Duplicated interval options under Configuration -> Schedule Interval.
* FIX: Draft saving issue after update.
* FIX: Sent & Draft Newsletters page showing the archived newsletters by default.
* FIX: Handling error on *subscribe_link functions.
* FIX: Drag and drop builder caused the Template edit page to have the template's background.
* FIX: Adding content area on create/edit newsletter page.
* FIX: Content Area content not showing up if it is added to the templates directly.
* FIX: Generate resubscribe links concat issue.
* FIX: Scheduled newsletter sends the emails to only one third of subscribers.
* FIX: Automated 'Latest Posts Subscriptions' Newsletter is empty.
* FIX: Subscription form navigation link issue.
* FIX: Scheduled Queue emails stuck after the update.
* FIX: PHP 7.4 errors.
* FIX: Other bugs.
* FIX: Free and paid Newsletter templates/themes responsive fixes. After installing this version, if you were already using this plugin and our free or paid templates, you should reimport the template(s) that you would like to use in Newsletters > Themes/Templates > Add New. First, if you haven't done so, rename the template(s) that you are using so that our default templates do not overwrite yours. Then, take a backup of your database. Paid templates, download the latest version from your account with us. Free templates, import this file from this version: wp-content/plugins/newsletters-lite/includes/themes/template-name/index.html

= 4.7.9.6 =
* FIX: PHP errors when saving draft newsletter.

= 4.7.9.5 =
* FIX: Insert Into Newsletter post categories now display properly.
* FIX: Use progress bar to queue/send error.
* FIX: PHP errors.

= 4.7.9.4 =
* IMPROVE: Updated CKEditor to the latest full version to have all assets.
* FIX: Consent field not saving in subscribe form.
* FIX: Removed multiple irrelevant cron options from the Configuration > Schedule Interval setting.

= 4.7.9.3 =
* ADD: New filter: wpml_phpmailer_before_send - Filters the PHPMailer object before calling the PHPMailer::send() method. Argument: $phpmailer (the PHPMailer instance).
* FIX: Badly escaped widget HTML output.
* FIX: Custom Fields > Field Type not showing any options.
* FIX: Saving configuration settings stripped HTML from text editor content.

= 4.7.9.2 =
* FIX: Import/Export Subscribers PHP error.
* FIX: Code optimisation.

= 4.7.9.1 =
* ADD: Delete WordPress user upon subscriber deletion. Set it up in Newsletters > Configuration > Subscribers.
* IMPROVE: Stricter checks for datatypes.
* IMPROVE: Globally securing SQL queries.
* IMPROVE: Updated CKEditor to 4.16.2.
* FIX: PHP wrong timezone critical error caused by date_default_timezone_set().
* FIX: Extended Email Validation prevented signups and caused other issues.
* FIX: Further cmpatibility with the qTranslate-X plugin.
* FIX: Script conflict resulting in not being able to switch tabs on the Manage Subscription page.
* FIX: This update fixes issues that were introduced in the previous 4.7.9 version, such as HTML being stripped out, unable to save Configuration, and more. These only affected this free version and not the premium Tribulant Newsletters.
* FIX: Removed tabindex from email fields.
* FIX: Update routine always runs due to stale object cache.
* Other fixes related to PHP errors.

= 4.7.9 =
* IMPROVE: Security improvements.
* IMPROVE: Updated JS libraries:
    - Charts.js is now v3.4.1
    - bootstrap-datepicker v1.9
* IMPROVE: Removed unneeded files and folders.

= 4.7.8 =
* IMPROVE: Renaming `MailGun` to `Mailgun`.
* FIX: Hourly sending limit interval was not being followed.
* FIX: Mailgun: Adding missing packages, updating API calls, hard-coding a fix into `buzz` http client.

= 4.7.7 =
* IMPROVE: Security fix. Updated CKEditor to version 4.16.1.

= 4.7.6 =
* FIX: PayPal IPN verified check.
* FIX: Auto-import users by secondary role as well.

= 4.7.4 =
* IMPROVE: Further compatibility for Amazon AWS SES Signature Version 4.
* IMPROVE: Some PHP 8 compatibility.
* FIX: Mailgun init.
* FIX: Mailgun errors.
* FIX: SendGrid mail send.
* FIX: SparkPost mail send.
* FIX: PHP errors.
* Other fixes.

= 4.7.1 = 
* ADD: Support for PHPMailer 5.0 (WordPress <=5.4) and PHPMailer 6.0 (WordPress >=5.5).
* ADD: Support for AWS Signature Version 4.
* IMPROVE: Updated colorbox.js package.
* IMPROVE: Updated composer dependencies.
* FIX: Removal from the mailing list from the manage subscription page.

= 4.6.20 =
* ADD: Compatibility with WordPress 5.4+.
* IMPROVE: Dropdown menu width in Configuration.
* FIX: Timezone and time fixes for queuing and scheduling newsletters, and viewing the scheduled time in the newsletter list.

= 4.6.19 =
* ADD: Two columns setting and layout in subscribe form builder
* ADD: Mailgun email validation
* ADD: Extended email validation (DNS and SMTP connect)
* ADD: Number and order settings for Insert Post to narrow
* IMPROVE: Update ChartJS to 2.8.0
* IMPROVE: Security: nonces for Ajax calls
* IMPROVE: Security: current_user_can for Ajax calls
* IMPROVE: Update CKEditor to 4.12.1
* IMPROVE: Use optin date for autoresponders delay
* IMPROVE: Integrate 3rd party CKEditor script inside the plugin itself
* IMPROVE: Fix About page layout for WordPress 5.2+
* IMPROVE: Prefill subscribe form with GET/POST values
* IMPROVE: Remove newsletter builder (beta)
* IMPROVE: Mailing list column in subscribers export
* IMPROVE: Error message at Auto Inline Styles if PHP DOM is not available
* IMPROVE: Validate the slug of custom fields, only lowercase letters
* FIX: Security fixes and improvements
* FIX: Custom field shortcodes not replacing correctly on users/roles
* FIX: Auto Inline Styles not working in PHP 7.3
* FIX: WPGlobus_Config_Builder::set_language() should not be called statically
* FIX: Logout on Manage Subscriptions page not always working
* FIX: HTML minifier breaks tracking image
* FIX: SG Optimizer memcached issue

= 4.6.13 =
* ADD: WordPress 5+ compatibility
* ADD: WPGlobus multilingual integration
* ADD: Check if wp-cron.php file exists
* ADD: Display WooCommerce products in newsletters
* IMPROVE: Smaller queue batch size for latest posts subscriptions
* FIX: WordPress database error: [Unknown column 'id' in 'field list'] in Manage lists section
* FIX: use_block_editor_for_post undefined
* FIX: Subscribers count incorrect under Newsletters > Subscribers
* FIX: Date problem in some languages
* FIX: Post thumbnail shortcode cropping
* FIX: Logout link cookie incorrect

= 4.6.12 =
* ADD: Import subscribers for deletion with a CSV
* ADD: Defaults for "Send as Newsletter" per category
* ADD: Gutenberg "Send as Newsletter" box
* ADD: Live preview under subscribe form settings
* ADD: Bootstrap upgrade to version 4
* ADD: New ClipboardJS for copying without Flash
* ADD: Check if PHP IMAP extension is installed/active
* ADD: Setting to turn on/off two columns for forms in posts/pages
* ADD: Multi-threaded email queue for faster sending of emails
* ADD: Linksonly attribute in [newsletters_history...] shortcode to link to online version
* ADD: Auto Import WordPress users - use new background process
* ADD: Always BCC a specific email on all newsletters setting
* IMPROVE: Faster importing of subscribers in the background
* IMPROVE: Cache video URL data for faster sending
* IMPROVE: Optimize slow queries
* IMPROVE: Parse shortcodes in latest posts subject line
* IMPROVE: Remove asterix for required fields and (optional) next to not required fields
* IMPROVE: Add "Other" option to predefined gender custom field
* IMPROVE: ixes to the old/legacy subscribe forms
* IMPROVE: Load online newsletter on newsletters archive
* IMPROVE: "Manage Subscribe Forms" link inside admin widget setup
* IMPROVE: Indented child configuration settings with arrows for better legibility
* IMPROVE: Show only sent newsletters with newsletters_history shortcode enhancement
* FIX: session_start breaks WordPress loopback request
* FIX: Subscribers list shows wrong number of subscribers
* FIX: Bounced emails are still synced with Auto Import WordPress Users
* FIX: View in browser link of any subscriber accessible
* FIX: MailGun EU server doesn't work
* FIX: Metaboxes not opening/closing
* FIX: Scheduled newsletter queues repeatedly when open
* FIX: PHP REMOTE_ADDR is 127.0.0.1 on some hosting environments
* FIX: Slash for apostrophe showing in subscribe form titles
* FIX: Rate/review request is showing up immediately
* FIX: reCAPTCHA not always showing
* FIX: Mediavine Control Panel conflict $Auth variable
* FIX: "No unique ID was specified" in the error log
* FIX: Can't choose category from "Insert Single Post" dialog
* FIX: endGrid "Content must be..." error on Preview
* FIX: DKIM private key box is empty
* FIX: Database error in autoresponders hook
* FIX: reCAPTCHA "Create a set of keys" link opens blank dialog
* FIX: Setting 1 email per interval doesn't remove email from the queue
* FIX: Queue sends too many emails at a time bug

= 4.6.11 =
* ADD: Additional subscribe form styling options
* IMPROVE: Focus field when "Send Preview" is clicked
* FIX: GMT dates on 'newsletter' custom post type is 0000-00-00 00:00:00
* FIX: "No Country" filter problem under Newsletters > Subscribers
* FIX: Avada/Fusion Builder Fontawesome conflict
* FIX: CSV import delimiter detected incorrectly sometimes
* FIX: Problem with checkbox/select fields on subscribe forms
* FIX: Default GDPR custom field not loading in free version
* FIX: Screen options under subscribers section not showing

= 4.6.10.2 =
* ADD: Show TEXT version of newsletter in live preview
* ADD: New, responsive newsletter template called "Creator"
* ADD: New "Import Settings" box under Subscribers configuration section
* ADD: Delete file after importing subscribers
* ADD: Default template for TEXT part of multi-part emails
* ADD: Setting to turn on/off IP address storage of subscribers
* ADD: See country of subscribers by IP and filter by country
* ADD: GDPR Requirements link under Newsletters > Configuration
* ADD: Button under Captha Settings to easily install Really Simple Captcha plugin
* ADD: Default GDPR consent field for free version of Newsletter plugin
* ADD: Ability to link an autoresponder to form/s as well
* ADD: Make reCAPTCHA multilingual
* ADD: Option/setting to process and delete all emails in bounce mailbox
* ADD: Allow pages post type to be used in latest posts subscriptions
* ADD: Multilingual subscribe/confirm redirect URL
* ADD: Unsubscribe bulk action under Newsletters > Subscribers
* ADD: etting to create custom field options with CSV import when they don't exist
* ADD: "Prevent autoresponders" checkbox on import
* ADD: Setting to import the main stylesheet (style.css) of the WordPress theme
* ADD: Delete subscribers in a mailing list
* IMPROVE: SendGrid API upgrade to version 3
* IMPROVE: Updated DKIM Crypt RSA library
* IMPROVE: CKEditor update to 4.9.2
* IMPROVE: Improved encoding detection on importing
* IMPROVE: Log autoresponders on created date of subscriber, not import date
* IMPROVE: Automatically check newly created mailing list using dialog
* IMPROVE: Make importing of custom field options not case-sensitive
* IMPROVE: Edit and drag icons on subscribe form builder fields
* IMPROVE: Warning: implode(): Invalid arguments passed
* IMPROVE: PHP FILTER_VALIDATE_EMAIL not always accurate
* IMPROVE: Redirect back to referrer after checking bounces manually
* IMPROVE: DKIM private_string
* IMPROVE: Delete bounce email notification if subscriber isn't found
* IMPROVE: Remove eval() from premade themes installation
* IMPROVE: Put ISO country codes in countries database table
* FIX: Possible to insert a space to validate a not empty custom field
* FIX: Activation link not in message when importing
* FIX: Encoding issue when switching to newsletter builder
* FIX: reCAPTCHA not working with non-ajax subscribe forms
* FIX: Subscriber values get reset when saving in admin
* FIX: Fatal error: Uncaught Error: Call to a member function get_languages_list() on null
* FIX: Color picker broken in some sections
* FIX: Mailing list/s not pre-checked when editing custom field
* FIX: Selecting a post from "Single Post" doesn't do anything
* FIX: Link in custom field description breaks
* FIX: grecaptcha.render is not a function Javascript error
* FIX: Fields not deleting from forms when deleted
* FIX: reCAPTCHA deprecated constructor warning
* FIX: DKIM broken and not working
* FIX: Very long paragraphs break layout
* FIX: Latest posts subscription to paid list/s sends to expired subscribers
* FIX: Manage Subscriptions with only profile not showing content immediately
* FIX: Multiple clicks recorded when clicking on unsubscribe link
* FIX: SparkPost links broken
* FIX: Not sending multi-part emails with SendGrid (only HTML)
* FIX: Autoresponders are sending out with import
* FIX: Autoresponder emails sometimes send twice
* FIX: Shortcodes don't work in custom TEXT version of newsletter
* FIX: Empty bounce action in some cases
* FIX: Delete subscriber from unsubscribe not working
* FIX: Bounces reset on subscriber edit
* FIX: Email address shows twice on subscriber view page in admin
* FIX: Unsubscribe all link problem
* FIX: Conflict with WooCommerce outdated Select2 dropdowns
* FIX: Using recurring + scheduled together causes confusion
* FIX: Serial key issue with WPML and subdomains/different domains
* FIX: Auto create lists during import creates multiple lists

= 4.6.9 =
* ADD: GDPR compliance help
* ADD: Setting to turn on/off wpautop for posts/latest posts excerpts
* FIX: Colorbox CSS conflicts with Divi + Tickera combination
* FIX: Shortcodes in newsletters not parsing in online version of newsletter
* FIX: The Events Calendar Select2 dropdown conflict
* FIX: Gutenberg editor conflict

= 4.6.8.6 =
* IMPROVE: Updated PayPal IPN code
* IMPROVE: Further autoresponder delay improvements
* IMPROVE: Change PHP requirement back to 5.4
* FIX: Confirmation email on subscribe form displays system default
* FIX: Subscribe form "Are you sure you want to leave?" message
* FIX: Possible PHP object injection security issue
* FIX: Content areas omitted from publish post from newsletter
* FIX: Metaboxes conflict with Timed Content plugin
* FIX: Latest posts schedule updates with "Update schedule interval" set to off when editing

= 4.6.8.5 =
* IMPROVE: Update inline styles library
* IMPROVE: Don't inherit recurring/scheduled status when duplicating a newsletter
* IMPROVE: Queue more than one scheduled newsletter at a time
* IMPROVE: Update PHP requirement to 5.6+ enhancement
* FIX: Autoresponder delay interval ineffective sometimes
* FIX: Queue sends too many emails at a time

= 4.6.8.2 =
* FIX: Some emails bounced shows read as well
* FIX: Some template files missing in /default/ folder
* FIX: Exporting "Emails Sent" - Are you sure you want to do this?
* FIX: Remove width/height setting causes encoding problem
* FIX: Sends more than specified emails per interval
* FIX: Excerpt not showing with [newsletters_post_excerpt]
* FIX: Plugin folder renames on update bug

= 4.6.8 =
* ADD: Setting to set the ALT attribute on the tracking image to desired value
* ADD: Integrate drag and drop newsletter builder
* ADD: Default value for empty custom fields eg. [newsletters_field name=name|Reader]
* ADD: Integration with Profile Builder plugin (extension)
* ADD: Unsubscribe redirect URL
* ADD: Ninja Forms integration (extension)
* IMPROVE: mbstring not installed - Fatal error: Call to undefined function mb_detect_encoding()
* IMPROVE: MailGun API upgrade to version 3.0
* IMPROVE: Change excerpt_length and excerpt_more priority to apply after everything else
* IMPROVE: Improved loading of reCAPTCHA
* IMPROVE: Accessibility for select drop downs
* FIX: Auto embed breaks HTML with click tracking turned off
* FIX: Amazon product links not showing anything in newsletter
* FIX: MailGun SSL issue
* FIX: Custom confirmation on subscribe form not effective
* FIX: Conflict with Elementor page/site builder
* FIX: Strict Standards: Declaration of check_update() should be compatible with wpMailPlugin::check_update
* FIX: "View Details" link on Plugins page shows a blank/empty overlay
* FIX: Shortcodes don't work in custom TEXT version of newsletter
* FIX: Using filters under "Emails Sent" for a newsletter gives a "Please try again" error
* FIX: HTML2TEXT, Could not load HTML - badly formed?
* FIX: View online link gives "Subscriber cannot be read" error
* FIX: "Add Media" opens blank media library, Jetpack conflict
* FIX: Email queue/scheduling interval not updating
* FIX: Some values like arrays incorrectly escaped
* FIX: Fatal error: Can't use function return value in write context
* FIX: Multiple, duplicate emails from latest posts subscriptions
* FIX: Email address shows twice on subscriber view page in admin

= 4.6.7.1 =
* FIX: Manage Subscriptions account takeover
* FIX: Possible XSS issue on Manage Subscriptions page

= 4.6.7 =
* ADD: New WP_List_Table layout for Newsletters > Sent & Draft Emails section
* ADD: "Screen Options" for columns under Sent & Draft Emails section
* ADD: Set confirmation/activation email per subscribe form
* ADD: Apply WordPress nonce to all forms and URLs for security
* ADD: Copy buttons next to shortcodes, hardcodes, etc.
* ADD: WordPress 4.8+ compatibility
* ADD: Download newsletter as HTML file
* ADD: Archive status for newsletters with a filter
* ADD: PolyLang multilingual integration feature 
* IMPROVE: Show import count under Scheduled Tasks section
* IMPROVE: Bypass serial key check on DOING_CRON
* IMPROVE: Fix PHP deprecated constructors
* IMPROVE: Preferred date format in admin sections for cron schedules, etc.
* IMPROVE: Update all URLs to SSL (https://) for all resources
* IMPROVE: Check the value of DISABLE_WP_CRON
* IMPROVE: Remove unused cron jobs
* IMPROVE: Status (active/paused) setting when saving/editing latest posts subscription
* IMPROVE: Delete posts on latest posts subscription with Ajax (no refresh)
* IMPROVE: Datepicker CSS improvements
* IMPROVE: Add an ALT attribute to the open/read tracking image
* IMPROVE: Do not clear the newsletters.log file daily
* IMPROVE: Apply inline styles to online newsletter view
* IMPROVE: Long HTML lines breaks newsletter in some clients PHP mailer 
* FIX: Manage Subscriptions account takeover
* FIX: Possible XSS issue on Manage Subscriptions page
* FIX: Spam score gauge/meter broken
* FIX: Accents values not importing from CSV
* FIX: Link/click tracking not working with Google Analytics extension
* FIX: Mail attachments from bounce mailbox saving to plugin's folder
* FIX: TEXT subscribers get garbled HTML
* FIX: Fatal error: Call to protected method PhpImap\Mailbox::disconnect()
* FIX: Slow down by multiple update checks in the dashboard
* FIX: Import confirmation doesn't activate subscription
* FIX: Custom fields search gives a database error
* FIX: earching clicks gives a database error
* FIX: Cannot uncheck "Password Authentication" checkbox
* FIX: Fatal error: addAllMappedShortcodes with older versions of Visual Composer
* FIX: Date/time changes when latest posts subscription is edited/saved
* FIX: Possible duplicate emails with Latest Posts Subscriptions feature
* FIX: Time incorrect on latest posts subscriptions
* FIX: Keep "Send as Newsletter" subject empty if it's empty
* FIX: Autoresponders loop with import WordPress users update and always send
* FIX: Welcome/about screen layout broken with WordPress 4.8
* FIX: W3 Total Cache duplicate/double Manage Subscriptions page

= 4.6.6.2 =
* ADD: Latest posts subscriptions "All Categories" option/setting
* ADD: Add a 1 minute schedule for the email queue scheduling 
* IMPROVE: Put "Add Field" button on subscribe form builder
* IMPROVE: Logout URL on manage subscriptions to log user out if linked
* IMPROVE: Setting to change Invisible reCAPTCHA theme (light/dark)
* IMPROVE: "Configure Queue" button on the Email Queue page
* IMPROVE: Show which emails are "Queue Completed" notifications
* IMPROVE: Clear update info cache before updating
* IMPROVE: Further Shopping Cart plugin compatibility 
* FIX: Multiple/duplicate latest posts newsletters generated
* FIX: Some buttons and filters in admin not working
* FIX: Cannot send newsletter to user roles
* FIX: Hidden custom field value not passing after 1st submission of form
* FIX: Hidden custom fields not prefilling when editing subscriber
* FIX: MemberPress unauthorized message with Send as Newsletter
* FIX: Per page drop down under clicks not working
* FIX: Unsubscribe confirmation just reloading
* FIX: Protected post content/excerpt not showing in newsletter
* FIX: Invisible reCAPTCHA conflict with other captcha plugins
* FIX: Multiple confirmation emails to subscribers on import 

= 4.6.6.1 =
* IMPROVE: Further performance improvements, feel it fly!
* IMPROVE: Make sure the premade themes/templates load
* FIX: Conflict with Shopping Cart plugin orders model/class
* FIX: WordPress cron schedules GMT issue, time incorrect
* FIX: rn characters when saving a theme/template
* FIX: Creating a link for each email sent 

= 4.6.6 =
* ADD: Insert HTML code before/after a subscribe form in it's settings
* ADD: Send Batch button per batch in the email queue to send emails
* ADD: Tips/instructions for why queue emails are not sending
* ADD: Show subscribers import scheduled task under Newsletters > Configuration
* ADD: Setting to enable/disable the API
* ADD: Set default mailing list to use in the system in various places
* ADD: Video shortcode and TinyMCE editor video button
* ADD: Use first image of post/page for blogs without featured image functionality (tx Ted Eytan)
* ADD: Settings to specify video image width/height
* ADD: Static textdomain for compatibility with WPML String Translation
* ADD: Invisible Google reCAPTCHA option for subscribe forms
* ADD: HTML support for the caption/description of custom fields
* ADD: Allowed hosts configuration for API for security
* ADD: MemberPress integration (extension)
* ADD: Remove from mailing list feature to bulk remove 
* IMPROVE: Timezone (date/time) improvements throughout
* IMPROVE: Only show queue errors for current batch page
* IMPROVE: Do not disable "Save Draft" button on auto saving
* IMPROVE: Parse Visual Composer shortcodes in newsletters
* IMPROVE: Use full TinyMCE editor instead of teeny in all places
* IMPROVE: Move all queue errors to it's own page
* IMPROVE: Check for DISABLE_WP_CRON and display notification message
* IMPROVE: MySQL NOW() not always accurate on all hosts
* IMPROVE: Improvements to subscribe form Javascript
* IMPROVE: Better and more reliable link/click tracking that works
* IMPROVE: Shortcodes/variables accessible under System Emails section
* IMPROVE: Sort mailing lists alphabetically in some lists/selects
* IMPROVE: Save the origin/referrer of subscriber for reference
* IMPROVE: More clear message after initiating the background import of subscribers
* IMPROVE: Prevent themes from applying events to tabs
* IMPROVE: Change input submit elements to button elements
* IMPROVE: Change unsubscribe comments textarea from cols 100%
* IMPROVE: Check if class DOMDocument exists before calling it
* IMPROVE: Track clicks on View Online and other internal links
* IMPROVE: Deregister outdated WooCommerce Select2 on Manage Subscriptions
* IMPROVE: Hostname empty on some hosting crons, breaks serial key validation
* IMPROVE: Use Emogrifier for faster, more accurate inline styles
* IMPROVE: Keep save draft button active with no subscribers selected
* IMPROVE: Language/translation improvements for translators
* IMPROVE: Improve UTC time zone compatibility 
* IMPROVE: Support for multiple reCAPTCHA per page
* FIX: New custom field prefilled with other field values
* FIX: Timezone reversal problem
* FIX: Importing with Show Progress always sets as active
* FIX: Multiple confirmation emails to subscribers on import
* FIX: Registered status of subscriber doesn't update if registered as user afterwards
* FIX: WPML false message "subscriber management post/page does not exist"
* FIX: Wrong mailing list/s on subscriber in admin areas
* FIX: File upload custom field issues in FireFox and IE
* FIX: Line breaks (Shift + Return) are stripped/ignored in newsletters
* FIX: Issue on manage subscriptions with reading cookie/session
* FIX: Importing checkbox custom fields don't work
* FIX: Unchecking "Specify Date Range" and "Fields Conditions" doesn't clear all fields, they still apply
* FIX: Online print link shows wrong newsletter
* FIX: Fatal error: Call to a member function delete_all()... when deleting post/page
* FIX: "Go to section..." under Configuration > System very wide/long
* FIX: Default newsletter themes/templates not loading on activation
* FIX: Possible XSS vulnerability (credit DefenseCode)
* FIX: W3TC object cache causes problems in admin panel
* FIX: 0 value in custom field doesn't save/work
* FIX: Click tracking doesn't work on hostname with multiple dots (.)
* FIX: Possibility of fields conditions segmentation database error
* FIX: Object cache causes too many redirects on about page
* FIX: Not all content areas duplicate with newsletter 

* Initial release/commit to WordPress.org plugins directory
* See the previous <a href="https://tribulant.com/docs/wordpress-mailing-list-plugin/31#doc5">release notes</a> in our docs.
