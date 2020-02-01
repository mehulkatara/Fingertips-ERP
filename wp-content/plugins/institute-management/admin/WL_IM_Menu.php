<?php
defined( 'ABSPATH' ) or die();

class WL_IM_Menu {
	/* Add menu */
	public static function create_menu() {
		/* Dashboard menu */
		$dashboard = add_menu_page( esc_html__( 'Institute Management', WL_IM_DOMAIN ), esc_html__( 'Institute', WL_IM_DOMAIN ), 'wl_im_manage_dashboard', 'institute-management', array( 'WL_IM_Menu', 'dashboard' ), 'dashicons-welcome-learn-more', 25 );
		add_action( 'admin_print_styles-' . $dashboard, array( 'WL_IM_Menu', 'dashboard_assets' ) );

		/* Dashboard submenu */
		$dashboard_submenu = add_submenu_page( 'institute-management', esc_html__( 'Institute Management', WL_IM_DOMAIN ), esc_html__( 'Dashboard', WL_IM_DOMAIN ), 'wl_im_manage_dashboard', 'institute-management', array( 'WL_IM_Menu', 'dashboard' ) );
		add_action( 'admin_print_styles-' . $dashboard_submenu, array( 'WL_IM_Menu', 'dashboard_assets' ) );

		/* Courses submenu */
		$courses = add_submenu_page( 'institute-management', esc_html__( 'Courses', WL_IM_DOMAIN ), esc_html__( 'Courses', WL_IM_DOMAIN ), 'wl_im_manage_courses', 'institute-management-courses', array( 'WL_IM_Menu', 'courses' ) );
		add_action( 'admin_print_styles-' . $courses, array( 'WL_IM_Menu', 'courses_assets' ) );

		/* Batches submenu */
		$batches = add_submenu_page( 'institute-management', esc_html__( 'Batches', WL_IM_DOMAIN ), esc_html__( 'Batches', WL_IM_DOMAIN ), 'wl_im_manage_batches', 'institute-management-batches', array( 'WL_IM_Menu', 'batches' ) );
		add_action( 'admin_print_styles-' . $batches, array( 'WL_IM_Menu', 'batches_assets' ) );

		/* Enquiries submenu */
		$enquiries = add_submenu_page( 'institute-management', esc_html__( 'Enquiries', WL_IM_DOMAIN ), esc_html__( 'Enquiries', WL_IM_DOMAIN ), 'wl_im_manage_enquiries', 'institute-management-enquiries', array( 'WL_IM_Menu', 'enquiries' ) );
		add_action( 'admin_print_styles-' . $enquiries, array( 'WL_IM_Menu', 'enquiries_assets' ) );

		/* Students submenu */
		$students = add_submenu_page( 'institute-management', esc_html__( 'Students', WL_IM_DOMAIN ), esc_html__( 'Students', WL_IM_DOMAIN ), 'wl_im_manage_students', 'institute-management-students', array( 'WL_IM_Menu', 'students' ) );
		add_action( 'admin_print_styles-' . $students, array( 'WL_IM_Menu', 'students_assets' ) );

		/* Fees submenu */
		$fees = add_submenu_page( 'institute-management', esc_html__( 'Fees', WL_IM_DOMAIN ), esc_html__( 'Fees', WL_IM_DOMAIN ), 'wl_im_manage_fees', 'institute-management-fees', array( 'WL_IM_Menu', 'fees' ) );
		add_action( 'admin_print_styles-' . $fees, array( 'WL_IM_Menu', 'fees_assets' ) );

		/* Administrators submenu */
		$administrators = add_submenu_page( 'institute-management', esc_html__( 'Administrators', WL_IM_DOMAIN ), esc_html__( 'Administrators', WL_IM_DOMAIN ), 'wl_im_manage_administrators', 'institute-management-administrators', array( 'WL_IM_Menu', 'administrators' ) );
		add_action( 'admin_print_styles-' . $administrators, array( 'WL_IM_Menu', 'administrators_assets' ) );

		/* Settings submenu */
		$settings = add_submenu_page( 'institute-management', esc_html__( 'Settings', WL_IM_DOMAIN ), esc_html__( 'Settings', WL_IM_DOMAIN ), 'wl_im_manage_settings', 'institute-management-settings', array( 'WL_IM_Menu', 'settings' ) );
		add_action( 'admin_print_styles-' . $settings, array( 'WL_IM_Menu', 'settings_assets' ) );
	}

	/* Dashboard menu/submenu callback */
	public static function dashboard() {
		require_once( 'inc/wl_im_dashboard.php' );
	}

	/* Dashboard menu/submenu assets */
	public static function dashboard_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Courses menu callback */
	public static function courses() {
		require_once( 'inc/wl_im_courses.php' );
	}

	/* Courses menu assets */
	public static function courses_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Batches menu callback */
	public static function batches() {
		require_once( 'inc/wl_im_batches.php' );
	}

