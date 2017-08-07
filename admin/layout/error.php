<?php
/**
 * Developer Comment
 * esc, translate => done
 */
/**
 * 404 | Some misuse action caught.
 */
function fed_landed_wrongly() {
	?>
    <div class="bc_fed container">
        <div class="alert alert-danger">
            <button type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-hidden="true">&times;</button>
            <strong><?php _e( '404!', 'fed' ) ?> </strong> <?php _e( 'Hope you have landed wrongly, please proceed to plugin', 'fed' ) ?>
            <a href="<?php  menu_page_url( 'fed_settings_menu' ) ?>"> <?php _e( 'home page', 'fed' ) ?> </a>.
        </div>
    </div>
	<?php
}