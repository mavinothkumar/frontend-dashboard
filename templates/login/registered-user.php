<?php
/**
 * Unregistered user for widget
 *
 * @package Frontend Dashboard.
 */

$dashboard = fed_get_dashboard_url();

$dashboard = ( false === $dashboard ) ? get_dashboard_url() : $dashboard;

$dashboard_title = apply_filters( 'fed_frontend_dashboard_title_btn', __( 'Visit Dashboard', 'frontend-dashboard' ) );
?>
<div class="bc_fed">
	<a href="<?php echo esc_url( $dashboard ); ?>">
		<button class="btn btn-primary"><?php echo esc_attr( $dashboard_title ); ?></button>
	</a>
</div>

