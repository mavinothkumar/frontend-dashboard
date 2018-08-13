<?php
/**
 * Logged in User form
 */

if ( isset( $_GET['action'], $_GET['key'], $_GET['login'] ) && $_GET['action'] === 'fed_reset' ) {
	$details = fed_reset_password_only();
	$type = 'Reset Password';
} else {
	$details = fed_login_only();
	$type = 'Login';
}

do_action( 'fed_before_login_only_form' );
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
										<?php echo ! empty( $content['name'] ) ? '<label>' . $content['name'] . '</label>' : ''; ?><?php echo $content['input'] ?>
                                    </div>
									<?php
								}
								?>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <input type="hidden" name="submit" value="<?php echo $type; ?>"/>
                                        <button class="btn btn-primary"
                                                type="submit"><?php echo $details['button'] ?></button>
                                    </div>
                                    <div class="col-md-12 padd_top_20 text-center">
										<?php if ( $registration = fed_get_registration_url() ) { ?>
                                            <a href="<?php echo $registration; ?>"><?php _e( 'Create an account', 'frontend-dashboard' ) ?></a> |
										<?php } ?>
										<?php if ( $forgot = fed_get_forgot_password_url() ) {
											?>
                                            <a href="<?php echo $forgot; ?>">
												<?php _e( 'Lost Password?', 'frontend-dashboard' ) ?>
                                            </a>
											<?php
										} ?>
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
do_action( 'fed_after_login_only_form' );