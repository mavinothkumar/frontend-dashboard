<?php

namespace FED\Security;

use FED\Models\Menu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RbacManager
 *
 * Role-Based Access Control and capability validator.
 */
class RbacManager {

	/**
	 * Check if user has permission to view a dashboard menu item.
	 *
	 * @param int         $userId
	 * @param string|Menu $menu
	 * @return bool
	 */
	public function canAccessMenu( int $userId, $menu ): bool {
		if ( ! $userId ) {
			return false;
		}

		$user = get_userdata( $userId );
		if ( ! $user ) {
			return false;
		}

		if ( user_can( $user, 'administrator' ) ) {
			return true;
		}

		if ( is_string( $menu ) ) {
			$menuObj = Menu::where( 'menu_slug', $menu )->first();
			$menu    = $menuObj ? Menu::hydrate( $menuObj ) : null;
		}

		if ( ! $menu instanceof Menu ) {
			return true;
		}

		$allowedRoles = (array) $menu->user_role;
		if ( empty( $allowedRoles ) ) {
			return true;
		}

		return count( array_intersect( (array) $user->roles, $allowedRoles ) ) > 0;
	}

	/**
	 * Check if user can submit a specific post type from frontend.
	 *
	 * @param int    $userId
	 * @param string $postType
	 * @return bool
	 */
	public function canSubmitPost( int $userId, string $postType = 'post' ): bool {
		if ( ! $userId ) {
			return false;
		}

		$postTypeObj = get_post_type_object( $postType );
		if ( ! $postTypeObj ) {
			return false;
		}

		return user_can( $userId, $postTypeObj->cap->publish_posts ?? 'edit_posts' );
	}

	/**
	 * Check if current user can edit a target user's profile.
	 *
	 * @param int $currentUserId
	 * @param int $targetUserId
	 * @return bool
	 */
	public function canEditProfile( int $currentUserId, int $targetUserId ): bool {
		if ( ! $currentUserId ) {
			return false;
		}

		if ( $currentUserId === $targetUserId ) {
			return true;
		}

		return user_can( $currentUserId, 'edit_users' );
	}
}
