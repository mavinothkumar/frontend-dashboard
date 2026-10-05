<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AuditLog
 *
 * Active Record Model for Audit and System Event Logs.
 */
class AuditLog extends Model {

	protected $table = 'fed_activity_log';

	protected $fillable = array(
		'user_id',
		'user_login',
		'user_email',
		'user_display_name',
		'channel',
		'level',
		'action',
		'message',
		'context',
		'action_type',
		'action_title',
		'description',
		'status',
		'ip_address',
		'user_agent',
	);

	protected $casts = array(
		'id'      => 'int',
		'user_id' => 'int',
		'context' => 'array',
	);
}
