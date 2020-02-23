<?php
/**
 * Common.
 *
 * @package Frontend Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Placeholder Field.
 *
 * @param  array $row  Row.
 */
function fed_get_placeholder_field( array $row ) {
	?>
    <label for=""><?php _e( 'Placeholder Text', 'frontend-dashboard' ) ?></label>
	<?php echo fed_input_box( 'placeholder', array( 'value' => $row['placeholder'] ), 'single_line' ); ?>
	<?php
}

/**
 * Get Class Field.
 *
 * @param  array $row  Row.
 */
function fed_get_class_field( array $row ) {
	?>
    <label for=""><?php _e( 'Class Name', 'frontend-dashboard' ) ?></label>
	<?php echo fed_input_box( 'class_name', array( 'value' => $row['class_name'] ), 'single_line' ); ?>
	<?php
}

/**
 * Get ID field.
 *
 * @param  array $row  Row.
 */
function fed_get_id_field( array $row ) {
	?>
    <label for=""><?php _e( 'ID Name', 'frontend-dashboard' ) ?></label>
	<?php echo fed_input_box( 'id_name', array( 'value' => $row['id_name'] ), 'single_line' ); ?>
	<?php
}