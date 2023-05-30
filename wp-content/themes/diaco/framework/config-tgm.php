<?php
/*
 * TGM
 */


class TGMRequiredPlugins {

	use pluginlist;

	public function __construct() {
		$this->themeversion     = wp_get_theme()->get( 'Version' );
		$this->wp_version        = get_bloginfo( 'version' );
		$this->liecence_endpoint = $this->update_url;
		add_action( 'tgmpa_register', array( $this, 'diaco_register_required_plugins' ) );
	}

	public function envato_theme_license_url_build( $licence_action, $filename = '', $array = array() ) {

		$apikeys               = get_option( 'envato_theme_license_token' );
		$apikeys               = json_decode( stripslashes( $apikeys ), true );
		$querystring           = '';
		$query                 = array();
		$query['themeversion'] = $this->themeversion;
		$query['wp_version']   = $this->wp_version;
		if ( $licence_action == 'update' ) {
			$query['filename'] = $filename;
			$query['validurl'] = time() + 24 * 60 * 60;
			if ( isset( $apikeys['update'] ) ) {
				$query['apikeys'] = $apikeys['update'];
			}
		} elseif ( $licence_action == 'activate' ) {
			$query['validtoken'] = 'have';
			$query['info']       = get_bloginfo();
			$query['multisite']  = is_multisite();
			if ( isset( $apikeys['activate'] ) ) {
				$query['apikeys'] = $apikeys['activate'];
			}
		} elseif ( $licence_action == 'deactivate' ) {
			$query['info']      = get_bloginfo();
			$query['multisite'] = is_multisite();
			if ( isset( $apikeys['deactive'] ) ) {
				$query['apikeys'] = $apikeys['deactive'];
			}
		} elseif ( $licence_action == 'checkdata' ) {
			$query['info']      = get_bloginfo();
			$query['multisite'] = is_multisite();
			if ( isset( $apikeys['checkdata'] ) ) {
				$query['apikeys'] = $apikeys['checkdata'];
			}
		} elseif ( $licence_action == 'start' ) {
			$query['item_id']        = $this->item_id;
			$query['site_url']       = get_site_url();
			$query['licence_action'] = $licence_action;
		}
		foreach ( $array as $key => $val ) {
			$query[ $key ] = $val;
		}
		$querystring   = http_build_query( $query );
		$enquerystring = $this->encryptDecrypt( 'ENCRYPT', $querystring );
		$encode        = urlencode( $enquerystring );
		$url           = $this->liecence_endpoint . 'ck-ensl-api?' . $encode;
		return $url;
	}

	public function encryptDecrypt( $action, $string ) {
		$output         = false;
		$encrypt_method = 'AES-128-ECB';
		$secret_key     = 'sdsdata';
		$key            = hash( 'sha256', $secret_key );

		if ( $action == 'ENCRYPT' ) {
			$output = openssl_encrypt( $string, $encrypt_method, $key );
		} elseif ( $action == 'DECRYPT' ) {
			$output = openssl_decrypt( $string, $encrypt_method, $key );
		}

		return $output;
	}


	public function diaco_register_required_plugins() {

           $plugins = array(
               array(
                   'name' => esc_html__('Redux Framework', 'diaco'), // The plugin name
                   'slug' => 'redux-framework', // The plugin slug (typically the folder name)
                   'required' => true, // If false, the plugin is only 'recommended' instead of required            
                   'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                   'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                   'external_url' => '', // If set, overrides default API URL and points to an external URL
               ),
               array(
                   'name' => esc_html__('Contact Form 7', 'diaco'), // The plugin name
                   'slug' => 'contact-form-7', // The plugin slug (typically the folder name)
                   'required' => true, // If false, the plugin is only 'recommended' instead of required
                   'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                   'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                   'external_url' => '', // If set, overrides default API URL and points to an external URL
               ),
               array(
                   'name' => esc_html__('Elementor', 'diaco'), // The plugin name
                   'slug' => 'elementor', // The plugin slug (typically the folder name)            
                   'required' => true, // If false, the plugin is only 'recommended' instead of required
                   'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                   'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                   'external_url' => '', // If set, overrides default API URL and points to an external URL
               ),
               array(
                   'name' => esc_html__('Meta Box', 'diaco'), // The plugin name
                   'slug' => 'meta-box', // The plugin slug (typically the folder name)            
                   'required' => true, // If false, the plugin is only 'recommended' instead of required
                   'force_activation' => false, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
                   'force_deactivation' => false, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
                   'external_url' => '', // If set, overrides default API URL and points to an external URL
			   ),
           );
           
           $purchase_key = trim( get_option( 'envato_theme_license_key' ) );
           $token        = get_option( 'envato_theme_license_token' );
           $token        = json_decode( stripslashes( $token ), true );
           // Change this to your theme text domain, used for internationalising strings
           foreach ( $this->plugin_org_name as $key => $value ) {
               $array                       = array();
               $array['name']               = wp_kses_post( $value );
               $array['slug']               = $key;
               $array['source']             = $this->envato_theme_license_url_build( 'update', $key );
               $array['required']           = true;
               $array['force_activation']   = false;
               $array['force_deactivation'] = false;
               $array['external_url']       = '';
               $plugins[]                   = $array;
           }

			// Change this to your theme text domain, used for internationalising strings
			$config = array(
				'domain'       => 'diaco', // Text domain - likely want to be the same as your theme.
				'default_path' => '', // Default absolute path to pre-packaged plugins
				'parent_slug'  => 'themes.php',
				'menu'         => 'install-required-plugins', // Menu slug
				'has_notices'  => true, // Show admin notices or not
				'is_automatic' => false, // Automatically activate plugins after installation or not
				'message'      => '', // Message to output right before the plugins table
				'strings'      => array(
					'page_title'                      => esc_html__( 'Install Required Plugins', 'diaco' ),
					'menu_title'                      => esc_html__( 'Install Plugins', 'diaco' ),
					'installing'                      => esc_html__( 'Installing Plugin: %s', 'diaco' ), // %1$s = plugin name
					'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'diaco' ),
					'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'diaco' ), // %1$s = plugin name(s)
					'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'diaco' ), // %1$s = plugin name(s)
					'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'diaco' ), // %1$s = plugin name(s)
					'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'diaco' ), // %1$s = plugin name(s)
					'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'diaco' ), // %1$s = plugin name(s)
					'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'diaco' ), // %1$s = plugin name(s)
					'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'diaco' ), // %1$s = plugin name(s)
					'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'diaco' ), // %1$s = plugin name(s)
					'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'diaco' ),
					'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'diaco' ),
					'return'                          => esc_html__( 'Return to Required Plugins Installer', 'diaco' ),
					'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'diaco' ),
					'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'diaco' ), // %1$s = dashboard link
					'nag_type'                        => 'updated', // Determines admin notice type - can only be 'updated' or 'error'
				),
			);

			tgmpa( $plugins, $config );
	}

}

new TGMRequiredPlugins();
