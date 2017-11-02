<?php
function fed_admin_user_profile_settings_tab( $fed_admin_options ) {
	$fed_upef = array_merge( fed_fetch_user_profile_extra_fields_key_value(), array( '' => 'Let it be default' ) );
	?>
	<form method="post"
		  class="fed_admin_menu fed_ajax"
		  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">
		<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

		<?php echo fed_loader(); ?>

		<input type="hidden"
			   name="fed_admin_unique"
			   value="fed_admin_setting_upl"/>


		<div class="fed_admin_panel_container">
			<div class="fed_admin_panel_content_wrapper">
				<div class="row">
					<div class="col-md-4 fed_menu_title">
						<?php _e( 'Change Profile Picture', 'fed' ) ?>
						<span
								class="fa fa-info-circle"
								data-toggle="popover"
								data-trigger="hover"
								data-original-title="Note"
								data-content="Image size should be min 600x600 px">
						</span>

					</div>

					<div class="col-md-4">
						<?php echo fed_input_box( 'fed_upl_change_profile_pic', array(
							'name'    => 'settings[fed_upl_change_profile_pic]',
							'options' => $fed_upef,
							'value'   => isset( $fed_admin_options['settings']['fed_upl_change_profile_pic'] ) ? $fed_admin_options['settings']['fed_upl_change_profile_pic'] : '',
						), 'select' ) ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 fed_menu_title"><?php _e( 'Disable Description', 'fed' ) ?></div>
					<div class="col-md-4">
						<?php echo fed_input_box( 'fed_upl_disable_desc', array(
							'name'    => 'settings[fed_upl_disable_desc]',
							'options' => fed_yes_no( 'ASC' ),
							'value'   => isset( $fed_admin_options['settings']['fed_upl_disable_desc'] ) ? $fed_admin_options['settings']['fed_upl_disable_desc'] : '',
						), 'select' ) ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4 fed_menu_title"><?php _e( 'Number of Recent Post to show', 'fed' ) ?></div>
					<div class="col-md-4">
						<?php echo fed_input_box( 'fed_upl_no_recent_post', array(
							'name'        => 'settings[fed_upl_no_recent_post]',
							'placeholder' => __( 'Number of Recent Post to show on User Profile' ),
							'value'       => isset( $fed_admin_options['settings']['fed_upl_no_recent_post'] ) ? $fed_admin_options['settings']['fed_upl_no_recent_post'] : '5',
						), 'number' ) ?>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<input type="submit" class="btn btn-primary" value="Submit"/>
			</div>
		</div>
	</form>

	<?php
}

function fed_admin_user_profile_colors_tab() {
	if ( defined( 'BC_FED_EXTRA_PLUGIN_VERSION' ) ) {
		$fed_admin_options = get_option( 'fed_admin_setting_upl_color' );
		?>
		<form method="post"
			  class="fed_admin_menu fed_ajax"
			  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form' ) ?>">
			<?php wp_nonce_field( 'fed_admin_setting_nonce', 'fed_admin_setting_nonce' ) ?>

			<?php echo fed_loader(); ?>

			<input type="hidden"
				   name="fed_admin_unique"
				   value="fed_admin_setting_upl_color"/>


			<div class="fed_admin_panel_container">
				<div class="fed_admin_panel_content_wrapper">
					<div class="row">
						<div class="col-md-4 fed_menu_title">
							<?php _e( 'Primary Background Color', 'fed' ) ?>
							<span
									class="fa fa-info-circle"
									data-toggle="popover"
									data-trigger="hover"
									data-original-title="Note"
									data-content="Default Primary Color #00B5AD">
						</span>

						</div>

						<div class="col-md-4">
							<?php echo fed_input_box( 'fed_upl_color_bg_color', array(
								'name'  => 'color[fed_upl_color_bg_color]',
								'value' => isset( $fed_admin_options['color']['fed_upl_color_bg_color'] ) ? $fed_admin_options['color']['fed_upl_color_bg_color'] : '#00B5AD',
							), 'color' ) ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 fed_menu_title">
							<?php _e( 'Primary Background Font Color', 'fed' ) ?></div>
						<div class="col-md-4">
							<?php echo fed_input_box( 'fed_upl_color_bg_font_color', array(
								'name'  => 'color[fed_upl_color_bg_font_color]',
								'value' => isset( $fed_admin_options['color']['fed_upl_color_bg_font_color'] ) ? $fed_admin_options['color']['fed_upl_color_bg_font_color'] : '#ffffff',
							), 'color' ) ?>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 fed_menu_title">
							<?php _e( 'Secondary Background Color', 'fed' ) ?>
							<span
									class="fa fa-info-circle"
									data-toggle="popover"
									data-trigger="hover"
									data-original-title="Note"
									data-content="Default Secondary Color #3b4e57">
						</span>

						</div>

						<div class="col-md-4">
							<?php echo fed_input_box( 'fed_upl_color_sbg_color', array(
								'name'  => 'color[fed_upl_color_sbg_color]',
								'value' => isset( $fed_admin_options['color']['fed_upl_color_sbg_color'] ) ? $fed_admin_options['color']['fed_upl_color_sbg_color'] : '#3b4e57',
							), 'color' ) ?>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 fed_menu_title">
							<?php _e( 'Secondary Background Font Color', 'fed' ) ?></div>
						<div class="col-md-4">
							<?php echo fed_input_box( 'fed_upl_color_sbg_font_color', array(
								'name'  => 'color[fed_upl_color_sbg_font_color]',
								'value' => isset( $fed_admin_options['color']['fed_upl_color_sbg_font_color'] ) ? $fed_admin_options['color']['fed_upl_color_sbg_font_color'] : '#ffffff',
							), 'color' ) ?>
						</div>
					</div>

				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<input type="submit" class="btn btn-primary" value="Submit"/>
				</div>
			</div>
		</form>
		<?php
	} else {
		?>
		<div class="alert alert-info">
			<strong>Please install Frontend Dashboard Extra Plugin to activate this section</strong>
			Download
			<a href="https://buffercode.com/plugin/frontend-dashboard-extra">Frontend Dashboard Extra</a>
		</div>
		<?php
	}
}


