<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );
?>

<div class="container-fluid wl_im_container">
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center text-white blue blue-gradient"><span class="border-bottom"><i class="fas fa-user-secret"></i> <?php esc_html_e( 'Administrators', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can either add a new administrator or assign administrative permissions to existing users.', WL_IM_DOMAIN ); ?>
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
						<div class="h3"><?php esc_html_e( 'Manage Administrators', WL_IM_DOMAIN ); ?></div>
					</div>
					<div class="col-md-3 col-xs-12">
						<button type="button" class="btn btn-outline-primary float-right add-administrator" data-toggle="modal" data-target="#add-administrator"  data-backdrop="static" data-keyboard="false"><i class="fas fa-plus"></i> <?php esc_html_e( 'Add New Administrator', WL_IM_DOMAIN ); ?>
						</button>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col">
						<table class="table table-hover table-striped table-bordered" id="administrator-table">
							<thead>
								<tr>
						        	<th scope="col"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Username', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Permissions', WL_IM_DOMAIN ); ?></th>
						        	<th scope="col"><?php esc_html_e( 'Added On', WL_IM_DOMAIN ); ?></th>
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

<!-- add new administrator modal -->
<div class="modal fade" id="add-administrator" tabindex="-1" role="dialog" aria-labelledby="add-administrator-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="add-administrator-label"><?php esc_html_e( 'Add New Administrator', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4">
				<form id="wlim-add-administrator-form">
					<?php $nonce = wp_create_nonce( 'add-administrator' ); ?>
	                <input type="hidden" name="add-administrator" value="<?php echo esc_attr($nonce); ?>">
					<div class="row">
						<div class="col form-group">
							<label for="wlim-administrator-first_name" class="col-form-label"><?php esc_html_e( 'First Name', WL_IM_DOMAIN ); ?>:</label>
							<input name="first_name" type="text" class="form-control" id="wlim-administrator-first_name" placeholder="<?php esc_attr_e( "First Name", WL_IM_DOMAIN ); ?>">
						</div>
						<div class="col form-group">
							<label for="wlim-administrator-last_name" class="col-form-label"><?php esc_html_e( 'Last Name', WL_IM_DOMAIN ); ?>:</label>
							<input name="last_name" type="text" class="form-control" id="wlim-administrator-last_name" placeholder="<?php esc_attr_e( "Last Name", WL_IM_DOMAIN ); ?>">
						</div>
					</div>
					<div class="form-group">
						<label for="wlim-administrator-username" class="col-form-label"><?php esc_html_e( 'Username', WL_IM_DOMAIN ); ?>:</label>
						<input name="username" type="text" class="form-control" id="wlim-administrator-username" placeholder="<?php esc_attr_e( "Username", WL_IM_DOMAIN ); ?>">
					</div>
					<div class="form-group">
						<label for="wlim-administrator-password" class="col-form-label"><?php esc_html_e( 'Password', WL_IM_DOMAIN ); ?>:</label>
						<input name="password" type="password" class="form-control" id="wlim-administrator-password" placeholder="<?php esc_attr_e( "Password", WL_IM_DOMAIN ); ?>">
					</div>
					<div class="form-group">
						<label for="wlim-administrator-password_confirm" class="col-form-label"><?php esc_html_e( 'Confirm Password', WL_IM_DOMAIN ); ?>:</label>
						<input name="password_confirm" type="password" class="form-control" id="wlim-administrator-password_confirm" placeholder="<?php esc_attr_e( "Confirm Password", WL_IM_DOMAIN ); ?>">
					</div>
					<label class="col-form-label"><?php esc_html_e( 'Permissions', WL_IM_DOMAIN ); ?>:</label>
					<?php
					foreach( WL_IM_Helper::get_capabilities() as $capability_key => $capability_value ) { ?>
					<div class="form-check pl-0">
						<input name="permissions[]" class="position-static mt-0 form-check-input" type="checkbox" id="<?php esc_attr($capability_key); ?>" value="<?php echo esc_attr($capability_key); ?>">
						<label class="form-check-label" for="<?php echo esc_attr($capability_key); ?>"><?php esc_html_e( $capability_value, WL_IM_DOMAIN ); ?></label>
					</div>
					<?php
					} ?>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary add-administrator-submit"><?php esc_html_e( 'Add New Administrator', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - add new administrator modal -->

<!-- update administrator modal -->
<div class="modal fade" id="update-administrator" tabindex="-1" role="dialog" aria-labelledby="update-administrator-label" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="update-administrator-label"><?php esc_html_e( 'Update Administrator', WL_IM_DOMAIN ); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pr-4 pl-4" id="fetch_administrator"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php esc_html_e( 'Cancel', WL_IM_DOMAIN ); ?></button>
				<button type="button" class="btn btn-primary update-administrator-submit"><?php esc_html_e( 'Update Administrator', WL_IM_DOMAIN ); ?></button>
			</div>
		</div>
	</div>
</div>
<!-- end - update administrator modal -->