<?php
defined( 'ABSPATH' ) or die();
?>

<div class="container-fluid wl_im_container">
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center text-white blue blue-gradient"><span class="border-bottom"><i class="fas fa-cog"></i> <?php esc_html_e( 'Settings', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e( 'Here, you can view and modify settings.', WL_IM_DOMAIN ); ?>
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
					<div class="col-xs-12">
						<div class="h3"><?php esc_html_e( 'Settings', WL_IM_DOMAIN ); ?></div>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row">
					<div class="col-sm-8 col-md-6 col-lg-4">
						<form action="options.php" method="post">
							<?php
								settings_fields( 'wl_im_settings_group' );
							?>

							<div class="form-check pl-0 mb-3">
								<input name="enable_enquiry_form_title" class="position-static mt-0 form-check-input" type="checkbox" id="wlim-setting-enable_enquiry_form_title" value="yes" <?php checked( get_option( 'enable_enquiry_form_title', 'yes' ), 'yes' ); ?>>
								<label for="wlim-setting-enable_enquiry_form_title" class="form-check-label text-secondary">
									<?php esc_html_e( 'Enable Enquiry Form Title', WL_IM_DOMAIN ); ?>
								</label>
							</div>

							<div class="form-group">
								<label for="wlim-setting-enquiry_form_title" class="col-form-label text-secondary">
									<?php esc_html_e( 'Enquiry Form Title', WL_IM_DOMAIN ); ?>:
								</label>
								<input name="enquiry_form_title" type="text" class="form-control" id="wlim-setting-enquiry_form_title" placeholder="<?php esc_attr_e( "Enquiry Form Title", WL_IM_DOMAIN ); ?>" value="<?php echo get_option( 'enquiry_form_title', 'Submit your Enquiry' ); ?>">
							</div>

							<?php submit_button( esc_html__( 'Save', WL_IM_DOMAIN ) ); ?>
						</form>
					</div>
				</div>
				<!-- end - card body content -->
			</div>
		</div>
	</div>
	<!-- end - row 2 -->
</div>