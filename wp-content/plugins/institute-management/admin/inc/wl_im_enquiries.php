<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

$wlim_active_courses = WL_IM_Helper::get_active_courses();
?>

<div class="container-fluid wl_im_container">
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center text-white blue blue-gradient"><span class="border-bottom"><i class="fas fa-envelope"></i> <?php esc_html_e( 'Enquiries', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can either add a new enquiry or edit existing enquiries.', WL_IM_DOMAIN ); ?>
			</div>
			<div class="text-center">
				<?php esc_html_e( 'To Display Enquiry Form on Front End, Copy and Paste Shortcode', WL_IM_DOMAIN ); ?>:
				<div class="col-12 justify-content-center align-items-center">
					<span class="col-6">
 						<strong id="wl_im_enquiry_form_shortcode">[institute_enquiry_form]</strong>
					</span>
					<span class="col-6">
						<button id="wl_im_enquiry_form_shortcode_copy" class="btn btn-outline-success btn-sm" type="button">Copy</button>
					</span>
				</div>
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
						<div class="h3"><?php esc_html_e( 'Manage Enquiries', WL_IM_DOMAIN ); ?></div>
					</div>
					<div class="col-md-3 col-xs-12">
						<button type="button" class="btn btn-outline-primary float-right add-enquiry" data-toggle="modal" data-target="#add-enquiry"  data-backdrop="static" data-keyboard="false"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add New Enquiry', WL_IM_DOMAIN ); ?>
						</button>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col">
						<table class="table table-hover table-striped table-bordered" id="enquiry-table">
							<thead>
								<tr>
						        	<th scope="col"><?php esc_html_e( 'Enquiry ID', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Course', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Is Active', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Added By', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Date', WL_IM_DOMAIN ); ?></th>
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

<!-- add new enquiry modal -->
<div class="modal fade" id="add-enquiry" tabindex="-1" role="dialog" aria-labelledby="add-enquiry-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="add-enquiry-label"><?php esc_html_e( 'Add New Enquiry', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4">
				<form id="wlim-add-enquiry-form">
					<?php $nonce = wp_create_nonce( 'add-enquiry' ); ?>
	                <input type="hidden" name="add-enquiry" value="<?php echo esc_attr( $nonce ); ?>">
	                <div class="wlim-add-enquiry-form-fields">
		                <div class="form-group">
	                        <label for="wlim-enquiry-course" class="col-form-label"><?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
	                        <select name="course" class="form-control selectpicker" id="wlim-enquiry-course">
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
							<div class="col-sm-6 form-group">
								<label for="wlim-enquiry-first_name" class="col-form-label"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?>:</label>
								<input name="first_name" type="text" class="form-control" id="wlim-enquiry-first_name" placeholder="<?php esc_attr_e( "First Name", WL_IM_DOMAIN ); ?>">
							</div>
							<div class="col-sm-6 form-group">
								<label for="wlim-enquiry-last_name" class="col-form-label"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?>:</label>
								<input name="last_name" type="text" class="form-control" id="wlim-enquiry-last_name" placeholder="<?php esc_attr_e( "Last Name", WL_IM_DOMAIN ); ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="wlim-enquiry-phone" class="col-form-label"><?php esc_html_e( 'Phone', WL_IM_DOMAIN ); ?>:</label>
							<input name="phone" type="text" class="form-control" id="wlim-enquiry-phone" placeholder="<?php esc_attr_e( "Phone", WL_IM_DOMAIN ); ?>">
						</div>
						<div class="form-group">
							<label for="wlim-enquiry-email" class="col-form-label"><?php esc_html_e( 'Email', WL_IM_DOMAIN ); ?>:</label>
							<input name="email" type="email" class="form-control" id="wlim-enquiry-email" placeholder="<?php esc_attr_e( "Email", WL_IM_DOMAIN ); ?>">
						</div>
						<div class="form-group">
							<label for="wlim-enquiry-message" class="col-form-label"><?php esc_html_e( 'Message', WL_IM_DOMAIN ); ?>:</label>
							<textarea name="message" class="form-control" rows="3" id="wlim-enquiry-message" placeholder="<?php esc_attr_e( "Message", WL_IM_DOMAIN ); ?>"></textarea>
						</div>
						<div class="form-check pl-0">
							<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-enquiry-is_active" checked>
							<label class="form-check-label" for="wlim-enquiry-is_active">
							<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
							</label>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary add-enquiry-submit"><?php esc_html_e( 'Add New Enquiry', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - add new enquiry modal -->

<!-- update enquiry modal -->
<div class="modal fade" id="update-enquiry" tabindex="-1" role="dialog" aria-labelledby="update-enquiry-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-enquiry-label"><?php esc_html_e( 'Update Enquiry', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4" id="fetch_enquiry"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary update-enquiry-submit"><?php esc_html_e( 'Update Enquiry', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - update enquiry modal -->