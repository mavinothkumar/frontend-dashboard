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
		$this->file = fopen( self::getFileName(), 'a+' );
		if ( ! $this->file ) {
			new WP_Error(
				'403_FILE_PERMISSION',
				sprintf( "Could not open file '%s' for writing.", self::getFileName() )
			);
		}
	}

	/**
	 * @return string
	 */
	public static function getFileName() {
		if ( self::$filename == null ) {
			self::$filename = BC_FED_PLUGIN_DIR . '/log/dashboard.log';
		}

		return self::$filename;
	}

	/**
	 * @param $filename
	 */
	public static function setFileName( $filename ) {
		self::$filename = $filename;
	}

	/**
	 * @param  bool  $condition
	 */
	public static function enableIf( $condition = true ) {
		if ( (bool) $condition ) {
			self::$enabled = true;
		}
	}

	/**
	 *
	 */
	public static function disable() {
		self::$enabled = false;
	}

	/**
	 * @param $message
	 * @param  int  $level
	 */
	public static function writeIfEnabled( $message, $level = self::DEBUG ) {
		if ( self::$enabled ) {
			self::writeLog( $message, $level );
		}
	}

	/**
	 * @param $message
	 * @param  int  $level
	 */
	public static function writeLog( $message, $level = self::DEBUG ) {
		self::getInstance()->writeLine( $message, $level );
	}

	/**
	 * @param $message
	 * @param $level
	 */
	protected function writeLine( $message, $level = 'DEBUG' ) {
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
	 * @param $message
	 * @param  string  $level
	 */
	public function write_into_file( $message, $level = 'DEBUG' ) {
		$date    = new DateTime();
		$en_tete = $date->format( 'd/m/Y H:i:s' );
		$write   = sprintf( " \n \n ==================%s (%s) START================== \n \n", $en_tete, $level );
		$write   .= print_r( $message, 1 );
		$write   .= sprintf( " \n \n ==================%s (%s) END================== \n \n \n", $en_tete, $level );
		fwrite( $this->file, $write );
	}

	/**
	 * @return FED_Log
	 */
	protected static function getInstance() {
		if ( ! self::hasInstance() ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return bool
	 */
	protected static function hasInstance() {
		return self::$instance instanceof self;
	}

	/**
	 * @param $condition
	 * @param $message
	 * @param  int  $level
	 */
	public static function writeIfEnabledAnd( $condition, $message, $level = self::DEBUG ) {
		if ( self::$enabled ) {
			self::writeIf( $condition, $message, $level );
		}
	}

	/**
	 * @param $condition
	 * @param $message
	 * @param  int  $level
	 */
	public static function writeIf( $condition, $message, $level = self::DEBUG ) {
		if ( $condition ) {
			self::writeLog( $message, $level );
		}
	}

	public function __destruct() {
		fclose( $this->file );
	}
}
