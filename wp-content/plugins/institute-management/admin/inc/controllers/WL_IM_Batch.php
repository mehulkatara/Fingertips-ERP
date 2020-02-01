<?php
defined( 'ABSPATH' ) or die();

require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

class WL_IM_Batch {
	/* Get batch data to display on table */
	public static function get_batch_data() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$data = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 ORDER BY id DESC" );
		$course_data = $wpdb->get_results( "SELECT id, course_name, course_code FROM {$wpdb->prefix}wl_im_courses ORDER BY course_name", OBJECT_K );

		if ( count( $data ) !== 0 ) {
			foreach ( $data as $row ) {
				$id         = $row->id;
				$batch_code = $row->batch_code;
				$batch_name = $row->batch_name ? $row->batch_name : '-';
				$is_acitve  = $row->is_active ? esc_html__( 'Yes', WL_IM_DOMAIN ) : esc_html__( 'No', WL_IM_DOMAIN );
				$added_on   = date_format( date_create( $row->created_at ), "d-m-Y g:i A" );
				$added_by   = ( $user = get_userdata( $row->added_by ) ) ? $user->user_login : '-';

				$course = '-';
				if ( $row->course_id && isset( $course_data[$row->course_id] ) ) {
					$course_name = $course_data[$row->course_id]->course_name;
					$course_code = $course_data[$row->course_id]->course_code;
					$course      = "$course_name ($course_code)";
				}

				$results["data"][] = array(
					$batch_code,
					$batch_name,
					$course,
					$is_acitve,
					$added_on,
					$added_by,
					'<a class="mr-3" href="#update-batch" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-id="' . $id . '"><i class="fa fa-edit"></i></a> <a href="javascript:void(0)" delete-batch-security="' . wp_create_nonce( "delete-batch-$id" ) . '"delete-batch-id="' . $id . '" class="delete-batch"> <i class="fa fa-trash text-danger"></i></a>'
				);
			}
		} else {
			$results["data"] = [];
		}
		wp_send_json( $results );
	}

	/* Add new batch */
	public static function add_batch() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_POST['add-batch'], 'add-batch' ) ) {
			die();
		}
		global $wpdb;

		$course_id  = isset( $_POST['course'] ) ? intval( sanitize_text_field( $_POST['course'] ) ) : NULL;
		$batch_code = isset( $_POST['batch_code'] ) ? sanitize_text_field( $_POST['batch_code'] ) : '';
		$batch_name = isset( $_POST['batch_name'] ) ? sanitize_text_field( $_POST['batch_name'] ) : '';
		$is_active  = isset( $_POST['is_active'] ) ? boolval( sanitize_text_field( $_POST['is_active'] ) ) : 0;

		/* Validations */
		$errors = [];
		if ( empty( $course_id ) ) {
			$errors['course'] = esc_html__( 'Please select a course.', WL_IM_DOMAIN );
		}

		if ( empty( $batch_code ) ) {
			$errors['batch_code'] = esc_html__( 'Please provide batch code.', WL_IM_DOMAIN );
		}

		if ( strlen( $batch_code ) > 191 ) {
			$errors['batch_code'] = esc_html__( 'Maximum length cannot exceed 191 characters.', WL_IM_DOMAIN );
		}

		if ( strlen( $batch_name ) > 255 ) {
			$errors['batch_name'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $course_id" );

		if ( ! $count ) {
			$errors['course'] = esc_html__( 'Please select a valid course.', WL_IM_DOMAIN );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND batch_code = '$batch_code' AND course_id = '$course_id'" );

		if ( $count ) {
			$errors['batch_code'] = esc_html__( 'Batch code in this course already exists.', WL_IM_DOMAIN );
		}
		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
			  	$wpdb->query( 'BEGIN;' );

				$data = array(
					'course_id'  => $course_id,
					'batch_code' => $batch_code,
					'batch_name' => $batch_name,
				    'is_active'  => $is_active,
				    'added_by'   => get_current_user_id()
				);

				$success = $wpdb->insert( "{$wpdb->prefix}wl_im_batches", $data );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

		  		$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Batch added successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Fetch batch to update */
	public static function fetch_batch() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id   = intval( sanitize_text_field( $_POST['id'] ) );
		$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND id = $id" );
		if ( ! $row ) {
			die();
		}
		$wlim_active_courses = WL_IM_Helper::get_active_courses();
		?>
		<form id="wlim-update-batch-form">
			<?php $nonce = wp_create_nonce( "update-batch-$id" ); ?>
		    <input type="hidden" name="update-batch-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($nonce); ?>">
			<div class="form-group">
	            <label for="wlim-batch-course_update" class="col-form-label">* <?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
	            <select name="course" class="form-control selectpicker" id="wlim-batch-course_update">
	                <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
	            <?php
	            if ( count( $wlim_active_courses ) > 0 ) {
	                foreach ( $wlim_active_courses as $active_course ) {  ?>
			        <option value="<?php echo esc_attr($active_course->id); ?>"><?php echo "$active_course->course_name ($active_course->course_code)"; ?></option>
	            <?php
	                }
	            } ?>
	            </select>
	        </div>
		    <div class="row">
				<div class="col form-group">
					<label for="wlim-batch-batch_code_update" class="col-form-label">* <?php esc_html_e( 'Batch Code', WL_IM_DOMAIN ); ?>:</label>
					<input name="batch_code" type="text" class="form-control" id="wlim-batch-batch_code_update" placeholder="<?php esc_attr_e( "Batch Code", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->batch_code); ?>">
				</div>
				<div class="col form-group">
					<label for="wlim-batch-batch_name_update" class="col-form-label"><?php esc_attr_e( 'Batch Name', WL_IM_DOMAIN ); ?>:</label>
					<input name="batch_name" type="text" class="form-control" id="wlim-batch-batch_name_update" placeholder="<?php esc_attr_e( "Batch Name", WL_IM_DOMAIN ); ?>" value="<?php echo esc_attr($row->batch_name); ?>">
				</div>
			</div>
			<div class="form-check pl-0">
				<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-batch-is_active_update" <?php echo esc_html($row->is_active) ? "checked" : ""; ?>>
				<label class="form-check-label" for="wlim-batch-is_active_update">
				<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
				</label>
			</div>
			<input type="hidden" name="batch_id" value="<?php echo esc_attr($row->id); ?>">
		</form>
		<script>
			/* Select single option */
			jQuery('#wlim-batch-course_update').selectpicker({
				liveSearch: true
			});
			jQuery('#wlim-batch-course_update').selectpicker('val', '<?php echo esc_html( $row->course_id ); ?>');
		</script>
	<?php
		die();
	}

	/* Update batch */
	public static function update_batch() {
		self::check_permission();
		$id = intval( sanitize_text_field( $_POST['batch_id'] ) );
		if ( ! wp_verify_nonce( $_POST["update-batch-$id"], "update-batch-$id" ) ) {
			die();
		}
		global $wpdb;

		$course_id  = isset( $_POST['course'] ) ? intval( sanitize_text_field( $_POST['course'] ) ) : NULL;
		$batch_code = isset( $_POST['batch_code'] ) ? sanitize_text_field( $_POST['batch_code'] ) : '';
		$batch_name = isset( $_POST['batch_name'] ) ? sanitize_text_field( $_POST['batch_name'] ) : '';
		$is_active  = isset( $_POST['is_active'] ) ? boolval( sanitize_text_field( $_POST['is_active'] ) ) : 0;

		/* Validations */
		$errors = [];
		if ( empty( $course_id ) ) {
			$errors['course'] = esc_html__( 'Please select a course.', WL_IM_DOMAIN );
		}

		if ( empty( $batch_code ) ) {
			$errors['batch_code'] = esc_html__( 'Please provide batch code.', WL_IM_DOMAIN );
		}

		if ( strlen( $batch_code ) > 191 ) {
			$errors['batch_code'] = esc_html__( 'Maximum length cannot exceed 191 characters.', WL_IM_DOMAIN );
		}

		if ( strlen( $batch_name ) > 255 ) {
			$errors['batch_name'] = esc_html__( 'Maximum length cannot exceed 255 characters.', WL_IM_DOMAIN );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $course_id" );

		if ( ! $count ) {
			$errors['course'] = esc_html__( 'Please select a valid course.', WL_IM_DOMAIN );
		}

		$count = $wpdb->get_var( "SELECT COUNT(*) as count FROM {$wpdb->prefix}wl_im_batches WHERE is_deleted = 0 AND id != $id AND batch_code = '$batch_code' AND course_id = '$course_id'" );

		if ( $count ) {
			$errors['batch_code'] = esc_html__( 'Batch code in this course already exists.', WL_IM_DOMAIN );
		}
		/* End validations */

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				$data = array(
					'course_id'  => $course_id,
					'batch_code' => $batch_code,
					'batch_name' => $batch_name,
				    'is_active'  => $is_active,
				    'updated_at' => date('Y-m-d H:i:s')
				);

				$success = $wpdb->update( "{$wpdb->prefix}wl_im_batches", $data, array( 'is_deleted' => 0, 'id' => $id ) );
				if ( ! $success ) {
					var_dump($wpdb->last_error);
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_htmlesc_html__( 'Batch updated successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Delete batch */
	public static function delete_batch() {
		$id = intval( sanitize_text_field( $_POST['id'] ) );
		if ( ! wp_verify_nonce( $_POST["delete-batch-$id"], "delete-batch-$id" ) ) {
			die();
		}
		global $wpdb;

		try {
			$wpdb->query( 'BEGIN;' );

			$success = $wpdb->update( "{$wpdb->prefix}wl_im_batches",
					array(
						'is_deleted' => 1,
						'deleted_at' => date('Y-m-d H:i:s')
					), array( 'is_deleted' => 0, 'id' => $id )
				);
			if ( ! $success ) {
	  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
			}

			$wpdb->query( 'COMMIT;' );
			wp_send_json_success( array( 'message' => esc_html__( 'Batch removed successfully.', WL_IM_DOMAIN ) ) );
		} catch ( Exception $exception ) {
	  		$wpdb->query( 'ROLLBACK;' );
			wp_send_json_error( $exception->getMessage() );
		}
	}

	/* Check permission to manage batch */
	private static function check_permission() {
		if ( ! current_user_can( 'wl_im_manage_batches' ) ) {
			die();
		}
	}
}