<?php

function fed_config() {
	return apply_filters( 'fed_config', array(
		'plugin_api' => 'https://buffercode.com/api/fed/plugins',
	) );
}