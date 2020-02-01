<?php
defined( 'ABSPATH' ) or die();

require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

class WL_IM_Enquiry_Front {
	/* Add new enquiry */
	public static function add_enquiry() {
		if ( ! wp_verify_nonce( $_POST['add-enquiry'], 'add-enquiry' ) ) {
			die();
		}
		global $wpdb;

		$course_id  = isset( $_POST['course'] ) ? intval( sanitize_text_field( $_POST['course'] ) ) : NULL;
		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';

		$errors = [];
		if ( empty( $course_id ) ) {
			$errors['course'] = esc_html__( 'Please select a course.', WL_IM_DOMAIN );
		}

		if ( empty( $first_name ) ) {
			$errors['first_name'] = esc_html__( 'Please provide first name.', WL_IM_DOMAIN );
		}

		if ( strlen( $first_name ) > 255 ) {
			$errors['first_name'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( strlen( $last_name ) > 255 ) {
			$errors['last_name'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( empty( $phone ) ) {
			$errors['phone'] = esc_html__( 'Please provide phone number.', WL_IM_DOMAIN );
		}

		if ( strlen( $phone ) > 255 ) {
			$errors['phone'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( strlen( $email ) > 255 ) {
			$errors['email'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( ! empty( $email ) && ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			$errors['email'] = esc_html__( 'Please provide a valid email address.', WL_IM_DOMAIN );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $course_id" );

		if ( ! $count ) {
			$errors['course'] = esc_html__( 'Please select a valid course', WL_IM_DOMAIN );
		}

		if ( count( $errors ) < 1 ) {
			try {
			  	$wpdb->query( 'BEGIN;' );

				$data = array(
					'course_id'  => $course_id,
					'first_name' => $first_name,
				    'last_name'  => $last_name,
				    'phone'      => $phone,
				    'email'      => $email,
				    'message'    => $message,
				    'is_active'  => 1
				);

				$success = $wpdb->insert( "{$wpdb->prefix}wl_im_enquiries", $data );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

		  		$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Your enquiry has been received. Thank you.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}
}
?>