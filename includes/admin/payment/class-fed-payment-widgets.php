<?php
/**
 * Payment Widget.
 *
 * @package Frontend Dashboard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDPaymentWidgets' ) ) {
	/**
	 * Class FEDPaymentWidgets
	 */
	class FEDPaymentWidgets {
		/**
		 * FEDPaymentWidgets constructor.
		 */
		public function __construct() {
			add_action( 'wp_dashboard_setup', array( $this, 'statistics' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Statistics.
		 */
		public function statistics() {
			wp_add_dashboard_widget(
				'fed_payment_statistics_widget',
				'Frontend Dashboard Payment Statistics',
				array( $this, 'chart' )
			);
		}

		/**
		 * Chart.
		 */
		public function chart() {
			global $wpdb;
			$created = array();
			$amount  = array();
			$table   = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$payments = $wpdb->get_results( "SELECT DATE_FORMAT(created,'%Y-%m-%d') as created, currency, SUM(amount) as amount FROM {$table} GROUP BY DATE_FORMAT(created,'%Y-%m-%d')", ARRAY_A );
			$currency = 'USD';

			if ( $payments && count( $payments ) > 0 ) {
				foreach ( $payments as $index => $payment ) {
					$currency          = $payment['currency'];
					$created[ $index ] = $payment['created'];
					$amount[ $index ]  = $payment['amount'];
				}
				?>
				<div class="bc_fed container">
					<canvas id="fed_payment_stat" width="1200" height="600"></canvas>
				</div>
				<script>
					var ctx = document.getElementById('fed_payment_stat').getContext('2d');
					var payment_stat = new Chart(ctx, {
						type: 'bar',
						data: {
							labels: <?php echo wp_json_encode( $created ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>,
							datasets: [{
								label: 'Total (<?php echo esc_js( $currency ); ?>)',
								data: <?php echo wp_json_encode( $amount ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>,
								backgroundColor: 'rgba(10, 170, 170,1)'
							}]
						},
						options: {
							scales: {
								yAxes: [{
									ticks: {
										beginAtZero: true
									}
									}]
							}
						}
					});
				</script>
				<?php
			} else {
				esc_html_e( 'No payment received yet', 'frontend-dashboard' );
			}
		}

		/**
		 * Enqueue.
		 */
		public function enqueue() {
			if ( fed_get_current_screen_id() === 'dashboard' ) {
				wp_enqueue_script(
					'fed_payment_chart',
					BC_FED_PLUGIN_URL . 'assets/admin/js/chart.min.js',
					array(),
					BC_FED_PLUGIN_VERSION,
					true
				);
				wp_enqueue_style(
					'fed_payment_chart',
					BC_FED_PLUGIN_URL . 'assets/admin/css/chart.min.css',
					array(),
					BC_FED_PLUGIN_VERSION,
					'all'
				);
			}
		}
	}

	new FEDPaymentWidgets();
}
