<?php
defined( 'ABSPATH' ) or die();

class WL_IM_Setting {
	/* Register settings */
	public static function register_settings() {
		register_setting( 'wl_im_settings_group', 'enable_enquiry_form_title' );
		register_setting( 'wl_im_settings_group', 'enquiry_form_title' );
		add_option( 'enable_enquiry_form_title', 'yes' );
		add_option( 'enquiry_form_title', esc_html__( 'Submit your Enquiry', WL_IM_DOMAIN ) );
	}
}