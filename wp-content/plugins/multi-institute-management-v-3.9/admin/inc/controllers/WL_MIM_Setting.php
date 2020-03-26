<?php
defined( 'ABSPATH' ) || die();

require_once( WL_MIM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_MIM_Helper.php' );
require_once( WL_MIM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_MIM_PaymentHelper.php' );

class WL_MIM_Setting {
	/* Save general setttings */
	public static function save_general_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-general-settings'], 'save-general-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		/* General settings - enquiry */
		$enquiry_form_title_enable = isset( $_POST['enquiry_form_title_enable'] ) ? boolval( sanitize_text_field( $_POST['enquiry_form_title_enable'] ) ) : 0;
		$enquiry_form_title        = isset( $_POST['enquiry_form_title'] ) ? sanitize_text_field( $_POST['enquiry_form_title'] ) : '';

		/* General settings - institute */
		$institute_logo        = ( isset( $_FILES['institute_logo'] ) && is_array( $_FILES['institute_logo'] ) ) ? $_FILES['institute_logo'] : null;
		$institute_logo_enable = isset( $_POST['institute_logo_enable'] ) ? boolval( sanitize_text_field( $_POST['institute_logo_enable'] ) ) : 0;
		$institute_name        = isset( $_POST['institute_name'] ) ? sanitize_text_field( $_POST['institute_name'] ) : '';
		$institute_address     = isset( $_POST['institute_address'] ) ? sanitize_textarea_field( $_POST['institute_address'] ) : '';
		$institute_center_code = isset( $_POST['institute_center_code'] ) ? sanitize_text_field( $_POST['institute_center_code'] ) : '';
		$institute_phone       = isset( $_POST['institute_phone'] ) ? sanitize_text_field( $_POST['institute_phone'] ) : '';
		$institute_email       = isset( $_POST['institute_email'] ) ? sanitize_email( $_POST['institute_email'] ) : '';

		/* General settings - enrollment prefix */
		$enrollment_prefix_value = isset( $_POST['enrollment_id_prefix'] ) ? sanitize_text_field( $_POST['enrollment_id_prefix'] ) : '';

		/* General settings - receipt prefix */
		$receipt_prefix_value = isset( $_POST['receipt_number_prefix'] ) ? sanitize_text_field( $_POST['receipt_number_prefix'] ) : '';

		/* General settings - enable roll number */
		$enable_roll_number_value = isset( $_POST['enable_roll_number'] ) ? (bool) ( $_POST['enable_roll_number'] ) : false;

		/* General settings - enable signature in admission detail */
		$enable_signature_in_admission_detail_value = isset( $_POST['enable_signature_admission'] ) ? (bool) ( $_POST['enable_signature_admission'] ) : false;

		/* Validations */
		$errors = array();

		if ( ! empty( $institute_logo ) ) {
			$file_name          = sanitize_file_name( $institute_logo['name'] );
			$file_type          = $institute_logo['type'];
			$allowed_file_types = WL_MIM_Helper::get_image_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				$errors['institute_logo'] = esc_html__( 'Please provide logo in JPG, JPEG or PNG format.', WL_MIM_DOMAIN );
			}
		}

		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				/* General settings - enquiry */
				$enquiry = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'general_enquiry'" );

				$enquiry_data = array(
					'enquiry_form_title_enable' => $enquiry_form_title_enable,
					'enquiry_form_title'        => $enquiry_form_title
				);

				if ( ! $enquiry ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'general_enquiry',
						'mim_value'    => serialize( $enquiry_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $enquiry_data )
					), array(
						'id'           => $enquiry->id,
						'institute_id' => $institute_id
					) );
				}

				/* General settings - institute */
				$institute = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'general_institute'" );

				if ( ! empty( $institute_logo ) ) {
					$institute_logo = media_handle_upload( 'institute_logo', 0 );
					if ( is_wp_error( $institute_logo ) ) {
						throw new Exception( esc_html__( $institute_logo->get_error_message(), WL_MIM_DOMAIN ) );
					}
				}

				$institute_data = array(
					'institute_logo'        => $institute_logo,
					'institute_logo_enable' => $institute_logo_enable,
					'institute_name'        => $institute_name,
					'institute_address'     => $institute_address,
					'institute_center_code' => $institute_center_code,
					'institute_phone'       => $institute_phone,
					'institute_email'       => $institute_email
				);

				if ( ! $institute ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'general_institute',
						'mim_value'    => serialize( $institute_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$institute_value = unserialize( $institute->mim_value );
					if ( isset( $institute_value['institute_logo'] ) && ! empty( $institute_value['institute_logo'] ) ) {
						if ( ! $institute_logo ) {
							$institute_data['institute_logo'] = $institute_value['institute_logo'];
						} else {
							$institute_logo_delete_id = $institute_value['institute_logo'];
						}
					}
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $institute_data )
					), array(
						'id'           => $institute->id,
						'institute_id' => $institute_id
					) );
					if ( isset( $institute_logo_delete_id ) ) {
						wp_delete_attachment( $institute_logo_delete_id, true );
					}
				}

				/* General settings - enrollment prefix */
				$enrollment_prefix = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'general_enrollment_prefix'" );

				if ( ! $enrollment_prefix ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'general_enrollment_prefix',
						'mim_value'    => $enrollment_prefix_value,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $enrollment_prefix_value
					), array(
						'id'           => $enrollment_prefix->id,
						'institute_id' => $institute_id
					) );
				}

				/* General settings - receipt prefix */
				$receipt_prefix = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'general_receipt_prefix'" );

				if ( ! $receipt_prefix ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'general_receipt_prefix',
						'mim_value'    => $receipt_prefix_value,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $receipt_prefix_value
					), array(
						'id'           => $receipt_prefix->id,
						'institute_id' => $institute_id
					) );
				}

				/* General settings - enable roll number */
				$enable_roll_number = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'general_enable_roll_number'" );

				if ( ! $enable_roll_number ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'general_enable_roll_number',
						'mim_value'    => $enable_roll_number_value,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $enable_roll_number_value
					), array(
						'id'           => $enable_roll_number->id,
						'institute_id' => $institute_id
					) );
				}

				/* General settings - enable signature in admission detail */
				$enable_signature_in_admission_detail = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'general_enable_signature_in_admission_detail'" );

				if ( ! $enable_signature_in_admission_detail ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'general_enable_signature_in_admission_detail',
						'mim_value'    => $enable_signature_in_admission_detail_value,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $enable_signature_in_admission_detail_value
					), array(
						'id'           => $enable_signature_in_admission_detail->id,
						'institute_id' => $institute_id
					) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array(
					'message' => esc_html__( 'General settings updated successfully.', WL_MIM_DOMAIN ),
					'reload'  => true
				) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Save email setttings */
	public static function save_email_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-email-settings'], 'save-email-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		/* Email settings */
		$email_host       = isset( $_POST['email_host'] ) ? sanitize_text_field( $_POST['email_host'] ) : '';
		$email_username   = isset( $_POST['email_username'] ) ? sanitize_text_field( $_POST['email_username'] ) : '';
		$email_password   = isset( $_POST['email_password'] ) ? sanitize_text_field( $_POST['email_password'] ) : '';
		$email_encryption = isset( $_POST['email_encryption'] ) ? sanitize_text_field( $_POST['email_encryption'] ) : '';
		$email_port       = isset( $_POST['email_port'] ) ? sanitize_text_field( $_POST['email_port'] ) : '';
		$email_from       = isset( $_POST['email_from'] ) ? sanitize_text_field( $_POST['email_from'] ) : '';

		/* Validations */
		$errors = array();

		if ( $email_port && ! is_numeric( $email_port ) ) {
			$errors['email_port'] = esc_html__( 'Please provide a valid port.', WL_MIM_DOMAIN );
		}
		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				/* Email settings */
				$email = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'email'" );

				$email_data = array(
					'email_host'       => $email_host,
					'email_username'   => $email_username,
					'email_password'   => $email_password,
					'email_encryption' => $email_encryption,
					'email_port'       => $email_port,
					'email_from'       => $email_from
				);

				if ( ! $email ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'email',
						'mim_value'    => serialize( $email_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $email_data )
					), array(
						'id'           => $email->id,
						'institute_id' => $institute_id
					) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Email settings updated successfully.', WL_MIM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Save sms setttings */
	public static function save_sms_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-sms-settings'], 'save-sms-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		/* SMS settings */
		$sms_admin_number = isset( $_POST['sms_admin_number'] ) ? sanitize_text_field( $_POST['sms_admin_number'] ) : '';
		$sms_provider     = isset( $_POST['sms_provider'] ) ? sanitize_text_field( $_POST['sms_provider'] ) : '';

		/* SMS nexmo settings */
		$sms_nexmo_api_key    = isset( $_POST['sms_nexmo_api_key'] ) ? sanitize_text_field( $_POST['sms_nexmo_api_key'] ) : '';
		$sms_nexmo_api_secret = isset( $_POST['sms_nexmo_api_secret'] ) ? sanitize_text_field( $_POST['sms_nexmo_api_secret'] ) : '';
		$sms_nexmo_from       = isset( $_POST['sms_nexmo_from'] ) ? sanitize_text_field( $_POST['sms_nexmo_from'] ) : '';

		/* SMS striker settings */
		$sms_striker_username  = isset( $_POST['sms_striker_username'] ) ? sanitize_text_field( $_POST['sms_striker_username'] ) : '';
		$sms_striker_password  = isset( $_POST['sms_striker_password'] ) ? sanitize_text_field( $_POST['sms_striker_password'] ) : '';
		$sms_striker_sender_id = isset( $_POST['sms_striker_sender_id'] ) ? sanitize_text_field( $_POST['sms_striker_sender_id'] ) : '';

		/* SMS pointsms settings */
		$sms_pointsms_username  = isset( $_POST['sms_pointsms_username'] ) ? sanitize_text_field( $_POST['sms_pointsms_username'] ) : '';
		$sms_pointsms_password  = isset( $_POST['sms_pointsms_password'] ) ? sanitize_text_field( $_POST['sms_pointsms_password'] ) : '';
		$sms_pointsms_sender_id = isset( $_POST['sms_pointsms_sender_id'] ) ? sanitize_text_field( $_POST['sms_pointsms_sender_id'] ) : '';

		/* SMS msgclub settings */
		$sms_msgclub_auth_key     = isset( $_POST['sms_msgclub_auth_key'] ) ? sanitize_text_field( $_POST['sms_msgclub_auth_key'] ) : '';
		$sms_msgclub_sender_id    = isset( $_POST['sms_msgclub_sender_id'] ) ? sanitize_text_field( $_POST['sms_msgclub_sender_id'] ) : '';
		$sms_msgclub_route_id     = isset( $_POST['sms_msgclub_route_id'] ) ? sanitize_text_field( $_POST['sms_msgclub_route_id'] ) : '';
		$sms_msgclub_content_type = isset( $_POST['sms_msgclub_content_type'] ) ? sanitize_text_field( $_POST['sms_msgclub_content_type'] ) : '';

		/* SMS textlocal settings */
		$sms_textlocal_api_key = isset( $_POST['sms_textlocal_api_key'] ) ? sanitize_text_field( $_POST['sms_textlocal_api_key'] ) : '';
		$sms_textlocal_sender  = isset( $_POST['sms_textlocal_sender'] ) ? sanitize_text_field( $_POST['sms_textlocal_sender'] ) : '';

		/* SMS ebulksms settings */
		$sms_ebulksms_username = isset( $_POST['sms_ebulksms_username'] ) ? sanitize_text_field( $_POST['sms_ebulksms_username'] ) : '';
		$sms_ebulksms_api_key  = isset( $_POST['sms_ebulksms_api_key'] ) ? sanitize_text_field( $_POST['sms_ebulksms_api_key'] ) : '';
		$sms_ebulksms_sender   = isset( $_POST['sms_ebulksms_sender'] ) ? sanitize_text_field( $_POST['sms_ebulksms_sender'] ) : '';

		/* SMS template settings: enquiry received */
		$sms_enquiry_received_enable  = isset( $_POST['sms_enquiry_received_enable'] ) ? boolval( sanitize_text_field( $_POST['sms_enquiry_received_enable'] ) ) : 0;
		$sms_enquiry_received_message = isset( $_POST['sms_enquiry_received_message'] ) ? sanitize_text_field( $_POST['sms_enquiry_received_message'] ) : '';

		/* SMS template settings: enquiry received to admin */
		$sms_enquiry_received_to_admin_enable  = isset( $_POST['sms_enquiry_received_to_admin_enable'] ) ? boolval( sanitize_text_field( $_POST['sms_enquiry_received_to_admin_enable'] ) ) : 0;
		$sms_enquiry_received_to_admin_message = isset( $_POST['sms_enquiry_received_to_admin_message'] ) ? sanitize_text_field( $_POST['sms_enquiry_received_to_admin_message'] ) : '';

		/* SMS template settings: student registered */
		$sms_student_registered_enable  = isset( $_POST['sms_student_registered_enable'] ) ? boolval( sanitize_text_field( $_POST['sms_student_registered_enable'] ) ) : 0;
		$sms_student_registered_message = isset( $_POST['sms_student_registered_message'] ) ? sanitize_text_field( $_POST['sms_student_registered_message'] ) : '';

		/* SMS template settings: fees submitted */
		$sms_fees_submitted_enable  = isset( $_POST['sms_fees_submitted_enable'] ) ? boolval( sanitize_text_field( $_POST['sms_fees_submitted_enable'] ) ) : 0;
		$sms_fees_submitted_message = isset( $_POST['sms_fees_submitted_message'] ) ? sanitize_text_field( $_POST['sms_fees_submitted_message'] ) : '';

		/* SMS template settings: student birthday */
		$sms_student_birthday_enable  = isset( $_POST['sms_student_birthday_enable'] ) ? boolval( sanitize_text_field( $_POST['sms_student_birthday_enable'] ) ) : 0;
		$sms_student_birthday_message = isset( $_POST['sms_student_birthday_message'] ) ? sanitize_text_field( $_POST['sms_student_birthday_message'] ) : '';

		/* Validations */
		$errors = array();

		if ( ! in_array( $sms_provider, array_keys( WL_MIM_Helper::get_sms_providers() ) ) ) {
			$errors['sms_provider'] = esc_html__( 'Please select sms provider.', WL_MIM_DOMAIN );
		}
		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				/* SMS settings */
				$sms = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms'" );

				$sms_data = array(
					'sms_provider'     => $sms_provider,
					'sms_admin_number' => $sms_admin_number
				);

				if ( ! $sms ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms',
						'mim_value'    => serialize( $sms_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_data )
					), array(
						'id'           => $sms->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS nexmo settings */
				$sms_nexmo = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_nexmo'" );

				$sms_nexmo_data = array(
					'api_key'    => $sms_nexmo_api_key,
					'api_secret' => $sms_nexmo_api_secret,
					'from'       => $sms_nexmo_from
				);

				if ( ! $sms_nexmo ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_nexmo',
						'mim_value'    => serialize( $sms_nexmo_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_nexmo_data )
					), array(
						'id'           => $sms_nexmo->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS striker settings */
				$sms_striker = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_striker'" );

				$sms_striker_data = array(
					'username'  => $sms_striker_username,
					'password'  => $sms_striker_password,
					'sender_id' => $sms_striker_sender_id
				);

				if ( ! $sms_striker ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_striker',
						'mim_value'    => serialize( $sms_striker_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_striker_data )
					), array(
						'id'           => $sms_striker->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS pointsms settings */
				$sms_pointsms = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_pointsms'" );

				$sms_pointsms_data = array(
					'username'  => $sms_pointsms_username,
					'password'  => $sms_pointsms_password,
					'sender_id' => $sms_pointsms_sender_id
				);

				if ( ! $sms_pointsms ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_pointsms',
						'mim_value'    => serialize( $sms_pointsms_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_pointsms_data )
					), array(
						'id'           => $sms_pointsms->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS msgclub settings */
				$sms_msgclub = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_msgclub'" );

				$sms_msgclub_data = array(
					'auth_key'     => $sms_msgclub_auth_key,
					'sender_id'    => $sms_msgclub_sender_id,
					'route_id'     => $sms_msgclub_route_id,
					'content_type' => $sms_msgclub_content_type,
				);

				if ( ! $sms_msgclub ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_msgclub',
						'mim_value'    => serialize( $sms_msgclub_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_msgclub_data )
					), array(
						'id'           => $sms_msgclub->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS textlocal settings */
				$sms_textlocal = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_textlocal'" );

				$sms_textlocal_data = array(
					'api_key' => $sms_textlocal_api_key,
					'sender'  => $sms_textlocal_sender
				);

				if ( ! $sms_textlocal ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_textlocal',
						'mim_value'    => serialize( $sms_textlocal_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_textlocal_data )
					), array(
						'id'           => $sms_textlocal->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS ebulksms settings */
				$sms_ebulksms = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_ebulksms'" );

				$sms_ebulksms_data = array(
					'username' => $sms_ebulksms_username,
					'api_key'  => $sms_ebulksms_api_key,
					'sender'   => $sms_ebulksms_sender
				);

				if ( ! $sms_ebulksms ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_ebulksms',
						'mim_value'    => serialize( $sms_ebulksms_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_ebulksms_data )
					), array(
						'id'           => $sms_ebulksms->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS template settings: enquiry received */
				$sms_template_enquiry_received = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_template_enquiry_received'" );

				$sms_template_enquiry_received_data = array(
					'enable'  => $sms_enquiry_received_enable,
					'message' => $sms_enquiry_received_message
				);

				if ( ! $sms_template_enquiry_received ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_template_enquiry_received',
						'mim_value'    => serialize( $sms_template_enquiry_received_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_template_enquiry_received_data )
					), array(
						'id'           => $sms_template_enquiry_received->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS template settings: enquiry received to admin */
				$sms_template_enquiry_received_to_admin = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_template_enquiry_received_to_admin'" );

				$sms_template_enquiry_received_to_admin_data = array(
					'enable'  => $sms_enquiry_received_to_admin_enable,
					'message' => $sms_enquiry_received_to_admin_message
				);

				if ( ! $sms_template_enquiry_received_to_admin ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_template_enquiry_received_to_admin',
						'mim_value'    => serialize( $sms_template_enquiry_received_to_admin_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_template_enquiry_received_to_admin_data )
					), array(
						'id'           => $sms_template_enquiry_received_to_admin->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS template settings: student registered */
				$sms_template_student_registered = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_template_student_registered'" );

				$sms_template_student_registered_data = array(
					'enable'  => $sms_student_registered_enable,
					'message' => $sms_student_registered_message
				);

				if ( ! $sms_template_student_registered ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_template_student_registered',
						'mim_value'    => serialize( $sms_template_student_registered_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_template_student_registered_data )
					), array(
						'id'           => $sms_template_student_registered->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS template settings: fees submitted */
				$sms_template_fees_submitted = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_template_fees_submitted'" );

				$sms_template_fees_submitted_data = array(
					'enable'  => $sms_fees_submitted_enable,
					'message' => $sms_fees_submitted_message
				);

				if ( ! $sms_template_fees_submitted ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_template_fees_submitted',
						'mim_value'    => serialize( $sms_template_fees_submitted_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_template_fees_submitted_data )
					), array(
						'id'           => $sms_template_fees_submitted->id,
						'institute_id' => $institute_id
					) );
				}

				/* SMS template settings: student birthday */
				$sms_template_student_birthday = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'sms_template_student_birthday'" );

				$sms_template_student_birthday_data = array(
					'enable'  => $sms_student_birthday_enable,
					'message' => $sms_student_birthday_message
				);

				if ( ! $sms_template_student_birthday ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'sms_template_student_birthday',
						'mim_value'    => serialize( $sms_template_student_birthday_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $sms_template_student_birthday_data )
					), array(
						'id'           => $sms_template_student_birthday->id,
						'institute_id' => $institute_id
					) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'SMS settings updated successfully.', WL_MIM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Save payment setttings */
	public static function save_payment_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-payment-settings'], 'save-payment-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		/* Payment settings */
		$payment_currency = isset( $_POST['payment_currency'] ) ? sanitize_text_field( $_POST['payment_currency'] ) : '';

		/* Payment settings - PayPal */
		$payment_paypal_enable         = isset( $_POST['payment_paypal_enable'] ) ? boolval( sanitize_text_field( $_POST['payment_paypal_enable'] ) ) : 0;
		$payment_paypal_mode           = isset( $_POST['payment_paypal_mode'] ) ? sanitize_text_field( $_POST['payment_paypal_mode'] ) : '';
		$payment_paypal_business_email = isset( $_POST['payment_paypal_business_email'] ) ? sanitize_text_field( $_POST['payment_paypal_business_email'] ) : '';

		/* Payment settings - Razorpay */
		$payment_razorpay_enable = isset( $_POST['payment_razorpay_enable'] ) ? boolval( sanitize_text_field( $_POST['payment_razorpay_enable'] ) ) : 0;
		$payment_razorpay_key    = isset( $_POST['payment_razorpay_key'] ) ? sanitize_text_field( $_POST['payment_razorpay_key'] ) : '';
		$payment_razorpay_secret = isset( $_POST['payment_razorpay_secret'] ) ? sanitize_text_field( $_POST['payment_razorpay_secret'] ) : '';

		/* Payment settings - Paystack */
		$payment_paystack_enable = isset( $_POST['payment_paystack_enable'] ) ? boolval( sanitize_text_field( $_POST['payment_paystack_enable'] ) ) : 0;
		$payment_paystack_key    = isset( $_POST['payment_paystack_key'] ) ? sanitize_text_field( $_POST['payment_paystack_key'] ) : '';
		$payment_paystack_secret = isset( $_POST['payment_paystack_secret'] ) ? sanitize_text_field( $_POST['payment_paystack_secret'] ) : '';

		/* Payment settings - Stripe */
		$payment_stripe_enable          = isset( $_POST['payment_stripe_enable'] ) ? boolval( sanitize_text_field( $_POST['payment_stripe_enable'] ) ) : 0;
		$payment_stripe_publishable_key = isset( $_POST['payment_stripe_publishable_key'] ) ? sanitize_text_field( $_POST['payment_stripe_publishable_key'] ) : '';
		$payment_stripe_secret_key      = isset( $_POST['payment_stripe_secret_key'] ) ? sanitize_text_field( $_POST['payment_stripe_secret_key'] ) : '';

		/* Validations */
		$errors = array();

		if ( ! in_array( $payment_currency, array_keys( WL_MIM_PaymentHelper::get_all_currencies() ) ) ) {
			$errors['payment_currency'] = esc_html__( 'Please select valid currency.', WL_MIM_DOMAIN );
		}
		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				/* Payment settings */
				$payment = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'payment'" );

				$payment_data = array(
					'payment_currency' => $payment_currency
				);

				if ( ! $payment ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'payment',
						'mim_value'    => serialize( $payment_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $payment_data )
					), array(
						'id'           => $payment->id,
						'institute_id' => $institute_id
					) );
				}

				/* Payment settings - PayPal */
				$payment_paypal = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'payment_paypal'" );

				$payment_paypal_data = array(
					'enable'         => $payment_paypal_enable,
					'mode'           => $payment_paypal_mode,
					'business_email' => $payment_paypal_business_email
				);

				if ( ! $payment_paypal ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'payment_paypal',
						'mim_value'    => serialize( $payment_paypal_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $payment_paypal_data )
					), array(
						'id'           => $payment_paypal->id,
						'institute_id' => $institute_id
					) );
				}

				/* Payment settings - Razorpay */
				$payment_razorpay = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'payment_razorpay'" );

				$payment_razorpay_data = array(
					'enable' => $payment_razorpay_enable,
					'key'    => $payment_razorpay_key,
					'secret' => $payment_razorpay_secret
				);

				if ( ! $payment_razorpay ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'payment_razorpay',
						'mim_value'    => serialize( $payment_razorpay_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $payment_razorpay_data )
					), array(
						'id'           => $payment_razorpay->id,
						'institute_id' => $institute_id
					) );
				}

				/* Payment settings - paystack */
				$payment_paystack = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'payment_paystack'" );

				$payment_paystack_data = array(
					'enable' => $payment_paystack_enable,
					'key'    => $payment_paystack_key,
					'secret' => $payment_paystack_secret
				);

				if ( ! $payment_paystack ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'payment_paystack',
						'mim_value'    => serialize( $payment_paystack_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $payment_paystack_data )
					), array(
						'id'           => $payment_paystack->id,
						'institute_id' => $institute_id
					) );
				}

				/* Payment settings - Stripe */
				$payment_stripe = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'payment_stripe'" );

				$payment_stripe_data = array(
					'enable'          => $payment_stripe_enable,
					'publishable_key' => $payment_stripe_publishable_key,
					'secret_key'      => $payment_stripe_secret_key
				);

				if ( ! $payment_stripe ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'payment_stripe',
						'mim_value'    => serialize( $payment_stripe_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $payment_stripe_data )
					), array(
						'id'           => $payment_stripe->id,
						'institute_id' => $institute_id
					) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array(
					'message' => esc_html__( 'Payment settings updated successfully.', WL_MIM_DOMAIN ),
					'reload'  => true
				) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Save admit card setttings */
	public static function save_admit_card_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-admit-card-settings'], 'save-admit-card-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		$admit_card_dob = isset( $_POST['admit_card_dob_enable'] ) ? (bool)( $_POST['admit_card_dob_enable'] ) : 0;

		$admit_card_signature        = ( isset( $_FILES['admit_card_signature'] ) && is_array( $_FILES['admit_card_signature'] ) ) ? $_FILES['admit_card_signature'] : null;
		$admit_card_signature_enable = isset( $_POST['admit_card_signature_enable'] ) ? boolval( sanitize_text_field( $_POST['admit_card_signature_enable'] ) ) : 0;

		/* Validations */
		$errors = array();

		if ( ! empty( $admit_card_signature ) ) {
			$file_name          = sanitize_file_name( $admit_card_signature['name'] );
			$file_type          = $admit_card_signature['type'];
			$allowed_file_types = WL_MIM_Helper::get_image_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				$errors['admit_card_signature'] = esc_html__( 'Please provide logo in JPG, JPEG or PNG format.', WL_MIM_DOMAIN );
			}
		}

		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				$admit_card_dob_enable = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'admit_card_dob_enable'" );

				if ( ! $admit_card_dob_enable ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'admit_card_dob_enable',
						'mim_value'    => $admit_card_dob,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $admit_card_dob
					), array(
						'id'           => $admit_card_dob_enable->id,
						'institute_id' => $institute_id
					) );
				}

				$admit_card = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'admit_card'" );

				if ( ! empty( $admit_card_signature ) ) {
					$admit_card_signature = media_handle_upload( 'admit_card_signature', 0 );
					if ( is_wp_error( $admit_card_signature ) ) {
						throw new Exception( esc_html__( $admit_card_signature->get_error_message(), WL_MIM_DOMAIN ) );
					}
				}

				$admit_card_data = array(
					'sign'        => $admit_card_signature,
					'sign_enable' => $admit_card_signature_enable,
				);

				if ( ! $admit_card ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'admit_card',
						'mim_value'    => serialize( $admit_card_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$admit_card_value = unserialize( $admit_card->mim_value );
					if ( isset( $admit_card_value['sign'] ) && ! empty( $admit_card_value['sign'] ) ) {
						if ( ! $admit_card_signature ) {
							$admit_card_data['sign'] = $admit_card_value['sign'];
						} else {
							$sign_delete_id = $admit_card_value['sign'];
						}
					}
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $admit_card_data )
					), array(
						'id'           => $admit_card->id,
						'institute_id' => $institute_id
					) );
					if ( isset( $sign_delete_id ) ) {
						wp_delete_attachment( $sign_delete_id, true );
					}
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array(
					'message' => esc_html__( 'Admit card settings updated successfully.', WL_MIM_DOMAIN ),
					'reload'  => true
				) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Save id card setttings */
	public static function save_id_card_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-id-card-settings'], 'save-id-card-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		$id_card_dob = isset( $_POST['id_card_dob_enable'] ) ? (bool)( $_POST['id_card_dob_enable'] ) : 0;

		$id_card_signature        = ( isset( $_FILES['id_card_signature'] ) && is_array( $_FILES['id_card_signature'] ) ) ? $_FILES['id_card_signature'] : null;
		$id_card_signature_enable = isset( $_POST['id_card_signature_enable'] ) ? boolval( sanitize_text_field( $_POST['id_card_signature_enable'] ) ) : 0;

		/* Validations */
		$errors = array();

		if ( ! empty( $id_card_signature ) ) {
			$file_name          = sanitize_file_name( $id_card_signature['name'] );
			$file_type          = $id_card_signature['type'];
			$allowed_file_types = WL_MIM_Helper::get_image_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				$errors['id_card_signature'] = esc_html__( 'Please provide logo in JPG, JPEG or PNG format.', WL_MIM_DOMAIN );
			}
		}

		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				/* General settings - id_card_dob_enable */
				$id_card_dob_enable = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'id_card_dob_enable'" );

				if ( ! $id_card_dob_enable ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'id_card_dob_enable',
						'mim_value'    => $id_card_dob,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $id_card_dob
					), array(
						'id'           => $id_card_dob_enable->id,
						'institute_id' => $institute_id
					) );
				}

				$id_card = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'id_card'" );

				if ( ! empty( $id_card_signature ) ) {
					$id_card_signature = media_handle_upload( 'id_card_signature', 0 );
					if ( is_wp_error( $id_card_signature ) ) {
						throw new Exception( esc_html__( $id_card_signature->get_error_message(), WL_MIM_DOMAIN ) );
					}
				}

				$id_card_data = array(
					'sign'        => $id_card_signature,
					'sign_enable' => $id_card_signature_enable,
				);

				if ( ! $id_card ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'id_card',
						'mim_value'    => serialize( $id_card_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$id_card_value = unserialize( $id_card->mim_value );
					if ( isset( $id_card_value['sign'] ) && ! empty( $id_card_value['sign'] ) ) {
						if ( ! $id_card_signature ) {
							$id_card_data['sign'] = $id_card_value['sign'];
						} else {
							$sign_delete_id = $id_card_value['sign'];
						}
					}
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $id_card_data )
					), array(
						'id'           => $id_card->id,
						'institute_id' => $institute_id
					) );
					if ( isset( $sign_delete_id ) ) {
						wp_delete_attachment( $sign_delete_id, true );
					}
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array(
					'message' => esc_html__( 'ID card settings updated successfully.', WL_MIM_DOMAIN ),
					'reload'  => true
				) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Save certificate setttings */
	public static function save_certificate_settings() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['save-certificate-settings'], 'save-certificate-settings' ) ) {
			die();
		}
		global $wpdb;
		$institute_id = WL_MIM_Helper::get_current_institute_id();

		$certificate_dob = isset( $_POST['certificate_dob_enable'] ) ? (bool)( $_POST['certificate_dob_enable'] ) : 0;

		$certificate_signature        = ( isset( $_FILES['certificate_signature'] ) && is_array( $_FILES['certificate_signature'] ) ) ? $_FILES['certificate_signature'] : null;
		$certificate_signature_enable = isset( $_POST['certificate_signature_enable'] ) ? boolval( sanitize_text_field( $_POST['certificate_signature_enable'] ) ) : 0;

		/* Validations */
		$errors = array();

		if ( ! empty( $certificate_signature ) ) {
			$file_name          = sanitize_file_name( $certificate_signature['name'] );
			$file_type          = $certificate_signature['type'];
			$allowed_file_types = WL_MIM_Helper::get_image_file_types();

			if ( ! in_array( $file_type, $allowed_file_types ) ) {
				$errors['certificate_signature'] = esc_html__( 'Please provide logo in JPG, JPEG or PNG format.', WL_MIM_DOMAIN );
			}
		}

		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				/* General settings - certificate_dob_enable */
				$certificate_dob_enable = $wpdb->get_row( "SELECT id FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'certificate_dob_enable'" );

				if ( ! $certificate_dob_enable ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'certificate_dob_enable',
						'mim_value'    => $certificate_dob,
						'institute_id' => $institute_id
					) );
				} else {
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => $certificate_dob
					), array(
						'id'           => $certificate_dob_enable->id,
						'institute_id' => $institute_id
					) );
				}

				$certificate = $wpdb->get_row( "SELECT id, mim_value FROM {$wpdb->prefix}wl_min_settings WHERE institute_id = $institute_id AND mim_key = 'certificate'" );

				if ( ! empty( $certificate_signature ) ) {
					$certificate_signature = media_handle_upload( 'certificate_signature', 0 );
					if ( is_wp_error( $certificate_signature ) ) {
						throw new Exception( esc_html__( $certificate_signature->get_error_message(), WL_MIM_DOMAIN ) );
					}
				}

				$certificate_data = array(
					'sign'        => $certificate_signature,
					'sign_enable' => $certificate_signature_enable,
				);

				if ( ! $certificate ) {
					$wpdb->insert( "{$wpdb->prefix}wl_min_settings", array(
						'mim_key'      => 'certificate',
						'mim_value'    => serialize( $certificate_data ),
						'institute_id' => $institute_id
					) );
				} else {
					$certificate_value = unserialize( $certificate->mim_value );
					if ( isset( $certificate_value['sign'] ) && ! empty( $certificate_value['sign'] ) ) {
						if ( ! $certificate_signature ) {
							$certificate_data['sign'] = $certificate_value['sign'];
						} else {
							$sign_delete_id = $certificate_value['sign'];
						}
					}
					$wpdb->update( "{$wpdb->prefix}wl_min_settings", array(
						'mim_value' => serialize( $certificate_data )
					), array(
						'id'           => $certificate->id,
						'institute_id' => $institute_id
					) );
					if ( isset( $sign_delete_id ) ) {
						wp_delete_attachment( $sign_delete_id, true );
					}
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array(
					'message' => esc_html__( 'Certificate settings updated successfully.', WL_MIM_DOMAIN ),
					'reload'  => true
				) );
			} catch ( Exception $exception ) {
				$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Register settings */
	public static function register_settings() {
		register_setting( 'wl_min_settings_group', 'multi_institute_enable_enquiry_form_title' );
		register_setting( 'wl_min_settings_group', 'multi_institute_enquiry_form_title' );
		register_setting( 'wl_min_settings_group', 'multi_institute_enable_university_header' );
		register_setting( 'wl_min_settings_group', 'multi_institute_university_logo', array( 'WL_MIM_Setting', 'handle_university_logo' ) );
		register_setting( 'wl_min_settings_group', 'multi_institute_university_name' );
		register_setting( 'wl_min_settings_group', 'multi_institute_university_address' );
		register_setting( 'wl_min_settings_group', 'multi_institute_university_phone' );
		register_setting( 'wl_min_settings_group', 'multi_institute_university_email' );
	}

	public static function handle_university_logo() {
		if( ! empty( $_FILES["multi_institute_university_logo"]["tmp_name"] ) ) {
		    $urls = wp_handle_upload( $_FILES["multi_institute_university_logo"], array( 'test_form' => FALSE ) );
		    $temp = $urls["url"];
		    return $temp;
		}
	  	return get_option( 'multi_institute_university_logo' );
	}

	/* Check permission to manage settings */
	private static function check_permission() {
		$institute_id = WL_MIM_Helper::get_current_institute_id();
		if ( ! current_user_can( 'wl_min_manage_settings' ) || ! $institute_id ) {
			die();
		}
	}
}
?>