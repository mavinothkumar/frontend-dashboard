<?php

namespace FED\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 *
 * Manages custom table creation, schema upgrades, and plugin activation.
 */
class Activator {

	/**
	 * Register upgrade check with HookLoader.
	 *
	 * @param \FED\Hooks\HookLoader $loader
	 */
	public function register_hooks( $loader ) {
		$loader->add_action( 'admin_init', $this, 'check_upgrade' );
	}

	/**
	 * Run upgrade routines if plugin version has changed.
	 */
	public function check_upgrade() {
		$new_version = defined( 'BC_FED_PLUGIN_VERSION' ) ? BC_FED_PLUGIN_VERSION : '3.0';
		$old_version = get_option( 'fed_plugin_version', '0' );

		if ( $old_version === $new_version ) {
			return;
		}

		$this->migrate_tables();

		if ( function_exists( 'fed_upgrade_actions' ) ) {
			fed_upgrade_actions( $new_version, $old_version );
		}

		update_option( 'fed_plugin_version', $new_version );
	}

	/**
	 * Execute DB schema migration using dbDelta.
	 */
	public function migrate_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$user_profile_table  = $wpdb->prefix . BC_FED_TABLE_USER_PROFILE;
		$menu_table          = $wpdb->prefix . BC_FED_TABLE_MENU;
		$menu_meta_table     = $wpdb->prefix . BC_FED_TABLE_MENU_META;
		$post_table          = $wpdb->prefix . BC_FED_TABLE_POST;
		$payment_table       = $wpdb->prefix . BC_FED_TABLE_PAYMENT;
		$payment_items_table = $wpdb->prefix . BC_FED_TABLE_PAYMENT_ITEMS;

		// User Profile Table (harmonized)
		$sql_user_profile = "CREATE TABLE `$user_profile_table` (
			id BIGINT(20) NOT NULL AUTO_INCREMENT,
			input_meta VARCHAR(255) NOT NULL,
			label_name VARCHAR(255) NULL,
			label VARCHAR(255) NULL,
			input_order INT(10) NULL,
			show_register VARCHAR(255) NOT NULL DEFAULT 'Disable',
			show_dashboard VARCHAR(255) NOT NULL DEFAULT 'Disable',
			show_user_profile VARCHAR(255) NOT NULL DEFAULT 'Enable',
			is_required VARCHAR(255) NOT NULL DEFAULT 'false',
			input_type VARCHAR(255) NULL,
			placeholder VARCHAR(255) NULL,
			class_name VARCHAR(255) NULL,
			id_name VARCHAR(255) NULL,
			input_step VARCHAR(255) NULL,
			input_min VARCHAR(255) NULL,
			input_max VARCHAR(255) NULL,
			input_row VARCHAR(255) NULL,
			is_tooltip VARCHAR(50) NOT NULL DEFAULT 'no',
			tooltip_title VARCHAR(255) NULL,
			tooltip_body VARCHAR(255) NULL,
			input_value LONGTEXT NULL,
			user_role LONGTEXT NULL,
			input_location VARCHAR(255) NULL,
			extra VARCHAR(255) NOT NULL DEFAULT 'yes',
			menu VARCHAR(255) NOT NULL DEFAULT 'profile',
			extended LONGTEXT NULL,
			options LONGTEXT NULL,
			status VARCHAR(255) NULL,
			PRIMARY KEY  (id),
			KEY input_meta (input_meta)
		) $charset_collate;";
		dbDelta( $sql_user_profile );

		// Menu Table (harmonized)
		$sql_menu = "CREATE TABLE `$menu_table` (
			id BIGINT(20) NOT NULL AUTO_INCREMENT,
			menu_slug VARCHAR(255) NOT NULL,
			menu VARCHAR(255) NULL,
			menu_name VARCHAR(255) NULL,
			menu_order INT(10) NULL,
			menu_image_id VARCHAR(255) NULL,
			menu_icon VARCHAR(255) NULL,
			show_user_profile VARCHAR(255) NULL,
			extra VARCHAR(255) NOT NULL DEFAULT 'yes',
			user_role LONGTEXT NULL,
			extended LONGTEXT NULL,
			parent_id VARCHAR(10) NOT NULL DEFAULT '0',
			menu_type VARCHAR(255) NULL,
			status VARCHAR(255) NULL,
			PRIMARY KEY  (id),
			KEY menu_slug (menu_slug)
		) $charset_collate;";
		dbDelta( $sql_menu );

		// Menu Meta Table
		$sql_menu_meta = "CREATE TABLE `$menu_meta_table` (
			meta_id BIGINT(20) NOT NULL AUTO_INCREMENT,
			menu_id BIGINT(20) NOT NULL,
			meta_key VARCHAR(255) NULL,
			meta_value LONGTEXT NULL,
			PRIMARY KEY  (meta_id),
			KEY menu_meta (menu_id, meta_key)
		) $charset_collate;";
		dbDelta( $sql_menu_meta );

		// Post Table
		$sql_post = "CREATE TABLE `$post_table` (
			id BIGINT(20) NOT NULL AUTO_INCREMENT,
			post_type VARCHAR(255) NULL,
			label VARCHAR(255) NULL,
			input_meta VARCHAR(255) NULL,
			input_type VARCHAR(255) NULL,
			input_order INT(10) NULL,
			placeholder VARCHAR(255) NULL,
			class_name VARCHAR(255) NULL,
			id_name VARCHAR(255) NULL,
			options LONGTEXT NULL,
			extra LONGTEXT NULL,
			is_required VARCHAR(255) NULL,
			status VARCHAR(255) NULL,
			PRIMARY KEY  (id),
			KEY post_meta (post_type, input_meta)
		) $charset_collate;";
		dbDelta( $sql_post );

		// Payment Table
		$sql_payment = "CREATE TABLE `$payment_table` (
			id BIGINT(20) NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) NULL,
			payment_type VARCHAR(255) NULL,
			payment_source VARCHAR(255) NULL,
			transaction_id VARCHAR(255) NULL,
			amount DECIMAL(10,2) NULL,
			currency VARCHAR(10) NULL,
			status VARCHAR(255) NULL,
			created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY user_trans (user_id, transaction_id)
		) $charset_collate;";
		dbDelta( $sql_payment );

		// Payment Items Table
		$sql_payment_items = "CREATE TABLE `$payment_items_table` (
			id BIGINT(20) NOT NULL AUTO_INCREMENT,
			payment_id BIGINT(20) NOT NULL,
			item_name VARCHAR(255) NULL,
			item_price DECIMAL(10,2) NULL,
			quantity INT(10) NOT NULL DEFAULT 1,
			PRIMARY KEY  (id),
			KEY payment_id (payment_id)
		) $charset_collate;";
		dbDelta( $sql_payment_items );
	}
}
