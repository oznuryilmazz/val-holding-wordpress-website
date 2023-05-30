<?php

if (!class_exists('wpmlUpdating')) {	
	class wpmlUpdating extends wpMailPlugin {
	
		function __construct() {
			if ($_GET['method'] != "wizard_install") {
				$this -> updating_plugin();
			}
		}
		
		function install_wizard() {
			$this -> remove_server_limits();
			$wpml_add_option_count = $this -> options();
			$this -> add_option('wizard_install', 1);
		}
		
		function update_wizard() {
			
		}
	}
}

$wpmlUpdating = new wpmlUpdating();

?>