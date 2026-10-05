<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PostField
 *
 * Active Record Model for Post Submission Configuration.
 */
class PostField extends Model {

	protected $table = 'fed_post';

	protected $fillable = array(
		'post_type',
		'label',
		'input_meta',
		'input_type',
		'input_order',
		'placeholder',
		'class_name',
		'id_name',
		'options',
		'extra',
		'is_required',
		'status',
	);

	protected $casts = array(
		'id'          => 'int',
		'input_order' => 'int',
		'options'     => 'array',
		'is_required' => 'bool',
	);
}
