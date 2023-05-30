<?php
class DashboardEssential {

	use pluginlist;
	private $liecence_endpoint = '';
	private $theme_name;
	private $theme_slug;
	private $token;
	private $item_id = null;
	private $themeversion;
	private $wp_version;
	private $returnmassage;
	public function __construct() {
		$this->liecence_endpoint = $this->update_url;
		$this->theme_name         = wp_get_theme();
		$this->theme_slug         = $this->theme_name->template;
		$this->item_id            = $this->themeitem_id;
		update_option( 'envato_theme_item_id', $this->item_id );
		$this->token = '';
		if ( get_option( 'envato_theme_license_token' ) ) {
			$this->token = get_option( 'envato_theme_license_token' );
		}
		$status = get_option( 'envato_theme_license_key_status' );
		if ( $this->token != '' && $status == 'valid' ) {
			add_filter( 'plugins_api', array( $this, 'envato_theme_license_dashboard_check_info' ), 10, 3 );
		}
		add_action( 'admin_menu', array( $this, 'envato_theme_license_dashboard_add_menu' ), 8 );
	//	add_action( 'admin_notices', array( $this, 'envato_theme_license_dashboard_sample_admin_notice' ) );
	//	add_action( 'admin_notices', array( $this, 'envato_theme_license_system_change_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'envato_theme_license_dashboard_style' ) );
		register_setting( 'envato_theme_license', 'envato_theme_license_key' );
		// register_setting('envato_theme_license', 'envato_clientemail', array($this, 'envato_client_sanitize'));
		add_action( 'admin_init', array( $this, 'envato_theme_license_dashboard_theme_activate_license' ) );
		add_action( 'admin_notices', array( $this, 'envato_theme_license_dashboard_conditional_admin_notice' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'envato_theme_license_dashboard_transient_update_plugins' ) );
		foreach ( $this->plugin_list_with_file as $key => $val ) {
			add_action( 'in_plugin_update_message-' . $key . '/' . $val, array( $this, 'envato_theme_license_dashboard_update_message_cb' ), 10, 2 );
		}
		if ( class_exists( 'OCDI_Plugin' ) && $this->token != '' ) {
			add_action( 'add_tab_menu_for_dashboard', array( $this, 'envato_theme_license_dashboard_get_tabs' ), 10, 1 );
		}
		if ( class_exists( 'iconmoonFontAdd' ) && $this->token != '' ) {
			add_action( 'add_icon_tab_menu_for_dashboard', array( $this, 'envato_theme_license_dashboard_get_tabs' ), 10, 1 );
		}
		add_action( 'wp_loaded', array( $this, 'envato_theme_license_dashboard_remove_js_composser_hook' ), 99 );
		add_filter( 'custom_menu_order', array( $this, 'envato_theme_license_dashboard_order_menu_page' ), 10 );

		add_action( 'wp_ajax_save_api_key', array( $this, 'save_api_key' ) );
		add_action( 'wp_ajax_nopriv_save_api_key', array( $this, 'save_api_key' ) );

		add_action( 'wp_ajax_delete_api_key', array( $this, 'delete_api_key' ) );
		add_action( 'wp_ajax_nopriv_delete_api_key', array( $this, 'delete_api_key' ) );

		$this->themeversion = wp_get_theme()->get( 'Version' );
		$this->wp_version   = get_bloginfo( 'version' );

		add_action( 'upgrader_process_complete', array( $this, 'my_upgrade_function' ), 10, 2 );
		
	}



