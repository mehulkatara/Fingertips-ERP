<?php
defined( 'ABSPATH' ) or die();

class WL_IM_Language {
	public static function load_translation() {
		load_plugin_textdomain( WL_IM_DOMAIN, false, basename( WL_IM_PLUGIN_DIR_PATH ) . '/languages' );
	}
}