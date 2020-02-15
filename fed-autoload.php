<?php
/**
 * Include all files.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Admin Files
 */
require_once BC_FED_PLUGIN_DIR . '/includes/admin/install/install.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/install/FEDInstallAddons.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/install/initial_setup.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/FED_AdminMenu.php';

// require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/frontend_dashboard.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/dashboard_menu.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/user_profile.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/post_fields.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/add_profile_post_fields.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/addons.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/help.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/menu/items/status.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/model/user_profile.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/model/menu.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/model/common.php';


require_once BC_FED_PLUGIN_DIR . '/includes/admin/function-admin.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/menu.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/admin.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/function.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/user_profile.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/status.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/tabs/user_profile_layout.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/tabs/post_options.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/tabs/login.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/request/tabs/user.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/checkbox.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/email.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/number.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/password.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/radio.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/select.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/text.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/textarea.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/url.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/input_fields/common.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/FED_AdminUserProfile.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/add_edit_profile.php';


require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/metabox/post-meta-box.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/error.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/user_profile/user_profile_tab.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/user_profile/settings.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/user/user_tab.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/user/role.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/user/user_upload.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/post/permissions.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/post/dashboard.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/post/post_tab.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/post/settings.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/post/menu.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/general/FED_Admin_General.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/email/FEDEmail.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/login/login_tab.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/login/register_tab.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/login/settings.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/login/restrict_wp_tab.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/login/restrict_username.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/settings_tab/login/frontend_login_menu.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/custom_layout/FEDCustomCSS.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/layout/custom_layout/helper.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/hooks/FED_ActionHooks.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/validation/FED_Validation.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/FEDPaymentMenu.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/FEDPayment.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/FEDTransaction.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/payment.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/FEDInvoice.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/FEDInvoiceTemplate.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/payment/FEDPaymentWidgets.php';


require_once BC_FED_PLUGIN_DIR . '/includes/admin/pro/plugins/FEDSCPRO.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/pro/plugins/FEDPPPRO.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/pro/plugins/FEDMPPRO.php';

require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-singleline.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-multiline.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-hidden.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-email.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-url.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-password.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-checkbox.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-radio.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-select.php';
require_once BC_FED_PLUGIN_DIR . '/includes/admin/fields/fed-form-number.php';

require_once BC_FED_PLUGIN_DIR . '/includes/config/config.php';

require_once BC_FED_PLUGIN_DIR . '/route/FED_Routes.php';
require_once BC_FED_PLUGIN_DIR . '/route/FED_Requests.php';


/**
 * Widget
 */
require_once BC_FED_PLUGIN_DIR . '/includes/admin/widgets/FEDUserCountWidget.php';


/**
 * Loader
 */
require_once BC_FED_PLUGIN_DIR . '/vendor/template-loaders/loader/class-fed-template-loader.php';
require_once BC_FED_PLUGIN_DIR . '/vendor/template-loaders/page-template/class-fed-page-template.php';

require_once BC_FED_PLUGIN_DIR . '/includes/log/class-fed-log.php';

require_once BC_FED_PLUGIN_DIR . '/includes/common/function-common.php';
require_once BC_FED_PLUGIN_DIR . '/includes/common/validation.php';
require_once BC_FED_PLUGIN_DIR . '/includes/common/script.php';

/**
 * Shortcodes | Login
 */
require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/login/login-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/login/login-data.php';
require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/login/login-only-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/login/register-only-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/login/forgot-password-only-shortcode.php';
require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/widget/taxonomy.php';

require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/user_role.php';

require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/dashboard/dashboard-shortcode.php';

require_once BC_FED_PLUGIN_DIR . '/includes/shortcodes/payments/transactions.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/menu/menus.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/login/login.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/login/index.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/login/forgot.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/login/reset.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/login/register.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/login/validation.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/support/support.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/dashboard/post.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/controller/profile.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/controller/menu.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/controller/payment.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/controller/posts.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/controller/support.php';
require_once BC_FED_PLUGIN_DIR . '/includes/frontend/controller/logout.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/user_profile/user_profile.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/request/validation/validation.php';

require_once BC_FED_PLUGIN_DIR . '/includes/frontend/function-frontend.php';


