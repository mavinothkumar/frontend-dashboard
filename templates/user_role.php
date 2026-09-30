<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * User Role.
 *
 * @package Frontend Dashboard.
 */

// phpcs:ignore WordPress.Security.NonceVerification.Recommended
if ( isset( $_REQUEST['fed_user_profile'] ) ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	fed_show_user_by_role( $fed_user_attr, sanitize_text_field( wp_unslash( $_REQUEST['fed_user_profile'] ) ) );
} else {
	fed_show_users_by_role( $fed_user_attr );
}
