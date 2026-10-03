<?php

namespace FED\Database\Repositories;

use FED\Database\BaseRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PostRepository
 *
 * Repository handling custom post fields, frontend submission rules, and post permissions.
 */
class PostRepository extends BaseRepository {

	protected $table = 'fed_post';

	/**
	 * Retrieve all configured fields for a specific post type.
	 *
	 * @param string $postType
	 * @return array
	 */
	public function getFieldsForPostType( $postType = 'post' ) {
		return $this->where( array( 'post_type' => $postType ), 'input_order ASC, id ASC' );
	}

	/**
	 * Find a post field by post type and input meta key.
	 *
	 * @param string $postType
	 * @param string $inputMeta
	 * @return array|null
	 */
	public function findByMeta( $postType, $inputMeta ) {
		$table = $this->getTableName();
		$sql   = $this->db->prepare(
			"SELECT * FROM `{$table}` WHERE `post_type` = %s AND `input_meta` = %s LIMIT 1",
			$postType,
			$inputMeta
		);

		$row = $this->db->get_row( $sql, ARRAY_A );
		return $row ?: null;
	}

	/**
	 * Retrieve distinct post types configured in Frontend Dashboard.
	 *
	 * @return array
	 */
	public function getConfiguredPostTypes() {
		$table = $this->getTableName();
		$sql   = "SELECT DISTINCT `post_type` FROM `{$table}` WHERE `status` = 'active'";
		$types = $this->db->get_col( $sql );

		return $types ?: array();
	}
}
