<?php
function fed_common_simple_layout( $form ) {
	$form_method       = isset( $form['form']['method'] ) && ! empty( $form['form']['method'] ) ? esc_attr(
		$form['form']['method'] ) :
		'post';
	$form_class        = isset( $form['form']['class'] ) && ! empty( $form['form']['class'] ) ? esc_attr(
		$form['form']['class'] )
		: '';
	$form_attr         = isset( $form['form']['attr'] ) && ! empty( $form['form']['attr'] ) ? esc_attr(
		$form['form']['attr'] ) : '';
	$form_loader       = isset( $form['form']['is_loader'] ) && ! empty( $form['form']['attr'] ) ? esc_attr(
		$form['form']['attr']
	) :
		fed_loader();
	$form_nonce_action = isset( $form['form']['nonce']['action'] ) && ! empty( $form['form']['nonce']['action'] ) ?
		esc_attr(
			$form['form']['nonce']['action'] ) : 'fed_nonce';
	$form_nonce_name   = isset( $form['form']['nonce']['name'] ) && ! empty( $form['form']['nonce']['name'] ) ? esc_attr(
		$form['form']['nonce']['name'] ) : 'fed_nonce';
	if ( isset( $form['form']['action'] ) && is_array( $form['form']['action'] ) ) {
		$url         = isset( $form['form']['action']['url'] ) && ! empty( $form['form']['action']['url'] ) ? esc_url( $form['form']['action']['url'] ) : admin_url(
			'admin-ajax.php' );
		$form_action = isset( $form['form']['action']['action'] ) && ! empty( $form['form']['action']['action'] ) ? $url . '?action=' . esc_attr( $form['form']['action']['action'] ) : $url;

	} else {
		$form_action = admin_url( 'admin-ajax.php?action=fed_admin_setting_form' );
	}
	?>
	<div class="p-20">
		<?php
		echo isset( $form['note']['header'] ) && ! empty( $form['note']['header'] ) ? $form['note']['header'] : '';
		?>
		<form method="<?php echo $form_method ?>" class="<?php echo $form_class; ?>" <?php echo $form_attr; ?> action="<?php echo $form_action ?>">

			<?php fed_wp_nonce_field( $form_nonce_action, $form_nonce_name ) ?>

			<?php echo $form_loader; ?>

			<?php if ( isset( $form['hidden'] ) && is_array( $form['hidden'] ) ) {
				foreach ( $form['hidden'] as $hindex => $hidden ) {
					echo fed_get_input_details( $hidden );
				}
			} ?>
			<div class="row">
				<?php if ( isset( $form['input'] ) && is_array( $form['input'] ) ) {
					foreach ( $form['input'] as $iindex => $input ) {
						$col   = isset( $input['col'] ) && ! empty( $input['col'] ) ? $input['col'] : 'col-m-12';
						$class = isset( $input['class'] ) && ! empty( $input['class'] ) ? $input['class'] : '';
						$name  = isset( $input['name'] ) && ! empty( $input['name'] ) ? $input['name'] : $iindex;
						?>
						<div class="<?php echo $col . $class ?> ">
							<div class="form-group">
								<?php if ( isset( $input['name'] ) && null !== $input['name'] ) { ?>
									<label>
										<?php echo isset( $input['required'] ) ? '<span class="bg-red-font">' . $name . '</span>' : $name; ?>
										<?php echo isset( $input['help_message'] ) ? $input['help_message'] : '' ?>
									</label>
								<?php } ?>

								<?php if ( isset( $input['header'] ) ) { ?>
									<div class="p-t-10 p-b-10">
										<label>
											<?php echo isset( $input['required'] ) ? '<span class="bg-red-font">' . $input['header'] . '</span>' : $input['header']; ?>
											<?php echo isset( $input['help_message'] ) ? $input['help_message'] : '' ?>
										</label>
									</div>
								<?php } ?>
								<?php if ( isset( $input['input'] ) ) {
									if ( is_array( $input['input'] ) ) {
										echo fed_get_input_details( $input['input'] );
									} else {
										echo $input['input'];
									}
								}
								if ( isset( $input['extra']['input'] ) && is_array( $input['extra']['input'] ) ) {
									$sub_col = isset( $input['sub_col'] ) ? $input['sub_col'] : 'col-md-6';
									foreach ( $input['extra']['input'] as $eindex => $extra ) {
										?>
										<div class="<?php echo $sub_col; ?> p-b-10">
											<?php
											echo fed_get_input_details( $extra );
											?>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
						<?php
					}
				} ?>
			</div>


			<?php do_action( 'fed_admin_login_settings_template', $form ) ?>

			<div class="row">
				<div class="col-md-12">
					<input type="submit" class="btn btn-primary" value="Submit"/>
				</div>
			</div>
		</form>
		<?php
		echo isset( $form['note']['footer'] ) && ! empty( $form['note']['footer'] ) ? $form['note']['footer'] : '';
		?>
	</div>
	<?php
}