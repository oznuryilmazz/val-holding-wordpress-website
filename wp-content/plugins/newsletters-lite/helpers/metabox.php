<?php
// phpcs:ignoreFile
if (!class_exists('wpmlMetaboxHelper')) {
	class wpmlMetaboxHelper extends wpMailPlugin {
		
		var $name = 'Metabox';
		
		function __construct() {
			return true;
		}
		
		function write_advanced() {
			$this -> render('metaboxes' . DS . 'write-advanced', false, true, 'admin');
		}
		
		function welcome_stats() {
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'stats', false, true, 'admin');
		}
		
		function welcome_history() {
			global $wpdb, $Db;
			$histories = $this -> History() -> find_all(false, false, array('modified', "DESC"), 5);
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'history', array('histories' => $histories), true, 'admin');
		}
		
		function welcome_quicksearch() {
			
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'quicksearch', false, true, 'admin');
		}
		
		function welcome_subscribers() {
			global $wpdb, $Subscriber, $Db;
			$Db -> model = $Subscriber -> model;
			$subscriberstotal = $Db -> count();		
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'subscribers', array('total' => $subscriberstotal), true, 'admin');
		}
		
		function welcome_lists() {
			global $wpdb, $Mailinglist, $Db;		
			$Db -> model = $Mailinglist -> model;
			$total_public = $Db -> count(array('privatelist' => "N"));
			$Db -> model = $Mailinglist -> model;
			$total_private = $Db -> count(array('privatelist' => "Y"));
			
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'lists', array('total_public' => $total_public, 'total_private' => $total_private), true, 'admin');
		}
		
		function welcome_emails() {
			global $wpdb, $Email, $Db;
			$Db -> model = $Email -> model;
			$emailstotal = $Db -> count();
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'emails', array('total' => $emailstotal), true, 'admin');
		}
		
		function welcome_bounces() {
			global $Bounce, $Db;
			$Db -> model = $Bounce -> model;
			$total = $Db -> count(false, true, 'count');
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'bounces', array('total' => $total), true, 'admin');
		}
		
		function welcome_unsubscribes() {
			global $Unsubscribe, $Db;
			$Db -> model = $Unsubscribe -> model;
			$total = $Db -> count();
			$this -> render('metaboxes' . DS . 'welcome' . DS . 'unsubscribes', array('total' => $total), true, 'admin');
		}
		
		/* Forms */
		function forms_fields() {		
			$this -> render('metaboxes' . DS . 'forms' . DS . 'fields', false, true, 'admin');
		}
		
		function forms_submit() {
			
			$this -> render('metaboxes' . DS . 'forms' . DS . 'submit', false, true, 'admin');
		}
		
		function forms_form() {
			
			$this -> render('metaboxes' . DS . 'forms' . DS . 'form', false, true, 'admin');
		}
		
		function forms_field($post = null, $metabox = null) {
			global $Db, $Field;
			$Db -> model = $Field -> model;
			$form_field = $metabox['args']['form_field'];
			$field = $Db -> find(array('id' => $form_field -> field_id));
			
			$this -> render('metaboxes' . DS . 'forms' . DS . 'field-intro', array('field' => $field), true, 'admin');
			
			switch ($field -> type) {
				case 'hidden'					:
					$this -> render('metaboxes' . DS . 'forms' . DS . 'field-hidden', array('form_field' => $form_field, 'field' => $field), true, 'admin');
					break;
				case 'special'					:
					if ($field -> slug == "list") {
						$this -> render('metaboxes' . DS . 'forms' . DS . 'field-list', array('form_field' => $form_field, 'field' => $field), true, 'admin');
					}
					break;
				case 'file'						:
					$this -> render('metaboxes' . DS . 'forms' . DS . 'field-file', array('form_field' => $form_field, 'field' => $field), true, 'admin');
					break;
				case 'select'					:
				case 'radio'					:
				case 'checkbox'					:
					$this -> render('metaboxes' . DS . 'forms' . DS . 'field-select', array('form_field' => $form_field, 'field' => $field), true, 'admin');
					break;
				case 'text'						:
				default 						:
					if ($field -> slug == "email") {
						$this -> render('metaboxes' . DS . 'forms' . DS . 'field-email', array('form_field' => $form_field, 'field' => $field), true, 'admin');
					} else {
						$this -> render('metaboxes' . DS . 'forms' . DS . 'field', array('form_field' => $form_field, 'field' => $field), true, 'admin');
					}
					break;
			}
		}
		
		/* Send */
		function send_spamscore() {
			$this -> render('metaboxes' . DS . 'send' . DS . 'spamscore', false, true, 'admin');
		}
		
		function newsletters_spamscore() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'spamscore', false, true, 'admin');
		}
		
		function send_mailinglists() {
			$this -> render('metaboxes' . DS . 'send-mailinglists', false, true, 'admin');
		}
		
		function newsletters_mailinglists() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'mailinglists', false, true, 'admin');
		}
		
		function send_theme() {
			$this -> render('metaboxes' . DS . 'send-theme', false, true, 'admin');	
		}
		
		function newsletters_theme() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'theme', false, true, 'admin');	
		}
		
		function send_author() {
			$this -> render('metaboxes' . DS . 'send' . DS . 'author', false, true, 'admin');
		}
		
		function send_insert() {
			$this -> render('metaboxes' . DS . 'send-insert', false, true, 'admin');
		}
		
		function newsletters_insert() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'insert', false, true, 'admin');
		}
		
		function send_submit() {
			$this -> render('metaboxes' . DS . 'send-submit', false, true, 'admin');
		}
		
		function send_otheractions() {
			$this -> render('metaboxes' . DS . 'send-otheractions', false, true, 'admin');
		}
		
		function send_contentarea($post = null, $metabox = null) {
			
			if (!empty($metabox['args']['contentarea'])) {
				$this -> render('metaboxes' . DS . 'send' . DS . 'contentarea', array('contentarea' => $metabox['args']['contentarea']), true, 'admin');
			}
		}
		
		function send_multimime() {
			$this -> render('metaboxes' . DS . 'send-multimime', false, true, 'admin');
		}
		
		function newsletters_text() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'text', false, true, 'admin');
		}
		
		function newsletters_preview() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'preview', false, true, 'admin');
		}
		
		function send_preview() {
			$this -> render('metaboxes' . DS . 'send-preview', false, true, 'admin');
		}
		
		function send_setvariables() {
			$this -> render('metaboxes' . DS . 'send-setvariables', false, true, 'admin');
		}
		
		function send_attachment() {
			$this -> render('metaboxes' . DS . 'send-attachment', false, true, 'admin');
		}
		
		function newsletters_attachment() {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'attachment', false, true, 'admin');
		}
		
		function send_publish() {
			$this -> render('metaboxes' . DS . 'send-publish', false, true, 'admin');
		}
		
		function templates_submit() {
			$this -> render('metaboxes' . DS . 'templates-submit', false, true, 'admin');
		}
		
		function themes_submit() {
			$this -> render('metaboxes' . DS . 'themes' . DS . 'submit', false, true, 'admin');
		}
		
		function themes_general() {
			$this -> render('metaboxes' . DS . 'themes' . DS . 'general', false, true, 'admin');
		}
		
		/* Settings */
		function settings_language() {
			$this -> render('metaboxes' . DS . 'settings-language', false, true, 'admin');
		}
		
		function settings_submit() {
			$this -> render('metaboxes' . DS . 'settings-submit', false, true, 'admin');
		}
		
		function settings_tableofcontents() {
			$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents', false, true, 'admin');
		}
		
		function settings_subscribers_tableofcontents() {
			$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents-subscribers', false, true, 'admin');
		}
		
		function settings_templates_tableofcontents() {
			$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents-templates', false, true, 'admin');
		}
		
		function settings_system_tableofcontents() {
			$this -> render('metaboxes' . DS . 'settings' . DS . 'tableofcontents-system', false, true, 'admin');
		}
		
		function settings_sections() {
			$this -> render('metaboxes' . DS . 'settings-sections', false, true, 'admin');
		}
		
		function settings_wprelated() {
			$this -> render('metaboxes' . DS . 'settings-wprelated', false, true, 'admin');
		}
		
		function settings_permissions() {
			$this -> render('metaboxes' . DS . 'system' . DS . 'permissions', false, true, 'admin');
		}
		
		function settings_importusers() {
			$this -> render('metaboxes' . DS . 'settings-importusers', false, true, 'admin');
		}
		
		function settings_commentform() {
			$this -> render('metaboxes' . DS . 'settings-comments', false, true, 'admin');	
		}
		
		function settings_system_general() {
			$this -> render('metaboxes' . DS . 'system' . DS . 'general', false, true, 'admin');
		}
		
		function settings_system_captcha() {
			$this -> render('metaboxes' . DS . 'system' . DS . 'captcha', false, true, 'admin');
		}
		
		function settings_general() {
			$this -> render('metaboxes' . DS . 'settings-general', false, true, 'admin');
		}
		
		/* Sending Settings */
		function settings_sending() {
			$this -> render('metaboxes' . DS . 'settings-sending', false, true, 'admin');	
		}
		
		function settings_import() {
			$this -> render('metaboxes' . DS . 'subscribers' . DS . 'import', false, true, 'admin');	
		}
		
		/* Subscriber management section */
		function settings_management() {
			$this -> render('metaboxes' . DS . 'settings-management', false, true, 'admin');	
		}
		
		function settings_optin() {
			$this -> render('metaboxes' . DS . 'settings-optin', false, true, 'admin');
		}
		
		function settings_subscriptions() {
			$this -> render('metaboxes' . DS . 'settings-subscriptions', false, true, 'admin');
		}
		
		function settings_pp() {
			$this -> render('metaboxes' . DS . 'settings-pp', false, true, 'admin');
		}
		
		function settings_tc() {
			$this -> render('metaboxes' . DS . 'settings-tc', false, true, 'admin');
		}
		
		function settings_subscribers() {
			$this -> render('metaboxes' . DS . 'settings-subscribers', false, true, 'admin');
		}
		
		function settings_unsubscribe() {
			$this -> render('metaboxes' . DS . 'settings-unsubscribe', false, true, 'admin');
		}
		
		function settings_publishing() {
			$this -> render('metaboxes' . DS . 'settings-publishing', false, true, 'admin');
		}
		
		function settings_scheduling() {
			$this -> render('metaboxes' . DS . 'settings-scheduling', false, true, 'admin');
		}
		
		function settings_bounce() {
			$this -> render('metaboxes' . DS . 'settings-bounce', false, true, 'admin');
		}
		
		function settings_emails() {
			$this -> render('metaboxes' . DS . 'settings' . DS . 'emails', false, true, 'admin');
		}
		
		function settings_latestposts() {
			echo '<div id="latestposts_wrapper">';
			$this -> render('metaboxes' . DS . 'settings-latestposts', false, true, 'admin');	
			echo '</div>';
		}
		
		function settings_customcss() {
			$this -> render('metaboxes' . DS . 'settings-customcss', false, true, 'admin');	
		}
		
		function settings_templates_sendas() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'sendas', false, true, 'admin');
		}
		
		function settings_templates_posts() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'posts', false, true, 'admin');
		}
		
		function settings_templates_latestposts() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'latestposts', false, true, 'admin');
		}
		
		function settings_templates_authenticate() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'authenticate', false, true, 'admin');
		}
		
		function settings_templates_confirm() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'confirm', false, true, 'admin');
		}
		
		function settings_templates_bounce() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'bounce', false, true, 'admin');
		}
		
		function settings_templates_unsubscribe() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'unsubscribe', false, true, 'admin');
		}
		
		function settings_templates_unsubscribeuser() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'unsubscribeuser', false, true, 'admin');
		}
		
		function settings_templates_expire() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'expire', false, true, 'admin');
		}
		
		function settings_templates_order() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'order', false, true, 'admin');
		}
		
		function settings_templates_schedule() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'schedule', false, true, 'admin');
		}
		
		function settings_templates_subscribe() {
			$this -> render('metaboxes' . DS . 'templates' . DS . 'subscribe', false, true, 'admin');
		}
		
		function extensions_settings_submit() {
			$this -> render('metaboxes' . DS . 'extensions' . DS . 'submit', false, true, 'admin');
		}
		
		function newsletters_submit( $post, $args = array() ) {
			$this -> render('metaboxes' . DS . 'block-editor' . DS . 'submit', array('post' => $post, 'args' => $args), true, 'admin');
		}
	}
}