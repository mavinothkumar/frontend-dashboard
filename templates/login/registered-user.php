<?php
/**
 * Unregistered user for widget
 */

$dashboard = fed_get_dashboard_url();

$dashboard = $dashboard == false ? get_dashboard_url() : $dashboard;

$dashboard_title = apply_filters( 'fed_frontend_dashboard_title_btn', 'Visit Dashboard' );

?>
<div class="bc_fed">
	<a href="<?php echo $dashboard; ?>">
		<button class="btn btn-primary"><?php _e( $dashboard_title, 'frontend-dashboard' ) ?></button>
	</a>
</div>

