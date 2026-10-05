<?php

namespace FED\Database\Repositories;

use FED\Database\BaseRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class UserProfileRepository
 *
 * Repository handling user profile fields and custom meta definitions.
 */
class UserProfileRepository extends BaseRepository {

	protected $table = 'fed_user_profile';

	/**
	 * Retrieve all profile fields ordered by input_order.
	 *
	 * @return array
	 */
	public function getOrderedFields() {
		return $this->all( 'input_order ASC, id ASC' );
	}

	/**
	 * Find field by its unique meta key.
	 *
	 * @param string $metaKey
	 * @return array|null
	 */
	public function findByMeta( $metaKey ) {
		return $this->findBy( 'input_meta', $metaKey );
	}

	/**
	 * Retrieve fields enabled for user registration form.
	 *
	 * @return array
	 */
	public function getRegistrationFields() {
		return $this->where( array( 'show_register' => 'Enable' ), 'input_order ASC' );
	}

	/**
	 * Retrieve fields enabled for frontend profile editor.
	 *
	 * @return array
	 */
	public function getProfileFields() {
		return $this->where( array( 'show_user_profile' => 'Enable' ), 'input_order ASC' );
	}
}
