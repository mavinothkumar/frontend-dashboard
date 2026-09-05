<?php

namespace FED\Hooks\Action;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Terms {
	/**
	 * Instance.
	 *
	 * @var object Instance.
	 */
	private static $instance;

	/**
	 * Get Instance.
	 *
	 * @return self
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'wp_ajax_fed_get_terms_by_taxonomy', array( $this, 'get_terms_by_taxonomy' ) );
		add_action( 'wp_ajax_nopriv_fed_get_terms_by_taxonomy', 'fed_block_the_action' );
	}

	public function get_terms_by_taxonomy() {
		$post_payload = isset( $_POST ) ? wp_unslash( $_POST ) : array();
		$get_payload  = isset( $_GET ) ? array_map( 'sanitize_text_field', wp_unslash( $_GET ) ) : array();

		fed_verify_nonce( $get_payload );

		if ( isset( $post_payload['taxonomy'] ) && ! empty( $post_payload['taxonomy'] ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => fed_sanitize_text_field( $post_payload['taxonomy'] ),
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'parent'     => '0',
				)
			);

			wp_send_json_success(
				array(
					'message' => array( '0' => 'Please Select' ) + wp_list_pluck( $terms, 'name', 'term_id' ),
				)
			);
		}

		wp_send_json_success( array( 'message' => array() ) );
	}
}
