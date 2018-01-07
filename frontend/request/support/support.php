<?php

add_action( 'wp_ajax_fed_user_contact', 'fed_user_contact_fn' );
add_action( 'wp_ajax_fed_add_answer', 'fed_add_answer_fn' );
add_action( 'wp_ajax_fed_support_get_qa', 'fed_fed_support_get_qa_fn' );

add_action( 'wp_ajax_nopriv_fed_support_get_qa', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_add_answer', 'fed_block_the_action' );
add_action( 'wp_ajax_nopriv_fed_user_contact', 'fed_block_the_action' );

function fed_user_contact_fn() {
	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	if ( ! wp_verify_nonce( $request['fed_user_contact_nonce'], 'fed_user_contact_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		exit();
	}
	$support  = new FEDSupport();
	$response = $support->addNewQuestion( $request );

	if ( $response ) {
		wp_send_json_success( array( 'message' => __( 'Your question has been successfully sent', 'frontend-dashboard' ) ) );
	}
	wp_send_json_error( array( 'message' => __( 'Sorry Something went wrong!, please try again later', 'frontend-dashboard' ) ) );
}

function fed_fed_support_get_qa_fn() {
	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	if ( ! wp_verify_nonce( $request['fed_support_get_qa'], 'fed_support_get_qa' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		exit();
	}

	$html      = '';
	$support   = new FEDSupport();
	$responses = $support->getUserAnswerById( $request['question_id'] );
	$support->markUnread( $request['question_id'], 'yes' );
	$html .= fed_show_add_answers( $request['question_id'], $responses );

	wp_send_json_success( array( 'message' => $html ) );
}

function fed_add_answer_fn() {
	$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );

	if ( ! wp_verify_nonce( $request['fed_add_answer'], 'fed_add_answer' ) ) {
		wp_send_json_error( array( 'message' => 'Invalid Request' ) );
		exit();
	}

	$support  = new FEDSupport();
	$response = $support->addAnswer( $request );
	$html     = fed_show_add_answers( $response['question_id'], $response['response'] );

	wp_send_json_success( array( 'message' => $html ) );
}

function fed_show_add_answers( $question_id, $responses ) {
	$getQuestion = fed_fetch_table_row_by_id( BC_FED_QUESTION_DB, $question_id );
	$html        = '';
	$html        .= '<div class="fed_qa_wrapper">
        <div class="fed_qa_textarea_container">
	        <form method="post" class="fed_add_new_answer" action="' . admin_url( 'admin-ajax.php?action=fed_add_answer' ) . '">
									' . fed_wp_nonce_field( 'fed_add_answer', 'fed_add_answer', true, false ) . '
            <div class="fed_qa_textarea">
            	<input type="hidden" name="question_id" value="' . $question_id . '" />
                <textarea name = "answer" rows = "3"></textarea>
            </div>
            <div class="fed_qa_submit_btn">
                <button class="btn btn-primary"
                        type = "submit">
                    <i class="fa fa-envelope"></i>
                    Send Message
                </button>
            </div>
            </form>
        </div>
        <div class="padd_5">
        <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				' . $getQuestion['content'] . '
			</div>
			</div>
        <div class="fed_qa_chat_area_container">';

	if ( count( $responses ) > 0 ) {
		$responses = array_reverse( $responses );
		foreach ( $responses as $response ) {
			$float = '';
			if ( $response['user_id'] == get_current_user_id() ) {
				$float = 'text-right';
			}
			$html .= '<div class="row fed_chat_only">
						<div class="col-md-12 padd_top_10 ' . $float . '">';
			if ( $float !== 'text-right' ) {
				$html .= '<div class="fed_float_left_chat"><div class="col-md-2 fed_user_image text-center">';
				$html .= fed_get_avatar( $response['user_id'], '', 'img-circle', '', array(
						50,
						50
					), array( 'class' => 'img-circle' ) ) . ' ';
				$html .= '</div><div class="col-md-10 fed_messages"><div class="row"><div class="col-md-12 fed_support_display_name">' . fed_get_user_name_by_id( $response['user_id'] ) . ' <span class="fed_support_display_date">
' . $response['updated_at'] . '</s></div><div 
class="col-md-12 fed_support_question">
' . $response['answer'] . '</div></div></div></div>';
			}
			if ( $float === 'text-right' ) {
				$html .= '<div class="fed_float_right_chat"><div class="col-md-10 fed_messages"><div class="row"><div class="col-md-12 fed_support_display_name">' . fed_get_user_name_by_id( $response['user_id'] ) . '<span class="fed_support_display_date">' . $response['updated_at'] . '</span></div><div class="col-md-12 fed_support_question">
' . $response['answer'] . '</div></div></div>';
				$html .= '<div class="col-md-2 fed_user_image text-center">';
				$html .= fed_get_avatar( $response['user_id'], '', 'img-circle', '', array(
						50,
						50
					), array( 'class' => 'img-circle' ) ) . ' ';
				$html .= '</div></div>';


			}
			$html .= ' </div ></div > ';
		}
	}
	$html .= '</div ></div > ';

	return $html;
}
