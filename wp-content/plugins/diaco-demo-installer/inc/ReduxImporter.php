<?php
/**
 * Class for the Redux importer used in the One Click Demo Import plugin.
 *
 * @see https://wordpress.org/plugins/redux-framework/
 * @package ocdi
 */

namespace OCDI;

class ReduxImporter {
	/**
	 * Import Redux data from a JSON file, generated by the Redux plugin.
	 *
	 * @param array $import_data Array of arrays. Child array contains 'option_name' and 'file_path'.
	 */
	public static function import( $import_data ) {
		$ocdi          = OneClickDemoImport::get_instance();
		$log_file_path = $ocdi->get_log_file_path();

		// Redux plugin is not active!
		if ( ! class_exists( 'ReduxFramework' ) ) {
			$error_message = esc_html__( 'The Redux plugin is not activated, so the Redux import was skipped!', 'pt-ocdi' );

			// Add any error messages to the frontend_error_messages variable in OCDI main class.
			$ocdi->append_to_frontend_error_messages( $error_message );

			// Write error to log file.
			Helpers::append_to_file(
				$error_message,
				$log_file_path,
				esc_html__( 'Importing Redux settings' , 'pt-ocdi' )
			);

			return;
		}

		foreach ( $import_data as $redux_item ) {
			$redux_options_raw_data = Helpers::data_from_file( $redux_item['file_path'] );
			preg_match_all("!https?:[^?#]+\.(?:jpe?g|png|gif)!Ui",$redux_options_raw_data , $remote_url_new);
			foreach($remote_url_new[0] as $remote_url){
				if(strpos($remote_url,"wp-content")!==false){
					$urlpath=substr_replace($remote_url, "", 0, strpos($remote_url,"wp-content")+11);
					$site_url=rtrim(site_url(), '/');
					$remote_url_rep= $site_url."/wp-content/".$urlpath;
					$redux_options_raw_data=str_replace($remote_url,$remote_url_rep,$redux_options_raw_data);
				}
			}


			$redux_options_data = json_decode( $redux_options_raw_data, true );

			$redux_framework = \ReduxFrameworkInstances::get_instance( $redux_item['option_name'] );

			if ( isset( $redux_framework->args['opt_name'] ) ) {
				// Import Redux settings.
				// $redux_framework->set_options( $redux_options_data );
				$redux_framework->options_class->set( $redux_options_data );

				// Add this message to log file.
				$log_added = Helpers::append_to_file(
					sprintf( esc_html__( 'Redux settings import for: %s finished successfully!', 'pt-ocdi' ), $redux_item['option_name'] ),
					$log_file_path,
					esc_html__( 'Importing Redux settings' , 'pt-ocdi' )
				);
			}
			else {
				$error_message = sprintf( esc_html__( 'The Redux option name: %s, was not found in this WP site, so it was not imported!', 'pt-ocdi' ), $redux_item['option_name'] );

				// Add any error messages to the frontend_error_messages variable in OCDI main class.
				$ocdi->append_to_frontend_error_messages( $error_message );

				// Write error to log file.
				Helpers::append_to_file(
					$error_message,
					$log_file_path,
					esc_html__( 'Importing Redux settings' , 'pt-ocdi' )
				);
			}
		}
	}
}
