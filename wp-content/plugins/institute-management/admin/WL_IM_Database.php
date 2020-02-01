<?php
defined( 'ABSPATH' ) or die();

require_once( 'inc/helpers/WL_IM_Helper.php' );

class WL_IM_Database {
	/* On plugin activation */
	public static function activation() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/* Create courses table */
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_im_courses (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				course_code varchar(191) NOT NULL,
				course_name varchar(255) DEFAULT NULL,
				course_detail text DEFAULT NULL,
				duration int(11) UNSIGNED DEFAULT NULL,
				duration_in varchar(255) DEFAULT NULL,
				fees decimal(12,2) UNSIGNED DEFAULT NULL,
				is_active tinyint(1) NOT NULL DEFAULT '1',
				added_by bigint(20) UNSIGNED DEFAULT NULL,
				is_deleted tinyint(1) NOT NULL DEFAULT '0',
				created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at timestamp NULL DEFAULT NULL,
				deleted_at timestamp NULL DEFAULT NULL,
				PRIMARY KEY (id),
				UNIQUE (course_code, is_deleted, deleted_at),
				INDEX (added_by),
				FOREIGN KEY (added_by) REFERENCES {$wpdb->base_prefix}users (ID) ON DELETE SET NULL
				) $charset_collate";
		dbDelta( $sql );

		/* Create enquiries table */
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_im_enquiries (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				course_id bigint(20) UNSIGNED DEFAULT NULL,
				first_name varchar(255) NOT NULL,
				last_name varchar(255) DEFAULT NULL,
				phone varchar(255) DEFAULT NULL,
				email varchar(255) DEFAULT NULL,
				message text DEFAULT NULL,
				is_active tinyint(1) NOT NULL DEFAULT '1',
				added_by bigint(20) UNSIGNED DEFAULT NULL,
				is_deleted tinyint(1) NOT NULL DEFAULT '0',
				created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at timestamp NULL DEFAULT NULL,
				deleted_at timestamp NULL DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX (course_id),
				INDEX (added_by),
				FOREIGN KEY (course_id) REFERENCES {$wpdb->prefix}wl_im_courses (id) ON DELETE SET NULL,
				FOREIGN KEY (added_by) REFERENCES {$wpdb->base_prefix}users (ID) ON DELETE SET NULL
				) $charset_collate";
		dbDelta( $sql );

		/* Create students table */
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_im_students (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				course_id bigint(20) UNSIGNED DEFAULT NULL,
				first_name varchar(255) NOT NULL,
				last_name varchar(255) DEFAULT NULL,
				phone varchar(255) DEFAULT NULL,
				email varchar(255) DEFAULT NULL,
				address text DEFAULT NULL,
				city varchar(255) DEFAULT NULL,
				zip varchar(255) DEFAULT NULL,
				fees_payable decimal(12,2) UNSIGNED DEFAULT '0',
				fees_paid decimal(12,2) UNSIGNED DEFAULT '0',
				is_active tinyint(1) NOT NULL DEFAULT '1',
				added_by bigint(20) UNSIGNED DEFAULT NULL,
				course_completed tinyint(1) NOT NULL DEFAULT '0',
				completion_date timestamp NULL DEFAULT NULL,
				is_deleted tinyint(1) NOT NULL DEFAULT '0',
				created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at timestamp NULL DEFAULT NULL,
				deleted_at timestamp NULL DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX (course_id),
				INDEX (added_by),
				FOREIGN KEY (course_id) REFERENCES {$wpdb->prefix}wl_im_courses (id) ON DELETE SET NULL,
				FOREIGN KEY (added_by) REFERENCES {$wpdb->base_prefix}users (ID) ON DELETE SET NULL
				) $charset_collate";
		dbDelta( $sql );

		/* Create installments table */
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_im_installments (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				student_id bigint(20) UNSIGNED DEFAULT NULL,
				amount decimal(12,2) UNSIGNED DEFAULT NULL,
				added_by bigint(20) UNSIGNED DEFAULT NULL,
				is_deleted tinyint(1) NOT NULL DEFAULT '0',
				created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at timestamp NULL DEFAULT NULL,
				deleted_at timestamp NULL DEFAULT NULL,
				PRIMARY KEY (id),
				INDEX (student_id),
				INDEX (added_by),
				FOREIGN KEY (student_id) REFERENCES {$wpdb->prefix}wl_im_students (id) ON DELETE SET NULL,
				FOREIGN KEY (added_by) REFERENCES {$wpdb->base_prefix}users (ID) ON DELETE SET NULL
				) $charset_collate";
		dbDelta( $sql );

		/* Create batches table */
		$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}wl_im_batches (
				id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				course_id bigint(20) UNSIGNED DEFAULT NULL,
				batch_code varchar(191) NOT NULL,
				batch_name varchar(255) DEFAULT NULL,
				is_active tinyint(1) NOT NULL DEFAULT '1',
				added_by bigint(20) UNSIGNED DEFAULT NULL,
				is_deleted tinyint(1) NOT NULL DEFAULT '0',
				created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at timestamp NULL DEFAULT NULL,
				deleted_at timestamp NULL DEFAULT NULL,
				PRIMARY KEY (id),
				UNIQUE (batch_code, course_id, is_deleted, deleted_at),
				INDEX (course_id),
				INDEX (added_by),
				FOREIGN KEY (course_id) REFERENCES {$wpdb->prefix}wl_im_courses (id) ON DELETE SET NULL,
				FOREIGN KEY (added_by) REFERENCES {$wpdb->base_prefix}users (ID) ON DELETE SET NULL
				) $charset_collate";
		dbDelta( $sql );

		/* Add batch_id column if not exists to students table */
		$row = $wpdb->get_results( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '" . DB_NAME . "' AND TABLE_NAME = '{$wpdb->prefix}wl_im_students' AND COLUMN_NAME = 'batch_id'" );
		if ( empty( $row ) ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}wl_im_students ADD batch_id bigint(20) UNSIGNED DEFAULT NULL" );
			$wpdb->query( "CREATE INDEX batch_id ON {$wpdb->prefix}wl_im_students (batch_id)" );
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}wl_im_students ADD FOREIGN KEY (batch_id) REFERENCES {$wpdb->prefix}wl_im_batches (id) ON DELETE SET NULL" );
		}

		/* Assign custom capabilities to admin */
		WL_IM_Helper::assign_capabilities();
	}
}