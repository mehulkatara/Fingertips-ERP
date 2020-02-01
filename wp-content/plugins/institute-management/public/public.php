<?php
defined( 'ABSPATH' ) or die();

require_once( 'WL_IM_Language.php' );
require_once( 'WL_IM_Shortcode.php' );
require_once( 'inc/controllers/WL_IM_Enquiry_Front.php' );

add_action( 'plugins_loaded', array( 'WL_IM_Language', 'load_translation' ) );

add_action( 'wp_enqueue_scripts', array( 'WL_IM_Shortcode', 'enqueue_assets' ) );

add_shortcode( 'institute_enquiry_form', array( 'WL_IM_Shortcode', 'create_enquiry_form' ) );

/* Actions to add enquiry */
add_action( 'admin_post_wl-im-add-enquiry', array( 'WL_IM_Enquiry_Front', 'add_enquiry' ) );
add_action( 'admin_post_nopriv_wl-im-add-enquiry', array( 'WL_IM_Enquiry_Front', 'add_enquiry' ) );
?>