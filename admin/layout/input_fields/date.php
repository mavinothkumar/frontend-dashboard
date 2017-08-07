<?php

//function fed_admin_input_fields_date( $row, $action ) {
//	//var_dump($row);
//
//}

function fed_get_date_formats() {
	$date_formats = array(
		'd-m-Y' => 'Date-Month-Year',
		'm-d-Y' => 'Month-Date-Year',
	);

	return apply_filters( 'fed_get_date_formats_filter', $date_formats );
}

function fed_get_date_mode() {
	return array( 'single' => 'Single', 'multiple' => 'Multiple', 'range' => 'Range' );
}