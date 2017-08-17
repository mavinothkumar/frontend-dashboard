<?php
/**
 * Plugin Name: Frontend Dashboard
 * Plugin URI: https://buffercode.com/plugin/frontend-dashboard
 * Description: Front end dashboard provide high flexible way to customize the user dashboard on front end rather than WordPress wp-admin dashboard.
 * Version: 1.1
 * Author: vinoth06
 * Author URI: http://buffercode.com/
 * License: GPLv2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: fed
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Version Number
 */
define( 'BC_FED_PLUGIN_VERSION', '1.1' );
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
define( 'BC_FED_USER_PROFILE_DB', 'fed_user_profile' );
/**
 * Dashboard Menu Items
 */
define( 'BC_FED_MENU_DB', 'fed_menu' );
/**
 * Post Fields
 */
define( 'BC_FED_POST_DB', 'fed_post' );
/**
 * Plugin URL
 */
define('BC_FED_API_PLUGIN_LIST','https://buffercode/api/v1/fed/plugin_list');


require_once BC_FED_PLUGIN_DIR . '/admin/install/install.php';

/**
 * Loader
 */
require_once BC_FED_PLUGIN_DIR . '/include/loader/FED_Template_loader.php';
require_once BC_FED_PLUGIN_DIR . '/include/page-template/FED_Page_Template.php';


/**
 * Include Necessary Files
 */
require_once BC_FED_PLUGIN_DIR . '/admin/menu/menu.php';
require_once BC_FED_PLUGIN_DIR . '/admin/menu/menu_functions.php';

require_once BC_FED_PLUGIN_DIR . '/admin/model/user_profile.php';
require_once BC_FED_PLUGIN_DIR . '/admin/model/menu.php';
require_once BC_FED_PLUGIN_DIR . '/admin/model/common.php';


require_once BC_FED_PLUGIN_DIR . '/admin/function-admin.php';

require_once BC_FED_PLUGIN_DIR . '/admin/request/menu.php';
require_once BC_FED_PLUGIN_DIR . '/admin/request/admin.php';
require_once BC_FED_PLUGIN_DIR . '/admin/request/function.php';
require_once BC_FED_PLUGIN_DIR . '/admin/request/user_profile.php';



require_once BC_FED_PLUGIN_DIR . '/admin/request/tabs/user_profile_layout.php';
require_once BC_FED_PLUGIN_DIR . '/admin/request/tabs/post_options.php';
require_once BC_FED_PLUGIN_DIR . '/admin/request/tabs/login.php';
require_once BC_FED_PLUGIN_DIR . '/admin/request/tabs/user.php';


require_once BC_FED_PLUGIN_DIR . '/common/function-common.php';
require_once BC_FED_PLUGIN_DIR . '/common/script.php';

/**
 * Shortcodes | Login
 */
require_once BC_FED_PLUGIN_DIR . '/shortcodes/login/login-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/shortcodes/login/login-data.php';
require_once BC_FED_PLUGIN_DIR . '/shortcodes/login/login-only-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/shortcodes/login/register-only-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/shortcodes/login/forgot-password-only-shortcode.php';


require_once BC_FED_PLUGIN_DIR . '/shortcodes/user_role.php';



require_once BC_FED_PLUGIN_DIR . '/shortcodes/dashboard/dashboard-shortcode.php';


require_once BC_FED_PLUGIN_DIR . '/frontend/menu/menus.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/request/login/login.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/request/login/index.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/request/login/forgot.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/request/login/register.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/request/login/validation.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/request/support/support.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/request/dashboard/post.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/controller/profile.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/controller/menu.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/controller/payment.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/controller/posts.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/controller/support.php';
require_once BC_FED_PLUGIN_DIR . '/frontend/controller/logout.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/request/user_profile/user_profile.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/request/validation/validation.php';

require_once BC_FED_PLUGIN_DIR . '/frontend/function-frontend.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/checkbox.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/email.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/number.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/password.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/radio.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/select.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/text.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/textarea.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/url.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/input_fields/common.php';


require_once BC_FED_PLUGIN_DIR . '/admin/layout/admin.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/profile.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/post_fields.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/add_edit_profile.php';


require_once BC_FED_PLUGIN_DIR . '/admin/layout/metabox/post-meta-box.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/error.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/dashboard_menu.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/help.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/status.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/plugins.php';



require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user_profile/user_profile_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user_profile/settings.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user/user_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user/role.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/permissions.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/dashboard.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/post_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/settings.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/menu.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/login_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/register_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/settings.php';

