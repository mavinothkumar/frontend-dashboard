<?php
/**
 * Plugin Name: Frontend Dashboard
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard
 * Description: Frontend dashboard makes you flexible way to customize the user dashboard on frontend rather than WordPress wp-admin dashboard.
 * Version: 2.2.1
 * Author: vinoth06.
 * Author URI: https://buffercode.com/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: frontend-dashboard
 * Domain Path: /languages
 *
 * @package frontend-dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Version Number
 */
define( 'BC_FED_PLUGIN_VERSION', '2.2.1' );
define( 'BC_FED_PLUGIN_VERSION_TYPE', 'FREE' );

/**
 * App Name
 */
define( 'BC_FED_APP_NAME', 'Frontend Dashboard' );

/**
 * Root Path
 */
define( 'BC_FED_PLUGIN', __FILE__ );
/**
 * Plugin Base Name
 */
define( 'BC_FED_PLUGIN_BASENAME', plugin_basename( BC_FED_PLUGIN ) );
/**
 * Plugin Name
 */
define( 'BC_FED_PLUGIN_NAME', trim( dirname( BC_FED_PLUGIN_BASENAME ), '/' ) );
/**
 * Plugin Directory
 */
define( 'BC_FED_PLUGIN_DIR', untrailingslashit( dirname( BC_FED_PLUGIN ) ) );

/**
 * User Profile Table Name
 */
define( 'BC_FED_TABLE_USER_PROFILE', 'fed_user_profile' );
/**
 * Dashboard Menu Items
 */
define( 'BC_FED_TABLE_MENU', 'fed_menu' );
define( 'BC_FED_TABLE_MENU_META', 'fed_menu_meta' );
/**
 * Post Fields
 */
define( 'BC_FED_TABLE_POST', 'fed_post' );
define( 'BC_FED_TABLE_PAYMENT', 'fed_payment' );
define( 'BC_FED_TABLE_PAYMENT_ITEMS', 'fed_payment_items' );
/**
 * Plugin URL
 */
define( 'BC_FED_API_PLUGIN_LIST', 'https://buffercode/api/v1/fed/plugin_list' );

require_once BC_FED_PLUGIN_DIR . '/fed-autoload.php';
