<?php
function fed_logout_process( $first_element ) {
	$index        = 'logout';
	$payment_menu = fed_get_logout_menu();
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
				<form action="<?php echo wp_logout_url(); ?>" method="post" role="form">
					<button type="submit" class="btn btn-danger">
						<i class="fa <?php echo $payment_menu[ $index ]['menu_image_id'] ?>"></i>
						Please click here to logout
					</button>
				</form>
			</div>
		</div>
	</div>


	<?php
}