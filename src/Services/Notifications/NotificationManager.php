<?php

namespace FED\Services\Notifications;

use FED\Models\Notification;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class NotificationManager
 *
 * In-App Notification dispatcher and manager.
 */
class NotificationManager {

	/**
	 * Create and dispatch an in-app notification.
	 *
	 * @param int    $userId
	 * @param string $title
	 * @param string $message
	 * @param string $type ('info', 'success', 'warning', 'error')
	 * @param string $actionUrl
	 * @return Notification
	 */
	public function notify( int $userId, string $title, string $message, string $type = 'info', string $actionUrl = '' ): Notification {
		return Notification::create(
			array(
				'user_id'    => $userId,
				'title'      => sanitize_text_field( $title ),
				'message'    => sanitize_textarea_field( $message ),
				'type'       => sanitize_key( $type ),
				'action_url' => esc_url_raw( $actionUrl ),
				'is_read'    => false,
			)
		);
	}

	/**
	 * Get unread notifications for user.
	 *
	 * @param int $userId
	 * @param int $limit
	 * @return Notification[]
	 */
	public function getUnread( int $userId, int $limit = 10 ): array {
		$rows = Notification::query()
			->where( 'user_id', $userId )
			->where( 'is_read', 0 )
			->orderBy( 'created_at', 'DESC' )
			->limit( $limit )
			->get();

		$models = array();
		foreach ( $rows as $row ) {
			$models[] = Notification::hydrate( $row );
		}
		return $models;
	}

	/**
	 * Count unread notifications.
	 *
	 * @param int $userId
	 * @return int
	 */
	public function countUnread( int $userId ): int {
		return Notification::query()
			->where( 'user_id', $userId )
			->where( 'is_read', 0 )
			->count();
	}

	/**
	 * Mark a single notification as read.
	 *
	 * @param int $notificationId
	 * @param int $userId
	 * @return bool
	 */
	public function markAsRead( int $notificationId, int $userId ): bool {
		$notification = Notification::find( $notificationId );
		if ( $notification && (int) $notification->user_id === $userId ) {
			return $notification->markAsRead();
		}
		return false;
	}

	/**
	 * Mark all user notifications as read.
	 *
	 * @param int $userId
	 * @return bool
	 */
	public function markAllAsRead( int $userId ): bool {
		$status = Notification::query()
			->where( 'user_id', $userId )
			->where( 'is_read', 0 )
			->update( array( 'is_read' => 1 ) );

		return false !== $status;
	}
}
