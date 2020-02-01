<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );
?>

<div class="container-fluid wl_im_container">
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center blue-gradient text-white"><span class="border-bottom"><i class="fas fa-graduation-cap  "></i> <?php esc_html_e( 'Courses', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can either add a new course or edit existing courses.', WL_IM_DOMAIN ); ?>
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
						<div class="h3"><?php esc_html_e( 'Manage Courses', WL_IM_DOMAIN ); ?></div>
					</div>
					<div class="col-md-3 col-xs-12">
						<button type="button" class="btn btn-outline-primary float-right add-course" data-toggle="modal" data-target="#add-course"  data-backdrop="static" data-keyboard="false"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add New Course', WL_IM_DOMAIN ); ?>
						</button>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col">
						<table class="table table-hover table-striped table-bordered" id="course-table">
							<thead>
								<tr>
						        	<th scope="col"><?php esc_html_e( 'Course Code', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Course Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Duration', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Fees', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Is Active', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Added On', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Added By', WL_IM_DOMAIN ); ?></th>
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

<!-- add new course modal -->
<div class="modal fade" id="add-course" tabindex="-1" role="dialog" aria-labelledby="add-course-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="add-course-label"><?php esc_html_e( 'Add New Course', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4">
				<form id="wlim-add-course-form">
					<?php $nonce = wp_create_nonce( 'add-course' ); ?>
	                <input type="hidden" name="add-course" value="<?php echo esc_attr($nonce); ?>">
					<div class="row">
						<div class="col form-group">
							<label for="wlim-course-course_code" class="col-form-label"><?php esc_html_e( 'Course Code', WL_IM_DOMAIN ); ?>:</label>
							<input name="course_code" type="text" class="form-control" id="wlim-course-course_code" placeholder="<?php esc_attr_e( "Course Code", WL_IM_DOMAIN ); ?>">
						</div>
						<div class="col form-group">
							<label for="wlim-course-course_name" class="col-form-label"><?php esc_html_e( 'Course Name', WL_IM_DOMAIN ); ?>:</label>
							<input name="course_name" type="text" class="form-control" id="wlim-course-course_name" placeholder="<?php esc_attr_e( "Course Name", WL_IM_DOMAIN ); ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="wlim-course-course_detail" class="col-form-label"><?php esc_html_e( 'Course Detail', WL_IM_DOMAIN ); ?>:</label>
						<textarea name="course_detail" class="form-control" rows="3" id="wlim-course-course_detail" placeholder="<?php esc_attr_e( "Course Detail", WL_IM_DOMAIN ); ?>"></textarea>
					</div>
					<div class="row">
						<div class="col form-group">
							<label for="wlim-course-duration" class="col-form-label"><?php esc_html_e( 'Duration', WL_IM_DOMAIN ); ?>:</label>
							<input name="duration" type="number" class="form-control" id="wlim-course-duration" placeholder="<?php esc_attr_e( "Duration", WL_IM_DOMAIN ); ?>" step="1" min="0">
						</div>
						<div class="col form-group wlim_select_col">
							<label for="wlim-course-duration_in" class="pt-2"><?php esc_html_e( 'Duration In', WL_IM_DOMAIN ); ?>:</label>
							<select name="duration_in" class="form-control" id="wlim-course-duration_in">
								<?php
								foreach( WL_IM_Helper::get_duration_in() as $value ) { ?>
								<option value="<?php echo esc_attr($value); ?>"><?php esc_html_e( $value, WL_IM_DOMAIN ); ?></option>
								<?php
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="wlim-course-fees" class="col-form-label"><?php esc_html_e( 'Fees', WL_IM_DOMAIN ); ?>:</label>
						<input name="fees" type="number" class="form-control" id="wlim-course-fees" placeholder="<?php esc_attr_e( "Fees", WL_IM_DOMAIN ); ?>" min="0">
					</div>
					<div class="form-check pl-0">
						<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-course-is_active" checked>
						<label class="form-check-label" for="wlim-course-is_active">
						<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
						</label>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary add-course-submit"><?php esc_html_e( 'Add New Course', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - add new course modal -->

<!-- update course modal -->
<div class="modal fade" id="update-course" tabindex="-1" role="dialog" aria-labelledby="update-course-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-course-label"><?php esc_html_e( 'Update Course', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4" id="fetch_course"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary update-course-submit"><?php esc_html_e( 'Update Course', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - update course modal -->