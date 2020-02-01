<?php
/*
 * Plugin Name: Institute Management - Learning Management System 
 * Plugin URI: https://wordpress.org/plugins/institute-management/
 * Description: Institute Management is a comprehensive plugin to manage institute related activities such as courses, batches, enquirers, registrations, fees, students, staff etc.
 * Version: 2.8
 * Author: Weblizar
 * Author URI: https://weblizar.com
 * Text Domain: WL-IM
*/

defined( 'ABSPATH' ) or die();

if ( ! defined( 'WL_IM_DOMAIN' ) ) {
	define( 'WL_IM_DOMAIN', 'WL-IM' );
}

if ( ! defined( 'WL_IM_PLUGIN_URL') ) {
	define( 'WL_IM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WL_IM_PLUGIN_DIR_PATH' ) ) {
	define( 'WL_IM_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

function ism_links( $links ) {
	$ism_pro_link = '<a href="http://demo.weblizar.com/multi-institute-management/" target="_blank">' . esc_html__( 'Try Pro', WL_IM_DOMAIN ) . '</a>';
	array_unshift( $links, $ism_pro_link );
	return $links;
}
$ism_plugin_name = plugin_basename(__FILE__);
add_filter("plugin_action_links_$ism_plugin_name", 'ism_links' );

final class WL_IM_InstituteManagement {
	private static $instance = null;

	private function __construct() {
		$this->initialize_hooks();
		$this->setup_database();
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function initialize_hooks() {
		if ( is_admin() ) {
			require_once( 'admin/admin.php' );
		}
		require_once( 'public/public.php' );
	}

	private function setup_database() {
		require_once( 'admin/WL_IM_Database.php' );
		register_activation_hook( __FILE__, array( 'WL_IM_Database', 'activation' ) );
	}
}
WL_IM_InstituteManagement::get_instance();
?>