add_action( 'fed_add_inline_css_at_head', 'fed_add_inline_css_at_head_color' );

function fed_add_inline_css_at_head_color() {
	$fed_colors = get_option( 'fed_admin_setting_upl_color' );
	if ( $fed_colors !== false ) {
		$pbg_color      = $fed_colors['color']['fed_upl_color_bg_color'];
		$pbg_font_color = $fed_colors['color']['fed_upl_color_bg_font_color'];
		$sbg_color      = $fed_colors['color']['fed_upl_color_sbg_color'];
		$sbg_font_color = $fed_colors['color']['fed_upl_color_sbg_font_color'];
		?>
		<style>
			.bc_fed .fed_header_font_color {
				color: <?php echo $pbg_color; ?> !important;
				font-weight: bolder;
			}

			.bc_fed .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover,
			.bc_fed .btn-primary,
			.bc_fed .bg-primary,
			.bc_fed .nav-pills > li.active > a,
			.bc_fed .nav-pills > li.active > a:focus,
			.bc_fed .nav-pills > li.active > a:hover,
			.bc_fed .list-group-item.active,
			.bc_fed .list-group-item.active:focus,
			.bc_fed .list-group-item.active:hover,
			.bc_fed .panel-primary > .panel-heading,
			.bc_fed .btn-primary.focus, .btn-primary:focus,
			.bc_fed .btn-primary:hover,
			.bc_fed .btn.active, .btn:active,
			.bc_fed input[type="button"]:hover,
			.bc_fed input[type="button"]:focus,
			.bc_fed input[type="submit"]:hover,
			.bc_fed input[type="submit"]:focus,
			.bc_fed .popover-title {
				background-color: <?php echo $pbg_color  ?> !important;
				background-image: none !important;
				border-color: <?php echo $pbg_color  ?> !important;
				color: <?php echo $pbg_font_color ?> !important;
			}

			.bc_fed .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover,
			.bc_fed .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
				background-color: <?php echo $pbg_color  ?> !important;
				border-color: <?php echo $pbg_color  ?> !important;
				color: <?php echo $pbg_font_color ?> !important;
			}

			.bc_fed .nav-tabs {
				border-bottom: 1px solid <?php echo $pbg_color  ?> !important;
			}

			.bc_fed .panel-primary {
				border-color: <?php echo $pbg_color  ?> !important;
			}

			.bc_fed .bg-primary-font {
				color: <?php echo $pbg_color; ?>;
			}

			.bc_fed .fed_login_menus {
				background-color: <?php echo $pbg_color; ?> !important;
				color: <?php echo $pbg_font_color ?> !important;
			}

			.bc_fed .fed_login_content {
				border: 1px solid <?php echo $pbg_color; ?> !important;
				padding: 20px 40px;
			}

			.bc_fed .list-group-item {
				background-color: <?php echo $sbg_color?> !important;
				border-color: #ffffff !important;
				color: <?php echo $sbg_font_color?> !important;
			}

			.bc_fed .list-group-item.active, .bc_fed .list-group-item.active:hover, .bc_fed .list-group-item.active:focus {
				text-shadow: none !important;
			}

			.bc_fed .btn-default, .bc_fed .btn-primary, .bc_fed .btn-success, .bc_fed .btn-info, .bc_fed .btn-warning, .bc_fed .btn-danger {
				text-shadow: none !important;
			}
		</style>

		<?php
	}
}