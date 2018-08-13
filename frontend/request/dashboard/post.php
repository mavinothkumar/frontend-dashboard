<?php

add_action( 'wp_ajax_fed_dashboard_add_new_post', 'fed_dashboard_add_new_post_fn' );
//add_action( 'wp_ajax_fed_dashboard_add_new_post_request', 'fed_dashboard_add_new_post_request_fn' );

add_action( 'wp_ajax_fed_dashboard_show_post_list_request', 'fed_dashboard_show_post_list_request_fn' );

//add_action( 'wp_ajax_fed_dashboard_delete_post_by_id', 'fed_dashboard_delete_post_by_id_fn' );

add_action( 'wp_ajax_fed_dashboard_edit_post_by_id', 'fed_dashboard_edit_post_by_id_fn' );
add_action( 'wp_ajax_fed_dashboard_process_edit_post_request', 'fed_dashboard_process_edit_post_request_fn' );

add_action( 'wp_ajax_nopriv_fed_dashboard_add_new_post', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_dashboard_delete_post_by_id', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_dashboard_add_new_post_request', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_dashboard_show_post_list_request', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_dashboard_process_edit_post_request', 'fed_block_the_action' );


function fed_dashboard_add_new_post_request_fn() {
	$request = $_REQUEST;
	fed_nonce_check( $request );
	$post_type = isset( $request['fed_post_type'] ) ? $request['fed_post_type'] : 'post';

	fed_display_dashboard_add_new_post( $post_type );
	exit();

}

function fed_dashboard_add_new_post_fn() {
	$request = $_REQUEST;
	fed_nonce_check( $request );

	fed_process_dashboard_add_new_post( $request );
	exit();
}

function fed_dashboard_delete_post_by_id_fn() {
	$post = $_REQUEST;

	if ( ! wp_verify_nonce( $post['fed_dashboard_delete_post_by_id'], 'fed_dashboard_delete_post_by_id' ) ) {
		wp_send_json_error( array( 'message' => array( 'Invalid Request, Please reload the page and try again' ) ) );

	}

	$status = wp_delete_post( $post['post_id'] );
	if ( ! $status ) {
		wp_send_json_error( array( 'message' => 'Something went wrong, please refresh and try again later' ) );

	}

	wp_send_json_success( array( 'message' => 'Successfully Deleted' ) );
}

function fed_dashboard_show_post_list_request_fn() {
	$post = $_REQUEST;

	if ( ! wp_verify_nonce( $post['fed_dashboard_show_post_list_request'], 'fed_dashboard_show_post_list_request' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request, Please reload the page and try again' ) );
		exit();
	}
	$post_type = isset( $post['fed_post_type'] ) ? $post['fed_post_type'] : '';
	echo fed_display_dashboard_view_post_list( $post_type );
	exit();
}

function fed_dashboard_edit_post_by_id_fn() {
	$request = $_REQUEST;
	if ( ! wp_verify_nonce( $request['fed_dashboard_edit_post_by_id'], 'fed_dashboard_edit_post_by_id' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request, Please reload the page and try again' ) );
	}

	/**
	 * Check whether post ID is belongs to that user
	 */
	if ( isset( $request['post_id'] ) && (int) $request['post_id'] !== 0 ) {
		$user = get_userdata( get_current_user_id() );
		$post = get_post( (int) $request['post_id'] );
		if ( $post !== null && $post->post_author == $user->ID ) {
			echo fed_display_dashboard_edit_post_by_id( $post );
			exit();
		}
		wp_send_json_error( array( 'message' => 'Invalid Post ID' ) );
	}

	wp_send_json_error( array( 'message' => 'Hey Dude!, Are you trying to modify the content...' ) );
}

function fed_dashboard_process_edit_post_request_fn() {
	$post = $_REQUEST;

	if ( ! wp_verify_nonce( $post['fed_dashboard_process_edit_post_request'],
		'fed_dashboard_process_edit_post_request' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request, Please reload the page and try again' ) );
	}

	$success = fed_process_dashboard_add_new_post( $post );

	if ( $success instanceof WP_Error ) {
		wp_send_json_error( $success->get_error_messages() );
	}

	echo $success;
	exit();
}