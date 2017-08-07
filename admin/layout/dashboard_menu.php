<?php

/**
 * Admin Dashboard Menu
 */
function fed_admin_dashboard_menu() {
	$menus      = fed_fetch_table_rows_with_key( BC_FED_MENU_DB, 'menu_slug' );
	$user_roles = fed_get_user_roles();
	?>
	<div class="bc_fed container">
		<!-- Show Empty form to add Dashboard Menu-->
		<div class="row padd_top_20 hide"
			 id="fed_add_new_menu_container">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
							<b><?php _e( 'Add New Menu', 'fed' ) ?></b>
						</h3>
					</div>
					<div class="panel-body">
						<form method="post"
							  class="fed_admin_menu fed_menu_ajax"
							  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form_dashboard_menu' ) ?>">

							<?php wp_nonce_field( 'fed_admin_setting_form_dashboard_menu', 'fed_admin_setting_form_dashboard_menu' ) ?>
							<div class="row">
								<div class="col-md-10">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label>Menu Name</label>
												<input type="text"
													   title="Menu Name"
													   name="fed_menu_name"
													   class="form-control fed_menu_name"
													   value=""
												/>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label>Menu Slug</label>
												<input type="text"
													   title="Menu Slug"
													   name="fed_menu_slug"
													   class="form-control fed_menu_slug"
													   value=""
												/>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label>Menu Icon</label>
												<input type="text"
													   name="menu_image_id"
													   class="form-control menu_image_id"
													   data-toggle="modal"
													   data-target=".fed_show_fa_list"
													   placeholder=""
													   data-fed_menu_box_id="menu_image_id"

												/>
											</div>
										</div>

										<div class="col-md-2">
											<div class="form-group">
												<label>Menu Order</label>
												<input type="number"
													   name="fed_menu_order"
													   class="form-control fed_menu_order"
													   placeholder=""
												/>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group">
												<label>Disable User Profile</label><br>
												<input title="Disable User Profile" type="checkbox"
													   name="show_user_profile"
													   value="Disable"
												/>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12 padd_top_20">
											<label>Select user role to show this input field</label>
										</div>
										<div class="col-md-12 padd_top_20">
											<?php
											foreach ( $user_roles as $key => $user_role ) {
												?>
												<div class="col-md-2">
													<?php echo fed_input_box( 'user_role', array(
														'default_value' => 'Enable',
														'name'          => 'user_role[' . $key . ']',
														'label'         => esc_attr( $user_role ),
														'value'         => 'Enable'
													), 'checkbox' ); ?>
												</div>
												<?php
											}
											?>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="col-md-12">
										<div class="form-group fed_menu_save_button">
											<button type="submit"
													class="btn btn-primary fed_menu_save">
												<i class="fa fa-plus"></i>
												<?php _e( 'Add New Menu', 'fed' ) ?>
											</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!--List / Edit Dashboard Menus-->
		<div class="row padd_top_20">
			<div class="col-md-12">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
							<b><?php _e( 'Menu Lists', 'fed' ) ?></b>
						</h3>
					</div>
					<div class="panel-body">
						<div class="fed_dashboard_menu_items_container">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<button type="submit"
												class="btn btn-primary fed_menu_save fed_menu_save_button_toggle">
											<i class="fa fa-plus"></i>
											<?php _e( 'Add New Menu', 'fed' ) ?>
										</button>
									</div>
								</div>
								<!--                                <div class="col-md-6">-->
								<!--                                    <div class="input-group">-->
								<!--                                        <input type="text" class="form-control" placeholder="Search Menu Name...">-->
								<!--                                        <span class="input-group-btn">-->
								<!--        <button class="btn btn-primary" type="button">Search</button>-->
								<!--      </span>-->
								<!--                                    </div>-->
								<!--                                </div>-->
							</div>
							<?php
							foreach ( $menus as $menu ) {
								?>
								<div class="fed_dashboard_menu_single_item">
									<form method="post"
										  class="fed_admin_menu fed_menu_ajax"
										  action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_form_dashboard_menu' ) ?>">

										<?php wp_nonce_field( 'fed_admin_setting_form_dashboard_menu', 'fed_admin_setting_form_dashboard_menu' ) ?>
										<input type="hidden"
											   name="menu_id"
											   value="<?php echo $menu['id'] ?>"/>
										<div class="row">
											<div class="col-md-10">
												<div class="row">
													<div class="col-md-12">
														<div class="col-md-3">
															<div class="form-group">
																<label><?php _e( 'Menu Name', 'fed' ) ?></label>
																<input type="text"
																	   name="fed_menu_name"
																	   class="form-control fed_menu_name"
																	   value="<?php echo esc_attr( $menu['menu'] ) ?>"
																	   required="required"
																	   placeholder="Menu Name"
																/>
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label><?php _e( 'Menu Slug', 'fed' ) ?></label>
																<input type="text"
																	   name="fed_menu_slug"
																	   class="form-control fed_menu_slug"
																	   value="<?php echo esc_attr(
																	       $menu['menu_slug'] ) ?>"
																	   required="required"
																	   placeholder="Menu Slug"
																/>
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label><?php _e( 'Menu Icon', 'fed' ) ?></label>
																<input type="text"
																	   name="menu_image_id"
																	   class="form-control <?php echo esc_attr( $menu['menu_slug'] ) ?>"
																	   value="<?php echo esc_attr( $menu['menu_image_id'] ) ?>"
																	   data-toggle="modal"
																	   data-target=".fed_show_fa_list"
																	   placeholder="Menu Icon"
																	   data-fed_menu_box_id="<?php echo esc_attr( $menu['menu_slug'] ) ?>"
																/>
															</div>
														</div>
														<div class="col-md-2">
															<div class="form-group">
																<label><?php _e( 'Menu Order', 'fed' ) ?></label>
																<input type="number"
																	   name="fed_menu_order"
																	   class="form-control fed_menu_order"
																	   value="<?php echo esc_attr(
																	       $menu['menu_order'] ) ?>"
																	   required="required"
																	   placeholder="Menu Order"
																/>
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group text-center">
																<label><?php _e( 'Disable User Profile', 'fed' ) ?></label>
																<?php
																echo fed_input_box( 'show_user_profile', array(
																	'default_value' => 'Disable',
																	'label'         => '',
																	'value'         => esc_attr( $menu['show_user_profile'] ),
																), 'checkbox' );
																?>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-md-2">
												<div class="col-md-12">
													<div class="form-group">
														<button type="submit"
																class="btn btn-primary fed_menu_save"
														>
															<i class="fa fa-save"></i>
														</button>
														<?php if ( $menu['extra'] !== 'no' ) { ?>
															<button type="submit"
																	class="btn btn-danger fed_menu_delete">
																<i class="fa fa-trash"></i>
															</button>
															<?php
														}
														?>
													</div>
												</div>
											</div>
										</div>
										<hr>
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="col-md-12 padd_top_10">
														<div class="col-md-2">
															<label><?php _e( 'Select User Role(s)' ) ?></label>
														</div>
														<?php
														foreach ( $user_roles as $key => $user_role ) {
															$c_value = in_array( $key, unserialize( $menu['user_role'] ) ) ? 'Enable' : 'Disable';

															?>
															<div class="col-md-2">
																<?php echo fed_input_box( 'user_role', array(
																	'default_value' => 'Enable',
																	'name'          => 'user_role[' . $key . ']',
																	'label'         => esc_attr( $user_role ),
																	'value'         => $c_value,
																), 'checkbox' ); ?>
															</div>
															<?php
														}
														?>
													</div>
												</div>
											</div>
										</div>
									</form>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php fed_menu_icons_popup(); ?>
	</div>
	<?php
}

/**
 * Show Menu Icons Popup
 */
function fed_menu_icons_popup() {
	?>
	<div class="bc_fed">
		<div class="modal fade fed_show_fa_list"
			 tabindex="-1"
			 role="dialog"
		>

			<div class="modal-dialog modal-lg"
				 role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button"
								class="close"
								data-dismiss="modal"
								aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<h4 class="modal-title"><?php _e( 'Please Select one Image', 'fed' ) ?></h4>
					</div>
					<div class="modal-body">
						<input type="hidden"
							   id="fed_menu_box_id"
							   name="fed_menu_box_id"
							   value=""/>
						<div class="row fed_fa_container">
							<?php foreach ( fed_font_awesome_list() as $key => $list ) {
								echo '<div class="col-md-1 fed_single_fa" 
							data-dismiss="modal"
							data-id="' . $key . '"
							data-toggle="popover"
							title="' . $list . '"
							data-trigger="hover"
							data-viewport=""
							data-content="' . $list . '"
							>
							<span class="' . $key . '"  data-id="' . $key . '" id="' . $key . '"></span>
							</div>';
							} ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}