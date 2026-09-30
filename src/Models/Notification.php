<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Notification
 *
 * Active Record Model for In-App User Notifications.
 */
class Notification extends Model {

	protected $table = 'fed_notifications';

	protected $fillable = [
		'user_id',
		'title',
		'message',
		'type',
		'action_url',
		'is_read',
	];

	protected $casts = [
		'id'      => 'int',
		'user_id' => 'int',
		'is_read' => 'bool',
	];

	/**
	 * Mark notification as read.
	 *
	 * @return bool
	 */
	public function markAsRead(): bool {
		$this->is_read = true;
		return $this->save();
	}
}
