<?php
defined( 'ABSPATH' ) or die();

require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

class WL_IM_Fee {
	/* Get installment data to display on table */
	public static function get_installment_data() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$data = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wl_im_installments WHERE is_deleted = 0 ORDER BY id DESC" );
		$student_data = $wpdb->get_results( "SELECT id, first_name, last_name FROM {$wpdb->prefix}wl_im_students ORDER BY first_name, last_name, id DESC", OBJECT_K );

		if ( count( $data ) !== 0 ) {
			foreach ( $data as $row ) {
				$id       =  $row->id;
				$receipt  =  WL_IM_Helper::get_receipt( $id );
				$amount   = $row->amount;
				$date     = date_format( date_create( $row->created_at ), "d-m-Y g:i A" );
				$added_by = ( $user = get_userdata( $row->added_by ) ) ? $user->user_login : '-';

				$student_name = '-';
				if ( $row->student_id && isset( $student_data[$row->student_id] ) ) {
					$student_name  = $student_data[$row->student_id]->first_name . " " . $student_data[$row->student_id]->last_name;
					$enrollment_id = WL_IM_Helper::get_enrollment_id( $student_data[$row->student_id]->id );
				}

				$results["data"][] = array(
					$receipt,
					$amount,
					$enrollment_id,
					$student_name,
					$date,
					$added_by,
					'<a class="mr-3" href="#update-installment" data-keyboard="false" data-backdrop="static" data-toggle="modal" data-id="' . $id . '"><i class="fa fa-edit"></i></a> <a href="javascript:void(0)" delete-installment-security="' . wp_create_nonce( "delete-installment-$id" ) . '"delete-installment-id="' . $id . '" class="delete-installment"> <i class="fa fa-trash text-danger"></i></a>'
				);
			}
		} else {
			$results["data"] = [];
		}
		wp_send_json( $results );
	}

	/* Add new installment */
	public static function add_installment() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_POST['add-installment'], 'add-installment' ) ) {
			die();
		}
		global $wpdb;

		$amount     = number_format( isset( $_POST['amount'] ) ? max( floatval( sanitize_text_field( $_POST['amount'] ) ), 0 ) : 0, 2, '.', '' );
		$student_id = isset( $_POST['student'] ) ? intval( sanitize_text_field( $_POST['student'] ) ) : NULL;

		$errors = [];

		if ( $amount <= 0 ) {
			$errors['amount'] = esc_html__( 'Amount must be positive.', WL_IM_DOMAIN );
		}

		$student = $wpdb->get_row( "SELECT fees_payable, fees_paid, course_id FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND is_active = 1 AND id = $student_id" );

		if ( ! $student ) {
			$errors['student'] = esc_html__( 'Please select a valid student.', WL_IM_DOMAIN );
		}

		$course = $wpdb->get_row( "SELECT fees FROM {$wpdb->prefix}wl_im_courses WHERE is_deleted = 0 AND is_active = 1 AND id = $student->course_id" );

		if ( ! $course ) {
			$errors['student'] = esc_html__( 'Student is not enrolled in any course.', WL_IM_DOMAIN );
		}

		if ( $student->fees_payable < ( $student->fees_paid + $amount ) ) {
			$errors['amount'] = esc_html__( 'Total amount exceeded payable amount.', WL_IM_DOMAIN );
		}

		if ( count( $errors ) < 1 ) {
			try {
			  	$wpdb->query( 'BEGIN;' );

				$data = array(
					'amount'     => $amount,
					'student_id' => $student_id,
				    'added_by'   => get_current_user_id()
				);

				$success = $wpdb->insert( "{$wpdb->prefix}wl_im_installments", $data );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

				$data = array(
					'fees_paid' => $student->fees_paid + $amount,
				    'updated_at' => date('Y-m-d H:i:s')
				);

				$success = $wpdb->update( "{$wpdb->prefix}wl_im_students", $data, array( 'is_deleted' => 0, 'id' => $student_id ) );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

		  		$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Installment added successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Fetch installment to update */
	public static function fetch_installment() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id   = intval( sanitize_text_field( $_POST['id'] ) );
		$row = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wl_im_installments WHERE is_deleted = 0 AND id = $id" );
		if ( ! $row ) {
			die();
		}
		$student = $wpdb->get_row( "SELECT id, course_id, fees_payable, fees_paid, first_name, last_name FROM {$wpdb->prefix}wl_im_students WHERE id = $row->student_id" );
		if ( ! $student ) {
			die();
		}
		$course = $wpdb->get_row( "SELECT course_code, course_name FROM {$wpdb->prefix}wl_im_courses WHERE id = $student->course_id" );
		if ( ! $course ) {
			die();
		}
		$pending_fees = number_format( $student->fees_payable - $student->fees_paid, 2, '.', '' );
		?>
		<form id="wlim-update-installment-form">
			<?php $nonce = wp_create_nonce( "update-installment-$id" ); ?>
		    <input type="hidden" name="update-installment-<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($nonce); ?>">
			<div class="row" id="wlim-student-enrollment_id">
				<div class="col">
					<label  class="col-form-label pb-0"><?php esc_html_e( 'Student', WL_IM_DOMAIN ); ?>:</label>
					<div class="card mb-3 mt-2">
						<div class="card-block">
		    				<span class="text-dark"><?php echo esc_html($student->first_name) . " " . $student->last_name; ?> (<?php echo WL_IM_Helper::get_enrollment_id( $student->id ); ?>)</span>
		  				</div>
					</div>
					<ul class="list-group list-group-flush border-bottom mt-4 mb-3">
						<li class="list-group-item"><?php esc_html_e( 'Course', WL_IM_DOMAIN ); ?>: <strong><?php echo esc_html("$course->course_name ($course->course_code)"); ?></strong></li>
						<li class="list-group-item"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>: <strong><?php echo esc_html($student->fees_payable); ?></strong></li>
						<li class="list-group-item"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?>: <strong><?php echo esc_html($student->fees_paid); ?></strong></li>
						<li class="list-group-item"><?php esc_html_e( 'Pending Fees', WL_IM_DOMAIN ); ?>: <strong class="text-danger"><?php echo esc_html($pending_fees); ?></strong></li>
					</ul>
				</div>
			</div>
			<div class="form-group">
				<label for="wlim-installment-amount_update" class="col-form-label"><?php esc_html_e( 'Amount', WL_IM_DOMAIN ); ?>:</label>
				<input name="amount" type="number" class="form-control" id="wlim-installment-amount_update" placeholder="<?php esc_attr_e( "Amount", WL_IM_DOMAIN ); ?>" min="0" value="<?php echo esc_attr($row->amount); ?>">
			</div>
			<input type="hidden" name="installment_id" value="<?php echo esc_attr($row->id); ?>">
		</form>
	<?php
		die();
	}

	/* Update installment */
	public static function update_installment() {
		self::check_permission();
		$id = intval( sanitize_text_field( $_POST['installment_id'] ) );
		if ( ! wp_verify_nonce( $_POST["update-installment-$id"], "update-installment-$id" ) ) {
			die();
		}
		global $wpdb;

		$amount = number_format( isset( $_POST['amount'] ) ? max( floatval( sanitize_text_field( $_POST['amount'] ) ), 0 ) : 0, 2, '.', '' );

		$errors = [];

		if ( $amount <= 0 ) {
			$errors['amount'] = esc_html__( 'Amount must be positive.', WL_IM_DOMAIN );
		}

		$installment = $wpdb->get_row( "SELECT amount, student_id FROM {$wpdb->prefix}wl_im_installments WHERE is_deleted = 0 AND id = $id" );

		if ( ! $installment ) {
			$errors['amount'] = esc_html__( 'Installment not found.', WL_IM_DOMAIN );
		}

		$student = $wpdb->get_row( "SELECT id, fees_payable, fees_paid, course_id FROM {$wpdb->prefix}wl_im_students WHERE id = $installment->student_id" );

		if ( ! $student ) {
			$errors['student'] = esc_html__( 'Please select a valid student.', WL_IM_DOMAIN );
		}

		if ( $student->fees_payable < ( $student->fees_paid - $installment->amount + $amount ) ) {
			$errors['amount'] = esc_html__( 'Total amount exceeded payable amount.', WL_IM_DOMAIN );
		}

		if ( count( $errors ) < 1 ) {
			try {
				$wpdb->query( 'BEGIN;' );

				$data = array(
					'amount'     => $amount,
				    'updated_at' => date('Y-m-d H:i:s')
				);

				$success = $wpdb->update( "{$wpdb->prefix}wl_im_installments", $data, array( 'is_deleted' => 0, 'id' => $id ) );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

				$data = array(
					'fees_paid' => $student->fees_paid - $installment->amount + $amount,
				    'updated_at' => date('Y-m-d H:i:s')
				);

				$success = $wpdb->update( "{$wpdb->prefix}wl_im_students", $data, array( 'id' => $student->id ) );
				if ( ! $success ) {
		  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
				}

				$wpdb->query( 'COMMIT;' );
				wp_send_json_success( array( 'message' => esc_html__( 'Installment updated successfully.', WL_IM_DOMAIN ) ) );
			} catch ( Exception $exception ) {
		  		$wpdb->query( 'ROLLBACK;' );
				wp_send_json_error( $exception->getMessage() );
			}
		}
		wp_send_json_error( $errors );
	}

	/* Delete installment */
	public static function delete_installment() {
		$id = intval( sanitize_text_field( $_POST['id'] ) );
		if ( ! wp_verify_nonce( $_POST["delete-installment-$id"], "delete-installment-$id" ) ) {
			die();
		}
		global $wpdb;

		try {
			$installment = $wpdb->get_row( "SELECT amount, student_id FROM {$wpdb->prefix}wl_im_installments WHERE is_deleted = 0 AND id = $id" );

			if ( ! $installment ) {
	  			throw new Exception( esc_html__( 'Installment not found.', WL_IM_DOMAIN ) );
			}

			$student = $wpdb->get_row( "SELECT id, fees_paid FROM {$wpdb->prefix}wl_im_students WHERE id = $installment->student_id" );

			if ( ! $student ) {
	  			throw new Exception( esc_html__( 'Student not found for this installment.', WL_IM_DOMAIN ) );
			}

			$wpdb->query( 'BEGIN;' );

			$success = $wpdb->update( "{$wpdb->prefix}wl_im_installments",
					array(
						'is_deleted' => 1,
						'deleted_at' => date('Y-m-d H:i:s')
					), array( 'is_deleted' => 0, 'id' => $id )
				);
			if ( ! $success ) {
	  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
			}

			$data = array(
				'fees_paid' => $student->fees_paid - $installment->amount,
			    'updated_at' => date('Y-m-d H:i:s')
			);

			$success = $wpdb->update( "{$wpdb->prefix}wl_im_students", $data, array( 'id' => $student->id ) );
			if ( ! $success ) {
	  			throw new Exception( esc_html__( 'An unexpected error occurred.', WL_IM_DOMAIN ) );
			}

			$wpdb->query( 'COMMIT;' );
			wp_send_json_success( array( 'message' => esc_html__( 'Installment removed successfully.', WL_IM_DOMAIN ) ) );
		} catch ( Exception $exception ) {
	  		$wpdb->query( 'ROLLBACK;' );
			wp_send_json_error( $exception->getMessage() );
		}
	}

	/* Fetch Fee */
	public static function fetch_fees() {
		self::check_permission();
		if ( ! wp_verify_nonce( $_REQUEST['security'], 'wl-im' ) ) {
			die();
		}
		global $wpdb;

		$id      = intval( sanitize_text_field( $_POST['id'] ) );
		$student = $wpdb->get_row( "SELECT fees_payable, fees_paid, course_id FROM {$wpdb->prefix}wl_im_students WHERE is_deleted = 0 AND id = $id" );
		if ( ! $student ) {
			die();
		}
		$course = $wpdb->get_row( "SELECT course_code, course_name FROM {$wpdb->prefix}wl_im_courses WHERE id = $student->course_id" );
		if ( ! $course ) {
			die();
		}
		$pending_fees = number_format( $student->fees_payable - $student->fees_paid, 2, '.', '' );
		?>
		<ul class="list-group list-group-flush border-top mt-4">
			<li class="list-group-item"><?php esc_html_e( 'Course', WL_IM_DOMAIN ); ?>: <strong><?php echo esc_html("$course->course_name ($course->course_code)"); ?></strong></li>
			<li class="list-group-item"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?>: <strong><?php echo esc_html($student->fees_payable); ?></strong></li>
			<li class="list-group-item"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?>: <strong><?php echo esc_html($student->fees_paid); ?></strong></li>
			<li class="list-group-item"><?php esc_html_e( 'Pending Fees', WL_IM_DOMAIN ); ?>: <strong class="text-danger"><?php echo esc_html($pending_fees); ?></strong></li>
		</ul>
		<?php
		die();
	}

	/* Check permission to manage installment */
	private static function check_permission() {
		if ( ! current_user_can( 'wl_im_manage_fees' ) ) {
			die();
		}
	}
}