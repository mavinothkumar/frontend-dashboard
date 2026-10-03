<?php
/**
 * Admin Executive Overview Dashboard Template - Full Width Enterprise Edition.
 *
 * @package Frontend Dashboard
 * @var array $metrics
 * @var array $checklist
 * @var array $health
 * @var array $recent_logs
 * @var array $roles_data
 * @var array $recent_users
 * @var array $engine_data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap fed-admin-wrap" style="width: calc(100% - 20px); max-width: 100%; margin: 20px 20px 40px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; box-sizing: border-box;">
	
	<?php if ( function_exists( 'fed_render_addon_compatibility_banner' ) ) : ?>
		<?php echo fed_render_addon_compatibility_banner(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php endif; ?>

	<!-- Header Section -->
	<div style="background: linear-gradient(135deg, #033333 0%, #0aaaaa 100%); border-radius: 12px; padding: 28px 36px; color: #ffffff; margin-bottom: 24px; box-shadow: 0 4px 20px rgba(10, 170, 170, 0.15); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
		<div>
			<h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 800; line-height: 1.2;">Frontend Dashboard Control Center</h1>
			<p style="color: rgba(255, 255, 255, 0.9); margin: 6px 0 0 0; font-size: 14px;">Real-time metrics, system health, shortcodes, and frontend user portal management.</p>
		</div>
		<div style="display: flex; gap: 12px; flex-wrap: wrap;">
			<?php if ( $checklist['dashboard_page']['configured'] ) : ?>
				<a href="<?php echo esc_url( $checklist['dashboard_page']['url'] ); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #ffffff; color: #033333; font-weight: 600; font-size: 13px; padding: 10px 18px; border-radius: 8px; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
					<i class="fas fa-external-link-alt"></i> <?php esc_html_e( 'View Frontend Portal', 'frontend-dashboard' ); ?>
				</a>
			<?php endif; ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_settings' ) ); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; font-weight: 600; font-size: 13px; padding: 10px 18px; border-radius: 8px; text-decoration: none;">
				<i class="fas fa-cog"></i> <?php esc_html_e( 'Global Settings', 'frontend-dashboard' ); ?>
			</a>
		</div>
	</div>

	<!-- 4 Key Top Metric Cards -->
	<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
		
		<!-- Users Card -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; display: flex; align-items: center; gap: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
			<div style="width: 52px; height: 52px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
				<i class="fas fa-users"></i>
			</div>
			<div>
				<div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Total Users', 'frontend-dashboard' ); ?></div>
				<div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 2px;"><?php echo esc_html( number_format( $metrics['total_users'] ) ); ?></div>
			</div>
		</div>

		<!-- Active Menus Card -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; display: flex; align-items: center; gap: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
			<div style="width: 52px; height: 52px; border-radius: 12px; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
				<i class="fas fa-bars"></i>
			</div>
			<div>
				<div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Dashboard Menus', 'frontend-dashboard' ); ?></div>
				<div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 2px;"><?php echo esc_html( $metrics['active_menus'] ); ?></div>
			</div>
		</div>

		<!-- Custom Fields Card -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; display: flex; align-items: center; gap: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
			<div style="width: 52px; height: 52px; border-radius: 12px; background: #fef3c7; color: #d97706; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
				<i class="fas fa-th-list"></i>
			</div>
			<div>
				<div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Custom Fields', 'frontend-dashboard' ); ?></div>
				<div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 2px;">
					<?php echo esc_html( $metrics['total_fields'] ); ?>
					<span style="font-size: 12px; font-weight: 500; color: #94a3b8;"><?php echo esc_html( sprintf( '(%d Profile / %d Post)', (int) $metrics['profile_fields_count'], (int) $metrics['post_fields_count'] ) ); ?></span>
				</div>
			</div>
		</div>

		<!-- Revenue Card -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; display: flex; align-items: center; gap: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
			<div style="width: 52px; height: 52px; border-radius: 12px; background: #ede9fe; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
				<i class="fas fa-credit-card"></i>
			</div>
			<div>
				<div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php esc_html_e( 'Payment Volume', 'frontend-dashboard' ); ?></div>
				<div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 2px;">
					$<?php echo esc_html( $metrics['total_revenue'] ); ?>
					<span style="font-size: 12px; font-weight: 500; color: #94a3b8;"><?php echo esc_html( sprintf( '(%d txns)', (int) $metrics['total_payments'] ) ); ?></span>
				</div>
			</div>
		</div>

	</div>

	<!-- Row 1: 3-Column Grid -->
	<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 24px;">
		
		<!-- Widget 1: Quick Setup Checklist -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
			<div>
				<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
					<i class="fas fa-tasks" style="color: #0aaaaa;"></i> <?php esc_html_e( 'Quick Setup Checklist', 'frontend-dashboard' ); ?>
				</h3>
				
				<div style="display: flex; flex-direction: column; gap: 12px;">
					
					<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
						<div style="display: flex; align-items: center; gap: 10px;">
							<?php if ( $checklist['dashboard_page']['configured'] ) : ?>
								<i class="fas fa-check-circle" style="color: #10b981; font-size: 17px;"></i>
							<?php else : ?>
								<i class="fas fa-exclamation-circle" style="color: #f59e0b; font-size: 17px;"></i>
							<?php endif; ?>
							<div>
								<div style="font-weight: 600; font-size: 13px; color: #334155;"><?php esc_html_e( 'Frontend Portal Page', 'frontend-dashboard' ); ?></div>
								<div style="font-size: 11px; color: #64748b;">[fed_dashboard]</div>
							</div>
						</div>
						<a href="<?php echo esc_url( $checklist['dashboard_page']['url'] ); ?>" style="font-size: 12px; font-weight: 600; color: #0284c7; text-decoration: none;">
							<?php echo $checklist['dashboard_page']['configured'] ? esc_html__( 'Edit', 'frontend-dashboard' ) : esc_html__( 'Create', 'frontend-dashboard' ); ?> &rarr;
						</a>
					</div>

					<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
						<div style="display: flex; align-items: center; gap: 10px;">
							<?php if ( $checklist['login_page']['configured'] ) : ?>
								<i class="fas fa-check-circle" style="color: #10b981; font-size: 17px;"></i>
							<?php else : ?>
								<i class="fas fa-info-circle" style="color: #64748b; font-size: 17px;"></i>
							<?php endif; ?>
							<div>
								<div style="font-weight: 600; font-size: 13px; color: #334155;"><?php esc_html_e( 'Custom Login Page', 'frontend-dashboard' ); ?></div>
								<div style="font-size: 11px; color: #64748b;">[fed_login]</div>
							</div>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_settings#login' ) ); ?>" style="font-size: 12px; font-weight: 600; color: #0284c7; text-decoration: none;">
							<?php esc_html_e( 'Configure', 'frontend-dashboard' ); ?> &rarr;
						</a>
					</div>

					<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
						<div style="display: flex; align-items: center; gap: 10px;">
							<?php if ( $checklist['wp_restrict']['enabled'] ) : ?>
								<i class="fas fa-shield-alt" style="color: #10b981; font-size: 17px;"></i>
							<?php else : ?>
								<i class="fas fa-unlock" style="color: #f59e0b; font-size: 17px;"></i>
							<?php endif; ?>
							<div>
								<div style="font-weight: 600; font-size: 13px; color: #334155;"><?php esc_html_e( 'WP-Admin Lockdown', 'frontend-dashboard' ); ?></div>
								<div style="font-size: 11px; color: #64748b;">
									<?php
									/* translators: %d: number of roles */
									$wp_restrict_label = sprintf( esc_html__( 'Active for %d roles', 'frontend-dashboard' ), count( $checklist['wp_restrict']['roles'] ) );
									echo $checklist['wp_restrict']['enabled'] ? esc_html( $wp_restrict_label ) : esc_html__( 'Disabled', 'frontend-dashboard' );
									?>
								</div>
							</div>
						</div>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_settings#login' ) ); ?>" style="font-size: 12px; font-weight: 600; color: #0284c7; text-decoration: none;">
							<?php esc_html_e( 'Manage', 'frontend-dashboard' ); ?> &rarr;
						</a>
					</div>

				</div>
			</div>
		</div>

		<!-- Widget 2: Live System Health & Diagnostics -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
			<div>
				<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
					<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
						<i class="fas fa-stethoscope" style="color: #0aaaaa;"></i> <?php esc_html_e( 'System Health Status', 'frontend-dashboard' ); ?>
					</h3>
					<span style="background: <?php echo ( $health['overall_status'] ?? 'pass' ) === 'pass' ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo ( $health['overall_status'] ?? 'pass' ) === 'pass' ? '#166534' : '#991b1b'; ?>; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
						<?php echo esc_html( strtoupper( $health['overall_status'] ?? 'PASS' ) ); ?>
					</span>
				</div>

				<div style="display: flex; flex-direction: column; gap: 10px;">
					<?php
					$checks = array_slice( $health['checks'] ?? array(), 0, 5 );
					foreach ( $checks as $chk ) :
						$is_good = ( $chk['status'] ?? 'pass' ) === 'pass';
						?>
						<div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
							<div style="display: flex; align-items: center; gap: 8px; color: #334155;">
								<span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: <?php echo $is_good ? '#10b981' : '#f59e0b'; ?>;"></span>
								<span><?php echo esc_html( $chk['name'] ?? '' ); ?></span>
							</div>
							<div style="font-weight: 600; color: #64748b; font-size: 12px;"><?php echo esc_html( $chk['value'] ?? '' ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div style="margin-top: 14px; text-align: right;">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_tools' ) ); ?>" style="font-size: 12px; font-weight: 600; color: #0aaaaa; text-decoration: none;">
					<?php esc_html_e( 'View Full System Report', 'frontend-dashboard' ); ?> &rarr;
				</a>
			</div>
		</div>

		<!-- Widget 3: Quick Shortcodes & Reference -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
			<div>
				<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
					<i class="fas fa-code" style="color: #0aaaaa;"></i> <?php esc_html_e( 'Shortcode Reference', 'frontend-dashboard' ); ?>
				</h3>
				<div style="display: flex; flex-direction: column; gap: 10px;">
					
					<div style="padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
						<div>
							<code style="font-weight: 700; color: #033333; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 12px;">[fed_dashboard]</code>
							<div style="font-size: 11px; color: #64748b; margin-top: 2px;">Main User Frontend Portal</div>
						</div>
						<i class="fas fa-desktop" style="color: #94a3b8;"></i>
					</div>

					<div style="padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
						<div>
							<code style="font-weight: 700; color: #033333; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 12px;">[fed_login]</code>
							<div style="font-size: 11px; color: #64748b; margin-top: 2px;">Login & Register Form</div>
						</div>
						<i class="fas fa-sign-in-alt" style="color: #94a3b8;"></i>
					</div>

					<div style="padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
						<div>
							<code style="font-weight: 700; color: #033333; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 12px;">[fed_post_form]</code>
							<div style="font-size: 11px; color: #64748b; margin-top: 2px;">Frontend Post Submissions</div>
						</div>
						<i class="fas fa-edit" style="color: #94a3b8;"></i>
					</div>

				</div>
			</div>
			<div style="margin-top: 14px; text-align: right;">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_help' ) ); ?>" style="font-size: 12px; font-weight: 600; color: #0aaaaa; text-decoration: none;">
					<?php esc_html_e( 'View Shortcode Guide', 'frontend-dashboard' ); ?> &rarr;
				</a>
			</div>
		</div>

	</div>

	<!-- Row 2: 3-Column Grid -->
	<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 24px;">
		
		<!-- Widget 4: User Roles & Community Distribution -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
			<div>
				<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
					<i class="fas fa-user-shield" style="color: #0aaaaa;"></i> <?php esc_html_e( 'User Roles Distribution', 'frontend-dashboard' ); ?>
				</h3>
				<div style="display: flex; flex-direction: column; gap: 10px;">
					<?php foreach ( $roles_data as $role ) : ?>
						<div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px; padding: 6px 0; border-bottom: 1px solid #f8fafc;">
							<div style="display: flex; align-items: center; gap: 8px; color: #334155;">
								<i class="fas fa-shield-alt" style="color: #cbd5e1; font-size: 13px;"></i>
								<span style="font-weight: 600;"><?php echo esc_html( $role['name'] ); ?></span>
							</div>
							<span style="background: #f1f5f9; color: #475569; font-weight: 700; font-size: 12px; padding: 2px 10px; border-radius: 12px;">
								<?php echo esc_html( $role['count'] ); ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div style="margin-top: 14px; text-align: right;">
				<a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>" style="font-size: 12px; font-weight: 600; color: #0aaaaa; text-decoration: none;">
					<?php esc_html_e( 'Manage All WordPress Users', 'frontend-dashboard' ); ?> &rarr;
				</a>
			</div>
		</div>

		<!-- Widget 5: Recent Registered Members -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
			<div>
				<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
					<i class="fas fa-user-plus" style="color: #0aaaaa;"></i> <?php esc_html_e( 'Recent Members', 'frontend-dashboard' ); ?>
				</h3>
				<div style="display: flex; flex-direction: column; gap: 10px;">
					<?php foreach ( $recent_users as $ru ) : ?>
						<div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 6px 0; border-bottom: 1px solid #f8fafc;">
							<div style="display: flex; align-items: center; gap: 10px;">
								<img src="<?php echo esc_url( $ru['avatar'] ); ?>" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid #e2e8f0;" alt="" />
								<div>
									<div style="font-weight: 600; font-size: 13px; color: #1e293b;"><?php echo esc_html( $ru['name'] ); ?></div>
									<div style="font-size: 11px; color: #64748b;"><?php echo esc_html( $ru['email'] ); ?></div>
								</div>
							</div>
							<span style="font-size: 11px; font-weight: 600; color: #0284c7; background: #e0f2fe; padding: 2px 8px; border-radius: 10px; text-transform: capitalize;">
								<?php echo esc_html( $ru['role'] ); ?>
							</span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<div style="margin-top: 14px; text-align: right;">
				<a href="<?php echo esc_url( admin_url( 'user-new.php' ) ); ?>" style="font-size: 12px; font-weight: 600; color: #0aaaaa; text-decoration: none;">
					<?php esc_html_e( '+ Add New User', 'frontend-dashboard' ); ?> &rarr;
				</a>
			</div>
		</div>

		<!-- Widget 6: Background Services & Security Logs -->
		<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); display: flex; flex-direction: column; justify-content: space-between;">
			<div>
				<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
					<i class="fas fa-history" style="color: #0aaaaa;"></i> <?php esc_html_e( 'Audit Stream & Engine', 'frontend-dashboard' ); ?>
				</h3>
				
				<div style="margin-bottom: 12px; padding: 10px 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 12px;">
					<div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
						<span style="color: #64748b;">Cron Engine:</span>
						<span style="font-weight: 600; color: #10b981;"><?php echo esc_html( $engine_data['cron_status'] ); ?></span>
					</div>
					<div style="display: flex; justify-content: space-between;">
						<span style="color: #64748b;">REST API Layer:</span>
						<span style="font-weight: 600; color: #0284c7;">/wp-json/fed/v1</span>
					</div>
				</div>

				<?php if ( empty( $recent_logs ) ) : ?>
					<div style="text-align: center; padding: 14px; color: #94a3b8; font-size: 12px;">
						<i class="fas fa-clipboard-check" style="font-size: 24px; margin-bottom: 4px; display: block; color: #cbd5e1;"></i>
						<?php esc_html_e( 'System is running smoothly. Zero security alerts.', 'frontend-dashboard' ); ?>
					</div>
				<?php else : ?>
					<div style="display: flex; flex-direction: column; gap: 6px;">
						<?php
						foreach ( $recent_logs as $log ) :
							$action      = is_array( $log ) ? ( $log['action'] ?? $log['action_title'] ?? $log['message'] ?? '' ) : ( $log->action ?? $log->action_title ?? $log->message ?? '' );
							$channel     = is_array( $log ) ? ( $log['channel'] ?? '' ) : ( $log->channel ?? '' );
							$created_at  = is_array( $log ) ? ( $log['created_at'] ?? 'now' ) : ( $log->created_at ?? 'now' );
							$description = is_array( $log ) ? ( $log['description'] ?? '' ) : ( $log->description ?? '' );

							if ( empty( $action ) ) {
								$action = ! empty( $channel ) ? ucfirst( $channel ) . ' ' . __( 'Event', 'frontend-dashboard' ) : __( 'System Event', 'frontend-dashboard' );
							}

							$trimmed_desc = trim( (string) $description );
							$show_desc    = ! empty( $trimmed_desc ) && $trimmed_desc !== $action && 0 !== strpos( $trimmed_desc, '{' );
							?>
							<div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px; font-size: 12px; padding: 4px 0; border-bottom: 1px solid #f8fafc;">
								<div>
									<span style="font-weight: 600; color: #1e293b;"><?php echo esc_html( $action ); ?></span>
									<?php if ( $show_desc ) : ?>
										<span style="color: #64748b;"> - <?php echo esc_html( $trimmed_desc ); ?></span>
									<?php endif; ?>
								</div>
								<span style="color: #94a3b8; font-size: 11px; white-space: nowrap;">
									<?php echo esc_html( human_time_diff( strtotime( $created_at ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'frontend-dashboard' ) ); ?>
								</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

	</div>

	<!-- Row 3: Quick Navigation Shortcuts Bar -->
	<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
		<h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
			<i class="fas fa-compass" style="color: #0aaaaa;"></i> <?php esc_html_e( 'Quick Module Shortcuts', 'frontend-dashboard' ); ?>
		</h3>
		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px;">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_dashboard_menu' ) ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; text-decoration: none; font-weight: 600; font-size: 13px;">
				<i class="fas fa-bars" style="color: #0aaaaa; font-size: 16px;"></i> <?php esc_html_e( 'Dashboard Menus', 'frontend-dashboard' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_user_profile' ) ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; text-decoration: none; font-weight: 600; font-size: 13px;">
				<i class="fas fa-user-edit" style="color: #0aaaaa; font-size: 16px;"></i> <?php esc_html_e( 'User Profile Fields', 'frontend-dashboard' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_post_fields' ) ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; text-decoration: none; font-weight: 600; font-size: 13px;">
				<i class="fas fa-edit" style="color: #0aaaaa; font-size: 16px;"></i> <?php esc_html_e( 'Post & CPT Fields', 'frontend-dashboard' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_payments' ) ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; text-decoration: none; font-weight: 600; font-size: 13px;">
				<i class="fas fa-credit-card" style="color: #0aaaaa; font-size: 16px;"></i> <?php esc_html_e( 'Payments & Billing', 'frontend-dashboard' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_settings' ) ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; text-decoration: none; font-weight: 600; font-size: 13px;">
				<i class="fas fa-sliders-h" style="color: #0aaaaa; font-size: 16px;"></i> <?php esc_html_e( 'Settings Tabs', 'frontend-dashboard' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=fed_tools' ) ); ?>" style="display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 8px; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; text-decoration: none; font-weight: 600; font-size: 13px;">
				<i class="fas fa-wrench" style="color: #0aaaaa; font-size: 16px;"></i> <?php esc_html_e( 'Tools & Maintenance', 'frontend-dashboard' ); ?>
			</a>
		</div>
	</div>

</div>
