<?php
/**
 * Frontend Dashboard Log
 *
 * @package Frontend Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FED_Logging
 */
class FED_Log {
	const ERROR_LEVEL = 255;
	const DEBUG = 'DEBUG';
	const NOTICE = 'NOTICE';
	const WARNING = 'WARNING';
	const ERROR = 'ERROR';

	/**
	 * Instance.
	 *
	 * @var $instance
	 */
	static protected $instance;

	/**
	 * Enabled.
	 *
	 * @var bool $enabled
	 */
	static protected $enabled = false;

	/**
	 * Filename.
	 *
	 * @var string $filename .
	 */
	static protected $filename;

	/**
	 * File.
	 *
	 * @var false|resource
	 */
	protected $file;

	/**
	 * FED_Log constructor.
	 */
	protected function __construct() {
		$this->file = fopen( self::get_file_name(), 'a+' );
		if ( ! $this->file ) {
			new WP_Error(
				'403_FILE_PERMISSION',
				sprintf( "Could not open file '%s' for writing.", self::get_file_name() )
			);
		}
	}

	/**
	 * Get File Name.
	 *
	 * @return string
	 */
	public static function get_file_name() {
		if ( null == self::$filename ) {
			$upload_dir = wp_get_upload_dir();
			$dir        = $upload_dir['basedir'] . '/frontend-dashboard/log/';
			$file       = 'fed.log';
			if ( is_dir( $dir ) ) {
				self::$filename = $dir . $file;
			} else {
				wp_mkdir_p( $dir );
				self::$filename = $dir . $file;
			}
		}

		return self::$filename;
	}

	/**
	 * Set File Name.
	 *
	 * @param  string $filename  File Name.
	 */
	public static function set_filename( $filename ) {
		self::$filename = $filename;
	}

	/**
	 * Write Log. [Retaining for old actions.]
	 *
	 * @param  string | array $message  message.
	 * @param  int | string   $level  Level.
	 */
	public static function writeLog( $message, $level = self::DEBUG ) {
		self::get_instance()->write_line( $message, $level );
	}

	/**
	 * Write Line.
	 *
	 * @param  string       $message  message.
	 * @param  int | string $level  Level.
	 */
	protected function write_line( $message, $level = 'DEBUG' ) {
		switch ( $level ) {
			case self::NOTICE:
				$this->write_into_file( $message, 'NOTICE' );
				break;
			case self::WARNING:
				$this->write_into_file( $message, 'WARNING' );
				break;
			case self::ERROR:
				$this->write_into_file( $message, 'ERROR' );
				break;
			default:
				$this->write_into_file( $message, 'DEBUG' );
				break;

		}

	}

	/**
	 * Write into file.
	 *
	 * @param  string       $message  message.
	 * @param  int | string $level  Level.
	 */
	public function write_into_file( $message, $level = 'DEBUG' ) {
		$date    = new DateTime();
		$en_tete = $date->format( 'd/m/Y H:i:s' );
		$write   = sprintf( " \n \n ==================%s (%s) START================== \n \n", $en_tete, $level );
		// phpcs:ignore
		$write .= print_r( $message, 1 );
		$write .= sprintf( " \n \n ==================%s (%s) END================== \n \n \n", $en_tete, $level );
		// phpcs:ignore
		fwrite( $this->file, $write );
	}

	/**
	 * Get  Instance.
	 *
	 * @return FED_Log
	 */
	protected static function get_instance() {
		if ( ! self::has_instance() ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Has Instance.
	 *
	 * @return bool
	 */
	protected static function has_instance() {
		return self::$instance instanceof self;
	}

	/**
	 * Enable If.
	 *
	 * @param  bool $condition  Condition.
	 */
	public static function enable_if( $condition = true ) {
		if ( (bool) $condition ) {
			self::$enabled = true;
		}
	}

	/**
	 * Disable.
	 */
	public static function disable() {
		self::$enabled = false;
	}

	/**
	 * Write If Enabled.
	 *
	 * @param  string       $message  Message.
	 * @param  int | string $level  Level.
	 */
	public static function write_if_enabled( $message, $level = self::DEBUG ) {
		if ( self::$enabled ) {
			self::write_log( $message, $level );
		}
	}

	/**
	 * Write Log.
	 *
	 * @param  string       $message  message.
	 * @param  int | string $level  Level.
	 */
	public static function write_log( $message, $level = self::DEBUG ) {
		self::get_instance()->write_line( $message, $level );
	}

	/**
	 * Write If Enabled
	 *
	 * @param  string       $condition  Condition.
	 * @param  string       $message  Message.
	 * @param  int | string $level  Level.
	 */
	public static function write_if_enabled_and( $condition, $message, $level = self::DEBUG ) {
		if ( self::$enabled ) {
			self::write_if( $condition, $message, $level );
		}
	}

	/**
	 * Write if.
	 *
	 * @param  string       $condition  Condition.
	 * @param  string       $message  Message.
	 * @param  int | string $level  Level.
	 */
	public static function write_if( $condition, $message, $level = self::DEBUG ) {
		if ( $condition ) {
			self::write_log( $message, $level );
		}
	}

	/**
	 * Destruct.
	 */
	public function __destruct() {
		// phpcs:ignore
		fclose( $this->file );
	}
}
