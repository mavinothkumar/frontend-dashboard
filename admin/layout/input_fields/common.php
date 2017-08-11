<?php

/**
 * Placeholder Field
 * @param array $row
 */
function fed_get_placeholder_field( array $row ) {
	?>
	<label for=""><?php _e( 'Placeholder Text', 'fed' ) ?></label>
	<?php echo fed_input_box( 'placeholder', array( 'value' => $row['placeholder'] ), 'text' ); ?>
	<?php
}

function fed_get_class_field( array $row ) {
	?>
	<label for=""><?php _e( 'Class Name', 'fed' ) ?></label>
	<?php echo fed_input_box( 'class_name', array( 'value' => $row['class_name'] ), 'text' ); ?>
	<?php
}

function fed_get_id_field( array $row ) {
	?>
	<label for=""><?php _e( 'ID Name', 'fed' ) ?></label>
	<?php echo fed_input_box( 'id_name', array( 'value' => $row['id_name'] ), 'text' ); ?>
	<?php
}