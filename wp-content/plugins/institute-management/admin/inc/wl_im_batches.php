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
			<h1 class="display-4 text-center text-white blue blue-gradient"><span class="border-bottom"><i class="fas fa-object-group"></i> <?php esc_html_e( 'Batches', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can either add a new batch or edit existing batches.', WL_IM_DOMAIN ); ?>
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
						<div class="h3"><?php esc_html_e( 'Manage Batches', WL_IM_DOMAIN ); ?></div>
					</div>
					<div class="col-md-3 col-xs-12">
						<button type="button" class="btn btn-outline-primary float-right add-batch" data-toggle="modal" data-target="#add-batch"  data-backdrop="static" data-keyboard="false"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add New Batch', WL_IM_DOMAIN ); ?>
						</button>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col">
						<table class="table table-hover table-striped table-bordered" id="batch-table">
							<thead>
								<tr>
						        	<th scope="col"><?php esc_html_e( 'Batch Code', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Batch Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Course', WL_IM_DOMAIN ); ?></th>
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

<!-- add new batch modal -->
<div class="modal fade" id="add-batch" tabindex="-1" role="dialog" aria-labelledby="add-batch-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="add-batch-label"><?php esc_html_e( 'Add New Batch', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4">
				<form id="wlim-add-batch-form">
					<?php $nonce = wp_create_nonce( 'add-batch' ); ?>
	                <input type="hidden" name="add-batch" value="<?php echo esc_attr($nonce); ?>">
		            <div class="form-group wlim-selectpicker">
		                <label for="wlim-batch-course" class="col-form-label">* <?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
		                <select name="course" class="form-control selectpicker" id="wlim-batch-course">
		                    <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
		                <?php
		                if ( count( $wlim_active_courses ) > 0 ) {
		                    foreach ( $wlim_active_courses as $active_course ) {  ?>
		                    <option value="<?php echo esc_attr($active_course->id); ?>"><?php echo esc_attr("$active_course->course_name ($active_course->course_code)"); ?></option>
		                <?php
		                    }
		                } ?>
		                </select>
		            </div>
					<div class="row">
						<div class="col form-group">
							<label for="wlim-batch-batch_code" class="col-form-label">* <?php esc_html_e( 'Batch Code', WL_IM_DOMAIN ); ?>:</label>
							<input name="batch_code" type="text" class="form-control" id="wlim-course-batch_code" placeholder="<?php esc_attr_e( "Batch Code", WL_IM_DOMAIN ); ?>">
						</div>
						<div class="col form-group">
							<label for="wlim-batch-batch_name" class="col-form-label"><?php esc_html_e( 'Batch Name', WL_IM_DOMAIN ); ?>:</label>
							<input name="batch_name" type="text" class="form-control" id="wlim-batch-batch_name" placeholder="<?php esc_attr_e( "Batch Name", WL_IM_DOMAIN ); ?>">
						</div>
					</div>
					<div class="form-check pl-0">
						<input name="is_active" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-batch-is_active" checked>
						<label class="form-check-label" for="wlim-batch-is_active">
						<?php esc_html_e( 'Is Active?', WL_IM_DOMAIN ); ?>
						</label>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary add-batch-submit"><?php esc_html_e( 'Add New Batch', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - add new batch modal -->

<!-- update batch modal -->
<div class="modal fade" id="update-batch" tabindex="-1" role="dialog" aria-labelledby="update-batch-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-batch-label"><?php esc_html_e( 'Update Batch', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4" id="fetch_batch"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary update-batch-submit"><?php esc_html_e( 'Update Batch', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - update batch modal -->