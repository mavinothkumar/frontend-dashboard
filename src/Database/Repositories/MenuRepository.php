<?php

namespace FED\Database\Repositories;

use FED\Database\BaseRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MenuRepository
 *
 * Repository handling all operations for Frontend Dashboard menus.
 */
class MenuRepository extends BaseRepository {

	protected $table = 'fed_menu';

	/**
	 * Find a menu item by slug.
	 *
	 * @param string $slug
	 * @return array|null
	 */
	public function findBySlug( $slug ) {
		return $this->findBy( 'menu_slug', $slug );
	}

	/**
	 * Check if a slug already exists for another menu ID.
	 *
	 * @param string $slug
	 * @param int    $excludeId
	 * @return bool
	 */
	public function hasDuplicate( $slug, $excludeId = 0 ) {
		$table = $this->getTableName();
		$sql   = $this->db->prepare(
			"SELECT id FROM `{$table}` WHERE `menu_slug` = %s AND `id` != %d LIMIT 1",
			$slug,
			(int) $excludeId
		);

		return (bool) $this->db->get_var( $sql );
	}

	/**
	 * Retrieve all active menus ordered by menu_order.
	 *
	 * @return array
	 */
	public function getOrderedMenus() {
		return $this->all( 'menu_order ASC, id ASC' );
	}

	/**
	 * Retrieve menus accessible by a specific user role.
	 *
	 * @param string $role User role name (e.g. 'administrator', 'subscriber').
	 * @return array
	 */
	public function getMenusForRole( $role ) {
		$menus      = $this->getOrderedMenus();
		$accessible = array();

		foreach ( $menus as $menu ) {
			if ( empty( $menu['user_role'] ) ) {
				$accessible[] = $menu;
				continue;
			}

			$roles = maybe_unserialize( $menu['user_role'] );
			if ( is_array( $roles ) && in_array( $role, $roles, true ) ) {
				$accessible[] = $menu;
			}
		}

		return $accessible;
	}

	/**
	 * Build hierarchical menu tree (root items with children).
	 *
	 * @return array
	 */
	public function getMenuTree() {
		$menus  = $this->getOrderedMenus();
		$tree   = array();
		$lookup = array();

		foreach ( $menus as $menu ) {
			$menu['children']      = array();
			$lookup[ $menu['id'] ] = $menu;
		}

		foreach ( $lookup as $id => $menu ) {
			$parentId = (int) $menu['parent_id'];
			if ( $parentId > 0 && isset( $lookup[ $parentId ] ) ) {
				$lookup[ $parentId ]['children'][] = &$lookup[ $id ];
			} else {
				$tree[] = &$lookup[ $id ];
			}
		}

		return $tree;
	}
}