	public function my_upgrade_function( $upgrader_object, $options ) {
		// All installations have been completed
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach ( $options['plugins'] as $each_plugin ) {
			}
		}
	}

	public function save_api_key() {
		if ( is_array( $_POST['api'] ) ) {
			$api = json_encode( $_POST['api'] );
		} else {
			$api = $_POST['api'];
		}
		update_option( 'envato_theme_license_token', $api );
		update_option( 'envato_theme_license_key', $_POST['key'] );
		update_option( 'envato_theme_license_key_status', $_POST['status'] );
		update_option( 'envato_theme_license_key_error_massage', "" );
		echo json_encode( array( 'status' => true ) );
		wp_die();
	}

	public function delete_api_key() {
		$url           = $_POST['url'];
		$args          = array(
			'timeout'   => 15,
			'sslverify' => false,
		);
		$response      = wp_remote_get( $url, $args );
		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'diaco' ) );
		}
		if ( is_wp_error( $response ) ) {
			echo json_encode(
				array(
					'status'  => false,
					'message' => 'Some Thing Wrong. Contact With Support..',
				)
			);
		}
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		$status       = $license_data->status;
		$success      = $license_data->success;
		if ( $success ) {
			update_option( 'envato_theme_license_key_status', $status );
			update_option( 'envato_theme_license_key', '' );
			update_option( 'envato_theme_license_token', '' );
			update_option( 'envato_theme_license_traker', 'true' );
			update_option( 'envato_theme_license_checkbox', 0 );
			delete_transient( 'core_plugin_check' );
			echo json_encode(
				array(
					'status'  => true,
					'message' => 'Deactivate',
				)
			);
		} else {
			echo json_encode(
				array(
					'status'  => false,
					'message' => 'Contact With Support.',
				)
			);
		}

		wp_die();
	}
	public function envato_theme_license_dashboard_remove_js_composser_hook() {
		 global $wp_filter;
		if ( isset( $wp_filter['in_plugin_update_message-js_composer/js_composer.php'] ) ) {
			foreach ( $wp_filter['in_plugin_update_message-js_composer/js_composer.php']->callbacks[10] as $key => $value ) {
				if ( strpos( $key, 'envato_theme_license_dashboard_update_message_cb' ) === false ) {
					remove_action( 'in_plugin_update_message-js_composer/js_composer.php', $key, 10 );
					break;
				}
			}
		}

		if ( isset( $wp_filter['pre_set_site_transient_update_plugins'] ) ) {
			foreach ( $wp_filter['pre_set_site_transient_update_plugins']->callbacks[10] as $key => $value ) {
				if ( strpos( $key, 'check_update' ) !== false ) {
					remove_action( 'pre_set_site_transient_update_plugins', $key, 10 );
					break;
				}
			}
		}
	}

	public function envato_theme_license_dashboard_order_menu_page( $menu_ord ) {
		global $submenu;
		$support = '';
		if ( isset( $submenu[ $this->menu_slug_dashboard ] ) ) {
			foreach ( $submenu[ $this->menu_slug_dashboard ] as $key => $val ) {
				if ( $val[0] == 'Support' ) {
					$support = $submenu[ $this->menu_slug_dashboard ][ $key ];
					unset( $submenu[ $this->menu_slug_dashboard ][ $key ] );
				}
			}
			if ( $support != '' ) {
				array_push( $submenu[ $this->menu_slug_dashboard ], $support );
			}
			$submenu[ $this->menu_slug_dashboard ] = array_values( $submenu[ $this->menu_slug_dashboard ] );
		}
	}

	/* Active Licence */

	public function envato_theme_license_dashboard_check_info( $false, $action, $arg ) {
		$plugin_check = get_transient( 'core_plugin_check' );
		if ( $plugin_check ) {
			$response = json_decode( $plugin_check );
		} else {
			$url           = $this->envato_theme_license_url_build( 'checkdata' );
			$response      = wp_remote_get(
				$url,
				array(
					'headers' => array(
						'referer' => home_url(),
					),
				)
			);
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== (int) $response_code ) {
				return $false;
			}
			if ( is_wp_error( $response ) ) {
				return $false;
			}

			$response = json_decode( $response['body'] );
			set_transient( 'core_plugin_check', json_encode( $response ), 4 * DAY_IN_SECONDS );

		}
		foreach ( $response as $key => $item ) {
			if ( file_exists( WP_PLUGIN_DIR . '/' . $key ) ) {
				if ( isset( $arg->slug ) && isset( $item->slug ) ) {
					if ( $arg->slug == $item->slug ) {
						$information                        = new stdClass();
						$information->name                  = $item->pname;
						$information->slug                  = $item->slug;
						$information->new_version           = $item->new_version;
						$information->last_updated          = '';
						$information->sections              = array(
							'details'   => 'Details',
							'changelog' => 'Changelog',
						);
						$information->sections['details']   = $item->details;
						$information->sections['changelog'] = $item->changelog;
						if ( isset( $item->notice ) ) {
							$admin_notice_check = get_option( 'envato_theme_admin_notice' );
							$timestamp_over     = get_option( 'envato_theme_timestamp_over_' . $item->timestamp );
							if ( ! $admin_notice_check && ! $timestamp_over ) {
								$array              = array();
								$array['notice']    = $item->notice;
								$array['timestamp'] = $item->timestamp;
								update_option( 'envato_theme_admin_notice', json_encode( $array ) );
								update_option( 'envato_theme_timestamp_over_' . $item->timestamp, false );
							}
						}
						return $information;
					}
				}
			}
		}
		return $false;
	}

	public function envato_theme_license_dashboard_update_message_cb( $plugin_data, $result ) {
		$status = get_option( 'envato_theme_license_key_status' );
		if ( $status != 'valid' ) {
			echo sprintf( __( 'To receive automatic updates license activation is required. Please visit <a href="%s">Setting</a> page.', 'diaco' ), esc_url( admin_url() . 'admin.php?page=' . $this->menu_slug . 'product-registration' ) );
		}
	}

	public function envato_theme_license_url_build( $licence_action, $filename = '', $array = array() ) {

		$apikeys               = get_option( 'envato_theme_license_token' );
		$apikeys               = json_decode( stripslashes( $apikeys ), true );
		$querystring           = '';
		$query                 = array();
		$query['themeversion'] = $this->themeversion;
		$query['wp_version']   = $this->wp_version;
		$query['target']       = get_site_url();
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
			} else {
				$query['site_url']       = get_site_url();
				$query['item_id']        = $this->item_id;
				$query['licence_action'] = $licence_action;
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

		$encode = urlencode( $enquerystring );
		$url    = $this->liecence_endpoint . 'ck-ensl-api?' . $encode;
		return $url;
	}



	public function envato_theme_license_dashboard_transient_update_plugins( $transient ) {
		$plugin_check = get_transient( 'core_plugin_check' );
		if ( $plugin_check && $plugin_check != '' ) {
			$response = json_decode( $plugin_check );
		} else {
			$url           = $this->envato_theme_license_url_build( 'checkdata' );
			$response      = wp_remote_get(
				$url,
				array(
					'headers' => array(
						'referer' => home_url(),
					),
				)
			);
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== (int) $response_code ) {
				return new \WP_Error( $response_code, __( 'HTTP Error', 'diaco' ) );
			}
			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$response = json_decode( $response['body'] );
			set_transient( 'core_plugin_check', json_encode( $response ), 4 * DAY_IN_SECONDS );

		}
		$purchase_key = trim( get_option( 'envato_theme_license_key' ) );
		$status       = get_option( 'envato_theme_license_key_status' );
		if ( isset( $response->message ) &&  $status != 'valid' ) {
			update_option( 'envato_theme_license_key_error_massage', $response->message );
		} else {
			update_option( 'envato_theme_license_key_error_massage', '' );
		}
		
		if ( $status == 'valid' && $purchase_key != '' && $this->token != '' ) {
			foreach ( $response as $key => $item ) {
				if ( file_exists( WP_PLUGIN_DIR . '/' . $key ) ) {
					$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $key, true, true );
					if ( version_compare( $data['Version'], $item->new_version, '<' ) ) {
						$item->package               = $item->url = $this->envato_theme_license_url_build( 'update', $item->slug );
						$transient->response[ $key ] = $item;
					}
				}
			}
		} else {
			foreach ( $response as $key => $item ) {
				if ( file_exists( WP_PLUGIN_DIR . '/' . $key ) ) {
					$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $key, true, true );
					if ( version_compare( $data['Version'], $item->new_version, '<' ) ) {
						$item->package               = $item->url = $this->envato_theme_license_url_build( 'update', $item->slug );
						$transient->response[ $key ] = $item;
					}
				}
			}
		}
		return $transient;
	}

	public function envato_theme_license_dashboard_conditional_admin_notice() {
		 $traker = get_option( 'envato_theme_license_traker' );
		if ( isset( $_GET['settings-updated'] ) ) {
			if ( $traker != '' ) {
				$status = get_option( 'envato_theme_license_key_status' );
				if ( $status == 'valid' ) { ?>
					<div class="notice notice-success">
						<p><strong><?php esc_html_e( 'License Activated', 'diaco' ); ?> </strong></p>
					</div>
				<?php } elseif ( $status == 'deactivated' ) { ?>
					<div class="notice notice-success">
						<p><strong><?php esc_html_e( 'License Deactiveted', 'diaco' ); ?><strong></p>
					</div>
				<?php } else { ?>
					<div class="notice notice-error">
						<p><strong><?php echo sprintf( __( '%s', 'diaco' ), $status ); ?><strong></p>
					</div>
					<?php
				}
			} else {
				$token = get_option( 'envato_theme_license_key' );
				if ( $token != '' ) {
					?>
					<div class="notice notice-success">
						<p><strong><?php esc_html_e( 'License Key saved', 'diaco' ); ?><strong></p>
					</div>
				<?php } else { ?>
					<div class="notice notice-error">
						<p><strong><?php esc_html_e( 'License Key blank', 'diaco' ); ?><strong></p>
					</div>
					<?php
				}
			}
		}
		update_option( 'envato_theme_license_traker', '' );
	}

	public function envato_theme_license_dashboard_theme_activate_license() {
		if ( isset( $_POST['envato_theme_theme_license_activate'] ) ) {
			if ( ! check_admin_referer( 'envato_theme_nonce', 'envato_theme_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}
			if ( isset( $_POST['envato_theme_theme_license_activate_checkbox'] ) && sanitize_text_field( $_POST['envato_theme_theme_license_activate_checkbox'] ) == 1 ) {
				$this->activated();
			}
		} elseif ( isset( $_POST['envato_theme_theme_license_deactivate'] ) ) {
			if ( ! check_admin_referer( 'envato_theme_nonce', 'envato_theme_nonce' ) ) {
				return; // get out if we didn't click the Activate button
			}
			$this->deactivated();
		}
		return;
	}

	public function activated() {
		if ( isset( $_POST['envato_theme_license_key'] ) ) {
			$license_data = json_decode( $_POST['envato_theme_license_key'] );
			if ( $license_data->status != 'alreadyactive' && $license_data->status != 'invalid' ) {
				update_option( 'envato_theme_license_key_status', $license_data->status );
				update_option( 'envato_theme_license_token', $license_data->apikeys );
			}
			update_option( 'envato_theme_license_checkbox', 1 );
			update_option( 'envato_theme_license_traker', 'true' );
			delete_transient( 'core_plugin_check' );
			update_option( 'envato_theme_license_key_error_massage', false );
			
		}
	}


	public function deactivated() {
		 $url = $this->envato_theme_license_url_build( 'deactivate' );
		if ( ! $url ) {
			$this->returnmassage = esc_html__( 'Api Key Not Found..', 'diaco' );
			$status              = 'deactivated';
		} else {
			$args     = array(
				'timeout'   => 15,
				'sslverify' => false,
			);
			$response = wp_remote_get( $url, $args );
			if ( is_wp_error( $response ) ) {
				return false;
			}
			$response_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== (int) $response_code ) {
				return new \WP_Error( $response_code, __( 'HTTP Error', 'diaco' ) );
			}
			if ( is_wp_error( $response ) ) {
				return new \WP_Error( $response_code, __( 'Error Found', 'diaco' ) );
			}
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$status       = $license_data->status;

		}
		update_option( 'envato_theme_license_key_status', $status );
		update_option( 'envato_theme_license_key', '' );
		update_option( 'envato_theme_license_token', '' );
		update_option( 'envato_theme_license_traker', 'true' );
		update_option( 'envato_theme_license_checkbox', 0 );
		delete_transient( 'core_plugin_check' );
		$license = get_option( 'envato_theme_license_key' );

	}

	public function envato_theme_license_sanitize( $new ) {
		$old = get_option( 'envato_theme_license_key' );
		if ( $old && $old != $new ) {
			update_option( 'envato_theme_license_key_status', 'deactivated' );
		}
		return esc_attr( $new );
	}

	/* End Active Licence */
	public function envato_theme_license_dashboard_sample_admin_notice() {
		$purchase_key  = trim( get_option( 'envato_theme_license_key' ) );
		$status        = get_option( 'envato_theme_license_key_status' );
		$admin_notice  = get_option( 'envato_theme_admin_notice' );
		$error_massage = get_option( 'envato_theme_license_key_error_massage' );
		if ( $admin_notice ) {
			$admin_notice = json_decode( $admin_notice, true );
			if ( $admin_notice['timestamp'] < strtotime( 'now' ) ) {
				update_option( 'envato_theme_timestamp_over_' . $admin_notice['timestamp'], true );
				update_option( 'envato_theme_admin_notice', '' );
			} else {
				add_settings_error( 'envato_theme_license_m_1', 'envato_theme_license_m1', $error_massage );
				settings_errors( 'envato_theme_license_m_1' );

			}
		}
		if ( $status != 'valid' || $purchase_key == '' || $this->token == '' ) {
			?>
			<div id="setting-error-notice" class="error settings-error notice is-dismissible">
				<p><strong><span class="setting-error-notice-heading" style="margin-top:-0.4em"><?php echo esc_html__( 'Require Activation', 'diaco' ); ?></span><span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;"><?php echo sprintf( __( "%1\$s Theme Need to active with purchase code. Otherwise you can't Active / Update Bundle Plugin. You can active from <a href='%2\$s'>Here</a>.", 'diaco' ), $this->dashboard_Name, esc_url( admin_url() . 'admin.php?page=' . $this->menu_slug . 'product-registration' ) ); ?> </span>
					</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_html__( 'Dismiss this notice.', 'diaco' ); ?> </span></button>
			</div>
			<?php
		}

		if ( $error_massage && $status !="valid" ) {
			add_settings_error( 'envato_theme_license_m_1', 'envato_theme_license_m1', $error_massage );
			settings_errors( 'envato_theme_license_m_1' );
		}
	}
	public function envato_theme_license_system_change_admin_notice() {
		$purchase_key = trim( get_option( 'envato_theme_license_key' ) );
		$apikeys      = get_option( 'envato_theme_license_token' );
		$apikeys      = json_decode( stripslashes( $apikeys ), true );
		if ( $purchase_key != '' & $apikeys == '' ) {
			  $error_massage = esc_html__( 'Our theme license system has been changed, Please active again your theme license', 'diaco' );
			  add_settings_error( 'envato_theme_license_change_1', 'envato_theme_license_change', $error_massage );
			  settings_errors( 'envato_theme_license_change_1' );
		}
	}
	/**
	 * Register a custom menu page.
	 */

	public function envato_theme_license_dashboard_add_menu() {
		 global $submenu;
		$page = add_menu_page(
			$this->dashboard_Name,
			$this->dashboard_Name,
			'read',
			$this->menu_slug_dashboard,
			array( $this, 'render' ),
			'',
			6
		);
		add_submenu_page( $this->menu_slug_dashboard, 'Welcome', 'Welcome', 'manage_options', $this->menu_slug_dashboard );
		add_submenu_page( $this->menu_slug_dashboard, 'Product Registration', 'Product Registration', 'manage_options', $this->menu_slug . 'product-registration', array( $this, 'product_registration' ) );
		add_submenu_page( $this->menu_slug_dashboard, 'System Status', 'System Status', 'manage_options', $this->menu_slug . 'system-status', array( $this, 'system_status' ) );
		add_submenu_page( $this->menu_slug_dashboard, 'Plugin', 'Plugin', 'manage_options', $this->menu_slug . 'install-required-plugins', array( $this, 'plugin' ) );
	
		if ( $this->token != '' ){
			if ( class_exists( 'OCDI_Plugin' && $this->token != '') ){
				add_submenu_page( $this->menu_slug_dashboard, 'Import Demo Data', 'Import Demo Data', 'manage_options', $this->menu_slug . 'demo-content-install', array( $this, 'demo_content_install' ) );
			} elseif ( ! class_exists( 'OCDI_Plugin' ) ) {
				add_submenu_page( $this->menu_slug_dashboard, 'Import Demo Data', 'Import Demo Data', 'manage_options', $this->menu_slug . 'demo-content-install', array( $this, 'demo_content_install' ) );
			}
		}

		add_submenu_page( $this->menu_slug_dashboard, 'Support', 'Support', 'manage_options', $this->menu_slug . 'support', array( $this, 'support' ) );
	}

	public function envato_theme_license_dashboard_style() {
		wp_enqueue_style( $this->menu_slug_dashboard . '-style', get_template_directory_uri() . '/framework/dashboard/admin/css/dashboard-style.css', '', time() );
		wp_enqueue_script( $this->menu_slug_dashboard . '-js', get_template_directory_uri() . '/framework/dashboard/admin/js/dashboard-js.js', array( 'jquery', 'jquery-ui-tooltip' ), time(), true );
		wp_localize_script(
			$this->menu_slug_dashboard . '-js',
			'ajax_dashboard_js',
			array(
				'copytext'          => esc_html__( 'Copied!', 'diaco' ),
				'item_id'           => $this->item_id,
				'site_url'          => get_site_url(),
				'name'              => $this->dashboard_Name,
				'liecence_endpoint' => $this->liecence_endpoint,
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	public function demo_content_install() {
		$this->envato_theme_license_dashboard_get_tabs( 'demo' );
		include get_template_directory() . '/framework/dashboard/admin/demo-content-install.php';
	}

	public function support() {
		 $this->envato_theme_license_dashboard_get_tabs( 'support' );
		include get_template_directory() . '/framework/dashboard/admin/support.php';
	}
	public function plugin() {
		$this->envato_theme_license_dashboard_get_tabs( 'plugin' );
		include get_template_directory() . '/framework/dashboard/admin/plugin.php';
	}

	public function system_status() {
		$this->envato_theme_license_dashboard_get_tabs( 'systemstatus' );
		include get_template_directory() . '/framework/dashboard/admin/system-status.php';
	}

	public function envato_theme_license_dashboard_get_tabs( $activetab ) {

		$tabarray = array(
			'start'        => array(
				'title' => esc_html__( 'Getting Started', 'diaco' ),
				'link'  => '?page=' . $this->menu_slug_dashboard,
			),
			'registration' => array(
				'title' => esc_html__( 'Registration', 'diaco' ),
				'link'  => '?page=' . $this->menu_slug . 'product-registration',
			),
			'systemstatus' => array(
				'title' => esc_html__( 'System Status', 'diaco' ),
				'link'  => '?page=' . $this->menu_slug . 'system-status',
			),
			'plugin'       => array(
				'title' => esc_html__( 'Plugins', 'diaco' ),
				'link'  => '?page=' . $this->menu_slug . 'install-required-plugins',
			),
		);

		if ( $this->token != '' ) {
			if ( class_exists( 'OCDI_Plugin' ) ) {
				$tabarray['demo'] = array(
					'title' => esc_html__( 'Demo Import', 'diaco' ),
					'link'  => '?page=' . $this->menu_slug . 'one-click-demo-import',
				);
			} else {
				$tabarray['demo'] = array(
					'title' => esc_html__( 'Demo Import', 'diaco' ),
					'link'  => '?page=' . $this->menu_slug . 'demo-content-install',
				);
			}
		}
		if ( class_exists( 'iconmoonFontAdd' ) && $this->token != '' ) {
			$tabarray['icon'] = array(
				'title' => esc_html__( 'Icon Add', 'diaco' ),
				'link'  => '?page=custom-icon-upload',
			);
		}
		$tabarray['support'] = array(
			'title' => esc_html__( 'Support', 'diaco' ),
			'link'  => '?page=' . $this->menu_slug . 'support',
		);
		?>
		<h2 class="nav-tab-wrapper">
			<?php
			foreach ( $tabarray as $key => $tab ) {
				if ( $activetab == $key ) {
					?>
					<span class="nav-tab nav-tab-active"><?php echo sprintf( __( '%s', 'diaco' ), $tab['title'] ); ?></span>
					<?php
				} else {
					?>
					<a href="<?php echo esc_url( $tab['link'] ); ?>" class="nav-tab"><?php echo sprintf( __( '%s', 'diaco' ), $tab['title'] ); ?></a>
					<?php
				}
			}
			?>
		</h2>
		<?php

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

	public function product_registration() {
		$this->envato_theme_license_dashboard_get_tabs( 'registration' );
		$urlapihit      = $this->envato_theme_license_url_build( 'start' );
		$urlapideactive = $this->envato_theme_license_url_build( 'deactivate' );
		$returnmassage  = $this->returnmassage;
		include get_template_directory() . '/framework/dashboard/admin/activation.php';
	}
	public function render() {
		?>
		<div class="wrap">
			<div id="envato-theme-license-dashboard">
				<div id="post-body" class="columns-2">
					<div id="post-body-content">
						<div class="about-wrap">
							<?php include get_template_directory() . '/framework/dashboard/admin/wellcome.php'; ?>
							<?php $this->envato_theme_license_dashboard_get_tabs( 'start' ); ?>
							<?php include get_template_directory() . '/framework/dashboard/admin/getting-started.php'; ?>
						</div>
					</div>
				</div>

			</div>
		</div>

		<?php
	}
}

new DashboardEssential();