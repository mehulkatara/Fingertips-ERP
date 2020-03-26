<?php
defined( 'ABSPATH' ) || die();

require_once( WL_MIM_PLUGIN_DIR_PATH . 'admin/WL_MIM_LM.php' );
require_once( WL_MIM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_MIM_SettingHelper.php' );
require_once( WL_MIM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_MIM_SMSHelper.php' );

class WL_MIM_Helper {
	public static $core_capability = 'manage_options';

	/* Get capabilities */
	public static function get_capabilities() {
		return array(
			'wl_min_manage_dashboard'      => esc_html__( 'Manage Dashboard', WL_MIM_DOMAIN ),
			'wl_min_manage_courses'        => esc_html__( 'Manage Courses', WL_MIM_DOMAIN ),
			'wl_min_manage_batches'        => esc_html__( 'Manage Batches', WL_MIM_DOMAIN ),
			'wl_min_manage_enquiries'      => esc_html__( 'Manage Enquiries', WL_MIM_DOMAIN ),
			'wl_min_manage_students'       => esc_html__( 'Manage Students', WL_MIM_DOMAIN ),
			'wl_min_manage_attendance'     => esc_html__( 'Manage Attendance', WL_MIM_DOMAIN ),
			'wl_min_manage_notes'          => esc_html__( 'Manage Notes', WL_MIM_DOMAIN ),
			'wl_min_manage_expense'        => esc_html__( 'Manage Expense', WL_MIM_DOMAIN ),
			'wl_min_manage_report'         => esc_html__( 'Manage Report', WL_MIM_DOMAIN ),
			'wl_min_manage_admit_cards'    => esc_html__( 'Manage Admit Cards', WL_MIM_DOMAIN ),
			'wl_min_manage_results'        => esc_html__( 'Manage Results', WL_MIM_DOMAIN ),
			'wl_min_manage_fees'           => esc_html__( 'Manage Fees', WL_MIM_DOMAIN ),
			'wl_min_manage_notifications'  => esc_html__( 'Manage Notifications', WL_MIM_DOMAIN ),
			'wl_min_manage_noticeboard'    => esc_html__( 'Manage Noticeboard', WL_MIM_DOMAIN ),
			'wl_min_manage_administrators' => esc_html__( 'Manage Administrators', WL_MIM_DOMAIN ),
			'wl_min_manage_settings'       => esc_html__( 'Manage Settings', WL_MIM_DOMAIN )
		);
	}

	/* Get student capability */
	public static function get_student_capability() {
		return 'wl_min_student';
	}

	/* Get multi institute capability */
	public static function get_multi_institute_capability() {
		return 'wl_min_multi_institute';
	}

	/* Assign custom capabilities to admin */
	public static function assign_capabilities() {
		$roles = get_editable_roles();
		foreach ( $GLOBALS['wp_roles']->role_objects as $key => $role ) {
			if ( isset( $roles[ $key ] ) && $role->has_cap( self::$core_capability ) ) {
				foreach ( self::get_capabilities() as $capability_key => $capability_value ) {
					$role->add_cap( $capability_key );
				}
				$role->add_cap( self::get_multi_institute_capability() );
			}
		}
	}

	/* Remove custom capabilities of admin */
	public static function remove_capabilities() {
		$roles = get_editable_roles();
		foreach ( $GLOBALS['wp_roles']->role_objects as $key => $role ) {
			if ( isset ( $roles[ $key ] ) && $role->has_cap( self::$core_capability ) ) {
				foreach ( self::get_capabilities() as $capability_key => $capability_value ) {
					$role->remove_cap( $capability_key );
				}
				$role->remove_cap( self::get_multi_institute_capability() );
			}
		}
	}

	/* Get duration in */
	public static function get_duration_in() {
		return array(
			esc_html__( 'Days', WL_MIM_DOMAIN ),
			esc_html__( 'Months', WL_MIM_DOMAIN ),
			esc_html__( 'Years', WL_MIM_DOMAIN )
		);
	}

	public static function get_period_in() {
		return array(
			'one-time' => esc_html__( 'One-time', WL_MIM_DOMAIN ),
			'monthly'  => esc_html__( 'Monthly', WL_MIM_DOMAIN )
		);
	}

	/* Get notification by list */
	public static function get_notification_by_list() {
		return array(
			'by-batch'               => esc_html__( 'By Batch', WL_MIM_DOMAIN ),
			'by-course'              => esc_html__( 'By Course', WL_MIM_DOMAIN ),
			'by-pending-fees'        => esc_html__( 'By Pending Fees', WL_MIM_DOMAIN ),
			'by-active-students'     => esc_html__( 'By Active Students', WL_MIM_DOMAIN ),
			'by-inactive-students'   => esc_html__( 'By Inactive Students', WL_MIM_DOMAIN ),
			'by-individual-students' => esc_html__( 'By Individual Student', WL_MIM_DOMAIN )
		);
	}

	/* Get overall report by list */
	public static function get_report_by_list() {
		return array(
			'student-registrations' => esc_html__( 'Student Registrations', WL_MIM_DOMAIN ),
			'current-students'      => esc_html__( 'Current Students', WL_MIM_DOMAIN ),
			'students-drop-out'     => esc_html__( 'Students Drop-out (Inactive)', WL_MIM_DOMAIN ),
			'fees-collection'       => esc_html__( 'Fees Collection', WL_MIM_DOMAIN ),
			'outstanding-fees'      => esc_html__( 'Outstanding Fees', WL_MIM_DOMAIN ),
			'pending-fees-by-batch' => esc_html__( 'Pending Fees By Batch', WL_MIM_DOMAIN ),
			'attendance-by-batch'   => esc_html__( 'Attendance By Batch', WL_MIM_DOMAIN ),
			'expense'               => esc_html__( 'Expense', WL_MIM_DOMAIN ),
			'enquiries'             => esc_html__( 'Enquiries', WL_MIM_DOMAIN )
		);
	}

	/* Get report period */
	public static function get_report_period() {
		return array(
			'today'      => esc_html__( 'Today', WL_MIM_DOMAIN ),
			'yesterday'  => esc_html__( 'Yesterday', WL_MIM_DOMAIN ),
			'this-week'  => esc_html__( 'This Week', WL_MIM_DOMAIN ),
			'this-month' => esc_html__( 'This Month', WL_MIM_DOMAIN ),
			'this-year'  => esc_html__( 'This Year', WL_MIM_DOMAIN ),
			'last-year'  => esc_html__( 'Last Year', WL_MIM_DOMAIN )
		);
	}

	/* Get number of course months */
	public static function get_course_months_count( $duration, $duration_in ) {
		$course_duration_in_month = 1;
		if ( $duration_in == 'Months' ) {
			$course_duration_in_month = intval( $duration );
		} elseif ( $duration_in == 'Days' ) {
			$course_duration_in_month = floor( $duration / 30 );
		} elseif ( $duration_in == 'Years' ) {
			$course_duration_in_month = intval( $duration * 12 );
		}
		if ( $course_duration_in_month < 1 ) {
			return 0;
		}
		return $course_duration_in_month;
	}

	/* Get batch months */
	public static function get_batch_months( $batch_start_date, $batch_end_date ) {
		$ts1 = strtotime( $batch_start_date );
		$ts2 = strtotime( $batch_end_date );

		$year1 = date( 'Y', $ts1 );
		$year2 = date( 'Y', $ts2 );

		$month1 = date( 'm', $ts1 );
		$month2 = date('m', $ts2 );

		$diff = ( ( $year2 - $year1 ) * 12 ) + ( $month2 - $month1 );
		return $diff;
	}

	/* Get active categories of institute */
	public static function get_active_categories_institute( $institute_id ) {
		global $wpdb;

		return $wpdb->get_results( "SELECT id, name FROM {$wpdb->prefix}wl_min_course_categories WHERE is_active = 1 AND institute_id = $institute_id ORDER BY name" );
	}

	/* Get active courses */
	public static function get_active_courses() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT id, course_name, fees, course_code FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ORDER BY course_name" );
	}

	/* Get courses */
	public static function get_courses( $institute_id = '' ) {
		global $wpdb;
		if ( ! $institute_id ) {
			$institute_id = self::get_current_institute_id();
		}

		return $wpdb->get_results( "SELECT id, course_name, fees, course_code, is_active FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND institute_id = $institute_id ORDER BY course_name" );
	}

	/* Get courses ids */
	public static function get_courses_ids( $institute_id = '' ) {
		global $wpdb;
		if ( ! $institute_id ) {
			$institute_id = self::get_current_institute_id();
		}

		return $wpdb->get_col( "SELECT id FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND institute_id = $institute_id ORDER BY course_name" );
	}

	/* Get active courses of institute */
	public static function get_active_courses_institute( $institute_id ) {
		global $wpdb;

		return $wpdb->get_results( "SELECT id, course_name, fees, course_code FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ORDER BY course_name", OBJECT_K );
	}

	/* Get active batches of institute */
	public static function get_active_batches_institute( $institute_id ) {
		global $wpdb;

		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_min_batches WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id" );
	}

	/* Get institutes */
	public static function get_institutes() {
		global $wpdb;

		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_min_institutes ORDER BY name" );
	}

	/* Get main courses */
	public static function get_main_courses() {
		global $wpdb;

		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_min_main_courses ORDER BY course_name" );
	}

	/* Get active institutes */
	public static function get_active_institutes() {
		global $wpdb;

		return $wpdb->get_results( "SELECT id, name FROM {$wpdb->prefix}wl_min_institutes WHERE is_active = '1' ORDER BY name" );
	}

	/* Get current institute id */
	public static function get_current_institute_id() {
		global $wpdb;
		$institute_id = get_user_meta( get_current_user_id(), 'wlim_institute_id', true );
		if ( $institute_id ) {
			if ( $institute_id_from_cache = wp_cache_get( 'mim_current_institute_id' ) ) {
				return $institute_id_from_cache;
			}
			$institute = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_institutes WHERE id = $institute_id" );
			if ( $institute ) {
				wp_cache_add( 'mim_current_institute_id', $institute->id );
				return $institute->id;
			}
		}

		return false;
	}

	/* Get institute registration number */
	public static function get_institute_registration_number( $institute_id ) {
		global $wpdb;
		$institute = $wpdb->get_row( "SELECT registration_number FROM {$wpdb->prefix}wl_min_institutes WHERE id = $institute_id" );
		if ( $institute ) {
			return $institute->registration_number;
		}

		return '';
	}

	/* Get current institute status */
	public static function get_current_institute_status() {
		global $wpdb;
		$institute_id = get_user_meta( get_current_user_id(), 'wlim_institute_id', true );
		if ( $institute_id ) {
			$institute = $wpdb->get_row( "SELECT is_active FROM {$wpdb->prefix}wl_min_institutes WHERE id = $institute_id" );
			if ( $institute ) {
				return $institute->is_active;
			}
		}

		return false;
	}

	/* Get current institute name */
	public static function get_current_institute_name() {
		global $wpdb;
		$institute_id = get_user_meta( get_current_user_id(), 'wlim_institute_id', true );
		$institute    = $wpdb->get_row( "SELECT id, name FROM {$wpdb->prefix}wl_min_institutes WHERE id = $institute_id" );
		if ( $institute ) {
			return $institute->name;
		}

		return false;
	}

	/* Get students */
	public static function get_students() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT id, first_name, last_name FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ORDER BY first_name, last_name, id DESC" );
	}

	/* Get active students */
	public static function get_active_students() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT id, first_name, last_name FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ORDER BY first_name, last_name, id DESC" );
	}

	/* Get custom fields */
	public static function get_active_custom_fields() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT field_name FROM {$wpdb->prefix}wl_min_custom_fields WHERE is_active = 1 AND institute_id = $institute_id" );
	}

	/* Get custom fields of institute */
	public static function get_active_custom_fields_institute( $institute_id ) {
		global $wpdb;

		return $wpdb->get_results( "SELECT field_name FROM {$wpdb->prefix}wl_min_custom_fields WHERE is_active = 1 AND institute_id = $institute_id" );
	}

	/* Get fee types */
	public static function get_active_fee_types() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT fee_type, amount, period FROM {$wpdb->prefix}wl_min_fee_types WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id" );
	}

	/* Get fees total */
	public static function get_fees_total( $fees ) {
		if ( count( $fees ) ) {
			$total = array_sum( $fees );

			return number_format( max( floatval( $total ), 0 ), 2, '.', '' );
		}

		return number_format( 0, 2, '.', '' );
	}

	/* Get batch status */
	public static function get_batch_status( $start_date, $end_date ) {
		if ( date( 'Y-m-d' ) < date( 'Y-m-d', strtotime( $start_date ) ) ) {
			return '<strong class="text-danger">' . esc_html__( 'To Be Started', WL_MIM_DOMAIN ) . '</strong>';
		} else {
			if ( self::is_current_batch( $start_date, $end_date ) ) {
				return '<strong class="text-primary">' . esc_html__( 'Current Batch', WL_MIM_DOMAIN ) . '</strong>';
			} else {
				return '<strong class="text-success">' . esc_html__( 'Batch Ended', WL_MIM_DOMAIN ) . '</strong>';
			}
		}
	}

	/* Is current batch */
	public static function is_current_batch( $start_date, $end_date ) {
		$today      = date( 'Y-m-d' );
		$start_date = date( 'Y-m-d', strtotime( $start_date ) );
		$end_date   = date( 'Y-m-d', strtotime( $end_date ) );

		if ( ( $today >= $start_date ) && ( $today < $end_date ) ) {
			return true;
		} else {
			return false;
		}
	}

	/* Is batch ended */
	public static function is_batch_ended( $start_date, $end_date ) {
		$today    = date( 'Y-m-d' );
		$end_date = date( 'Y-m-d', strtotime( $end_date ) );

		if ( $today >= $end_date ) {
			return true;
		} else {
			return false;
		}
	}

	/* Get payment methods */
	public static function get_payment_methods() {
		return array(
			'razorpay' => esc_html__( 'Razorpay', WL_MIM_DOMAIN ),
			'paystack' => esc_html__( 'Paystack', WL_MIM_DOMAIN ),
			'paypal'   => esc_html__( 'PayPal', WL_MIM_DOMAIN ),
			'stripe'   => esc_html__( 'Stripe', WL_MIM_DOMAIN )
		);
	}

	/* Get enquiry ID */
	public static function get_enquiry_id( $id ) {
		return "E" . ( $id + 10000 );
	}

	/* Get invoice number */
	public static function get_invoice( $id ) {
		return ( $id + 10000 );
	}

	/* Get valid gender data */
	public static function get_gender_data() {
		return array( 'male', 'female' );
	}

	/* Get sms providers */
	public static function get_sms_providers() {
		return array(
			'nexmo'      => esc_html__( 'Nexmo', WL_MIM_DOMAIN ),
			'smsstriker' => esc_html__( 'SMS Striker', WL_MIM_DOMAIN ),
			'pointsms'   => esc_html__( 'Infigo Point', WL_MIM_DOMAIN ),
			'msgclub'    => esc_html__( 'Infigo Msg', WL_MIM_DOMAIN ),
			'textlocal'  => esc_html__( 'Textlocal', WL_MIM_DOMAIN ),
			'ebulksms'   => esc_html__( 'EBulkSMS', WL_MIM_DOMAIN ),
		);
	}

	/* Get sms templates */
	public static function get_sms_templates() {
		return array(
			'enquiry_received'          => esc_html__( 'Enquiry received confirmation to inquisitor', WL_MIM_DOMAIN ),
			'enquiry_received_to_admin' => esc_html__( 'Enquiry received confirmation to admin', WL_MIM_DOMAIN ),
			'student_registered'        => esc_html__( 'Student registered confirmation to student', WL_MIM_DOMAIN ),
			'fees_submitted'            => esc_html__( 'Fees submitted confirmation to student', WL_MIM_DOMAIN ),
			'student_birthday'          => esc_html__( 'Birthday message to student', WL_MIM_DOMAIN ),
		);
	}

	/* Get valid enquiry action data */
	public static function get_enquiry_action_data() {
		return array( 'delete_enquiry', 'mark_enquiry_inactive' );
	}

	/* Get id_proof file types */
	public static function get_id_proof_file_types() {
		return array( 'image/jpg', 'image/jpeg', 'image/png', 'application/pdf' );
	}

	/* Get image file types */
	public static function get_image_file_types() {
		return array( 'image/jpg', 'image/jpeg', 'image/png' );
	}

	/* Get Notice attachment file types */
	public static function get_notice_attachment_file_types() {
		return array(
			'image/jpg',
			'image/jpeg',
			'image/png',
			'application/pdf',
			'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/x-rar-compressed',
			'application/octet-stream',
			'application/zip',
			'application/octet-stream',
			'application/x-zip-compressed',
			'multipart/x-zip',
			'video/x-flv',
			'video/mp4',
			'application/x-mpegURL',
			'video/MP2T',
			'video/3gpp',
			'video/quicktime',
			'video/x-msvideo',
			'video/x-ms-wmv'
		);
	}

	/* Get enrollment ID with prefix */
	public static function get_enrollment_id_with_prefix( $id, $prefix ) {
		if ( ! $prefix ) {
			$prefix = 'EN';
		}

		return $prefix . ( $id + 10000 );
	}

	/* Get student ID with prefix */
	public static function get_student_id_with_prefix( $enrollment_id, $prefix ) {
		if ( ! $prefix ) {
			$prefix = 'EN';
		}

		return intval( substr( $enrollment_id, strlen( $prefix ), strlen( $enrollment_id ) ) ) - 10000;
	}

	/* Get form number */
	public static function get_form_number( $id ) {
		return ( $id + 10000 );
	}

	/* Get certificate number */
	public static function get_certificate_number( $id ) {
		return ( $id + 10000 );
	}

	/* Get receipt number */
	public static function get_receipt( $id ) {
		$prefix = get_option( "institute_advanced_settings" )['receipt_number_prefix'];
		if ( ! $prefix ) {
			$prefix = 'R';
		}

		return $prefix . ( $id + 10000 );
	}

	/* Get receipt number with prefix */
	public static function get_receipt_with_prefix( $id, $prefix ) {
		if ( ! $prefix ) {
			$prefix = 'R';
		}

		return $prefix . ( $id + 10000 );
	}

	/* Get exams */
	public static function get_exams() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT id, exam_title, exam_code FROM {$wpdb->prefix}wl_min_exams WHERE is_deleted = 0 AND institute_id = $institute_id ORDER BY id DESC" );
	}

	/* Get published exams */
	public static function get_published_exams() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();

		return $wpdb->get_results( "SELECT id, exam_title, exam_code FROM {$wpdb->prefix}wl_min_exams WHERE is_deleted = 0 AND is_published = 1 AND institute_id = $institute_id ORDER BY id DESC" );
	}

	/* Get course */
	public static function get_course( $id ) {
		global $wpdb;
		$institute_id = self::get_current_institute_id();
		$id           = intval( sanitize_text_field( $id ) );
		$row          = $wpdb->get_row( "SELECT course_code, course_name FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND id = $id AND institute_id = $institute_id" );
		if ( ! $row ) {
			return null;
		}

		return $row;
	}

	/* Get batch */
	public static function get_batch( $id, $institute_id = '' ) {
		global $wpdb;
		if ( ! $institute_id ) {
			$institute_id = self::get_current_institute_id();
		}
		$id           = intval( sanitize_text_field( $id ) );
		$row          = $wpdb->get_row( "SELECT batch_code, batch_name, time_from, time_to, start_date, end_date FROM {$wpdb->prefix}wl_min_batches WHERE is_deleted = 0 AND id = $id AND institute_id = $institute_id" );
		if ( ! $row ) {
			return null;
		}

		return $row;
	}

	/* Send birthday messages */
	public static function send_birthday_messages() {
		global $wpdb;

		$institues = $wpdb->get_results( "SELECT id FROM {$wpdb->prefix}wl_min_institutes ORDER BY id DESC" );

		foreach ( $institues as $institute ) {
			$institute_id = $institute->id;

			/* Get SMS template */
			$sms_template_student_birthday = WL_MIM_SettingHelper::get_sms_template_student_birthday( $institute_id );

			/* Get SMS settings */
			$sms = WL_MIM_SettingHelper::get_sms_settings( $institute_id );

			if ( $sms_template_student_birthday['enable'] && ! empty( $sms_template_student_birthday['message'] ) ) {

				$data = $wpdb->get_results( "SELECT first_name, last_name, phone FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND institute_id = $institute_id AND MONTH(date_of_birth) = MONTH(NOW()) AND DAY(date_of_birth) = DAY(NOW())" );

				foreach ( $data as $row ) {
					$sms_message = $sms_template_student_birthday['message'];
					$sms_message = str_replace( '[FIRST_NAME]', $row->first_name, $sms_message );
					$sms_message = str_replace( '[LAST_NAME]', $row->last_name, $sms_message );
					/* Send SMS */
					WL_MIM_SMSHelper::send_sms( $sms, $institute_id, $sms_message, $row->phone );
				}
			}
		}
	}

	/* Get data for dashboard */
	public static function get_data() {
		global $wpdb;
		$institute_id = self::get_current_institute_id();
		$sql          = "SELECT
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND institute_id = $institute_id ) as courses, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_batches WHERE is_deleted = 0 AND institute_id = $institute_id ) as batches, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_enquiries WHERE is_deleted = 0 AND institute_id = $institute_id ) as enquiries, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND institute_id = $institute_id ) as students, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_installments WHERE is_deleted = 0 AND institute_id = $institute_id ) as installments, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_courses WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ) as courses_active, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_batches WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ) as batches_active, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_enquiries WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ) as enquiries_active, 
		  ( SELECT COUNT(*) FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id) as students_active";

		$count = $wpdb->get_row( $sql );

		$students              = $wpdb->get_results( "SELECT id, fees FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id" );
		$students_fees_paid    = 0;
		$students_fees_pending = 0;
		foreach ( $students as $student ) {
			$fees = unserialize( $student->fees );
			if ( self::get_fees_total( $fees['payable'] ) > self::get_fees_total( $fees['paid'] ) ) {
				$students_fees_pending ++;
			} else {
				$students_fees_paid ++;
			}
		}

		$count->students_fees_paid    = $students_fees_paid;
		$count->students_fees_pending = $students_fees_pending;

		$course_data = $wpdb->get_results( "SELECT id, course_name, course_code, is_deleted, is_active FROM {$wpdb->prefix}wl_min_courses WHERE institute_id = $institute_id ORDER BY course_name", OBJECT_K );

		$sql              = "SELECT id, course_id, created_at FROM {$wpdb->prefix}wl_min_enquiries WHERE is_deleted = 0 AND is_active = 1 AND institute_id = $institute_id ORDER BY id DESC LIMIT 5";
		$recent_enquiries = $wpdb->get_results( $sql );

		$sql                       = "SELECT course_id, COUNT( course_id ) as students FROM {$wpdb->prefix}wl_min_students WHERE is_deleted = 0 AND institute_id = $institute_id GROUP BY course_id ORDER BY COUNT( course_id ) DESC LIMIT 5";
		$popular_courses_enquiries = $wpdb->get_results( $sql );

		$installments = $wpdb->get_results( "SELECT id, fees FROM {$wpdb->prefix}wl_min_installments WHERE is_deleted = 0 AND institute_id = $institute_id" );
		$revenue      = 0;
		if ( count( $installments ) ) {
			foreach ( $installments as $installment ) {
				$fees    = unserialize( $installment->fees );
				$revenue += array_sum( $fees['paid'] );
			}
		}
		$revenue = number_format( max( floatval( $revenue ), 0 ), 2, '.', '' );

		return array(
			'count'                     => $count,
			'course_data'               => $course_data,
			'recent_enquiries'          => $recent_enquiries,
			'popular_courses_enquiries' => $popular_courses_enquiries,
			'revenue'                   => $revenue
		);
	}

	public static function lm_valid() {
		$wl_mim_lm = WL_MIM_LM::get_instance();
		$wl_mim_lm_val = $wl_mim_lm->is_valid();
		if ( isset( $wl_mim_lm_val ) && $wl_mim_lm_val ) {
			return true;
		}
		return false;
	}
}
