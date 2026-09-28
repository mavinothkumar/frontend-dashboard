<?php
/**
 * User Count Widget.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDUserCountWidget' ) ) {
	/**
	 * Class FEDUserCountWidget
	 */
	class FEDUserCountWidget {
		/**
		 * FEDUserCountWidget constructor.
		 */
		public function __construct() {
			add_action( 'wp_dashboard_setup', array( $this, 'users' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}

		/**
		 * Users.
		 */
		public function users() {
			wp_add_dashboard_widget(
				'fed_user_statistics_widget',
				__( 'Frontend Dashboard User Statistics', 'frontend-dashboard' ),
				array( $this, 'chart' )
			);
		}

		/**
		 * Chart.
		 */
		public function chart() {
			global $wpdb;
			$table     = $wpdb->users;
			$now       = gmdate( 'Y-m-d H:i:s', time() );
			$one_month = gmdate( 'Y-m-d H:i:s', strtotime( '-1 month' ) );

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$users = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT DATE_FORMAT(user_registered,'%%Y-%%m-%%d') as created, COUNT(*) as count FROM {$wpdb->users} WHERE user_registered BETWEEN %s AND %s GROUP BY DATE_FORMAT(user_registered,'%%Y-%%m-%%d')",
					$one_month,
					$now
				),
				ARRAY_A
			);

			$total_user_count = count_users();

			$users_count = sprintf(
				/* Translators: %s Total Users */
				__( 'Total Users Count - %s', 'frontend-dashboard' ),
				$total_user_count['total_users']
			);


			if ( $users && count( $users ) > 0 ) {
				$count   = wp_list_pluck( $users, 'count' );
				$created = wp_list_pluck( $users, 'created' );
				?>
				<div class="bc_fed container">
					<canvas id="fed_users_stat" width="1200" height="600"></canvas>
				</div>
				<script>
                    var ctx = document.getElementById('fed_users_stat').getContext('2d');
                    var payment_stat = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: <?php echo wp_json_encode( $created ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>,
                            datasets: [{
                                label: '<?php echo esc_js( $users_count ); ?>',
                                data: <?php echo wp_json_encode( $count ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>,
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
				esc_html_e( 'No Users Subscribed yet', 'frontend-dashboard' );
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

	new FEDUserCountWidget();
}
