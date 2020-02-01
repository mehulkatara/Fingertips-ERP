<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

$wlim_active_courses = WL_IM_Helper::get_active_courses();
?>
<div class="wl_im_container wl_im">
	<div class="row">
    	<div class="col-xs-12 container-fluid">
    		<div class="card ">
    			<?php if ( get_option( 'enable_enquiry_form_title' ) ) { ?>
    			<div class="card-header">
	    			<div class="text-center wl_im_heading_title"><h2><span><?php esc_html_e(  get_option( 'enquiry_form_title' ), WL_IM_DOMAIN ); ?></span></h2></div>
    			</div>
    			<?php
    			} ?>
    			<div class="card-body">
					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="wlim-add-enquiry-form">
						<?php $nonce = wp_create_nonce( 'add-enquiry' ); ?>
			            <input type="hidden" name="add-enquiry" value="<?php echo esc_attr($nonce); ?>">
			            <div class="form-group">
			                <label for="wlim-enquiry-course" class="col-form-label"><?php esc_html_e( "Course", WL_IM_DOMAIN ); ?>:</label>
			                <select name="course" class="form-control" id="wlim-enquiry-course">
			                    <option value="">-------- <?php esc_html_e( "Select a Course", WL_IM_DOMAIN ); ?> --------</option>
			                <?php
			                if ( count( $wlim_active_courses ) > 0 ) {
			                    foreach ( $wlim_active_courses as $active_course ) {  ?>
			                    <option value="<?php echo esc_attr( $active_course->id ); ?>"><?php echo "$active_course->course_name ($active_course->course_code)"; ?></option>
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
						<div class="mt-3">
							<button type="button" class="btn btn-block add-enquiry-submit"><?php esc_html_e( 'Submit!', WL_IM_DOMAIN ); ?></button>
						</div>
					</form>
    			</div>
    		</div>
		</div>
	</div>
</div>