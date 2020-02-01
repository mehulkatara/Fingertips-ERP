<?php
defined( 'ABSPATH' ) or die();

require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

class WL_IM_Enquiry {
	/* Get enquiry data to display on table */
	public static function get_enquiry_data() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$data        = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 ORDER BY id DESC" );
		$course_data = $wpdb->get_results( "SELECT id, course_name, course_code FROM {$wpdb->prefix}wl_im_courses ORDER BY course_name", OBJECT_K );

		if ( count( $data ) !== 0 ) {
			foreach ( $data as $row ) {
				$id         = $row->id;
				$enquiry_id = WL_IM_Helper::get_enquiry_id( $row->id );
				$first_name = $row->first_name ? $row->first_name : '-';
				$last_name  = $row->last_name ? $row->last_name : '-';
				$phone      = $row->phone ? $row->phone : '-';
				$email      = $row->email ? $row->email : '-';
				$is_acitve  = $row->is_active ? esc_html__( 'Yes', WL_IM_DOMAIN ) : esc_html__( 'No', WL_IM_DOMAIN );
				$added_by   = ( $user = get_userdata( $row->added_by ) ) ? $user->user_login : '-';
				$date       = date_format( date_create( $row->created_at ), "d-m-Y g:i A" );

				$course = '-';
				if ( $row->course_id && isset( $course_data[$row->course_id] ) ) {
					$course_name = $course_data[$row->course_id]->course_name;
					$course_code = $course_data[$row->course_id]->course_code;
					$course      = "$course_name ($course_code)";
				}

				$results["data"][] = array(
					$enquiry_id,
					$course,
					$first_name,
					$last_name,
					$phone,
					$email,
					$is_acitve,
					$added_by,
					$date,
					'<a class="mr-3" href="#update-enquiry" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-id="' . $id . '"><i class="fa fa-edit"></i></a> <a href="javascript:void(0)" delete-enquiry-security="' . wp_create_nonce( "delete-enquiry-$id" ) . '"delete-enquiry-id="' . $id . '" class="delete-enquiry"> <i class="fa fa-trash text-danger"></i></a>'
				);
			}
		} else {
			$results["data"] = [];
		}
		wp_send_json( $results );
	}

	/* Add new enquiry */
	public static function add_enquiry() {
		self::check_permission();
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
		$is_active  = isset( $_POST['is_active'] ) ? boolval( sanitize_text_field( $_POST['is_active'] ) ) : 0;

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
			$errors['course'] = esc_html__( 'Please select a valid course.', WL_IM_DOMAIN );
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
				    'is_active'  => $is_active,
				    'added_by'   => get_current_user_id()
				);

				$success = $wpdb->insert( "{$wpdb->prefix}wl_im_enquiries", $data );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

		  		$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Enquiry added successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Fetch enquiry to update */
	public static function fetch_enquiry() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id   = intval( sanitize_text_field( $_POST['id'] ) );
		$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 AND id = $id" );
		if ( ! $row ) {
			die();
		}
		$wlim_active_courses = WL_IM_Helper::get_active_courses();
		?>
		<form id="wlim-update-enquiry-form">
			<?php $nonce = wp_create_nonce( "update-enquiry-$id" ); ?>
		    <input type="hidden" name="update-enquiry-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($nonce); ?>">
			<div class="row" id="wlim-enquiry-enquiry_id">
				<div class="col">
					<label  class="col-form-label pb-0"><?php esc_html_e( 'Enquiry ID', WL_IM_DOMAIN ); ?>:</label>
					<div class="card mb-3 mt-2">
						<div class="card-block">
		    				<span class="text-dark"><?php echo WL_IM_Helper::get_enquiry_id( $row->id ); ?></span>
		  				</div>
					</div>
				</div>
			</div>
            <div class="form-group">
                <label for="wlim-enquiry-course_update" class="col-form-label"><?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
                <select name="course" class="form-control" id="wlim-enquiry-course_update">
                    <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
                <?php
                if ( count( $wlim_active_courses ) > 0 ) {
                    foreach ( $wlim_active_courses as $active_course ) {  ?>
                    <option value="<?php echo esc_attr($active_course->id); ?>"><?php echo esc_html( "$active_course->course_name ($active_course->course_code)" ); ?></option>
                <?php
                    }
                } ?>
                </select>
            </div>
			<div class="row">
				<div class="col-sm-6 form-group">
					<label for="wlim-enquiry-first_name_update" class="col-form-label"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?>:</label>
					<input name="first_name" type="text" class="form-control" id="wlim-enquiry-first_name_update" placeholder="<?php esc_attr_e( "First Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->first_name); ?>">
				</div>
				<div class="col-sm-6 form-group">
					<label for="wlim-enquiry-last_name_update" class="col-form-label"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?>:</label>
					<input name="last_name" type="text" class="form-control" id="wlim-enquiry-last_name_update" placeholder="<?php esc_attr_e( "Last Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->last_name); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="wlim-enquiry-phone_update" class="col-form-label"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?>:</label>
				<input name="phone" type="text" class="form-control" id="wlim-enquiry-phone_update" placeholder="<?php esc_attr_e( "Phone", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr( $row->phone ); ?>">
			</div>
			<div class="form-group">
				<label for="wlim-enquiry-email_update" class="col-form-label"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?>:</label>
				<input name="email" type="email" class="form-control" id="wlim-enquiry-email_update" placeholder="<?php esc_attr_e( "Email", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->email); ?>">
			</div>
			<div class="form-group">
				<label for="wlim-enquiry-message_update" class="col-form-label"><?php esc_html_e( 'Message', WL_IM_DOMAIN ); ?>:</label>
				<textarea name="message" class="form-control" rows="3" id="wlim-enquiry-message_update" placeholder="<?php esc_attr_e( "Message", WL_IM_DOMAIN ); ?>"><?php echo esc_html($row->message); ?></textarea>
			</div>
			<div class="form-check pl-0">
				<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-enquiry-is_active_update" <?php echo esc_html($row->is_active) ? "checked" : ""; ?>>
				<label class="form-check-label" for="wlim-enquiry-is_active_update">
				<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
				</label>
			</div>
			<input type="hidden" name="enquiry_id" value="<?php echo esc_attr($row->id); ?>">
		</form>
		<script>
			/* Select single option */
			jQuery('#wlim-enquiry-course_update').selectpicker({
				liveSearch: true
			});
			jQuery('#wlim-enquiry-course_update').selectpicker('val', '<?php echo esc_html($row->course_id); ?>');
		</script>
	<?php
		die();
	}

	/* Update enquiry */
	public static function update_enquiry() {
		self::check_permission();
		$id = intval( sanitize_text_field( $_POST['enquiry_id'] ) );
		if ( ! wp_verify_nonce( $_POST["update-enquiry-$id"], "update-enquiry-$id" ) ) {
			die();
		}
		global $wpdb;

		$course_id  = isset( $_POST['course'] ) ? intval( sanitize_text_field( $_POST['course'] ) ) : NULL;
		$first_name = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$last_name  = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$phone      = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		$email      = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
		$is_active  = isset( $_POST['is_active'] ) ? boolval( sanitize_text_field( $_POST['is_active'] ) ) : 0;

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
			$errors['course'] = esc_html__( 'Please select a valid course.', WL_IM_DOMAIN );
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
				    'is_active'  => $is_active,
				    'updated_at'    => date('Y-m-d H:i:s')
				);

				$success = $wpdb->update( "{$wpdb->prefix}wl_im_enquiries", $data, array( 'is_deleted' => 0, 'id' => $id ) );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Course updated successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Delete enquiry */
	public static function delete_enquiry() {
		$id = intval( sanitize_text_field( $_POST['id'] ) );
		if ( ! wp_verify_nonce( $_POST["delete-enquiry-$id"], "delete-enquiry-$id" ) ) {
			die();
		}
		global $wpdb;

		try {
			$wpdb->query( 'BEGIN;' );

			$success = $wpdb->update( "{$wpdb->prefix}wl_im_enquiries",
					array(
						'is_deleted' => 1,
						'deleted_at' => date('Y-m-d H:i:s')
					), array( 'is_deleted' => 0, 'id' => $id )
				);
			if ( ! $success ) {
	  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
			}

			$wpdb->query( 'COMMIT;' );
			wp_send_json_success( array( 'message' => esc_html__( 'Enquiry removed successfully.', WL_IM_DOMAIN ) ) );
		} catch ( Exception $exception ) {
	  		$wpdb->query( 'ROLLBACK;' );
			wp_send_json_error( $exception->getMessage() );
		}
	}

	/* Check permission to manage enquiry */
	private static function check_permission() {
		if ( ! current_user_can( 'wl_im_manage_enquiries' ) ) {
			die();
		}
	}
}