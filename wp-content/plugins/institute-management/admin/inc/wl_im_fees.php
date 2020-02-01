<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

$wlim_active_students = WL_IM_Helper::get_active_students();
?>

<div class="container-fluid wl_im_container">
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center text-white blue blue-gradient"><span class="border-bottom"><i class="fas fa-dollar-sign"></i> <?php esc_html_e( 'Fees', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can find installments or add a new installment.', WL_IM_DOMAIN ); ?>
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
						<div class="h3"><?php esc_html_e( 'Manage Fees', WL_IM_DOMAIN ); ?></div>
					</div>
					<div class="col-md-3 col-xs-12">
						<button type="button" class="btn btn-outline-primary float-right add-installment" data-toggle="modal" data-target="#add-installment"  data-backdrop="static" data-keyboard="false"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add New Installment', WL_IM_DOMAIN ); ?>
						</button>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col">
						<table class="table table-hover table-striped table-bordered" id="installment-table">
							<thead>
								<tr>
						        	<th scope="col"><?php esc_html_e( 'Receipt', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Amount', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Enrollment ID', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Student Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Date', WL_IM_DOMAIN ); ?></th>
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

<!-- add new installment modal -->
<div class="modal fade" id="add-installment" tabindex="-1" role="dialog" aria-labelledby="add-installment-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="add-installment-label"><?php esc_html_e( 'Add New Installment', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4">
				<form id="wlim-add-installment-form">
					<?php $nonce = wp_create_nonce( 'add-installment' ); ?>
	                <input type="hidden" name="add-installment" value="<?php echo esc_attr($nonce); ?>">
	                <div class="wlim-add-installment-form-fields">
						<div class="form-group">
	                        <label for="wlim-installment-student" class="col-form-label"><?php esc_html_e( "Student", WL_IM_DOMAIN ); ?>:</label>
	                        <select name="student" class="form-control selectpicker" id="wlim-installment-student">
	                            <option value="">-------- <?php esc_html_e( "Select a Student", WL_IM_DOMAIN ); ?> --------</option>
	                        <?php
	                        if ( count( $wlim_active_students ) > 0 ) {
	                            foreach ( $wlim_active_students as $active_student ) {  ?>
	                            <option value="<?php echo esc_attr($active_student->id); ?>"><?php echo "$active_student->first_name $active_student->last_name (" . WL_IM_Helper::get_enrollment_id( $active_student->id ) . ")"; ?></option>
	                        <?php
	                            }
	                        } ?>
	                        </select>
	                        <div id="wlim_add_installment_fetch_fees"></div>
	                	</div>
						<div class="form-group">
							<label for="wlim-installment-amount" class="col-form-label"><?php esc_html_e( 'Amount', WL_IM_DOMAIN ); ?>:</label>
							<input name="amount" type="number" class="form-control" id="wlim-installment-amount" placeholder="<?php esc_attr_e( "Amount", WL_IM_DOMAIN ); ?>" min="0">
						</div>
	                </div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary add-installment-submit"><?php esc_html_e( 'Add New Installment', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - add new installment modal -->

<!-- update installment modal -->
<div class="modal fade" id="update-installment" tabindex="-1" role="dialog" aria-labelledby="update-installment-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-installment-label"><?php esc_html_e( 'Update Installment', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4" id="fetch_installment"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary update-installment-submit"><?php esc_html_e( 'Update Installment', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - update installment modal -->