<?php

namespace FED\Controllers\User;

use FED\Models\User\UserProfileModel;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UserProfileController
 *
 * Handles HTTP/POST submissions for Frontend Dashboard user profiles.
 */
class UserProfileController {

	/**
	 * @var UserProfileModel
	 */
	protected $model;

	public function __construct( UserProfileModel $model = null ) {
		$this->model = $model ?: new UserProfileModel();
	}

	/**
	 * Register actions and filters with the HookLoader.
	 *
	 * @param \FED\Hooks\HookLoader $loader
	 */
	public function register_hooks( $loader ) {
		$loader->add_action( 'admin_post_fed_save_user_profile', $this, 'save_user_profile' );
		$loader->add_action( 'admin_post_nopriv_fed_save_user_profile', $this, 'block_unauthorized' );
	}

	/**
	 * Block unauthenticated requests.
	 */
	public function block_unauthorized() {
		wp_die( esc_html__( 'Inappropriate Action', 'frontend-dashboard' ) );
	}

	/**
	 * Handle user profile save form submission.
	 */
	public function save_user_profile() {
		$post_payload = \FED\Helpers\InputHelper::post();
		$message      = __( 'Something Went Wrong', 'frontend-dashboard' );

		if (
			isset( $_REQUEST, $post_payload['tab_id'] ) &&
			isset( $_REQUEST['menu_type'] ) &&
			'user' === sanitize_text_field( wp_unslash( $_REQUEST['menu_type'] ) )
		) {
			if ( function_exists( 'fed_verify_nonce' ) ) {
				fed_verify_nonce();
			}

			$validation = function_exists( 'fed_validate_user_profile_form' )
				? fed_validate_user_profile_form( $post_payload )
				: true;

			if ( $validation instanceof WP_Error ) {
				$message = [
					'type'    => 'danger',
					'message' => implode( '<br>', $validation->get_error_messages() ),
				];
			} else {
				$user_data = $this->model->process_update_user_profile( $post_payload );

				if ( $user_data instanceof WP_Error ) {
					$message = [
						'type'    => 'danger',
						'message' => implode( '<br>', $user_data->get_error_messages() ),
					];
				} else {
					$saved = $this->model->save( $user_data );
					if ( is_wp_error( $saved ) ) {
						$message = [
							'type'    => 'danger',
							'message' => implode( '<br>', $saved->get_error_messages() ),
						];
					} else {
						$message = [
							'type'    => 'success',
							'message' => __( 'Successfully Updated', 'frontend-dashboard' ),
						];
						do_action( 'fed_user_profile_updated', get_current_user_id(), $post_payload, $user_data );
					}
				}
			}

			if ( function_exists( 'fed_set_alert' ) ) {
				fed_set_alert( 'fed_profile_save_message', $message );
			}
		}

		$redirect_url = ! empty( $post_payload['_wp_http_referer'] )
			? $post_payload['_wp_http_referer']
			: wp_get_referer();

		if ( ! $redirect_url ) {
			$redirect_url = home_url();
		}

		wp_safe_redirect(
			add_query_arg(
				[ 'fed_nonce' => wp_create_nonce( 'fed_nonce' ) ],
				$redirect_url
			)
		);
		exit;
	}
}
