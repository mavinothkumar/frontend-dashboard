<?php

namespace FED\Models;

use FED\Database\Orm\Model;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Menu
 *
 * Active Record Model for Frontend Dashboard Menu items.
 */
class Menu extends Model {

	protected $table = 'fed_menu';

	protected $fillable = [
		'menu_slug',
		'menu',
		'menu_name',
		'menu_order',
		'menu_image_id',
		'menu_icon',
		'show_user_profile',
		'extra',
		'user_role',
		'extended',
		'parent_id',
		'menu_type',
		'status',
	];

	protected $casts = [
		'id'         => 'int',
		'menu_order' => 'int',
		'user_role'  => 'array',
	];

	/**
	 * Get child sub-menus.
	 *
	 * @return Menu[]
	 */
	public function children(): array {
		return $this->hasMany( self::class, 'parent_id' );
	}

	/**
	 * Get parent menu item.
	 *
	 * @return Menu|null
	 */
	public function parent(): ?Menu {
		return $this->belongsTo( self::class, 'parent_id' );
	}
}
