<?php
/**
 * Error.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 404 | Some misuse action caught.
 */
function fed_landed_wrongly() {
	?>
	<div class="bc_fed container">
		<div class="alert alert-danger">
			<button type="button"
					class="close"
					data-dismiss="alert"
					aria-hidden="true">&times;
			</button>
			<strong>
				<?php esc_attr_e( '404!', 'frontend-dashboard' ); ?>
			</strong>
			<?php
			esc_attr_e(
				'Hope you have landed wrongly, please proceed to plugin',
				'frontend-dashboard'
			)
			?>
			<a href="<?php menu_page_url( 'fed_settings_menu' ); ?>">
				<?php
				esc_attr_e(
					'home page',
					'frontend-dashboard'
				)
				?>
			</a>
			.
		</div>
	</div>
	<?php
}
