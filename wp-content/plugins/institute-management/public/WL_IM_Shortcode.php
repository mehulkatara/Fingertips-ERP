<?php
defined( 'ABSPATH' ) or die();

class WL_IM_Shortcode {
	public static function create_enquiry_form( $attr ) {
		ob_start();
		require_once( 'inc/wl_im_enquiry_form.php' );
		return ob_get_clean();
	}

	public static function enqueue_assets() {
	    global $post;
	    if( is_a( $post, 'WP_Post' ) ) {
	    	if ( has_shortcode( $post->post_content, 'institute_enquiry_form') ) {
				/* Enqueue styles */
				wp_enqueue_style( 'wl-im-bootstrap', WL_IM_PLUGIN_URL . '/public/css/bootstrap.min.css' );
				wp_enqueue_style( 'wl-im-font-awesome', WL_IM_PLUGIN_URL . '/assets/css/all.min.css' );
				wp_enqueue_style( 'wl-mp-toastr', WL_IM_PLUGIN_URL . '/assets/css/toastr.min.css' );
				wp_enqueue_style( 'wl-im-style', WL_IM_PLUGIN_URL . '/public/css/wl-im-style.css' );

				/* Enqueue scripts */
				wp_enqueue_script( 'wl-im-popper-js', WL_IM_PLUGIN_URL . '/assets/js/popper.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wl-im-bootstrap-js', WL_IM_PLUGIN_URL . '/assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wl-mp-toastr-js', WL_IM_PLUGIN_URL . '/assets/js/toastr.min.js', array( 'jquery' ), true, true );
				wp_enqueue_script( 'wl-mp-ajax-js', WL_IM_PLUGIN_URL . '/public/js/wl-mp-ajax.js', array( 'jquery' ), true, true );
	    	}
	    }
	}
}