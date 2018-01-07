<?php
/**
 * Forgot Password only
 */

$details = fed_forgot_password_only();

do_action( 'fed_before_forgot_password_only_form' );
?>
	<div class="bc_fed container fed_login_container">
		<?php echo fed_loader(); ?>
		<div class="row">
			<div class="col-md-6 col-md-offset-3">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo $details['menu']['name']; ?></h3>
					</div>
					<div class="panel-body">
						<div class="fed_tab_content"
							 data-id="<?php echo $details['menu']['id'] ?>">
							<form method="post"
								  class="fed_form_post"
							>
								<?php
								$contents = $details['content'];
								uasort( $contents, 'fed_sort_by_order' );
								foreach ( $contents as $content ) {
									?>
									<div class="form-group">
										<label><?php echo $content['name'] ?></label>
										<?php echo $content['input'] ?>
									</div>
									<?php
								}
								?>
								<div class="row">
									<div class="col-md-3"></div>
									<div class="col-md-9">
										<input type="hidden"
											   name="submit"
											   value="Forgot Password"/>
										<button class="btn btn-primary" type="submit"><?php echo $details['button'] ?></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php
do_action( 'fed_after_forgot_password_only_form' );