<?php
/**
 * Help.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'fed_get_help_menu' ) ) {
	/**
	 * Help Menu
	 */
	function fed_get_help_menu() {
		$faq_domain_url = 'https://faq.frontenddashboard.com';
		$faq_domain_display = 'faq.frontenddashboard.com';

		$shortcodes = array(
			array(
				'code'        => '[fed_login]',
				'title'       => __( 'All-in-One Auth Suite', 'frontend-dashboard' ),
				'description' => __( 'Renders unified Login, Registration, and Password Reset tabs in a single responsive widget.', 'frontend-dashboard' ),
				'badge'       => __( 'Recommended', 'frontend-dashboard' ),
			),
			array(
				'code'        => '[fed_dashboard]',
				'title'       => __( 'User Frontend Dashboard', 'frontend-dashboard' ),
				'description' => __( 'Renders the complete user portal with navigation menu, profile editor, posts, and billing.', 'frontend-dashboard' ),
				'badge'       => __( 'Core Portal', 'frontend-dashboard' ),
			),
			array(
				'code'        => '[fed_login_only]',
				'title'       => __( 'Dedicated Login Form', 'frontend-dashboard' ),
				'description' => __( 'Renders an isolated login form suitable for custom authentication landing pages.', 'frontend-dashboard' ),
				'badge'       => __( 'Standalone', 'frontend-dashboard' ),
			),
			array(
				'code'        => '[fed_register_only]',
				'title'       => __( 'Dedicated Registration Form', 'frontend-dashboard' ),
				'description' => __( 'Renders standalone customer onboarding and user registration form with custom fields.', 'frontend-dashboard' ),
				'badge'       => __( 'Standalone', 'frontend-dashboard' ),
			),
			array(
				'code'        => '[fed_forgot_password_only]',
				'title'       => __( 'Password Recovery Form', 'frontend-dashboard' ),
				'description' => __( 'Renders standalone password reset request and email verification form.', 'frontend-dashboard' ),
				'badge'       => __( 'Standalone', 'frontend-dashboard' ),
			),
			array(
				'code'        => '[fed_user role="subscriber"]',
				'title'       => __( 'Role-Specific Member Directory', 'frontend-dashboard' ),
				'description' => __( 'Displays user directory list filtered by user roles (e.g. subscriber, author, customer).', 'frontend-dashboard' ),
				'badge'       => __( 'Directory', 'frontend-dashboard' ),
			),
		);

		$quick_faqs = array(
			array(
				'q' => __( 'How do I set up the Frontend Dashboard on my WordPress site?', 'frontend-dashboard' ),
				'a' => __( '1. Create a new WordPress Page (e.g. "Dashboard").<br>2. Paste the shortcode <code>[fed_dashboard]</code> into the page content.<br>3. Set the Page Template to "FED Login" or standard full-width in the Page Attributes.<br>4. Navigate to <strong>Frontend Dashboard &rarr; Settings</strong> and assign your Dashboard page.', 'frontend-dashboard' ),
			),
			array(
				'q' => __( 'How can I customize user profile fields and add custom inputs?', 'frontend-dashboard' ),
				'a' => __( 'Go to <strong>Frontend Dashboard &rarr; User Profile</strong>. From here you can create text fields, dropdowns, date pickers, file uploads, and role selectors, and configure whether they appear during registration or inside the member dashboard.', 'frontend-dashboard' ),
			),
			array(
				'q' => __( 'How do role permissions and menu visibility work?', 'frontend-dashboard' ),
				'a' => __( 'In <strong>Frontend Dashboard &rarr; Dashboard Menu</strong>, each menu item allows you to configure <em>Role Visibility</em>. You can restrict specific tabs (e.g. Posts, Invoices, Chat) to specific user roles or make them globally available.', 'frontend-dashboard' ),
			),
			array(
				'q' => __( 'How do I configure payments, gateways, and subscription packages?', 'frontend-dashboard' ),
				'a' => __( 'Visit <strong>Frontend Dashboard &rarr; Payments</strong>. You have 5 dedicated sections: <em>Dashboard</em> (financial metrics), <em>Transactions</em> (full ledger & manual entries), <em>Subscriptions</em> (subscribers ledger and subscription plans builder), <em>Payment Gateways</em> (Gateways Hub), and <em>Invoices</em> (template customizer & company profile).', 'frontend-dashboard' ),
			),
			array(
				'q' => __( 'Where can I find developer hooks, filters, and API documentation?', 'frontend-dashboard' ),
				'a' => __( 'Visit our official documentation hub at <a href="' . esc_url( $faq_domain_url ) . '" target="_blank" style="color: #0284c7; font-weight: 600;">' . esc_html( $faq_domain_display ) . '</a>. You will find complete reference guides for <code>fed_payment_gateways</code>, <code>fed_registered_payment_gateways</code>, <code>fed_add_main_sub_menu</code>, custom form submission hooks, and REST APIs.', 'frontend-dashboard' ),
			),
		);
		?>
		<div class="bc_fed fed_help_app_wrapper" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; width: 100%; max-width: 100%; margin: 15px 0 40px 0; padding: 0 20px 0 10px; box-sizing: border-box;">
			
			<!-- Hero Header Banner -->
			<div style="background: linear-gradient(135deg, #033333 0%, #064e4e 50%, #0a6565 100%); border-radius: 14px; padding: 26px 30px; color: #ffffff; margin-bottom: 24px; box-shadow: 0 4px 14px rgba(3, 51, 51, 0.18);">
				<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
					<div>
						<div style="display: flex; align-items: center; gap: 10px;">
							<span style="background: rgba(255, 255, 255, 0.15); width: 36px; height: 36px; border-radius: 9px; display: inline-flex; align-items: center; justify-content: center; font-size: 17px;">
								<i class="fas fa-life-ring"></i>
							</span>
							<h2 style="font-size: 22px; font-weight: 800; margin: 0; color: #ffffff; letter-spacing: -0.01em;">
								<?php esc_html_e( 'Help & Documentation Center', 'frontend-dashboard' ); ?>
							</h2>
						</div>
						<p style="margin: 6px 0 0 46px; font-size: 13.5px; color: #cbd5e1; max-width: 680px; line-height: 1.45;">
							<?php esc_html_e( 'Guides, shortcodes reference, common troubleshooting, and our comprehensive Knowledge Base.', 'frontend-dashboard' ); ?>
						</p>
					</div>

					<div>
						<a href="<?php echo esc_url( $faq_domain_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 8px; background: #10b981; border: 1px solid #059669; padding: 10px 18px; border-radius: 8px; color: #ffffff; font-size: 13.5px; font-weight: 700; text-decoration: none; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.35); transition: all 0.2s ease;">
							<i class="fas fa-external-link-alt"></i> <?php echo esc_html( sprintf( __( 'Visit %s', 'frontend-dashboard' ), $faq_domain_display ) ); ?>
						</a>
					</div>
				</div>
			</div>

			<!-- FAQ Domain Feature Hero Card -->
			<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 26px; margin-bottom: 26px; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 20px;">
				<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; border-bottom: 1px solid #f1f5f9; padding-bottom: 18px;">
					<div style="display: flex; align-items: center; gap: 14px;">
						<div style="width: 50px; height: 50px; border-radius: 12px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
							<i class="fas fa-book-open"></i>
						</div>
						<div>
							<div style="display: flex; align-items: center; gap: 8px;">
								<h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #0f172a;">
									<?php esc_html_e( 'Comprehensive Knowledge Base & FAQ Portal', 'frontend-dashboard' ); ?>
								</h3>
								<span style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; text-transform: uppercase;">
									<?php esc_html_e( 'Official Docs', 'frontend-dashboard' ); ?>
								</span>
							</div>
							<p style="margin: 4px 0 0 0; font-size: 13px; color: #64748b;">
								<?php echo esc_html( sprintf( __( 'We maintain complete, up-to-date documentation and detailed step-by-step FAQs at %s.', 'frontend-dashboard' ), $faq_domain_display ) ); ?>
							</p>
						</div>
					</div>

					<div>
						<a href="<?php echo esc_url( $faq_domain_url ); ?>" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 8px; background: #033333; color: #ffffff; border: 1px solid #033333; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none; box-shadow: 0 2px 6px rgba(3,51,51,0.2);">
							<i class="fas fa-compass"></i> <?php esc_html_e( 'Browse Knowledge Base', 'frontend-dashboard' ); ?>
						</a>
					</div>
				</div>

				<!-- 4 Domain Pillar Highlights -->
				<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
					
					<a href="<?php echo esc_url( $faq_domain_url . '/getting-started' ); ?>" target="_blank" rel="noopener noreferrer" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; text-decoration: none; display: flex; flex-direction: column; gap: 6px; transition: transform 0.15s ease, border-color 0.15s ease;">
						<div style="display: flex; align-items: center; gap: 8px; color: #0284c7; font-weight: 700; font-size: 13.5px;">
							<i class="fas fa-rocket"></i> <?php esc_html_e( 'Getting Started', 'frontend-dashboard' ); ?>
						</div>
						<div style="font-size: 12.5px; color: #64748b; line-height: 1.4;">
							<?php esc_html_e( 'Page setup, shortcodes installation, user login flows, and dashboard navigation.', 'frontend-dashboard' ); ?>
						</div>
					</a>

					<a href="<?php echo esc_url( $faq_domain_url . '/user-profiles' ); ?>" target="_blank" rel="noopener noreferrer" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; text-decoration: none; display: flex; flex-direction: column; gap: 6px; transition: transform 0.15s ease, border-color 0.15s ease;">
						<div style="display: flex; align-items: center; gap: 8px; color: #16a34a; font-weight: 700; font-size: 13.5px;">
							<i class="fas fa-user-shield"></i> <?php esc_html_e( 'Profiles & Roles', 'frontend-dashboard' ); ?>
						</div>
						<div style="font-size: 12.5px; color: #64748b; line-height: 1.4;">
							<?php esc_html_e( 'Custom profile fields, role visibility, permission levels, and layout customization.', 'frontend-dashboard' ); ?>
						</div>
					</a>

					<a href="<?php echo esc_url( $faq_domain_url . '/payments-billing' ); ?>" target="_blank" rel="noopener noreferrer" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; text-decoration: none; display: flex; flex-direction: column; gap: 6px; transition: transform 0.15s ease, border-color 0.15s ease;">
						<div style="display: flex; align-items: center; gap: 8px; color: #7c3aed; font-weight: 700; font-size: 13.5px;">
							<i class="fas fa-credit-card"></i> <?php esc_html_e( 'Payments & Invoicing', 'frontend-dashboard' ); ?>
						</div>
						<div style="font-size: 12.5px; color: #64748b; line-height: 1.4;">
							<?php esc_html_e( 'Gateway setup, recurring membership tiers, invoice branding, and transaction tracking.', 'frontend-dashboard' ); ?>
						</div>
					</a>

					<a href="<?php echo esc_url( $faq_domain_url . '/developer-api' ); ?>" target="_blank" rel="noopener noreferrer" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; text-decoration: none; display: flex; flex-direction: column; gap: 6px; transition: transform 0.15s ease, border-color 0.15s ease;">
						<div style="display: flex; align-items: center; gap: 8px; color: #0f766e; font-weight: 700; font-size: 13.5px;">
							<i class="fas fa-code"></i> <?php esc_html_e( 'Developer Hooks & API', 'frontend-dashboard' ); ?>
						</div>
						<div style="font-size: 12.5px; color: #64748b; line-height: 1.4;">
							<?php esc_html_e( 'Custom filter hooks, action triggers, REST APIs, and add-on architecture guides.', 'frontend-dashboard' ); ?>
						</div>
					</a>

				</div>
			</div>

			<!-- 2-Column Grid: Shortcodes Reference (Left) & FAQs Accordion (Right) -->
			<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 26px;">
				
				<!-- Left Column: Core Shortcodes Reference -->
				<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
					<div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
						<h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
							<i class="fas fa-terminal" style="color: #033333;"></i>
							<span><?php esc_html_e( 'Core Shortcodes Reference', 'frontend-dashboard' ); ?></span>
						</h3>
						<span style="font-size: 12px; color: #64748b; font-weight: 600;">
							<?php echo sprintf( esc_html__( '%d Shortcodes', 'frontend-dashboard' ), count( $shortcodes ) ); ?>
						</span>
					</div>

					<div style="padding: 18px; display: flex; flex-direction: column; gap: 12px; flex: 1;">
						<?php foreach ( $shortcodes as $item ) : ?>
							<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px;">
								<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
									<div style="display: flex; align-items: center; gap: 8px;">
										<code style="font-size: 13px; font-weight: 700; color: #033333; background: #ffffff; border: 1px solid #cbd5e1; padding: 3px 8px; border-radius: 6px; font-family: monospace;">
											<?php echo esc_html( $item['code'] ); ?>
										</code>
										<button type="button" class="fed_copy_shortcode_btn" data-code="<?php echo esc_attr( $item['code'] ); ?>" title="<?php esc_attr_e( 'Copy to Clipboard', 'frontend-dashboard' ); ?>" style="background: transparent; border: none; color: #64748b; cursor: pointer; padding: 2px 4px; font-size: 12px;">
											<i class="fas fa-copy"></i>
										</button>
									</div>
									<span style="background: #e2e8f0; color: #334155; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">
										<?php echo esc_html( $item['badge'] ); ?>
									</span>
								</div>
								<div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 2px;">
									<?php echo esc_html( $item['title'] ); ?>
								</div>
								<div style="font-size: 12px; color: #64748b; line-height: 1.4;">
									<?php echo esc_html( $item['description'] ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<!-- Right Column: Interactive Quick FAQs -->
				<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
					<div style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center;">
						<h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
							<i class="fas fa-question-circle" style="color: #033333;"></i>
							<span><?php esc_html_e( 'Frequently Asked Questions', 'frontend-dashboard' ); ?></span>
						</h3>
						<a href="<?php echo esc_url( $faq_domain_url ); ?>" target="_blank" rel="noopener noreferrer" style="font-size: 12px; color: #0284c7; font-weight: 600; text-decoration: none;">
							<?php esc_html_e( 'View All FAQs &rarr;', 'frontend-dashboard' ); ?>
						</a>
					</div>

					<div style="padding: 18px; display: flex; flex-direction: column; gap: 10px; flex: 1;">
						<?php foreach ( $quick_faqs as $i => $faq ) : ?>
							<div class="fed_faq_item" style="border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #ffffff;">
								<button type="button" class="fed_faq_toggle_btn" style="width: 100%; text-align: left; background: #ffffff; border: none; padding: 14px 16px; font-size: 13px; font-weight: 700; color: #0f172a; display: flex; justify-content: space-between; align-items: center; cursor: pointer; gap: 10px;">
									<span><?php echo esc_html( $faq['q'] ); ?></span>
									<i class="fas fa-chevron-down" style="font-size: 11px; color: #94a3b8; transition: transform 0.2s ease;"></i>
								</button>
								<div class="fed_faq_content" style="display: <?php echo ( 0 === $i ) ? 'block' : 'none'; ?>; padding: 0 16px 14px 16px; font-size: 12.5px; color: #475569; line-height: 1.5; border-top: 1px solid #f1f5f9;">
									<div style="padding-top: 10px;">
										<?php echo wp_kses_post( $faq['a'] ); ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

			</div>

			<!-- Bottom Contact & Community Channels -->
			<div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 26px; box-shadow: 0 2px 6px rgba(0,0,0,0.02);">
				<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
					<div>
						<h4 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a;">
							<?php esc_html_e( 'Need specialized assistance or custom implementation?', 'frontend-dashboard' ); ?>
						</h4>
						<p style="margin: 3px 0 0 0; font-size: 12.5px; color: #64748b;">
							<?php esc_html_e( 'Our development and support team is ready to assist you with configurations and inquiries.', 'frontend-dashboard' ); ?>
						</p>
					</div>

					<div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
						<a href="mailto:support@buffercode.com" style="display: inline-flex; align-items: center; gap: 7px; background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; text-decoration: none;">
							<i class="fas fa-envelope" style="color: #64748b;"></i> <?php esc_html_e( 'Email Support', 'frontend-dashboard' ); ?>
						</a>
						<a href="https://buffercode.com/plugin/frontend-dashboard" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 7px; background: #ffffff; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; color: #334155; text-decoration: none;">
							<i class="fas fa-comments" style="color: #0284c7;"></i> <?php esc_html_e( 'Live Chat', 'frontend-dashboard' ); ?>
						</a>
						<a href="https://wordpress.org/support/plugin/frontend-dashboard/reviews/?filter=5#new-post" target="_blank" rel="noopener noreferrer" style="display: inline-flex; align-items: center; gap: 7px; background: #033333; color: #ffffff; border: 1px solid #033333; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; text-decoration: none;">
							<i class="fas fa-star" style="color: #f59e0b;"></i> <?php esc_html_e( 'Rate Plugin', 'frontend-dashboard' ); ?>
						</a>
					</div>
				</div>
			</div>

			<!-- Interactive Script for FAQ Accordion & Copy Buttons -->
			<script type="text/javascript">
			jQuery(document).ready(function($){
				// FAQ Accordion Toggle
				$('.fed_faq_toggle_btn').on('click', function(){
					var content = $(this).siblings('.fed_faq_content');
					var icon = $(this).find('i.fa-chevron-down');
					
					$('.fed_faq_content').not(content).slideUp(200);
					$('.fed_faq_toggle_btn').find('i.fa-chevron-down').not(icon).css('transform', 'rotate(0deg)');

					if (content.is(':visible')) {
						content.slideUp(200);
						icon.css('transform', 'rotate(0deg)');
					} else {
						content.slideDown(200);
						icon.css('transform', 'rotate(180deg)');
					}
				});

				// Copy Shortcode
				$('.fed_copy_shortcode_btn').on('click', function(e){
					e.preventDefault();
					var code = $(this).data('code');
					var btn = $(this);
					
					if (navigator.clipboard) {
						navigator.clipboard.writeText(code).then(function(){
							btn.html('<i class="fas fa-check" style="color: #16a34a;"></i>');
							setTimeout(function(){
								btn.html('<i class="fas fa-copy"></i>');
							}, 1500);
						});
					}
				});
			});
			</script>

		</div>
		<?php
	}
}
