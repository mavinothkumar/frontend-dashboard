<?php
/**
 * Logout.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Logout Process.
 */
function fed_logout_process() {
	$index  = 'logout';
	$logout = fed_get_logout_menu();
	?>
	<div class="panel panel-primary fed_dashboard_item <?php echo esc_attr( $index ); ?>">
		<div class="panel-heading">
			<h3 class="panel-title">
				<span class="fa <?php echo esc_attr( $logout[ $index ]['menu_image_id'] ); ?>"></span>
				<?php echo esc_attr( ucwords( $logout[ $index ]['menu'] ) ); ?>
			</h3>
		</div>
		<div class="panel-body fed_dashboard_panel_body">
			<div class="fed_panel_body_container">
				<form action="<?php echo esc_url( wp_logout_url() ); ?>" method="post" role="form">
					<button type="submit" class="btn btn-danger">
						<i class="fa <?php echo esc_attr( $logout[ $index ]['menu_image_id'] ); ?>"></i>
						<?php esc_attr_e( 'Please click here to logout', 'frontend-dashboard' ); ?>
					</button>
				</form>
			</div>
		</div>
	</div>
	<?php
}