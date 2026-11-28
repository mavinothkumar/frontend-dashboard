<?php
/**
 * Field Operations Service: 1-Click Duplicate & JSON Import / Export.
 *
 * Handles cloning individual User Profile or Post fields, exporting
 * portable JSON field sets, and importing field definitions with
 * conflict resolution across environments.
 *
 * @package Frontend Dashboard
 * @since   3.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FED_Field_Operations' ) ) {

	/**
	 * Class FED_Field_Operations
	 */
	class FED_Field_Operations {

		/**
		 * Reserved system meta keys that cannot be overwritten or duplicated into custom fields.
		 *
		 * @var array
		 */
		private $reserved_keys = array(
			'role',
			'roles',
			'caps',
			'capabilities',
			'wp_capabilities',
			'user_level',
			'user_pass',
			'user_pass_confirm',
			'confirmation_password',
			'user_activation_key',
			'user_status',
			'ID',
			'id',
			'session_tokens',
		);

		/**
		 * Allowed input types for import validation.
		 *
		 * @var array
		 */
		private $allowed_input_types = array(
			'single_line',
			'multi_line',
			'number',
			'email',
			'password',
			'url',
			'select',
			'checkbox',
			'radio',
			'date',
			'file',
			'color',
			'wp_editor',
			'label',
			'table',
			'hidden',
		);

		/**
		 * Constructor: Register AJAX hooks.
		 */
		public function __construct() {
			add_action( 'wp_ajax_fed_duplicate_field', array( $this, 'handle_duplicate_field' ) );
			add_action( 'wp_ajax_fed_export_fields_json', array( $this, 'handle_export_fields' ) );
			add_action( 'wp_ajax_fed_import_fields_json', array( $this, 'handle_import_fields' ) );
		}

		/**
		 * Verify admin capability and nonce for field operations.
		 */
		private function authorize_request() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Permission denied. Administrator privileges required.', 'frontend-dashboard' ),
					),
					403
				);
			}

			$nonce = isset( $_REQUEST['fed_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['fed_nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $nonce, 'fed_nonce' ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid security token. Please reload the page and try again.', 'frontend-dashboard' ),
					),
					403
				);
			}
		}

		/**
		 * Resolve database table name from scope.
		 *
		 * @param string $scope 'profile' or 'post'.
		 * @return string|false
		 */
		private function get_table_name( $scope ) {
			global $wpdb;
			if ( 'profile' === $scope ) {
				return $wpdb->prefix . BC_FED_TABLE_USER_PROFILE;
			}
			if ( 'post' === $scope ) {
				return $wpdb->prefix . BC_FED_TABLE_POST;
			}
			return false;
		}

		/**
		 * Generate a unique, schema-safe input_meta key (max 32 chars).
		 *
		 * @param string $base_meta  Original meta key.
		 * @param string $table_name Database table name.
		 * @param string $suffix     Suffix to append (e.g. 'copy', 'imported').
		 * @return string
		 */
		private function generate_unique_meta_key( $base_meta, $table_name, $suffix = 'copy' ) {
			global $wpdb;

			$clean_base = sanitize_key( $base_meta );
			if ( empty( $clean_base ) ) {
				$clean_base = 'field';
			}

			// Strip existing _copy or _copy_N suffix so repeated clones stay clean.
			$clean_base = preg_replace( '/_(copy|imported)(_\d+)?$/', '', $clean_base );
			if ( empty( $clean_base ) ) {
				$clean_base = 'field';
			}

			$suffix_str = '_' . sanitize_key( $suffix );
			$max_base   = 32 - strlen( $suffix_str );
			$candidate  = substr( $clean_base, 0, max( 1, $max_base ) ) . $suffix_str;

			$counter = 2;
			while (
				in_array( $candidate, $this->reserved_keys, true ) ||
				$this->meta_key_exists( $table_name, $candidate )
			) {
				$num_suffix = $suffix_str . '_' . $counter;
				$max_b      = 32 - strlen( $num_suffix );
				$candidate  = substr( $clean_base, 0, max( 1, $max_b ) ) . $num_suffix;
				++$counter;

				if ( $counter > 500 ) {
					$candidate = 'fed_' . substr( md5( uniqid( '', true ) ), 0, 12 );
					break;
				}
			}

			return $candidate;
		}

		/**
		 * Check if an input_meta key already exists in the given table.
		 *
		 * @param string $table_name Table name.
		 * @param string $meta_key   Meta key to check.
		 * @return bool
		 */
		private function meta_key_exists( $table_name, $meta_key ) {
			global $wpdb;
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$found = $wpdb->get_var(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT id FROM {$table_name} WHERE input_meta = %s LIMIT 1",
					$meta_key
				)
			);
			return ! empty( $found );
		}

		/**
		 * AJAX Handler: 1-Click Duplicate Field.
		 */
		public function handle_duplicate_field() {
			$this->authorize_request();

			global $wpdb;

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$field_id = isset( $_POST['field_id'] ) ? absint( $_POST['field_id'] ) : 0;
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$scope = isset( $_POST['field_scope'] ) ? sanitize_key( wp_unslash( $_POST['field_scope'] ) ) : '';

			$table_name = $this->get_table_name( $scope );
			if ( ! $field_id || ! $table_name ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid field ID or scope specified.', 'frontend-dashboard' ),
					),
					400
				);
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$original = $wpdb->get_row(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM {$table_name} WHERE id = %d LIMIT 1",
					$field_id
				),
				ARRAY_A
			);

			if ( empty( $original ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Source field could not be found.', 'frontend-dashboard' ),
					),
					404
				);
			}

			$new_meta  = $this->generate_unique_meta_key( $original['input_meta'], $table_name, 'copy' );
			$new_label = $original['label_name'] . ' ' . __( '(Copy)', 'frontend-dashboard' );

			$insert_data = $original;
			unset( $insert_data['id'] );
			$insert_data['input_meta']  = $new_meta;
			$insert_data['label_name']  = $new_label;
			$insert_data['input_order'] = (int) $original['input_order'] + 1;

			if ( 'profile' === $scope ) {
				$insert_data['extra'] = 'yes';
			}

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
			$inserted = $wpdb->insert( $table_name, $insert_data );

			if ( false === $inserted ) {
				wp_send_json_error(
					array(
						'message' => __( 'Database error while duplicating the field.', 'frontend-dashboard' ),
					),
					500
				);
			}

			$new_id = (int) $wpdb->insert_id;

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$new_field = $wpdb->get_row(
				$wpdb->prepare(
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					"SELECT * FROM {$table_name} WHERE id = %d LIMIT 1",
					$new_id
				),
				ARRAY_A
			);

			$card_html = '';
			if ( ! empty( $new_field ) ) {
				// Unserialize user_role and extended to match fed_fetch_table_rows_with_key() format.
				if ( isset( $new_field['user_role'] ) && is_string( $new_field['user_role'] ) ) {
					$unserialized_role      = maybe_unserialize( $new_field['user_role'] );
					$new_field['user_role'] = is_array( $unserialized_role ) ? $unserialized_role : array();
				}
				if ( isset( $new_field['extended'] ) && is_string( $new_field['extended'] ) ) {
					$unserialized_ext      = maybe_unserialize( $new_field['extended'] );
					$new_field['extended'] = is_array( $unserialized_ext ) ? $unserialized_ext : array();
				}

				ob_start();
				if ( 'profile' === $scope && function_exists( 'fed_render_profile_field_card' ) ) {
					$menu_slug = isset( $new_field['menu'] ) && ! empty( $new_field['menu'] ) ? $new_field['menu'] : 'profile';
					fed_render_profile_field_card( $new_field, $menu_slug );
				} elseif ( 'post' === $scope && function_exists( 'fed_render_post_field_card' ) ) {
					$pt_slug = isset( $new_field['post_type'] ) && ! empty( $new_field['post_type'] ) ? $new_field['post_type'] : 'post';
					fed_render_post_field_card( $new_field, $pt_slug );
				}
				$card_html = ob_get_clean();
			}

			wp_send_json_success(
				array(
					/* translators: %s: New duplicated field label */
					'message'    => sprintf( __( 'Field "%s" duplicated successfully.', 'frontend-dashboard' ), $new_label ),
					'field_id'   => $new_id,
					'input_meta' => $new_meta,
					'html'       => $card_html,
				)
			);
		}

		/**
		 * AJAX Handler: Export Fields to JSON.
		 */
		public function handle_export_fields() {
			$this->authorize_request();

			global $wpdb;

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$scope = isset( $_POST['field_scope'] ) ? sanitize_key( wp_unslash( $_POST['field_scope'] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$filter_key = isset( $_POST['filter_key'] ) ? sanitize_text_field( wp_unslash( $_POST['filter_key'] ) ) : 'all';

			$table_name = $this->get_table_name( $scope );
			if ( ! $table_name ) {
				wp_send_json_error(
					array(
						'message' => __( 'Invalid export scope.', 'frontend-dashboard' ),
					),
					400
				);
			}

			if ( 'all' !== $filter_key && ! empty( $filter_key ) ) {
				$filter_col = ( 'post' === $scope ) ? 'post_type' : 'menu';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$rows = $wpdb->get_results(
					$wpdb->prepare(
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						"SELECT * FROM {$table_name} WHERE {$filter_col} = %s ORDER BY input_order ASC, id ASC",
						$filter_key
					),
					ARRAY_A
				);
			} else {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$rows = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY input_order ASC, id ASC", ARRAY_A );
			}

			if ( empty( $rows ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'No fields found to export in the selected scope.', 'frontend-dashboard' ),
					),
					404
				);
			}

			$export_fields = array();
			foreach ( $rows as $row ) {
				unset( $row['id'] );

				if ( isset( $row['user_role'] ) ) {
					$roles            = maybe_unserialize( $row['user_role'] );
					$row['user_role'] = is_array( $roles ) ? $roles : array();
				}
				if ( isset( $row['extended'] ) ) {
					$ext             = maybe_unserialize( $row['extended'] );
					$row['extended'] = is_array( $ext ) ? $ext : array();
				}
				if ( isset( $row['input_value'] ) && is_serialized( $row['input_value'] ) ) {
					$row['input_value'] = maybe_unserialize( $row['input_value'] );
				}

				$export_fields[] = $row;
			}

			$export_type = ( 'post' === $scope ) ? 'post_fields' : 'user_profile_fields';
			$site_slug   = sanitize_title( get_bloginfo( 'name' ) );
			if ( empty( $site_slug ) ) {
				$site_slug = 'fed';
			}
			$scope_suffix = ( 'all' !== $filter_key && ! empty( $filter_key ) ) ? '-' . sanitize_key( $filter_key ) : '-all';
			$filename     = sprintf( '%s-%s%s-%s.json', $site_slug, $export_type, $scope_suffix, gmdate( 'Y-m-d' ) );

			$payload = array(
				'generator'    => 'Frontend Dashboard',
				'version'      => defined( 'BC_FED_PLUGIN_VERSION' ) ? BC_FED_PLUGIN_VERSION : '3.8.0',
				'export_type'  => $export_type,
				'filter_scope' => $filter_key,
				'exported_at'  => current_time( 'mysql' ),
				'site_url'     => home_url(),
				'fields_count' => count( $export_fields ),
				'fields'       => $export_fields,
			);

			wp_send_json_success(
				array(
					'filename' => $filename,
					'payload'  => $payload,
					/* translators: %d: Number of exported fields */
					'message'  => sprintf( __( 'Exported %d fields successfully.', 'frontend-dashboard' ), count( $export_fields ) ),
				)
			);
		}

		/**
		 * AJAX Handler: Import Fields from JSON Payload.
		 */
		public function handle_import_fields() {
			$this->authorize_request();

			global $wpdb;

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$scope = isset( $_POST['field_scope'] ) ? sanitize_key( wp_unslash( $_POST['field_scope'] ) ) : '';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$conflict_mode = isset( $_POST['conflict_mode'] ) ? sanitize_key( wp_unslash( $_POST['conflict_mode'] ) ) : 'skip';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$target_group = isset( $_POST['target_group'] ) ? sanitize_text_field( wp_unslash( $_POST['target_group'] ) ) : 'preserve';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$raw_json = isset( $_POST['import_payload'] ) ? wp_unslash( $_POST['import_payload'] ) : '';

			$table_name = $this->get_table_name( $scope );
			if ( ! $table_name ) {
				wp_send_json_error( array( 'message' => __( 'Invalid import scope.', 'frontend-dashboard' ) ), 400 );
			}

			if ( ! in_array( $conflict_mode, array( 'skip', 'overwrite', 'rename' ), true ) ) {
				$conflict_mode = 'skip';
			}

			if ( empty( $raw_json ) ) {
				wp_send_json_error( array( 'message' => __( 'No JSON payload received.', 'frontend-dashboard' ) ), 400 );
			}

			$decoded = json_decode( $raw_json, true );
			if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
				wp_send_json_error( array( 'message' => __( 'Invalid JSON format. Please upload a valid Frontend Dashboard export file.', 'frontend-dashboard' ) ), 400 );
			}

			if ( empty( $decoded['fields'] ) || ! is_array( $decoded['fields'] ) ) {
				wp_send_json_error( array( 'message' => __( 'No valid "fields" array found inside the JSON file.', 'frontend-dashboard' ) ), 400 );
			}

			if ( count( $decoded['fields'] ) > 500 ) {
				wp_send_json_error( array( 'message' => __( 'Import limit exceeded (maximum 500 fields per batch).', 'frontend-dashboard' ) ), 400 );
			}

			$created = 0;
			$updated = 0;
			$skipped = 0;
			$errors  = 0;

			$default_system_keys = ( 'post' === $scope && function_exists( 'fed_get_default_post_items' ) )
				? fed_get_default_post_items()
				: ( function_exists( 'fed_get_default_profile_items' ) ? fed_get_default_profile_items() : array() );

			foreach ( $decoded['fields'] as $raw_field ) {
				if ( ! is_array( $raw_field ) || empty( $raw_field['input_meta'] ) || empty( $raw_field['label_name'] ) ) {
					++$errors;
					continue;
				}

				$input_type = isset( $raw_field['input_type'] ) ? sanitize_key( $raw_field['input_type'] ) : 'single_line';
				if ( ! in_array( $input_type, $this->allowed_input_types, true ) ) {
					$input_type = 'single_line';
				}

				$raw_meta = sanitize_key( $raw_field['input_meta'] );
				$raw_meta = substr( $raw_meta, 0, 32 );
				if ( empty( $raw_meta ) || in_array( $raw_meta, $this->reserved_keys, true ) ) {
					++$skipped;
					continue;
				}

				$is_existing   = $this->meta_key_exists( $table_name, $raw_meta );
				$is_system_key = in_array( $raw_meta, $default_system_keys, true );
				$final_meta    = $raw_meta;
				$action_mode   = 'insert';

				if ( $is_existing ) {
					if ( 'skip' === $conflict_mode ) {
						++$skipped;
						continue;
					} elseif ( 'overwrite' === $conflict_mode ) {
						$action_mode = 'update';
					} elseif ( 'rename' === $conflict_mode ) {
						if ( $is_system_key ) {
							++$skipped;
							continue;
						}
						$final_meta  = $this->generate_unique_meta_key( $raw_meta, $table_name, 'imported' );
						$action_mode = 'insert';
					}
				} elseif ( $is_system_key ) {
					++$skipped;
					continue;
				}

				$row_data = $this->sanitize_import_row( $raw_field, $final_meta, $input_type, $scope, $target_group );

				if ( 'update' === $action_mode ) {
					// Never overwrite core 'extra' flag on existing records.
					unset( $row_data['extra'] );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$res = $wpdb->update( $table_name, $row_data, array( 'input_meta' => $final_meta ) );
					if ( false !== $res ) {
						++$updated;
					} else {
						++$errors;
					}
				} else {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
					$res = $wpdb->insert( $table_name, $row_data );
					if ( false !== $res ) {
						++$created;
					} else {
						++$errors;
					}
				}
			}

			wp_send_json_success(
				array(
					'created' => $created,
					'updated' => $updated,
					'skipped' => $skipped,
					'errors'  => $errors,
					/* translators: 1: Created count, 2: Updated count, 3: Skipped count */
					'message' => sprintf( __( 'Import complete: %1$d created, %2$d updated, %3$d skipped.', 'frontend-dashboard' ), $created, $updated, $skipped ),
				)
			);
		}

		/**
		 * Sanitize a single imported field record into DB-ready format.
		 *
		 * @param array  $raw          Raw imported field array.
		 * @param string $input_meta   Validated/resolved input_meta key.
		 * @param string $input_type   Validated input type.
		 * @param string $scope        'profile' or 'post'.
		 * @param string $target_group 'preserve' or target menu/post_type slug.
		 * @return array
		 */
		private function sanitize_import_row( $raw, $input_meta, $input_type, $scope, $target_group ) {
			$user_role = array();
			if ( isset( $raw['user_role'] ) && is_array( $raw['user_role'] ) ) {
				$user_role = map_deep( $raw['user_role'], 'sanitize_text_field' );
			}

			$extended = array();
			if ( isset( $raw['extended'] ) && is_array( $raw['extended'] ) ) {
				$extended = map_deep( $raw['extended'], 'sanitize_text_field' );
			}

			$input_value = '';
			if ( isset( $raw['input_value'] ) ) {
				if ( is_array( $raw['input_value'] ) ) {
					$input_value = serialize( map_deep( $raw['input_value'], 'sanitize_text_field' ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				} elseif ( 'label' === $input_type ) {
					$input_value = wp_kses_post( $raw['input_value'] );
				} else {
					$input_value = sanitize_textarea_field( $raw['input_value'] );
				}
			}

			$data = array(
				'input_meta'     => $input_meta,
				'label_name'     => sanitize_text_field( $raw['label_name'] ),
				'input_order'    => isset( $raw['input_order'] ) ? absint( $raw['input_order'] ) : 10,
				'is_required'    => ( isset( $raw['is_required'] ) && 'true' === (string) $raw['is_required'] ) ? 'true' : 'false',
				'input_type'     => $input_type,
				'placeholder'    => isset( $raw['placeholder'] ) ? sanitize_text_field( $raw['placeholder'] ) : '',
				'class_name'     => isset( $raw['class_name'] ) ? sanitize_text_field( $raw['class_name'] ) : '',
				'id_name'        => isset( $raw['id_name'] ) ? sanitize_text_field( $raw['id_name'] ) : '',
				'input_step'     => isset( $raw['input_step'] ) ? sanitize_text_field( $raw['input_step'] ) : '',
				'input_min'      => isset( $raw['input_min'] ) ? sanitize_text_field( $raw['input_min'] ) : '',
				'input_max'      => isset( $raw['input_max'] ) ? sanitize_text_field( $raw['input_max'] ) : '',
				'input_row'      => isset( $raw['input_row'] ) ? sanitize_text_field( $raw['input_row'] ) : '',
				'is_tooltip'     => ( isset( $raw['is_tooltip'] ) && 'yes' === $raw['is_tooltip'] ) ? 'yes' : 'no',
				'tooltip_title'  => isset( $raw['tooltip_title'] ) ? sanitize_text_field( $raw['tooltip_title'] ) : '',
				'tooltip_body'   => isset( $raw['tooltip_body'] ) ? sanitize_textarea_field( $raw['tooltip_body'] ) : '',
				'input_value'    => $input_value,
				'user_role'      => serialize( $user_role ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
				'input_location' => isset( $raw['input_location'] ) ? sanitize_text_field( $raw['input_location'] ) : '',
				'extended'       => serialize( $extended ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			);

			if ( 'profile' === $scope ) {
				$menu_slug                 = ( 'preserve' !== $target_group && ! empty( $target_group ) )
					? sanitize_key( $target_group )
					: ( isset( $raw['menu'] ) && ! empty( $raw['menu'] ) ? sanitize_key( $raw['menu'] ) : 'profile' );
				$data['menu']              = $menu_slug;
				$data['show_register']     = ( isset( $raw['show_register'] ) && 'Enable' === $raw['show_register'] ) ? 'Enable' : 'Disable';
				$data['show_dashboard']    = ( isset( $raw['show_dashboard'] ) && 'Enable' === $raw['show_dashboard'] ) ? 'Enable' : 'Disable';
				$data['show_user_profile'] = ( isset( $raw['show_user_profile'] ) && 'Disable' === $raw['show_user_profile'] ) ? 'Disable' : 'Enable';
				$data['extra']             = 'yes';
			} elseif ( 'post' === $scope ) {
				$pt_slug           = ( 'preserve' !== $target_group && ! empty( $target_group ) )
					? sanitize_key( $target_group )
					: ( isset( $raw['post_type'] ) && ! empty( $raw['post_type'] ) ? sanitize_key( $raw['post_type'] ) : 'post' );
				$data['post_type'] = $pt_slug;
			}

			return $data;
		}
	}

	new FED_Field_Operations();
}
