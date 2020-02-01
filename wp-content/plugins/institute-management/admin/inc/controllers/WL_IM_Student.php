<?php
defined( 'ABSPATH' ) or die();

require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

class WL_IM_Student {
	private static $enquiry_action_data = array( 'delete_enquiry', 'mark_enquiry_inactive' );

	/* Get student data to display on table */
	public static function get_student_data() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$data        = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 ORDER BY id DESC" );

		$course_data = $wpdb->get_results( "SELECT id, course_name, course_code, fees, duration, duration_in FROM {$wpdb->prefix}wl_im_courses ORDER BY course_name", OBJECT_K );

		$batch_data = $wpdb->get_results( "SELECT id, batch_code FROM {$wpdb->prefix}wl_im_batches ORDER BY id", OBJECT_K );

		if ( count( $data ) !== 0 ) {
			foreach ( $data as $row ) {
				$id               = $row->id;
				$enrollment_id    = WL_IM_Helper::get_enrollment_id( $id );
				$first_name       = $row->first_name ? $row->first_name : '-';
				$last_name        = $row->last_name ? $row->last_name : '-';
				$fees_payable     = $row->fees_payable;
				$fees_paid        = $row->fees_paid;
				$pending_fees     = number_format( $fees_payable - $fees_paid, 2, '.', '' );
				$phone            = $row->phone ? $row->phone : '-';
				$email            = $row->email ? $row->email : '-';
				$is_acitve        = $row->is_active ? esc_html__( 'Yes', WL_IM_DOMAIN ) : esc_html__( 'No', WL_IM_DOMAIN );
				$date             = date_format( date_create( $row->created_at ), "d-m-Y g:i A" );
				$added_by         = ( $user = get_userdata( $row->added_by ) ) ? $user->user_login : '-';
				$course_completed = $row->course_completed;
				$completion_date  = $row->course_completed ? date_format( date_create( $row->completion_date ), "d-m-Y g:i A" ) : '-';

				$course   = '-';
				$duration = '-';
				$batch    = '-';
				if ( $row->course_id && isset( $course_data[$row->course_id] ) ) {
					$course_name = $course_data[$row->course_id]->course_name;
					$course_code = $course_data[$row->course_id]->course_code;
					$course      = "$course_name ($course_code)";
					$duration    = $course_data[$row->course_id]->duration . " " . $course_data[$row->course_id]->duration_in;
					if ( $course_completed ) {
						$duration .= ' <strong class="text-primary">' . esc_html__( 'Completed', WL_IM_DOMAIN ) . '</strong>';
					}
				}

				if ( $row->batch_id && isset( $batch_data[$row->batch_id] ) ) {
					$batch = $batch_data[$row->batch_id]->batch_code;
				}

				if ( $pending_fees > 0 ) {
					$fees_status = '<span class="wlim-text-danger"><strong>' . esc_html__( 'Pending', WL_IM_DOMAIN ) . '</strong></span><br><strong>' . $pending_fees . '</strong>';
				} else {
					$fees_status = '<span class="text-success"><strong>' . esc_html__( 'Paid', WL_IM_DOMAIN ) . '</strong></span>';
				}

				$results["data"][] = array(
					$enrollment_id,
					$course,
					$batch,
					$duration,
					$first_name,
					$last_name,
					$fees_payable,
					$fees_paid,
					$fees_status,
					$phone,
					$email,
					$is_acitve,
					$added_by,
					$date,
					$completion_date,
					'<a class="mr-3" href="#update-student" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-id="' . $id . '"><i class="fa fa-edit"></i></a> <a href="javascript:void(0)" delete-student-security="' . wp_create_nonce( "delete-student-$id" ) . '"delete-student-id="' . $id . '" class="delete-student"> <i class="fa fa-trash text-danger"></i></a>'
				);
			}
		} else {
			$results["data"] = [];
		}
		wp_send_json( $results );
	}

	/* Add new student */
	public static function add_student() {		
		self::check_permission();
		if ( ! wp_verify_nonce( $_POST['add-student'], 'add-student' ) ) {
			die();
		}
		global $wpdb;

		$course_id      = isset( $_POST['course'] ) ? intval( sanitize_text_field( $_POST['course'] ) ) : NULL;
		$batch_id       = isset( $_POST['batch'] ) ? intval( sanitize_text_field( $_POST['batch'] ) ) : NULL;
		$first_name     = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$last_name      = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$fees_payable   = number_format( isset( $_POST['fees_payable'] ) ? max( floatval( sanitize_text_field( $_POST['fees_payable'] ) ), 0 ) : 0, 2, '.', '' );
		$fees_paid      = number_format( isset( $_POST['fees_paid'] ) ? max( floatval( sanitize_text_field( $_POST['fees_paid'] ) ), 0 ) : 0, 2, '.', '' );
		$phone          = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		$email          = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$address        = isset( $_POST['address'] ) ? sanitize_textarea_field( $_POST['address'] ) : '';
		$city           = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
		$zip            = isset( $_POST['zip'] ) ? sanitize_text_field( $_POST['zip'] ) : '';
		$is_active      = isset( $_POST['is_active'] ) ? boolval( sanitize_text_field( $_POST['is_active'] ) ) : 0;
		$enquiry        = isset( $_POST['enquiry'] ) ? intval( sanitize_text_field( $_POST['enquiry'] ) ) : NULL;
		$from_enquiry   = isset( $_POST['from_enquiry'] ) ? boolval( sanitize_text_field( $_POST['from_enquiry'] ) ) : 0;
		$enquiry_action = isset( $_POST['enquiry_action'] ) ? sanitize_text_field( $_POST['enquiry_action'] ) : '';

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

		if ( strlen( $city ) > 255 ) {
			$errors['city'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( strlen( $zip ) > 255 ) {
			$errors['zip'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( $fees_paid > $fees_payable ) {
			$errors['fees_paid'] = esc_html__( 'Amount paid exceeded payable amount.', WL_IM_DOMAIN );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $course_id" );

		if ( ! $count ) {
			$errors['course'] = esc_html__( 'Please select a valid course.', WL_IM_DOMAIN );
		}

		if ( ! empty( $batch_id ) ) {
			$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND is_active = 1 AND id = $batch_id AND course_id = $course_id" );

			if ( ! $count ) {
				$errors['batch'] = esc_html__( 'Please select a valid batch.', WL_IM_DOMAIN );
			}
		} else {
			$batch_id = NULL;
		}

		$valid_enquiry_action = false;
		if ( $from_enquiry ) {
			$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 AND is_active = 1 AND id = $enquiry" );

			if ( ! $count ) {
				$errors['enquiry'] = esc_html__( 'Please select a valid enquiry', WL_IM_DOMAIN );
			} else {
				if ( ! in_array( $enquiry_action, self::$enquiry_action_data ) ) {
		  			throw new Exception( esc_html__( 'Please select valid action to perform after adding student.', WL_IM_DOMAIN ) );
				} else {
					$valid_enquiry_action = true;
				}
			}
		}

		if ( count( $errors ) < 1 ) {
			try {
			  	$wpdb->query( 'BEGIN;' );

				$data = array(
					'course_id'    => $course_id,
					'batch_id'     => $batch_id,
					'first_name'   => $first_name,
				    'last_name'    => $last_name,
				    'fees_payable' => $fees_payable,
				    'fees_paid'    => $fees_paid,
				    'phone'        => $phone,
				    'email'        => $email,
				    'address'      => $address,
				    'city'         => $city,
				    'zip'          => $zip,
				    'is_active'    => $is_active,
				    'added_by'     => get_current_user_id()
				);

				$success = $wpdb->insert( "{$wpdb->prefix}wl_im_students", $data );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}
				$student_id = $wpdb->insert_id;

				if ( $fees_paid > 0 ) {
					$data = array(
						'amount'     => $fees_paid,
						'student_id' => $student_id,
					    'added_by'   => get_current_user_id()
					);

					$success = $wpdb->insert( "{$wpdb->prefix}wl_im_installments", $data );
					if ( ! $success ) {
			  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
					}
				}

				if ( $valid_enquiry_action ) {
					if ( $enquiry_action == self::$enquiry_action_data[1] ) {
						$success = $wpdb->update( "{$wpdb->prefix}wl_im_enquiries",
							array(
								'is_active' => 0,
								'updated_at' => date('Y-m-d H:i:s')
							), array( 'is_deleted' => 0, 'id' => $enquiry )
						);
					} else {
						$success = $wpdb->update( "{$wpdb->prefix}wl_im_enquiries",
							array(
								'is_deleted' => 1,
								'deleted_at' => date('Y-m-d H:i:s')
							), array( 'is_deleted' => 0, 'id' => $enquiry )
						);
					}

					if ( ! $success ) {
			  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
					}
				}

		  		$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Student added successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Fetch student to update */
	public static function fetch_student() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id   = intval( sanitize_text_field( $_POST['id'] ) );
		$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND id = $id" );
		if ( ! $row ) {
			die();
		}
		$wlim_active_courses = WL_IM_Helper::get_active_courses();
		$batches             = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND is_active = 1 AND course_id = $row->course_id ORDER BY id DESC" );
		$pending_fees        = number_format( $row->fees_payable - $row->fees_paid, 2, '.', '' );
		?>
		<form id="wlim-update-student-form">
			<?php $nonce = wp_create_nonce( "update-student-$id" ); ?>
		    <input type="hidden" name="update-student-<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr($nonce); ?>">
			<div class="row" id="wlim-student-enrollment_id">
				<div class="col">
					<label  class="col-form-label pb-0"><?php esc_html_e( 'Enrollment ID', WL_IM_DOMAIN ); ?>:</label>
					<div class="card mb-3 mt-2">
						<div class="card-block">
		    				<span class="text-dark"><?php echo WL_IM_Helper::get_enrollment_id( $row->id ); ?></span>
		  				</div>
					</div>
				</div>
			</div>
            <div class="form-group">
                <label for="wlim-student-course_update" class="col-form-label"><?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
                <select name="course" class="form-control" id="wlim-student-course_update" data-batch_id='<?php echo esc_attr($row->batch_id); ?>'>
                    <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
                <?php
                if ( count( $wlim_active_courses ) > 0 ) {
                    foreach ( $wlim_active_courses as $active_course ) {  ?>
                    <option value="<?php echo esc_attr($active_course->id); ?>"><?php echo "$active_course->course_name ($active_course->course_code) (" . esc_html__( "Fees", WL_IM_DOMAIN ) . ": $active_course->fees)"; ?></option>
                <?php
                    }
                } ?>
                </select>
            </div>
        <?php
		if ( count( $batches ) > 0 ) { ?>
        	<div id="wlim-add-student-course-update-batches">
				<div class="form-group pt-3">
		            <label for="wlim-student-batch_update" class="col-form-label"><?php esc_html_e( "Batch", WL_IM_DOMAIN ); ?>:</label>
		            <select name="batch" class="form-control selectpicker" id="wlim-student-batch_update">
		                <option value="">-------- <?php esc_html_e( "Select a Batch", WL_IM_DOMAIN ); ?> --------</option>
		            <?php
		    			foreach ( $batches as $batch ) {  ?>
		                <option value="<?php echo esc_attr($batch->id); ?>"><?php echo esc_html($batch->batch_code); ?></option>
			            <?php
			    		} ?>
		            </select>
		        </div>
        	</div>
        <?php
        } ?>
			<div class="row">
				<div class="col form-group">
					<label for="wlim-student-first_name_update" class="col-form-label"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?>:</label>
					<input name="first_name" type="text" class="form-control" id="wlim-student-first_name_update" placeholder="<?php esc_attr_e( "First Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->first_name); ?>">
				</div>
				<div class="col form-group">
					<label for="wlim-student-last_name_update" class="col-form-label"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?>:</label>
					<input name="last_name" type="text" class="form-control" id="wlim-student-last_name_update" placeholder="<?php esc_attr_e( "Last Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->last_name); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="wlim-student-fees_payable_update" class="col-form-label"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>:</label>
				<input name="fees_payable" type="number" class="form-control" id="wlim-student-fees_payable_update" min="0" placeholder="<?php esc_attr_e( "Fees Payable", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->fees_payable); ?>">
			</div>
			<div class="row" id="wlim-student-fees_paid_update">
				<div class="col">
					<label  class="col-form-label pb-0"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?>:</label>
					<div class="card mb-3 mt-2">
						<div class="card-block">
		    				<span class="text-dark"><?php echo esc_html($row->fees_paid); ?></span>
		  				</div>
					</div>
				</div>
			</div>
			<div class="row" id="wlim-student-fees_status">
				<div class="col">
					<label  class="col-form-label pb-0"><?php esc_html_e( 'Fees Status', WL_IM_DOMAIN ); ?>:</label>
					<div class="card mb-3 mt-2">
						<div class="card-block">
							<?php
							if ( $pending_fees > 0 ) { ?>
							<span class="wlim-text-danger"><strong><?php esc_html_e( 'Pending', WL_IM_DOMAIN ); ?>: </strong></span>
							<strong><?php echo esc_html($pending_fees); ?></strong>
							<?php
							} else { ?>
							<span class="text-success"><strong><?php esc_html_e( 'Paid', WL_IM_DOMAIN ); ?></strong></span>
							<?php
							} ?>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="wlim-student-phone_update" class="col-form-label"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?>:</label>
				<input name="phone" type="text" class="form-control" id="wlim-student-phone_update" placeholder="<?php esc_attr_e( "Phone", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->phone); ?>">
			</div>
			<div class="form-group">
				<label for="wlim-student-address_update" class="col-form-label"><?php esc_html_e( 'Address', WL_IM_DOMAIN ); ?>:</label>
				<textarea name="address" class="form-control" rows="3" id="wlim-student-address_update" placeholder="<?php esc_attr_e( "Address", WL_IM_DOMAIN ); ?>"><?php echo esc_html($row->address); ?></textarea>
			</div>
			<div class="row">
				<div class="col form-group">
					<label for="wlim-student-city_update" class="col-form-label"><?php esc_html_e( 'City', WL_IM_DOMAIN ); ?>:</label>
					<input name="city" type="text" class="form-control" id="wlim-student-city_update" placeholder="<?php esc_attr_e( "City", WL_IM_DOMAIN ); ?>"  value="<?php echo esc_attr($row->city); ?>">
				</div>
				<div class="col form-group">
					<label for="wlim-student-zip_update" class="col-form-label"><?php esc_html_e( 'Zip Code', WL_IM_DOMAIN ); ?>:</label>
					<input name="zip" type="text" class="form-control" id="wlim-student-zip_update" placeholder="<?php esc_attr_e( "Zip Code", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->zip); ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="wlim-student-email_update" class="col-form-label"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?>:</label>
				<input name="email" type="email" class="form-control" id="wlim-student-email_update" placeholder="<?php esc_attr_e( "Email", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->email); ?>">
			</div>
			<div class="form-check pl-0">
				<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlmp-student-is_active_update" <?php echo esc_html($row->is_active) ? "checked" : ""; ?>>
				<label class="form-check-label" for="wlmp-student-is_active_update">
				<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
				</label>
			</div>
			<div class="form-check pl-0">
				<input name="course_completed" class="position-static mt-0 form-check-input" type="checkbox" id="wlmp-student-course_completed_update" <?php echo esc_html($row->course_completed) ? "checked" : ""; ?>>
				<label class="form-check-label text-success" for="wlmp-student-course_completed_update">
					<strong><?php esc_html_e( 'Mark course as completed?', WL_IM_DOMAIN ); ?></strong>
				</label>
			</div>
			<input type="hidden" name="student_id" value="<?php echo esc_attr($row->id); ?>">
		</form>
		<script>
			// Select single option
			jQuery('#wlim-student-course_update').selectpicker({
				liveSearch: true
			});
			jQuery('#wlim-student-course_update').selectpicker('val', '<?php echo esc_html($row->course_id); ?>');

			// Select single option
			jQuery('#wlim-student-batch_update').selectpicker({
				liveSearch: true
			});
			jQuery('#wlim-student-batch_update').selectpicker('val', '<?php echo esc_html($row->batch_id); ?>');
		</script>
	<?php
		die();
	}

	/* Update student */
	public static function update_student() {
		self::check_permission();
		$id = intval( sanitize_text_field( $_POST['student_id'] ) );
		if ( ! wp_verify_nonce( $_POST["update-student-$id"], "update-student-$id" ) ) {
			die();
		}
		global $wpdb;

		$course_id        = isset( $_POST['course'] ) ? intval( sanitize_text_field( $_POST['course'] ) ) : NULL;
		$batch_id         = isset( $_POST['batch'] ) ? intval( sanitize_text_field( $_POST['batch'] ) ) : NULL;
		$first_name       = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
		$last_name        = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
		$fees_payable     = number_format( isset( $_POST['fees_payable'] ) ? max( floatval( sanitize_text_field( $_POST['fees_payable'] ) ), 0 ) : 0, 2, '.', '' );
		$phone            = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';
		$email            = isset( $_POST['email'] ) ? sanitize_text_field( $_POST['email'] ) : '';
		$address          = isset( $_POST['address'] ) ? sanitize_textarea_field( $_POST['address'] ) : '';
		$city             = isset( $_POST['city'] ) ? sanitize_text_field( $_POST['city'] ) : '';
		$zip              = isset( $_POST['zip'] ) ? sanitize_text_field( $_POST['zip'] ) : '';
		$is_active        = isset( $_POST['is_active'] ) ? boolval( sanitize_text_field( $_POST['is_active'] ) ) : 0;
		$course_completed = isset( $_POST['course_completed'] ) ? boolval( sanitize_text_field( $_POST['course_completed'] ) ) : 0;
		$completion_date  = $course_completed ? date('Y-m-d H:i:s') : NULL;

		$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND id = $id" );
		if ( ! $row ) {
			die();
		}

		/* Validations */
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

		if ( strlen( $city ) > 255 ) {
			$errors['city'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( strlen( $zip ) > 255 ) {
			$errors['zip'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		if ( $course_completed ) {
			$pending_fees = number_format( $row->fees_payable - $row->fees_paid, 2, '.', '' );
			if ( $pending_fees > 0 ) {
				$errors['fees_payable'] = esc_html__( 'Pending Fees.', WL_IM_DOMAIN );
			}
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $course_id" );

		if ( ! $count ) {
			$errors['course'] = esc_html__( 'Please select a valid course.', WL_IM_DOMAIN );
		}

		if ( ! empty( $batch_id ) ) {
			$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND is_active = 1 AND id = $batch_id AND course_id = $course_id" );

			if ( ! $count ) {
				$errors['batch'] = esc_html__( 'Please select a valid batch.', WL_IM_DOMAIN );
			}
		} else {
			$batch_id = NULL;
		}
		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				$data = array(
					'course_id'        => $course_id,
					'batch_id'         => $batch_id,
					'first_name'       => $first_name,
				    'last_name'        => $last_name,
				    'fees_payable'     => $fees_payable,
				    'phone'            => $phone,
				    'email'            => $email,
				    'address'          => $address,
				    'city'             => $city,
				    'zip'              => $zip,
				    'is_active'        => $is_active,
				    'course_completed' => $course_completed,
				    'completion_date'  => $completion_date,
				    'updated_at'       => date('Y-m-d H:i:s')
				);

				$success = $wpdb->update( "{$wpdb->prefix}wl_im_students", $data, array( 'is_deleted' => 0, 'id' => $id ) );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Student updated successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Delete student */
	public static function delete_student() {
		$id = intval( sanitize_text_field( $_POST['id'] ) );
		if ( ! wp_verify_nonce( $_POST["delete-student-$id"], "delete-student-$id" ) ) {
			die();
		}
		global $wpdb;

		try {
			$wpdb->query( 'BEGIN;' );

			$success = $wpdb->update( "{$wpdb->prefix}wl_im_students",
					array(
						'is_deleted' => 1,
						'deleted_at' => date('Y-m-d H:i:s')
					), array( 'is_deleted' => 0, 'id' => $id )
				);
			if ( ! $success ) {
	  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
			}

			$wpdb->query( 'COMMIT;' );
			wp_send_json_success( array( 'message' => esc_html__( 'Student removed successfully.', WL_IM_DOMAIN ) ) );
		} catch ( Exception $exception ) {
	  		$wpdb->query( 'ROLLBACK;' );
			wp_send_json_error( $exception->getMessage() );
		}
	}

	/* Fetch course batches */
	public static function fetch_course_batches() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$course_id = intval( sanitize_text_field( $_POST['id'] ) );
		$row       = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND id = $course_id" );

		if ( ! $row ) {
			die();
		}

		$batches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND is_active = 1 AND course_id = $course_id ORDER BY id DESC" );
		if ( count( $batches ) > 0 ) {
		?>
		<div class="form-group pt-3">
            <label for="wlim-student-batch" class="col-form-label"><?php esc_html_e( "Batch", WL_IM_DOMAIN ); ?>:</label>
            <select name="batch" class="form-control selectpicker" id="wlim-student-batch">
                <option value="">-------- <?php esc_html_e( "Select a Batch", WL_IM_DOMAIN ); ?> --------</option>
            <?php
    			foreach ( $batches as $batch ) {  ?>
                <option value="<?php echo esc_attr($batch->id); ?>"><?php echo esc_html($batch->batch_code); ?></option>
	            <?php
	    		} ?>
            </select>
        </div>
		<script>
			/* Select single option */
			jQuery('#wlim-student-batch').selectpicker({
				liveSearch: true
			});
		</script>
		<?php
        } else { ?>
			<div class="text-danger pt-3 pb-3 border-bottom"><?php esc_html_e( "Batches not found.", WL_IM_DOMAIN ); ?></div>
        <?php
    	}
    	die();
	}

	/* Fetch course update batches */
	public static function fetch_course_update_batches() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$course_id = intval( sanitize_text_field( $_POST['id'] ) );
		$batch_id  = intval( sanitize_text_field( $_POST['batch_id'] ) );
		$row       = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND id = $course_id" );

		if ( ! $row ) {
			die();
		}

		$batches = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND is_active = 1 AND course_id = $course_id ORDER BY id DESC" );
		if ( count( $batches ) > 0 ) {
		?>
		<div class="form-group pt-3">
            <label for="wlim-student-batch_update" class="col-form-label"><?php esc_html_e( "Batch", WL_IM_DOMAIN ); ?>:</label>
            <select name="batch" class="form-control selectpicker" id="wlim-student-batch_update">
                <option value="">-------- <?php esc_html_e( "Select a Batch", WL_IM_DOMAIN ); ?> --------</option>
            <?php
    			foreach ( $batches as $batch ) {  ?>
                <option value="<?php echo esc_attr($batch->id); ?>"><?php echo esc_html($batch->batch_code); ?></option>
	            <?php
	    		} ?>
            </select>
        </div>
		<script>
			/* Select single option */
			jQuery('#wlim-student-batch_update').selectpicker({
				liveSearch: true
			});
			<?php
			if ( $batch_id ) { ?>
			jQuery('#wlim-student-batch_update').selectpicker('val', '<?php echo $batch_id; ?>');
			<?php
			} ?>
		</script>
		<?php
        } else { ?>
			<div class="text-danger pt-3 pb-3 border-bottom"><?php esc_html_e( "Batches not found.", WL_IM_DOMAIN ); ?></div>
        <?php
    	}
    	die();
	}

	/* Fetch enquiries */
	public static function fetch_enquiries() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$enquiries = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 AND is_active = 1 ORDER BY id DESC" );
		if ( count( $enquiries ) > 0 ) {
		?>
		<div class="form-group pt-3">
            <label for="wlim-student-enquiry" class="col-form-label"><?php esc_html_e( "Enquiry", WL_IM_DOMAIN ); ?>:</label>
            <select name="enquiry" class="form-control selectpicker" id="wlim-student-enquiry">
                <option value="">-------- <?php esc_html_e( "Select an Enquiry", WL_IM_DOMAIN ); ?> --------</option>
            <?php
    			foreach ( $enquiries as $enquiry ) {  ?>
                <option value="<?php echo esc_attr($enquiry->id); ?>"><?php echo "$enquiry->first_name $enquiry->last_name (" . WL_IM_Helper::get_enquiry_id( $enquiry->id ) . ")"; ?></option>
	            <?php
	    		} ?>>
            </select>
        </div>
		<script>
			/* Select single option */
			jQuery('#wlim-student-enquiry').selectpicker({
				liveSearch: true
			});
		</script>
		<?php
        } else { ?>
			<div class="text-danger pt-3 pb-3 border-bottom"><?php esc_html_e( "Enquiries not found.", WL_IM_DOMAIN ); ?></div>
        <?php
    	}
    	die();
	}

	/* Fetch enquiry */
	public static function fetch_enquiry() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id   = intval( sanitize_text_field( $_POST['id'] ) );
		$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_enquiries WHERE is_deleted = 0 AND id = $id" );
		$wlim_active_courses = WL_IM_Helper::get_active_courses();

		if ( $row ) {
		?>
		<div class="form-group">
            <label for="wlim-student-course" class="col-form-label"><?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
            <select name="course" class="form-control" id="wlim-student-course">
                <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
            <?php
            if ( count( $wlim_active_courses ) > 0 ) {
                foreach ( $wlim_active_courses as $active_course ) {  ?>
                <option value="<?php echo esc_attr($active_course->id); ?>">
		        	<?php echo "$active_course->course_name ($active_course->course_code) (" . esc_html__( "Fees", WL_IM_DOMAIN ) . ": $active_course->fees)"; ?>
            	</option>
            <?php
                }
            } ?>
            </select>
        </div>
        <div id="wlim-add-student-course-batches"></div>
		<div class="row">
			<div class="col form-group">
				<label for="wlim-student-first_name" class="col-form-label"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?>:</label>
				<input name="first_name" type="text" class="form-control" id="wlim-student-first_name" placeholder="<?php esc_attr_e( "First Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->first_name); ?>">
			</div>
			<div class="col form-group">
				<label for="wlim-student-last_name" class="col-form-label"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?>:</label>
				<input name="last_name" type="text" class="form-control" id="wlim-student-last_name" placeholder="<?php esc_attr_e( "Last Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->last_name); ?>">
			</div>
		</div>
		<?php if( ! empty( $row->message ) ) { ?>
		<div class="row" id="wlim-student-message">
			<div class="col">
				<label  class="col-form-label pb-0"><?php esc_html_e( 'Message', WL_IM_DOMAIN ); ?>:</label>
				<div class="card mb-3 mt-2">
					<div class="card-block">
	    				<span class="text-secondary"><?php echo $row->message; ?></span>
	  				</div>
				</div>
			</div>
		</div>
		<?php
		} ?>
		<div id="wlim-add-student-fetch-fees-payable">
			<div class="form-group">
				<label for="wlim-student-fees_payable" class="col-form-label"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>:</label>
				<input name="fees_payable" type="number" class="form-control" id="wlim-student-fees_payable" min="0" placeholder="<?php esc_attr_e( "Fees Payable", WL_IM_DOMAIN ); ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="wlim-student-fees_paid" class="col-form-label"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?>:</label>
			<input name="fees_paid" type="number" class="form-control" id="wlim-student-fees_paid" min="0" placeholder="<?php esc_attr_e( "Fees Paid", WL_IM_DOMAIN ); ?>">
		</div>
		<div class="form-group">
			<label for="wlim-student-phone" class="col-form-label"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?>:</label>
			<input name="phone" type="text" class="form-control" id="wlim-student-phone" placeholder="<?php esc_attr_e( "Phone", WL_IM_DOMAIN ); ?>" value="<?php echo $row->phone; ?>">
		</div>
		<div class="form-group">
			<label for="wlim-student-address" class="col-form-label"><?php esc_html_e( 'Address', WL_IM_DOMAIN ); ?>:</label>
			<textarea name="address" class="form-control" rows="3" id="wlim-student-address" placeholder="<?php esc_attr_e( "Address", WL_IM_DOMAIN ); ?>"></textarea>
		</div>
		<div class="row">
			<div class="col form-group">
				<label for="wlim-student-city" class="col-form-label"><?php esc_html_e( 'City', WL_IM_DOMAIN ); ?>:</label>
				<input name="city" type="text" class="form-control" id="wlim-student-city" placeholder="<?php esc_attr_e( "City", WL_IM_DOMAIN ); ?>">
			</div>
			<div class="col form-group">
				<label for="wlim-student-zip" class="col-form-label"><?php esc_html_e( 'Zip Code', WL_IM_DOMAIN ); ?>:</label>
				<input name="zip" type="text" class="form-control" id="wlim-student-zip" placeholder="<?php esc_attr_e( "Zip Code", WL_IM_DOMAIN ); ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="wlim-student-email" class="col-form-label"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?>:</label>
			<input name="email" type="email" class="form-control" id="wlim-student-email" placeholder="<?php esc_attr_e( "Email", WL_IM_DOMAIN ); ?>" value="<?php echo $row->email; ?>">
		</div>
		<div class="form-check pl-0">
			<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-student-is_active" checked>
			<label class="form-check-label" for="wlim-student-is_active">
			<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
			</label>
		</div>
	    <div class="form-group mt-3 pl-0 pt-3 border-top enquiry_action">
	    	<label><?php esc_html_e( 'After Adding Student', WL_IM_DOMAIN ); ?>:</label><br>
	    	<div class="row">
		    	<div class="col">
					<label class="radio-inline"><input checked type="radio" name="enquiry_action" value="mark_enquiry_inactive" id="wlim-student-mark_enquiry_inactive"><?php esc_html_e( 'Mark Enquiry As Inactive', WL_IM_DOMAIN ); ?></label>
				</div>
		    	<div class="col">
		    		<label class="radio-inline"><input type="radio" name="enquiry_action" value="delete_enquiry" id="wlim-student-delete_enquiry"><?php esc_html_e( 'Delete Enquiry', WL_IM_DOMAIN ); ?></label>
		    	</div>
	    	</div>
		</div>
		<script>
			/* Select single option */
			jQuery('#wlim-student-course').selectpicker({
				liveSearch: true
			});
			jQuery('#wlim-student-course').selectpicker('val', '<?php echo $row->course_id; ?>');

			/* Select single option */
			jQuery('#wlim-student-batch').selectpicker({
				liveSearch: true
			});
		</script>
		<?php
        } else {
        	self::render_add_student_form( $wlim_active_courses );
        }
		die();
	}

	/* Fetch student fees payable */
	public static function fetch_fees_payable() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id   = intval( sanitize_text_field( $_POST['id'] ) );
		$row = $wpdb->get_row( "SELECT fees FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $id" );

		if ( $row ) {
		?>
		<div class="form-group">
			<label for="wlim-student-fees_payable" class="col-form-label"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>:</label>
			<input name="fees_payable" type="number" class="form-control" id="wlim-student-fees_payable" min="0" placeholder="<?php esc_attr_e( "Fees Payable", WL_IM_DOMAIN ); ?>" value="<?php echo $row->fees; ?>">
		</div>
		<?php
        } else { ?>
		<div class="form-group">
			<label for="wlim-student-fees_payable" class="col-form-label"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>:</label>
			<input name="fees_payable" type="number" class="form-control" id="wlim-student-fees_payable" min="0" placeholder="<?php esc_attr_e( "Fees Payable", WL_IM_DOMAIN ); ?>">
		</div>
		<?php
        }
		die();
	}

	public static function add_student_form() {
		$wlim_active_courses = WL_IM_Helper::get_active_courses();
		self::render_add_student_form( $wlim_active_courses );
		die();
	}

	public static function render_add_student_form($wlim_active_courses = []) {
	?>
		<div class="form-group">
		    <label for="wlim-student-course" class="col-form-label"><?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
		    <select name="course" class="form-control selectpicker" id="wlim-student-course">
		        <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
		    <?php
		    if ( count( $wlim_active_courses ) > 0 ) {
		        foreach ( $wlim_active_courses as $active_course ) {  ?>
		        <option value="<?php echo $active_course->id; ?>">
		        	<?php echo "$active_course->course_name ($active_course->course_code) (" . esc_html__( "Fees", WL_IM_DOMAIN ) . ": $active_course->fees)"; ?>
	        	</option>
		    <?php
		        }
		    } ?>
		    </select>
		</div>
        <div id="wlim-add-student-course-batches"></div>
		<div class="row">
			<div class="col-sm-6 form-group">
				<label for="wlim-student-first_name" class="col-form-label"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?>:</label>
				<input name="first_name" type="text" class="form-control" id="wlim-student-first_name" placeholder="<?php esc_attr_e( "First Name", WL_IM_DOMAIN ); ?>">
			</div>
			<div class="col-sm-6 form-group">
				<label for="wlim-student-last_name" class="col-form-label"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?>:</label>
				<input name="last_name" type="text" class="form-control" id="wlim-student-last_name" placeholder="<?php esc_attr_e( "Last Name", WL_IM_DOMAIN ); ?>">
			</div>
		</div>
		<div id="wlim-add-student-fetch-fees-payable">
			<div class="form-group">
				<label for="wlim-student-fees_payable" class="col-form-label"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>:</label>
				<input name="fees_payable" type="number" class="form-control" id="wlim-student-fees_payable" min="0" placeholder="<?php esc_attr_e( "Fees Payable", WL_IM_DOMAIN ); ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="wlim-student-fees_paid" class="col-form-label"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?>:</label>
			<input name="fees_paid" type="number" class="form-control" id="wlim-student-fees_paid" min="0" placeholder="<?php esc_attr_e( "Fees Paid", WL_IM_DOMAIN ); ?>">
		</div>
		<div class="form-group">
			<label for="wlim-student-phone" class="col-form-label"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?>:</label>
			<input name="phone" type="text" class="form-control" id="wlim-student-phone" placeholder="<?php esc_attr_e( "Phone", WL_IM_DOMAIN ); ?>">
		</div>
		<div class="form-group">
			<label for="wlim-student-address" class="col-form-label"><?php esc_html_e( 'Address', WL_IM_DOMAIN ); ?>:</label>
			<textarea name="address" class="form-control" rows="3" id="wlim-student-address" placeholder="<?php esc_attr_e( "Address", WL_IM_DOMAIN ); ?>"></textarea>
		</div>
		<div class="row">
			<div class="col-sm-6 form-group">
				<label for="wlim-student-city" class="col-form-label"><?php esc_html_e( 'City', WL_IM_DOMAIN ); ?>:</label>
				<input name="city" type="text" class="form-control" id="wlim-student-city" placeholder="<?php esc_attr_e( "City", WL_IM_DOMAIN ); ?>">
			</div>
			<div class="col-sm-6 form-group">
				<label for="wlim-student-zip" class="col-form-label"><?php esc_html_e( 'Zip Code', WL_IM_DOMAIN ); ?>:</label>
				<input name="zip" type="text" class="form-control" id="wlim-student-zip" placeholder="<?php esc_attr_e( "Zip Code", WL_IM_DOMAIN ); ?>">
			</div>
		</div>
		<div class="form-group">
			<label for="wlim-student-email" class="col-form-label"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?>:</label>
			<input name="email" type="email" class="form-control" id="wlim-student-email" placeholder="<?php esc_attr_e( "Email", WL_IM_DOMAIN ); ?>">
		</div>
		<div class="form-check pl-0">
			<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-student-is_active" checked>
			<label class="form-check-label" for="wlim-student-is_active">
			<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
			</label>
		</div>
		<script>
			/* Select single option */
			try {
				jQuery('.selectpicker').selectpicker({
					liveSearch: true
				});
			} catch (error) {
			}
		</script>
	<?php
	}

	/* Check permission to manage student */
	private static function check_permission() {
		if ( ! current_user_can( 'wl_im_manage_students' ) ) {
			die();
		}
	}
}