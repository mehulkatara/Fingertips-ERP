<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/controllers/WL_IM_Student.php' );

$wlim_active_courses = WL_IM_Helper::get_active_courses();
?>

<div class="container-fluid wl_im_container">
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center text-white blue blue-gradient"><span class="border-bottom"><i class="fas fa-users"></i> <?php esc_html_e( 'Students', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can either add a new student or edit existing students.', WL_IM_DOMAIN ); ?>
			</div>
			<!-- end main header content -->
		</div>
	</div>
	<!-- end - row 1 -->

	<!-- row 2 -->
	<div class="row">
		<div class="card col">
			<div class="card-header">
				<!-- card header content -->
				<div class="row">
					<div class="col-md-9 col-xs-12">
						<div class="h3"><?php esc_html_e( 'Manage Students', WL_IM_DOMAIN ); ?></div>
					</div>
					<div class="col-md-3 col-xs-12">
						<button type="button" class="btn btn-outline-primary float-right add-student" data-toggle="modal" data-target="#add-student"  data-backdrop="static" data-keyboard="false"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add New Student', WL_IM_DOMAIN ); ?>
						</button>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col">
						<table class="table table-hover table-striped table-bordered" id="student-table">
							<thead>
								<tr>
						        	<th scope="col"><?php esc_html_e( 'Enrollment ID', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Course', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Batch', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Duration', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Fees Payable', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Fees Status', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Is Active', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Added By', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Registration Date', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Completion Date', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Edit', WL_IM_DOMAIN ); ?></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<!-- end - card body content -->
			</div>
		</div>
	</div>
	<!-- end - row 2 -->
</div>

<!-- add new student modal -->
<div class="modal fade" id="add-student" tabindex="-1" role="dialog" aria-labelledby="add-student-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="add-student-label"><?php esc_html_e( 'Add New Student', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4">
				<form id="wlim-add-student-form">
					<?php $nonce = wp_create_nonce( 'add-student' ); ?>
	                <input type="hidden" name="add-student" value="<?php echo esc_attr($nonce); ?>">
	                <div class="wlim-add-student-form-fields">
						<div class="form-check pl-0 pb-3 mb-2 border-bottom">
							<input name="from_enquiry" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-student-from_enquiry">
							<label class="form-check-label" for="wlim-student-from_enquiry">
							<?php esc_html_e( 'From Enquiry?', WL_IM_DOMAIN ); ?>
							</label>
						</div>
		                <div id="wlim-add-student-from-enquiries"></div>
		                <div id="wlim-add-student-form-fields">
			                <?php WL_IM_Student::render_add_student_form( $wlim_active_courses ); ?>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary add-student-submit"><?php esc_html_e( 'Add New Student', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - add new student modal -->

<!-- update student modal -->
<div class="modal fade" id="update-student" tabindex="-1" role="dialog" aria-labelledby="update-student-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-student-label"><?php esc_html_e( 'Update Student', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4" id="fetch_student"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary update-student-submit"><?php esc_html_e( 'Update Student', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - update student modal -->