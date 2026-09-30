<?php
/**
 * Consolidated Helper & Backward Compatibility Functions
 *
 * @package Frontend Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fed_core_files = array(
	// Common helpers & scripts
	'/includes/common/function-common.php',
	'/includes/common/validation.php',
	'/includes/common/script.php',

	// Models
	'/includes/admin/model/common.php',
	'/includes/admin/model/menu.php',
	'/includes/admin/model/user-profile.php',

	// Admin functions & install definitions
	'/includes/admin/install/install.php',
	'/includes/admin/install/class-fed-install-addons.php',
	'/includes/admin/install/initial-setup.php',
	'/includes/admin/function-admin.php',

	// Fields
	'/includes/admin/fields/fed-form-singleline.php',
	'/includes/admin/fields/fed-form-multiline.php',
	'/includes/admin/fields/fed-form-hidden.php',
	'/includes/admin/fields/fed-form-email.php',
	'/includes/admin/fields/fed-form-url.php',
	'/includes/admin/fields/fed-form-password.php',
	'/includes/admin/fields/fed-form-checkbox.php',
	'/includes/admin/fields/fed-form-radio.php',
	'/includes/admin/fields/fed-form-select.php',
	'/includes/admin/fields/fed-form-number.php',
	'/includes/admin/fields/fed-form-file.php',
	'/includes/admin/fields/fed-form-date.php',
	'/includes/admin/fields/fed-form-color.php',
	'/includes/admin/fields/fed-form-wpeditor.php',
	'/includes/admin/fields/fed-form-label.php',
	'/includes/admin/fields/fed-form-table.php',

	// Layout Input Fields
	'/includes/admin/layout/input_fields/checkbox.php',
	'/includes/admin/layout/input_fields/email.php',
	'/includes/admin/layout/input_fields/number.php',
	'/includes/admin/layout/input_fields/password.php',
	'/includes/admin/layout/input_fields/radio.php',
	'/includes/admin/layout/input_fields/select.php',
	'/includes/admin/layout/input_fields/text.php',
	'/includes/admin/layout/input_fields/textarea.php',
	'/includes/admin/layout/input_fields/url.php',
	'/includes/admin/layout/input_fields/date.php',
	'/includes/admin/layout/input_fields/file.php',
	'/includes/admin/layout/input_fields/color.php',
	'/includes/admin/layout/input_fields/wp_editor.php',
	'/includes/admin/layout/input_fields/label.php',
	'/includes/admin/layout/input_fields/table.php',
	'/includes/admin/layout/input_fields/common.php',

	// Admin Layout & Settings Tabs
	'/includes/admin/layout/class-fed-admin-user-profile.php',
	'/includes/admin/layout/add-edit-profile.php',
	'/includes/admin/layout/metabox/post-meta-box.php',
	'/includes/admin/layout/error.php',
	'/includes/admin/layout/settings_tab/user_profile/user-profile-tab.php',
	'/includes/admin/layout/settings_tab/user_profile/settings.php',
	'/includes/admin/layout/settings_tab/user_profile/admin-bar-tab.php',
	'/includes/admin/layout/settings_tab/user_profile/templates-tab.php',
	'/includes/admin/layout/settings_tab/user/user-tab.php',
	'/includes/admin/layout/settings_tab/user/role.php',
	'/includes/admin/layout/settings_tab/user/user-upload.php',
	'/includes/admin/layout/settings_tab/post/permissions.php',
	'/includes/admin/layout/settings_tab/post/dashboard.php',
	'/includes/admin/layout/settings_tab/post/post-tab.php',
	'/includes/admin/layout/settings_tab/post/settings.php',
	'/includes/admin/layout/settings_tab/post/menu.php',
	'/includes/admin/layout/settings_tab/general/class-fed-admin-general.php',
	'/includes/admin/layout/settings_tab/email/class-fed-email.php',
	'/includes/admin/layout/settings_tab/login/login-tab.php',
	'/includes/admin/layout/settings_tab/login/register-tab.php',
	'/includes/admin/layout/settings_tab/login/settings.php',
	'/includes/admin/layout/settings_tab/login/restrict-wp-tab.php',
	'/includes/admin/layout/settings_tab/login/restrict-username.php',
	'/includes/admin/layout/settings_tab/login/frontend-login-menu.php',
	'/includes/admin/layout/custom_layout/fed-custom-css.php',
	'/includes/admin/layout/custom_layout/helper.php',

	// Admin Menus & Submenu Items
	'/includes/admin/menu/class-fed-admin-menu.php',
	'/includes/admin/menu/items/dashboard-menu.php',
	'/includes/admin/menu/items/user-profile.php',
	'/includes/admin/menu/items/post-fields.php',
	'/includes/admin/menu/items/add-profile-post-fields.php',
	'/includes/admin/menu/items/addons.php',
	'/includes/admin/menu/items/help.php',
	'/includes/admin/menu/items/status.php',

	// Admin Requests
	'/includes/admin/request/menu.php',
	'/includes/admin/request/admin.php',
	'/includes/admin/request/function.php',
	'/includes/admin/request/user-profile.php',
	'/includes/admin/request/status.php',
	'/includes/admin/request/addons.php',
	'/includes/admin/request/orders.php',
	'/includes/admin/request/tabs/user-profile-layout.php',
	'/includes/admin/request/tabs/post-options.php',
	'/includes/admin/request/tabs/login.php',
	'/includes/admin/request/tabs/user.php',

	// Routes & Global Request Handlers
	'/route/class-fed-request.php',

	// Validation, Hooks, Payments
	'/includes/admin/hooks/class-fed-action-hooks.php',
	'/includes/admin/validation/class-fed-validation.php',
	'/includes/admin/validation/class-fed-validate.php',
	'/includes/admin/payment/class-fed-payment-dashboard.php',
	'/includes/admin/payment/class-fed-payment-menu.php',
	'/includes/admin/payment/class-fed-payment.php',
	'/includes/admin/payment/class-fed-transaction.php',
	'/includes/admin/payment/class-fed-subscription.php',
	'/includes/admin/payment/class-fed-payment-gateway-hub.php',
	'/includes/admin/payment/payment.php',
	'/includes/admin/payment/class-fed-invoice.php',
	'/includes/admin/payment/class-fed-invoice-template.php',
	'/includes/admin/payment/class-fed-payment-widgets.php',
	'/includes/admin/pro/plugins/class-fed-mp-pro.php',
	'/includes/admin/pro/plugins/class-fed-pp-pro.php',
	'/includes/admin/pro/plugins/class-fed-sc-pro.php',
	'/includes/config/config.php',
	'/includes/admin/widgets/class-fed-user-count-widget.php',
	'/includes/log/class-fed-log.php',

	// Frontend functions & controllers
	'/includes/frontend/menu/menus.php',
	'/includes/frontend/function-frontend.php',
	'/includes/frontend/request/validation/validation.php',
	'/includes/frontend/request/user_profile/user-profile.php',
	'/includes/shortcodes/login/login-data.php',
	'/includes/shortcodes/login/login-only-shortcode.php',
	'/includes/shortcodes/login/register-only-shortcode.php',
	'/includes/shortcodes/login/forgot-password-only-shortcode.php',
	'/includes/shortcodes/widget/taxonomy.php',
	'/includes/widgets/class-fed-post-widget.php',
	'/includes/frontend/request/login/validation.php',
	'/includes/frontend/request/login/login.php',
	'/includes/frontend/request/login/register.php',
	'/includes/frontend/request/login/forgot.php',
	'/includes/frontend/request/login/reset.php',
	'/src/Controllers/Frontend/menu.php',
	'/src/Controllers/Frontend/profile.php',
	'/src/Controllers/Frontend/posts.php',
	'/src/Controllers/Frontend/logout.php',
	'/src/Blocks/class-fed-blocks.php',
);

foreach ( $fed_core_files as $fed_file ) {
	$fed_filepath = BC_FED_PLUGIN_DIR . $fed_file;
	if ( file_exists( $fed_filepath ) ) {
		require_once $fed_filepath;
	}
}

if ( ! function_exists( 'fed_input_box' ) ) {
	/**
	 * Proxy to FED\Helpers\FormHelper::input_box
	 */
	function fed_input_box( $meta_key, $attr = array(), $type = 'text' ) {
		return \FED\Helpers\FormHelper::input_box( $meta_key, $attr, $type );
	}
}

/**
 * Backward compatibility class alias for FED_Template_Loader
 */
if ( ! class_exists( 'FED_Template_Loader' ) && class_exists( '\FED\Helpers\TemplateLoader' ) ) {
	class FED_Template_Loader extends \FED\Helpers\TemplateLoader {}
}

/**
 * Backward compatibility class alias for FED_Routes
 */
if ( ! class_exists( 'FED_Routes' ) && class_exists( '\FED\Routes\Dashboard\DashboardRoutes' ) ) {
	class FED_Routes extends \FED\Routes\Dashboard\DashboardRoutes {}
}

/**
 * Backward compatibility class alias for FED_Requests
 */
if ( ! class_exists( 'FED_Requests' ) && class_exists( '\FED\Requests\Dashboard\DashboardRequest' ) ) {
	class FED_Requests extends \FED\Requests\Dashboard\DashboardRequest {}
}
