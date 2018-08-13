<?php
function fed_admin_input_fields_checkbox( $row, $action,$menu_options ) {
	?>
	<div class="row fed_input_type_container fed_input_checkbox_container hide">
		<form method="post"
			  class="fed_admin_menu fed_ajax"
			  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ?>">

			<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>

			<?php echo fed_loader(); ?>

			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
							<b><?php _e( 'Checkbox', 'frontend-dashboard' ) ?></b>
						</h3>
					</div>
					<div class="panel-body">
						<div class="fed_input_text">
							<?php fed_get_admin_up_label_input_order( $row ); ?>
							<div class="row">
								<?php fed_get_admin_up_input_meta( $row ) ?>

								<div class="form-group col-md-3">
									<?php fed_get_class_field( $row ) ?>
								</div>

								<div class="form-group col-md-3">
									<?php fed_get_id_field( $row ) ?>
								</div>
							</div>
							<?php
							fed_get_admin_up_display_permission( $row, $action );

							fed_get_admin_up_role_based( $row, $action,$menu_options );

							fed_get_input_type_and_submit_btn( 'checkbox', $action );
							?>


						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php
}