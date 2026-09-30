<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UserProfileField
 *
 * Active Record Model for User Profile Custom Fields.
 */
class UserProfileField extends Model {

	protected $table = 'fed_user_profile';

	protected $fillable = [
		'input_meta',
		'label_name',
		'label',
		'input_order',
		'show_register',
		'show_dashboard',
		'show_user_profile',
		'is_required',
		'is_unique',
		'input_type',
		'placeholder',
		'class_name',
		'id_name',
		'input_step',
		'input_min',
		'input_max',
		'input_row',
		'is_tooltip',
		'tooltip_title',
		'tooltip_body',
		'input_value',
		'user_role',
		'input_location',
		'extra',
		'menu',
		'extended',
		'options',
		'user_read_only',
		'admin_read_only',
		'status',
	];

	protected $casts = [
		'id'                => 'int',
		'input_order'       => 'int',
		'user_role'         => 'array',
		'options'           => 'array',
		'is_required'       => 'bool',
		'is_unique'         => 'bool',
		'user_read_only'    => 'bool',
		'admin_read_only'   => 'bool',
	];
}
