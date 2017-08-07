<?php
/**
 * Unregistered user for widget
 */

$dashboard = fed_get_dashboard_url();

$dashboard = $dashboard == false ? get_dashboard_url() : $dashboard;

?>
<div class="bc_fed">
	<a href="<?php echo $dashboard; ?>">
		<button class="btn btn-primary"><?php _e( 'Visit Dashboard', 'fed' ) ?></button>
	</a>
</div>