	/* Batches menu assets */
	public static function batches_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Enquiries menu callback */
	public static function enquiries() {
		require_once( 'inc/wl_im_enquiries.php' );
	}

	/* Enquiries menu assets */
	public static function enquiries_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Students menu callback */
	public static function students() {
		require_once( 'inc/wl_im_students.php' );
	}

	/* Students menu assets */
	public static function students_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Fees menu callback */
	public static function fees() {
		require_once( 'inc/wl_im_fees.php' );
	}

	/* Fees menu assets */
	public static function fees_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Administrators menu callback */
	public static function administrators() {
		require_once( 'inc/wl_im_administrators.php' );
	}

	/* Administrators menu assets */
	public static function administrators_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Settings menu callback */
	public static function settings() {
		require_once( 'inc/wl_im_settings.php' );
	}

	/* Settings menu assets */
	public static function settings_assets() {
		self::enqueue_libraries();
		self::enqueue_datatable_assets();
		self::enqueue_custom_assets();
	}

	/* Enqueue third party libraties */
	public static function enqueue_libraries() {
		/* Enqueue styles */
		wp_enqueue_style( 'wl-im-mdb', WL_IM_PLUGIN_URL . '/admin/css/mdb.lite.min.css' );
		wp_enqueue_style( 'bootstrap', WL_IM_PLUGIN_URL . '/admin/css/bootstrap.min.css' );
		wp_enqueue_style( 'font awesome', WL_IM_PLUGIN_URL . '/assets/css/all.min.css' );
		wp_enqueue_style( 'wl-im-bootstrap-select', WL_IM_PLUGIN_URL . '/assets/css/bootstrap-select.min.css' );
		wp_enqueue_style( 'wl-im-toastr', WL_IM_PLUGIN_URL . '/assets/css/toastr.min.css' );
		wp_enqueue_style( 'wl-im-jquery-confirm', WL_IM_PLUGIN_URL . '/admin/css/jquery-confirm.min.css' );
		

		/* Enqueue scripts */
		wp_enqueue_script( 'wl-im-popper-js', WL_IM_PLUGIN_URL . '/assets/js/popper.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wl-im-bootstrap-js', WL_IM_PLUGIN_URL . '/assets/js/bootstrap.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wl-im-bootstrap-select-js', WL_IM_PLUGIN_URL . '/assets/js/bootstrap-select.min.js', array( 'wl-im-bootstrap-js' ), true, true );
		wp_enqueue_script( 'wl-im-toastr-js', WL_IM_PLUGIN_URL . '/assets/js/toastr.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wl-im-jquery-confirm-js', WL_IM_PLUGIN_URL . '/admin/js/jquery-confirm.min.js', array( 'jquery' ), true, true );
	}

	/* Enqueue datatable assets */
	public static function enqueue_datatable_assets() {
		/* Enqueue styles */
		wp_enqueue_style( 'wl-im-mdb-datatables-select', WL_IM_PLUGIN_URL . '/admin/css/datatables-select.min.css' );
		wp_enqueue_style( 'wl-im-mdb-datatables', WL_IM_PLUGIN_URL . '/admin/css/datatables.min.css' );

		/* Enqueue scripts */
		wp_enqueue_script( 'wl-im-mdb-dataTables-js', WL_IM_PLUGIN_URL . '/admin/js/datatables.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wl-im-mdb-dataTables-js', WL_IM_PLUGIN_URL . '/admin/js/datatables-select.min.js', array( 'jquery' ), true, true );


		wp_enqueue_script( 'wl-im-dataTables-responsive-js', WL_IM_PLUGIN_URL . '/admin/js/dataTables.responsive.min.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wl-im-responsive-bootstrap4-js', WL_IM_PLUGIN_URL . '/admin/js/responsive.bootstrap4.min.js', array( 'jquery' ), true, true );
	}

	/* Enqueue custom assets */
	public static function enqueue_custom_assets() {
		/* Enqueue styles */
		wp_enqueue_style( 'wl-im-style', WL_IM_PLUGIN_URL . '/admin/css/wl-im-style.css', array(), '1.6' );

		/* Enqueue scripts */
		wp_enqueue_script( 'wl-im-script-js', WL_IM_PLUGIN_URL . '/admin/js/wl-im-script.js', array( 'jquery' ), true, true );
		wp_enqueue_script( 'wl-im-ajax-js', WL_IM_PLUGIN_URL . '/admin/js/wl-im-ajax.js', array( 'jquery' ), true, true );
		wp_localize_script( 'wl-im-ajax-js', 'WLIMAjax', array( 'security' => wp_create_nonce( 'wl-im' ) ) );
		wp_localize_script( 'wl-im-ajax-js', 'WL_IM_PLUGIN_URL', WL_IM_PLUGIN_URL );
	}
}
?>