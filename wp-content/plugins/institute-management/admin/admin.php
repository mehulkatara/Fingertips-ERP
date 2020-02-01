<?php
defined( 'ABSPATH' ) or die();

require_once( 'WL_IM_Menu.php' );
require_once( 'inc/controllers/WL_IM_Setting.php' );
require_once( 'inc/controllers/WL_IM_Administrator.php' );
require_once( 'inc/controllers/WL_IM_Course.php' );
require_once( 'inc/controllers/WL_IM_Batch.php' );
require_once( 'inc/controllers/WL_IM_Enquiry.php' );
require_once( 'inc/controllers/WL_IM_Student.php' );
require_once( 'inc/controllers/WL_IM_Fee.php' );

/* Action for creating institute management menu pages */
add_action( 'admin_menu', array( 'WL_IM_Menu', 'create_menu' ) );

/* On admin init */
add_action( 'admin_init', array( 'WL_IM_Setting', 'register_settings' ) );

/* Actions for administrator */
add_action( 'wp_ajax_wl-im-get-administrator-data', array( 'WL_IM_Administrator', 'get_administrator_data' ) );
add_action( 'wp_ajax_wl-im-add-administrator', array( 'WL_IM_Administrator', 'add_administrator' ) );
add_action( 'wp_ajax_wl-im-fetch-administrator', array( 'WL_IM_Administrator', 'fetch_administrator' ) );
add_action( 'wp_ajax_wl-im-update-administrator', array( 'WL_IM_Administrator', 'update_administrator' ) );

/* Actions for course */
add_action( 'wp_ajax_wl-im-get-course-data', array( 'WL_IM_Course', 'get_course_data' ) );
add_action( 'wp_ajax_wl-im-add-course', array( 'WL_IM_Course', 'add_course' ) );
add_action( 'wp_ajax_wl-im-fetch-course', array( 'WL_IM_Course', 'fetch_course' ) );
add_action( 'wp_ajax_wl-im-update-course', array( 'WL_IM_Course', 'update_course' ) );
add_action( 'wp_ajax_wl-im-delete-course', array( 'WL_IM_Course', 'delete_course' ) );

/* Actions for batch */
add_action( 'wp_ajax_wl-im-get-batch-data', array( 'WL_IM_Batch', 'get_batch_data' ) );
add_action( 'wp_ajax_wl-im-add-batch', array( 'WL_IM_Batch', 'add_batch' ) );
add_action( 'wp_ajax_wl-im-fetch-batch', array( 'WL_IM_Batch', 'fetch_batch' ) );
add_action( 'wp_ajax_wl-im-update-batch', array( 'WL_IM_Batch', 'update_batch' ) );
add_action( 'wp_ajax_wl-im-delete-batch', array( 'WL_IM_Batch', 'delete_batch' ) );

/* Actions for enquiry */
add_action( 'wp_ajax_wl-im-get-enquiry-data', array( 'WL_IM_Enquiry', 'get_enquiry_data' ) );
add_action( 'wp_ajax_wl-im-add-enquiry', array( 'WL_IM_Enquiry', 'add_enquiry' ) );
add_action( 'wp_ajax_wl-im-fetch-enquiry', array( 'WL_IM_Enquiry', 'fetch_enquiry' ) );
add_action( 'wp_ajax_wl-im-update-enquiry', array( 'WL_IM_Enquiry', 'update_enquiry' ) );
add_action( 'wp_ajax_wl-im-delete-enquiry', array( 'WL_IM_Enquiry', 'delete_enquiry' ) );

/* Actions for student */
add_action( 'wp_ajax_wl-im-get-student-data', array( 'WL_IM_Student', 'get_student_data' ) );
add_action( 'wp_ajax_wl-im-add-student', array( 'WL_IM_Student', 'add_student' ) );
add_action( 'wp_ajax_wl-im-fetch-student', array( 'WL_IM_Student', 'fetch_student' ) );
add_action( 'wp_ajax_wl-im-update-student', array( 'WL_IM_Student', 'update_student' ) );
add_action( 'wp_ajax_wl-im-delete-student', array( 'WL_IM_Student', 'delete_student' ) );
add_action( 'wp_ajax_wl-im-add-student-fetch-course-batches', array( 'WL_IM_Student', 'fetch_course_batches' ) );
add_action( 'wp_ajax_wl-im-add-student-fetch-course-update-batches', array( 'WL_IM_Student', 'fetch_course_update_batches' ) );
add_action( 'wp_ajax_wl-im-add-student-fetch-enquiries', array( 'WL_IM_Student', 'fetch_enquiries' ) );
add_action( 'wp_ajax_wl-im-add-student-fetch-enquiry', array( 'WL_IM_Student', 'fetch_enquiry' ) );
add_action( 'wp_ajax_wl-im-add-student-fetch-fees-payable', array( 'WL_IM_Student', 'fetch_fees_payable' ) );
add_action( 'wp_ajax_wl-im-add-student-form', array( 'WL_IM_Student', 'add_student_form' ) );

/* Actions for fee */
add_action( 'wp_ajax_wl-im-get-installment-data', array( 'WL_IM_Fee', 'get_installment_data' ) );
add_action( 'wp_ajax_wl-im-add-installment', array( 'WL_IM_Fee', 'add_installment' ) );
add_action( 'wp_ajax_wl-im-fetch-installment', array( 'WL_IM_Fee', 'fetch_installment' ) );
add_action( 'wp_ajax_wl-im-update-installment', array( 'WL_IM_Fee', 'update_installment' ) );
add_action( 'wp_ajax_wl-im-delete-installment', array( 'WL_IM_Fee', 'delete_installment' ) );
add_action( 'wp_ajax_wl-im-add-installment-fetch-fees', array( 'WL_IM_Fee', 'fetch_fees' ) );
?>