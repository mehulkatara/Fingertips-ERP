<?php
defined( 'ABSPATH' ) or die();

class WL_IM_Helper {
	public static $core_capability = 'update_core';
	/* Get capabilities */
	public static function get_capabilities() {
		return array(
			'wl_im_manage_dashboard'      => 'Manage Dashboard',
			'wl_im_manage_courses'        => 'Manage Courses',
			'wl_im_manage_batches'        => 'Manage Batches',
			'wl_im_manage_enquiries'      => 'Manage Enquiries',
			'wl_im_manage_students'       => 'Manage Students',
			'wl_im_manage_fees'           => 'Manage Fees',
			'wl_im_manage_administrators' => 'Manage Administrators',
			'wl_im_manage_settings'       => 'Manage Settings'
		);
	}

	/* Assign custom capabilities to admin */
	public static function assign_capabilities() {
		$roles = get_editable_roles();
		foreach ( $GLOBALS['wp_roles']->role_objects as $key => $role ) {
			if ( isset( $roles[$key] ) && $role->has_cap( self::$core_capability ) ) {
				foreach( self::get_capabilities() as $capability_key => $capability_value ) {
					$role->add_cap( $capability_key );
				}
			}
		}
	}

	/* Remove custom capabilities of admin */
	public static function remove_capabilities() {
		$roles = get_editable_roles();
		foreach ( $GLOBALS['wp_roles']->role_objects as $key => $role ) {
			if ( isset ( $roles[$key] ) && $role->has_cap( self::$core_capability ) ) {
				foreach( self::get_capabilities() as $capability_key => $capability_value ) {
					$role->remove_cap( $capability_key );
				}
			}
		}
	}

	/* Get duration in */
	public static function get_duration_in() {
		return array( 'Days', 'Months', 'Years' );
	}

	/* Get active courses */
	public static function get_active_courses() {
		global $wpdb;
		return $wpdb->get_results( "SELECT id, course_name, fees, course_code FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 ORDER BY course_name" );
	}

	/* Get active students */
	public static function get_active_students() {
		global $wpdb;
		return $wpdb->get_results( "SELECT id, first_name, last_name FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND is_active = 1 ORDER BY first_name, last_name, id DESC" );
	}

	/* Get enquiry ID */
	public static function get_enquiry_id( $id ) {
		return "E" . ( $id + 10000 );
	}

	/* Get enrollment ID */
	public static function get_enrollment_id( $id ) {
		return "EN" . ( $id + 10000 );
	}

	public static function get_receipt( $id ) {
		return "R" . ( $id + 10000 );
	}

	/* Get data for dashboard */
	public static function get_data() {
		global $wpdb;
		$sql = "SELECT
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 ) as courses, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 ) as batches, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 ) as enquiries, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 ) as students, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_installments WHERE is_deleted = 0 ) as installments, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 ) as courses_active, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND is_active = 1 ) as batches_active, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 AND is_active = 1 ) as enquiries_active, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND is_active = 1 AND course_completed = 0) as students_current, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND course_completed = 1 ) as students_former, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND course_completed = 0 AND is_active = 0 ) as students_discontinued, 
  		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND ( fees_payable - fees_paid > 0 ) ) as students_fees_pending, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND ( fees_payable - fees_paid <= 0 ) ) as students_fees_paid";
		$count = $wpdb->get_row( $sql );

		$course_data = $wpdb->get_results( "SELECT id, course_name, course_code, is_deleted, is_active FROM {$wpdb->prefix}wl_im_courses ORDER BY course_name", OBJECT_K );

		$sql = "SELECT id, course_id, created_at FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 AND is_active = 1 ORDER BY id DESC LIMIT 5";
		$recent_enquiries = $wpdb->get_results( $sql );

		$sql = "SELECT course_id, COUNT( course_id ) as students FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 GROUP BY course_id ORDER BY COUNT( course_id ) DESC LIMIT 5";
		$popular_courses_enquiries = $wpdb->get_results( $sql );

		$sql = "SELECT SUM(amount) as revenue FROM {$wpdb->prefix}wl_im_installments WHERE is_deleted = 0";
		$revenue = $wpdb->get_var( $sql );

		return array(
			'count'                     => $count,
			'course_data'               => $course_data,
			'recent_enquiries'          => $recent_enquiries,
			'popular_courses_enquiries' => $popular_courses_enquiries,
			'revenue'                   => $revenue
		);
	}
}
?>