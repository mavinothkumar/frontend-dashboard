<?php

namespace FED\Hooks\Action;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Taxonomy {
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
		add_action( 'wp_ajax_fed_get_taxonomy_by_post_type', array( $this, 'get_taxonomy_by_post_type' ) );
		add_action( 'wp_ajax_nopriv_fed_get_taxonomy_by_post_type', 'fed_block_the_action' );
	}

	/**
	 * Get Terms by Taxonomy
	 *
	 * @return array
	 */
	public function get_taxonomy_by_post_type() {

		$post_payload = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
		$get_payload  = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );

		fed_verify_nonce( $get_payload );

		if ( isset( $post_payload['post_type'] ) && ! empty( $post_payload['post_type'] ) ) {
			$taxonomies = get_object_taxonomies( fed_sanitize_text_field( $post_payload['post_type'] ), 'object' );
			$taxonomies = array( '' => 'Please Select' ) + wp_list_pluck( $taxonomies, 'label', 'name' );
			wp_send_json_success( array(
				'message' => $taxonomies,
			) );
			exit();
		}

		wp_send_json_success( array( 'message' => array() ) );
		exit();
	}
}
