<?php
/**
 * Email.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FEDEmail
 */
if ( ! class_exists( 'FEDEmail' ) ) {
	/**
	 * Class FEDEmail
	 */
	class FEDEmail {

		/**
		 * Settings.
		 *
		 * @var array
		 */
		private $settings;

		/**
		 * FEDEmail constructor.
		 */
		public function __construct() {
			$this->settings = get_option( 'fed_settings_email' );
		}

		/**
		 * Show.
		 */
		public function show() {

			$tabs = apply_filters( 'fed_customize_admin_email_options', array(
				'settings' => array(
					'icon'      => 'fas fa-cogs',
					'name'      => __( 'Settings', 'frontend-dashboard' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'settings',
					),
					'arguments' => $this->settings,
				),
				'smtp'     => array(
					'icon'      => 'fas fa-wrench',
					'name'      => __( 'SMTP', 'frontend-dashboard' ),
					'callable'  => array(
						'object' => $this,
						'method' => 'smtp',
					),
					'arguments' => $this->settings,
				),
			) );
			?>
            <div class="row">
                <div class="col-md-3 padd_top_20">
                    <ul class="nav nav-pills nav-stacked"
                        id="fed_admin_setting_login_tabs"
                        role="tablist">
						<?php
						$menu_count = 0;
						foreach ( $tabs as $index => $tab ) {
							$active = $menu_count === 0 ? 'active' : '';
							$menu_count ++;
							?>
                            <li role="presentation"
                                class="<?php esc_attr_e( $active ); ?>">
                                <a href="#<?php echo $index; ?>"
                                   aria-controls="<?php echo $index; ?>"
                                   role="tab"
                                   data-toggle="tab">
                                    <i class="<?php echo $tab['icon']; ?>"></i>
									<?php echo $tab['name']; ?>
                                </a>
                            </li>
						<?php } ?>
                    </ul>
                </div>
                <div class="col-md-9">
                    <!-- Tab panes -->
                    <div class="tab-content">
						<?php
						$content_count = 0;
						foreach ( $tabs as $index => $tab ) {
							$active = $content_count === 0 ? 'active' : '';
							$content_count ++;
							?>
                            <div role="tabpanel"
                                 class="tab-pane <?php echo $active; ?>"
                                 id="<?php echo $index; ?>">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">
                                            <span class="<?php echo $tab['icon']; ?>"></span>
											<?php echo $tab['name']; ?>
                                        </h3>
                                    </div>
                                    <div class="panel-body">
										<?php
										fed_call_function_method( $tab )
										?>
                                    </div>
                                </div>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
			<?php
		}


		/**
		 * Save Admin Script Menu
		 */
		public function update() {
			$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
			fed_verify_nonce( $request );

			// Validation
			$validate = new FED_Validation();
			$validate->name( __( 'Send Email Via', 'frontend-dashboard' ) )->value( fed_get_data( 'via' ) )->required();
			$validate->name( __( 'Email Address',
				'frontend-dashboard' ) )->value( fed_get_data( 'credentials.email' ) )->pattern( 'email' );

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}

			fed_set_data( $this->settings, 'via', fed_get_data( 'via', $request ) );
			fed_set_data( $this->settings, 'credentials.email', fed_get_data( 'credentials.email', $request ) );
			fed_set_data( $this->settings, 'credentials.from_name', fed_get_data( 'credentials.from_name', $request ) );

			update_option( 'fed_settings_email', $this->settings );

			wp_send_json_success( array( 'message' => 'Email Settings Successfully Updated' ) );


		}

		/**
		 * Update SMTP.
		 */
		public function update_smtp() {
			$request = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
			fed_verify_nonce( $request );

			$validate = new FED_Validation();
			$validate->name( __( 'Host Name',
				'frontend-dashboard' ) )->value( fed_get_data( 'smtp.host_name' ) )->required();
			$validate->name( __( 'Password',
				'frontend-dashboard' ) )->value( fed_get_data( 'smtp.password' ) )->required();
			$validate->name( __( 'Username',
				'frontend-dashboard' ) )->value( fed_get_data( 'smtp.user_name' ) )->required();
			$validate->name( __( 'Port',
				'frontend-dashboard' ) )->value( fed_get_data( 'smtp.port' ) )->required();

			if ( ! $validate->is_success() ) {
				$errors = implode( '<br>', $validate->get_errors() );
				wp_send_json_error( array( 'message' => $errors ) );
			}

			fed_set_data( $this->settings, 'smtp.host_name', fed_get_data( 'smtp.host_name', $request ) );
			fed_set_data( $this->settings, 'smtp.password', fed_get_data( 'smtp.password', $request ) );
			fed_set_data( $this->settings, 'smtp.user_name', fed_get_data( 'smtp.user_name', $request ) );
			fed_set_data( $this->settings, 'smtp.encryption', fed_get_data( 'smtp.encryption', $request ) );
			fed_set_data( $this->settings, 'smtp.port', fed_get_data( 'smtp.port', $request ) );
			fed_set_data( $this->settings, 'smtp.auth', fed_get_data( 'smtp.auth', $request ) );

			update_option( 'fed_settings_email', $this->settings );

			wp_send_json_success( array( 'message' => 'SMTP Details Successfully Updated' ) );


		}

		/**
		 * Settings.
		 */
		public function settings() {
			?>
            <form method="post" class="fed_admin_menu fed_ajax"
                  action="<?php echo fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDEmail@update'; ?>">
				<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>

				<?php echo fed_loader(); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'Send Email Via', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_radio( array(
								'input_meta'  => 'via',
								'input_value' => array( 'WP_MAIL' => 'WP_MAIL', 'SMTP' => 'SMTP' ),
								'user_value'  => fed_get_data( 'via', $this->settings, 'WP_MAIL' ),
								'is_required' => 'true',
							) ); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'Email From Address', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_single_line( array(
								'input_meta'  => 'credentials[email]',
								'placeholder' => __( 'Email From Address', 'frontend-dashboard' ),
								'user_value'  => fed_get_data( 'credentials.email', $this->settings ),
							) ); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'Email From Name', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_single_line( array(
								'input_meta'  => 'credentials[from_name]',
								'placeholder' => __( 'Email From Name', 'frontend-dashboard' ),
								'user_value'  => fed_get_data( 'credentials.from_name', $this->settings ),
							) ); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 m-t-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-floppy-o"></i>
                            Save
                        </button>
                    </div>
                </div>
            </form>
			<?php
		}

		/**
		 * SMTP.
		 */
		public function smtp() {
			?>
            <form method="post" class="fed_admin_menu fed_ajax"
                  action="<?php echo fed_get_ajax_form_action( 'fed_ajax_request' ) . '&fed_action_hook=FEDEmail@update_smtp'; ?>">
				<?php fed_wp_nonce_field( 'fed_nonce', 'fed_nonce' ) ?>

				<?php echo fed_loader(); ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'Host Name', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_single_line( array(
								'input_meta'  => 'smtp[host_name]',
								'placeholder' => __( 'Host Name', 'frontend-dashboard' ),
								'user_value'  => fed_get_data( 'smtp.host_name', $this->settings ),
							) ); ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'User Name', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_single_line( array(
								'input_meta'  => 'smtp[user_name]',
								'placeholder' => __( 'User Name', 'frontend-dashboard' ),
								'user_value'  => fed_get_data( 'smtp.user_name', $this->settings ),
							) ); ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'Password', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_single_line( array(
								'input_meta'  => 'smtp[password]',
								'placeholder' => __( 'Password', 'frontend-dashboard' ),
								'user_value'  => fed_get_data( 'smtp.password', $this->settings ),
							) ); ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'Port', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_single_line( array(
								'input_meta'  => 'smtp[port]',
								'placeholder' => __( 'Port', 'frontend-dashboard' ),
								'user_value'  => fed_get_data( 'smtp.port', $this->settings ),
							) ); ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'SMTP Encryption Type', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_select( array(
								'input_meta'  => 'smtp[encryption]',
								'input_value' => array(
									'NONE'     => 'NONE',
									'SSL'      => 'SSL',
									'TLS'      => 'TLS',
									'STARTTLS' => 'STARTTLS',
								),
								'user_value'  => fed_get_data( 'smtp.encryption', $this->settings ),
							) ); ?>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label><?php _e( 'SMTP Authentication', 'frontend-dashboard' ) ?></label>
							<?php echo fed_form_select( array(
								'input_meta'  => 'smtp[auth]',
								'input_value' => fed_yes_no( 'ASC' ),
								'user_value'  => fed_get_data( 'smtp.auth', $this->settings ),
							) ); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 m-t-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-floppy-o"></i>
                            Save
                        </button>
                    </div>
                </div>
            </form>
			<?php
		}

		/**
		 * Sender Email.
		 *
		 * @param  string $email  Email.
		 *
		 * @return mixed|null
		 */
		public function sender_email( $email ) {
			return fed_get_data( 'credentials.email', $this->settings );
		}

		/**
		 * Sender Name.
		 *
		 * @param  string $name  Name.
		 *
		 * @return mixed|null
		 */
		public function sender_name( $name ) {
			return fed_get_data( 'credentials.from_name', $this->settings );
		}
	}
}
