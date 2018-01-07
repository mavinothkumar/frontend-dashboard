<?php

/**
 * Dashboard Support.
 *
 * @param string $first_element First Element.
 */
function fed_display_dashboard_support( $first_element ) {
	$index                = 'support';
	$payment_menu         = fed_get_support_menu();
	$question_collections = new FEDSupport();
	$questions            = $question_collections->getUserQuestions();

	if ( $index == $first_element ) {
		$active = '';
	} else {
		$active = 'hide';
	}
	?>
	<div class="panel panel-primary fed_dashboard_item <?php echo $active . ' ' . $index ?>">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="fa <?php echo $payment_menu[ $index ]['menu_image_id'] ?>"></span>
				<?php echo ucwords( $payment_menu[ $index ]['menu'] ) ?>
			</h3>
		</div>
		<div class="panel-body fed_dashboard_panel_body">
			<div class="fed_panel_body_container">
				<?php if ( count( $questions ) <= 0 ) {
					?>
					<div class="alert alert-info">
						<button type="button"
								class="close"
								data-dismiss="alert"
								aria-hidden="true">&times;
						</button>
						<?php _e( 'Sorry! No chat history' ) ?>
					</div>
					<?php
				} else {
					?>
					<div class="fed_questions_container">
						<div class="row">
							<?php fed_questions_lists( $questions ); ?>
						</div>
					</div>
					<?php

				} ?>

			</div>
		</div>
	</div>
	<?php
}

/**
 * Questions Lists
 *
 * @param array $questions Questions
 */
function fed_questions_lists( $questions ) {
	$current_user_id = get_current_user_id();
	?>
	<div class="col-md-4">
		<div class="fed_question_container">
			<div class="row padd_bot_20">
				<div class="col-md-12">
					<input type="search"
						   name="search"
						   id="fed_support_search"
						   placeholder="Search.."
					/>
				</div>
			</div>
			<div class="row fed_question_wrapper">
				<div class="col-md-12">
					<div class="list-group">
						<?php foreach ( array_reverse( $questions ) as $index => $question ) {
							$un_read      = fed_support_unread( $question, $current_user_id );
							$display_name = fed_get_display_name_by_id( $question['from_user_id'] );
							?>
							<form method="post"
								  class="fed_admin_menu fed_get_qa_ajax <?php echo $display_name; ?>"
								  action="<?php echo admin_url( 'admin-ajax.php?action=fed_support_get_qa' ) ?>">

								<?php fed_wp_nonce_field( 'fed_support_get_qa', 'fed_support_get_qa' ) ?>

								<input type="hidden"
									   name="question_id"
									   value="<?php echo $question['id']; ?>"/>

								<div class="list-group-item fed_question_on_click">

									<div class="row">
										<div class="fed_support_badge">
											<?php echo $un_read; ?>
										</div>
										<div class="col-md-4">
											<?php echo fed_get_avatar( $question['from_user_id'], $display_name, 'img-circle', '', '', array( 'class' => 'img-circle' ) ) ?>
										</div>
										<div class="col-md-7">

											<div class="fed_support_nq_container">
												<div class="fed_support_display_name">
													<?php
													echo esc_attr( $display_name );
													?>
												</div>
												<div class="fed_support_display_date">
													<?php
													echo esc_attr( $question['created_at'] );
													?>
												</div>

												<div class="fed_support_question">
													<?php
													echo esc_attr( $question['question'] );
													?>
												</div>
											</div>
										</div>

									</div>
								</div>
							</form>
						<?php } ?>
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="col-md-8">
		<?php echo fed_loader(); ?>
		<div class="fed_qa_container"
			 id="fed_qa_container">
		</div>
	</div>
	<?php
}

/**
 * Support unread.
 *
 * @param array $question Question
 * @param string $current_user_id current User ID
 *
 * @return string
 */
function fed_support_unread( $question, $current_user_id ) {
	//var_dump($question,$current_user_id);
	if ( $question['to_read'] === 'no' && $question['to_user_id'] == $current_user_id ) {
		return '<span class="fed_badge"></span>';
	}

	if ( $question['from_read'] === 'no' && $question['from_user_id'] == $current_user_id ) {
		return '<span class="fed_badge"></span>';
	}

	return '';
}
