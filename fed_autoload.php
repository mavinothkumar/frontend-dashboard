<?php
/**
 * Autoload Files
 */
require_once BC_FED_PLUGIN_DIR . '/admin/install/install.php';
require_once BC_FED_PLUGIN_DIR . '/admin/install/initial_setup.php';
/**
 * Loader
 */
require_once BC_FED_PLUGIN_DIR . '/include/loader/FED_Template_Loader.php';
require_once BC_FED_PLUGIN_DIR . '/include/page-template/FED_Page_Template.php';


/**
 * Include Necessary Files
 */
require_once BC_FED_PLUGIN_DIR . '/admin/menu/FED_AdminMenu.php';

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
require_once BC_FED_PLUGIN_DIR . '/frontend/request/login/reset.php';
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

require_once BC_FED_PLUGIN_DIR . '/admin/layout/FED_AdminUserProfile.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/add_edit_profile.php';


require_once BC_FED_PLUGIN_DIR . '/admin/layout/metabox/post-meta-box.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/error.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user_profile/user_profile_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user_profile/settings.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user/user_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user/role.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/user/user_upload.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/permissions.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/dashboard.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/post_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/settings.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/post/menu.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/login_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/register_tab.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/settings.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/settings_tab/login/restrict_wp_tab.php';

require_once BC_FED_PLUGIN_DIR . '/admin/layout/custom_layout/FEDCustomCSS.php';
require_once BC_FED_PLUGIN_DIR . '/admin/layout/custom_layout/helper.php';

require_once BC_FED_PLUGIN_DIR . '/config/config.php';

require_once BC_FED_PLUGIN_DIR . '/route/FED_Routes.php';

require_once BC_FED_PLUGIN_DIR . '/admin/hooks/FED_ActionHooks.php';

require_once BC_FED_PLUGIN_DIR . '/log/FED_Log.php';
