<?php

function fed_admin_input_fields_single_line($row, $action ) {
	?>
    <div class="row fed_input_type_container fed_input_single_line_container hide">
        <form method="post"
              class="fed_admin_menu fed_ajax"
              action="<?php echo admin_url( 'admin-ajax.php?action=fed_admin_setting_up_form' ) ?>">

			<?php wp_nonce_field( 'fed_admin_setting_up_nonce', 'fed_admin_setting_up_nonce' ) ?>

			<?php echo fed_loader(); ?>

            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><b>Single Line</b></h3>
                    </div>
                    <div class="panel-body">
                        <div class="fed_input_text">
		                    <?php fed_get_admin_up_label_input_order( $row ); ?>
                            <div class="row">
			                    <?php fed_get_admin_up_input_meta($row) ?>

                                <div class="form-group col-md-6">
                                    <label for="">Placeholder Text</label>
				                    <?php echo fed_input_box( 'placeholder', array( 'value' => $row['placeholder'] ), 'text' ); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="">Class Name</label>
				                    <?php echo fed_input_box( 'class_name', array( 'value' => $row['class_name'] ), 'text' ); ?>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">ID Name</label>
				                    <?php echo fed_input_box( 'id_name', array( 'value' => $row['id_name'] ), 'text' ); ?>
                                </div>
                            </div>
		                    <?php
		                    fed_get_admin_up_display_permission( $row, $action );

		                    fed_get_admin_up_role_based( $row, $action );

		                    fed_get_input_type_and_submit_btn( 'text', $action );
		                    ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
	<?php
}