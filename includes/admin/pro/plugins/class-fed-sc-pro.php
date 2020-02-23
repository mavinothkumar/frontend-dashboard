<?php
/**
 * Social Connect.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'FEDSCPRO' ) && ! defined( 'BC_FED_SC_PLUGIN' ) ) {
	/**
	 * Class FEDSCPRO
	 */
	class FEDSCPRO {

		/**
		 * FEDSCPRO constructor.
		 */
		public function __construct() {
			add_filter(
				'fed_admin_dashboard_settings_menu_header', array(
					$this,
					'dashboard_menu',
				)
			);
		}

		/**
		 * Dashboard Menu.
		 *
		 * @param  array $menu  Menu.
		 *
		 * @return mixed
		 */
		public function dashboard_menu( $menu ) {
			$menu['social_connect'] = array(
				'icon_class' => 'fa fa-globe',
				'name'       => __( 'Social', 'frontend-dashboard' ),
				'callable'   => array(
					'object'     => $this,
					'method'     => 'settings_menu',
					'parameters' => '',
				),
			);

			return $menu;
		}

		/**
		 * Settings Menu.
		 */
		public function settings_menu() {
			$tabs = $this->sub_menus();

			fed_common_layouts_admin_settings( array(), $tabs );

		}

		/**
		 * Sub Menus.
		 *
		 * @return array|mixed|void
		 */
		public function sub_menus() {
			$options = array(
				'fed_social_connect_settings' => array(
					'icon'      => 'fa fa-cogs',
					'name'      => __( 'Settings (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_connect_facebook' => array(
					'icon'      => 'fa fa-facebook',
					'name'      => __( 'Facebook (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_connect_twitter'  => array(
					'icon'      => 'fa fa-twitter',
					'name'      => __( 'Twitter (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_connect_linkedin' => array(
					'icon'      => 'fa fa-linkedin',
					'name'      => __( 'LinkedIn (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_connect_github'   => array(
					'icon'      => 'fa fa-github',
					'name'      => __( 'GitHub (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_button_style'     => array(
					'icon'      => 'fa fa-paint-brush',
					'name'      => __( 'Buttons (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_notifications'    => array(
					'icon'      => 'fa fa-warning',
					'name'      => __( 'Notifications (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
				'fed_social_instructions'     => array(
					'icon'      => 'fas fa-info-circle',
					'name'      => __( 'Instruction (Pro)', 'frontend-dashboard-social-connect' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'pro',
					),
					'arguments' => '',
				),
			);

			$options = apply_filters( 'fed_admin_social_connect_settings_menu', $options, '' );

			return $options;

		}

		/**
		 * Pro.
		 */
		public function pro() {
			?>
			<div class="row m-b-20">
				<div class="col-md-4">
					<form method="post"
							action="https://buffercode.com/payment/bc/payment_start">
						<input type='hidden' name='redirect_url' value="<?php echo fed_current_page_url(); ?>"/>
						<input type='hidden' name='domain' value="<?php echo fed_get_domain_name(); ?>"/>
						<input type='hidden' name='contact_email' value="<?php echo fed_get_admin_email(); ?>"/>
						<input type='hidden' name='plugin_name' value='frontend-dashboard-social-connect'/>
						<input type='hidden' name='amount' value='29'/>
						<input type='hidden' name='plan_type' value='annual'/>
						<button type="submit" style="
								background:url(
						<?php
						echo esc_url(
							plugins_url(
								'assets/admin/images/pro/buy-now-29.png',
								BC_FED_PLUGIN
							)
						);
						?>
								);
								background-repeat: no-repeat;
								width:200px;
								height: 148px;
								border: 0;">
						</button>
					</form>
				</div>
				<div class="col-md-4">
					<form method="post"
							action="https://buffercode.com/payment/bc/payment_start">
						<input type='hidden' name='redirect_url' value="<?php echo fed_current_page_url(); ?>"/>
						<input type='hidden' name='domain' value="<?php echo fed_get_domain_name(); ?>"/>
						<input type='hidden' name='contact_email' value="<?php echo fed_get_admin_email(); ?>"/>
						<input type='hidden' name='plugin_name' value='frontend-dashboard-social-connect'/>
						<input type='hidden' name='amount' value='99'/>
						<input type='hidden' name='plan_type' value='lifetime'/>
						<button type="submit" style="
								background:url(
						<?php
						echo esc_url(
							plugins_url(
								'assets/admin/images/pro/buy-now-99.png',
								BC_FED_PLUGIN
							)
						);
						?>
								);
								background-repeat: no-repeat;
								width:200px;
								height: 148px;
								border: 0;">
						</button>
					</form>
				</div>
			</div>


			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div class="panel panel-primary">
					<div class="panel-heading" role="tab" id="FB">
						<a class="fed_white_font" role="button" data-toggle="collapse" data-parent="#accordion"
								href="#Facebook"
								aria-expanded="true" aria-controls="Facebook">
							<h4 class="panel-title">
								<i class="fa fa-facebook"></i>
								<span class="p-l-20">
								How to setup Facebook
								</span>
							</h4>
						</a>
					</div>
					<div id="Facebook" class="panel-collapse collapse" role="tabpanel" aria-labelledby="FB">
						<div class="panel-body">
							<img width="200"
									src="https://buffercode.com/photos/1/posts/social-connect/frontend-dashboard-social-connect-facebook-360.jpg"
									class="img-responsive">
							<div class="p-t-20">
								<h4>
									<a href="https://buffercode.com/post/how-to-get-facebook-app-id-and-app-secret"
											target="_blank">
										<i class="fa fa-video-camera"></i>
										Video Link
									</a>
								</h4>
								<ol class="p-t-20">
									<li>Visit
										<a href="https://developers.facebook.com/" target="_blank">
											https://developers.facebook.com/
										</a>
										and Login using your credentials<br></li>
									<li>Click
										<strong>My Apps</strong>
										|
										<strong>Create App</strong>
										<br></li>
									<li>Enter
										<strong>Display Name</strong>
										and check Email Address is correct<br></li>
									<li>Click
										<strong>Create APP ID</strong>
										<br></li>
									<li>In Left Sidebar Click
										<strong>Settings</strong>
										|
										<strong>Basic</strong>
										<br></li>
									<li>Fill out the required mandatory fields like
										<strong> App Domain, Privacy Policy URL, Terms of Service URL, Category and App
											Icon
										</strong>
										<br></li>
									<li>Scroll down and click
										<strong>Save Changes</strong>
										<br></li>
									<li>In Left Sidebar Click
										<strong>Products</strong>
										<br></li>
									<li>Then in Facebook Login Click
										<strong>Setup</strong>
										<br></li>
									<li>Now go to your website
										<strong>WP Admin | Frontend Dashboard | Frontend Dashboard | Social | Settings
										</strong>
										<br></li>
									<li>Copy the
										<strong> Add This URL on Redirect URL</strong>
										on
										<strong>Redirect URL</strong>
										<br></li>
									<li> Now Go to Facebook Developer page Left Sidebar Click
										<strong>Products</strong>
										|
										<strong>Settings</strong>
										<br></li>
									<li>Paste the URL in
										<strong>Valid OAuth Redirect URIs</strong>
										<br></li>
									<li>Click
										<strong>Save Changes</strong>
										<br></li>
									<li>At the top of the Page Switch on the Status from
										<strong>OFF</strong>
										(Development) to
										<strong>ON</strong>
										(Live)<br></li>
									<li>Click
										<strong>Confirm</strong>
										on any popup request
									</li>
									<li>Then Copy the
										<strong>APP ID</strong>
										and
										<strong>APP Secret</strong>
										from
										<strong>Facebook</strong>
										and Paste it in
										<strong>your website WP Admin | Frontend Dashboard | Frontend Dashboard | Social
											|
											Facebook
										</strong>
									</li>
								</ol>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-primary">
					<div class="panel-heading" role="tab" id="Tw">
						<a class="fed_white_font" role="button" data-toggle="collapse" data-parent="#accordion"
								href="#Twitter"
								aria-expanded="true" aria-controls="Twitter">
							<h4 class="panel-title">
								<i class="fa fa-twitter"></i>
								<span class="p-l-20">
								How to setup Twitter
								</span>
							</h4>
						</a>
					</div>
					<div id="Twitter" class="panel-collapse collapse" role="tabpanel" aria-labelledby="Tw">
						<div class="panel-body">
							<img width="200"
									src="https://buffercode.com/photos/1/posts/social-connect/frontend-dashboard-social-connect-twitter-360.jpg"
									class="img-responsive">
							<div class="p-t-20">
								<h4>
									<a href="https://buffercode.com/post/how-to-get-twitter-app-id-and-app-secret"
											target="_blank">
										<i class="fa fa-video-camera"></i>
										Video Link
									</a>
								</h4>
								<ol class="p-t-20">
									<li>Visit
										<a href="https://developer.twitter.com" target="_blank">
											https://developer.twitter.com
										</a>
										and Login using your credentials
									</li>
									<li>Click on
										<strong>Your User Name</strong>
										|
										<strong>Apps</strong>
									</li>
									<li>Click
										<strong>Create an App</strong>
									</li>
									<li>Fill out the Mandatory fields like
										<strong>App name, Application Description, Website URL, Terms of Service,
											Privacy
											Policy, Organization Name, Organization Website URL
										</strong>
									</li>
									<li>Enable
										<strong>Sign in with Twitter</strong>
										.
									</li>
									<li>To get the Callback URLs, please visit
										<strong>your website | Frontend Dashboard | Frontend Dashboard | Social |
											Settings |
											Add This URL on Redirect URL
										</strong>
									</li>
									<li>Click
										<strong>Create</strong>
									</li>
									<li>Once the app created, you can find
										<strong>Permissions</strong>
										tab at the top, click it.
									</li>
									<li>Then Click
										<strong>Edit</strong>
									</li>
									<li>Select the
										<strong>Request email address from users</strong>
									</li>
									<li>Click
										<strong>Save</strong>
									</li>
									<li>Now click the
										<strong>Keys and tokens</strong>
										tab at the top
									</li>
									<li>Copy the Consumer API keys and Secret into
										<strong>your website | Frontend Dashboard | Frontend Dashboard | Social |
											Twitter
										</strong>
										and paste the keys in respective ID and Secret and click
										<strong>Submit</strong>
									</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading" role="tab" id="LI">
						<a class="fed_white_font" role="button" data-toggle="collapse" data-parent="#accordion"
								href="#LinkedIn"
								aria-expanded="true" aria-controls="LinkedIn">
							<h4 class="panel-title">
								<i class="fa fa-linkedin"></i>
								<span class="p-l-20">
									How to setup LinkedIn
								</span>
							</h4>
						</a>
					</div>
					<div id="LinkedIn" class="panel-collapse collapse" role="tabpanel" aria-labelledby="LI">
						<div class="panel-body">

							<img width="200"
									src="https://buffercode.com/photos/1/posts/social-connect/frontend-dashboard-social-connect-linkedin-360.jpg"
									class="img-responsive">
							<div class="p-t-20">
								<h4>
									<a href="https://buffercode.com/post/how-to-get-linkedin-app-id-and-app-secret"
											target="_blank">
										<i class="fa fa-video-camera"></i>
										Video Link
									</a>
								</h4>
								<ol class="p-t-20">
									<li>Visit
										<a href="https://linkedin.com/developers" target="_blank">
											https://linkedin.com/developers
										</a>
										and Login using your credentials
									</li>
									<li>Click
										<strong>Create app</strong>
										in centre the page
									</li>
									<li>Fill out the mandatory fields like
										<strong>App Name, Company, Privacy Policy URL, Business email and App logo
										</strong>
									</li>
									<li>Check the Legal terms and
										<strong>click Create App</strong>
									</li>
									<li>Click on the
										<strong>Verify</strong>
										Button
									</li>
									<li>In the Popup you   under Verification URL click
										<strong>Generate URL</strong>
										.
									</li>
									<li>Copy and Paste the
										<strong>URL</strong>
										in different window.
									</li>
									<li>It will ask to
										<strong>Approve Application</strong>
										.
									</li>
									<li>Click
										<strong>Approve Verification</strong>
										.
									</li>
									<li> Click
										<strong>Go to my App</strong>
									</li>
									<li>Click
										<strong>Auth</strong>
										, 2nd Tab at the top.
									</li>
									<li>Copy the
										<strong> Client ID</strong>
										and
										<strong>Client Secret</strong>
										and paste it in
										<strong> your website | WP Admin | Frontend Dashboard | Frontend Dashboard |
											Social
											| LinkedIn
										</strong>
										and
										<strong> Submit.</strong>
									</li>
									<li> Then in
										<strong>LinkedIn | Auth | OAuth 2.0</strong>
										Settings Click the
										<strong>Pencil</strong>
										Icon to edit, then click
										<strong>Add redirect URL</strong>
										.
									</li>
									<li>To Get the
										<strong> Redirect URL</strong>
										go to
										<strong>your website | WP Admin | Frontend Dashboard | Frontend Dashboard |
											Social |
											Settings
										</strong>
										and Copy the
										<strong>Add This URL on Redirect URL</strong>
										and
										<strong>paste it</strong>
										.
									</li>
									<li> Click
										<strong>Update</strong>
										.
									</li>
									<li> Now you can use the LinkedIn in your website as a Social Login</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-primary">
					<div class="panel-heading" role="tab" id="GH">
						<a class="fed_white_font" role="button" data-toggle="collapse" data-parent="#accordion"
								href="#GitHub"
								aria-expanded="true" aria-controls="GitHub">
							<h4 class="panel-title">
								<i class="fa fa-github"></i>
								<span class="p-l-20">
								How to setup GitHub
								</span>
							</h4>
						</a>
					</div>
					<div id="GitHub" class="panel-collapse collapse" role="tabpanel" aria-labelledby="GH">
						<div class="panel-body">
							<img width="200"
									src="https://buffercode.com/photos/1/posts/social-connect/frontend-dashboard-social-connect-github-360.jpg"
									class="img-responsive">
							<div class="p-t-20">
								<h4>
									<a href="https://buffercode.com/post/how-to-get-github-app-id-and-app-secret"
											target="_blank">
										<i class="fa fa-video-camera"></i>
										Video Link
									</a>
								</h4>
								<ol class="p-t-20">
									<li>Visit
										<a href="https://github.com" target="_blank">https://github.com</a>
										and Login using your credentials
									</li>
									<li>Click your
										<strong>profile picture</strong>
										at the Top Right Corner
									</li>
									<li>Click
										<strong>Settings</strong>
									</li>
									<li>Click
										<strong>Developer Settings</strong>
										at Left Sidebar
									</li>
									<li>Click
										<strong>OAuth Apps</strong>
										at Left Sidebar
									</li>
									<li>Click
										<strong>New OAuth App</strong>
										at Right Sidebar Top
									</li>
									<li>Fill out the mandatory fields like
										<strong>Application Name, Homepage URL, Application Description.</strong>
									</li>
									<li>You can get the Authorization Callback URL from
										<strong> your Website | WP Admin | Frontend Dashboard | Frontend Dashboard |
											Social
											| Settings | Add This URL on Redirect URL
										</strong>
									</li>
									<li>Paste the
										<strong>URL</strong>
										in
										<strong>Github Authorization Callback URL</strong>
										and click
										<strong>Register application</strong>
									</li>
									<li>Copy the
										<strong>Client ID</strong>
										and Paste it in
										<strong>App ID</strong>
										in
										<strong>your Website | WP Admin | Frontend Dashboard | Frontend Dashboard |
											GitHub
										</strong>
										. Similar to
										<strong>Client Secret</strong>
										to
										<strong>App Secret</strong>
										and Submit.
									</li>
								</ol>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<h4>
						For More Information, Please visit -
						<a href="https://buffercode.com/plugin/frontend-dashboard-social-connect">Frontend Dashboard
							Social Connect
						</a>
					</h4>
				</div>
			</div>

			<?php

		}
	}

	new FEDSCPRO();
}
