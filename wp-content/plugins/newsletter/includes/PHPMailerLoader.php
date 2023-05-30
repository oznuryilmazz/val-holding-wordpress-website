<?php

namespace TNP\Mailer;

class PHPMailerLoader {

	/**
	 *
	 */
	public static function load() {

		global $wp_version;

		if ( class_exists( 'PHPMailer' ) ) {
			return;
		}

		if ( version_compare( $wp_version, '5.5' ) >= 0 ) {
			require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
			require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
			require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

			class_alias( \PHPMailer\PHPMailer\PHPMailer::class, 'PHPMailer' );
			class_alias( \PHPMailer\PHPMailer\SMTP::class, 'SMTP' );
			class_alias( \PHPMailer\PHPMailer\Exception::class, 'phpmailerException' );
		} else {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
			require_once ABSPATH . WPINC . '/class-smtp.php';
		}

	}

        /**
         * 
         * @param boolean $exceptions
         * @return \PHPMailer\PHPMailer\PHPMailer
         */
	public static function make_instance($exceptions = false) {
		self::load();

		return new \PHPMailer( $exceptions );
	}


}
