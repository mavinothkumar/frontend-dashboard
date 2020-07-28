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

$files = array(
	'/includes/admin/install/install.php',
	'/includes/admin/install/class-fed-install-addons.php',
	'/includes/admin/install/initial-setup.php',
	'/includes/admin/menu/class-fed-admin-menu.php',
	'/includes/admin/menu/items/dashboard-menu.php',
	'/includes/admin/menu/items/user-profile.php',
	'/includes/admin/menu/items/post-fields.php',
	'/includes/admin/menu/items/add-profile-post-fields.php',
	'/includes/admin/menu/items/addons.php',
	'/includes/admin/menu/items/help.php',
	'/includes/admin/menu/items/status.php',
	'/includes/admin/model/user-profile.php',
	'/includes/admin/model/menu.php',
	'/includes/admin/model/common.php',
	'/includes/admin/function-admin.php',
	'/includes/admin/request/menu.php',
	'/includes/admin/request/admin.php',
	'/includes/admin/request/function.php',
	'/includes/admin/request/user-profile.php',
	'/includes/admin/request/status.php',
	'/includes/admin/request/tabs/user-profile-layout.php',
	'/includes/admin/request/tabs/post-options.php',
	'/includes/admin/request/tabs/login.php',
	'/includes/admin/request/tabs/user.php',
	'/includes/admin/layout/input_fields/checkbox.php',
	'/includes/admin/layout/input_fields/email.php',
	'/includes/admin/layout/input_fields/number.php',
	'/includes/admin/layout/input_fields/password.php',
	'/includes/admin/layout/input_fields/radio.php',
	'/includes/admin/layout/input_fields/select.php',
	'/includes/admin/layout/input_fields/text.php',
	'/includes/admin/layout/input_fields/textarea.php',
	'/includes/admin/layout/input_fields/url.php',
	'/includes/admin/layout/input_fields/common.php',
	'/includes/admin/layout/class-fed-admin-user-profile.php',
	'/includes/admin/layout/add-edit-profile.php',
	'/includes/admin/layout/metabox/post-meta-box.php',
	'/includes/admin/layout/error.php',
	'/includes/admin/layout/settings_tab/user_profile/user-profile-tab.php',
	'/includes/admin/layout/settings_tab/user_profile/settings.php',
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
	'/includes/admin/hooks/class-fed-action-hooks.php',
	'/includes/admin/validation/class-fed-validation.php',
	'/includes/admin/validation/class-fed-validate.php',
	'/includes/admin/payment/class-fed-payment-menu.php',
	'/includes/admin/payment/class-fed-payment.php',
	'/includes/admin/payment/class-fed-transaction.php',
	'/includes/admin/payment/payment.php',
	'/includes/admin/payment/class-fed-invoice.php',
	'/includes/admin/payment/class-fed-invoice-template.php',
	'/includes/admin/payment/class-fed-payment-widgets.php',
	'/includes/admin/pro/plugins/class-fed-sc-pro.php',
	'/includes/admin/pro/plugins/class-fed-pp-pro.php',
	'/includes/admin/pro/plugins/class-fed-mp-pro.php',
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
	'/includes/config/config.php',
	'/route/class-fed-routes.php',
	'/route/class-fed-request.php',
	'/includes/admin/widgets/class-fed-user-count-widget.php',
	'/vendor/template-loaders/loader/class-fed-template-loader.php',
	'/vendor/template-loaders/page-template/class-fed-page-template.php',
	'/includes/log/class-fed-log.php',
	'/includes/common/function-common.php',
	'/includes/common/script.php',
	'/includes/shortcodes/login/login-shortcode.php',
	'/includes/shortcodes/login/login-data.php',
	'/includes/shortcodes/login/login-only-shortcode.php',
	'/includes/shortcodes/login/register-only-shortcode.php',
	'/includes/shortcodes/login/forgot-password-only-shortcode.php',
	'/includes/shortcodes/widget/taxonomy.php',
	'/includes/shortcodes/user-role.php',
	'/includes/shortcodes/dashboard/dashboard-shortcode.php',
	'/includes/shortcodes/payments/transactions.php',
	'/includes/frontend/request/login/login.php',
	'/includes/frontend/request/login/index.php',
	'/includes/frontend/request/login/forgot.php',
	'/includes/frontend/request/login/reset.php',
	'/includes/frontend/request/login/register.php',
	'/includes/frontend/request/login/validation.php',
	'/includes/frontend/controller/profile.php',
	'/includes/frontend/controller/menu.php',
	'/includes/frontend/controller/posts.php',
	'/includes/frontend/controller/logout.php',
	'/includes/frontend/request/user_profile/user-profile.php',
	'/includes/frontend/request/validation/validation.php',
	'/includes/widgets/class-fed-post-widget.php',
	'/includes/admin/controllers/class-fed-terms.php',
	'/includes/admin/controllers/class-fed-taxonomy.php',
	'/includes/frontend/function-frontend.php',
);

/**
 * Disable Files.
 * array(
 * '/includes/frontend/controller/payment.php',
 * )
 */

foreach ( $files as $file ) {
	if ( file_exists( BC_FED_PLUGIN_DIR . $file ) ) {
		require_once BC_FED_PLUGIN_DIR . $file;
	}
}
