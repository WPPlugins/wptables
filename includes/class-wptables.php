<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables {
	const URL_PLUGIN_PAGE = "https://wordpress.org/plugins/wptables/";
	const URL_SUPPORT = "https://wordpress.org/support/plugin/wptables";
	const URL_FACEBOOK = "https://www.facebook.com/wptables/";
	const URL_TWITTER = "https://twitter.com/wptables";

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->plugin_name = 'wptables';
		$this->version = '1.2.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {
		// data parsers
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/data/class-wptables-parser.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/data/class-wptables-csv-parser.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/data/class-wptables-mysql-parser.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/data/class-wptables-mysql-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/data/class-wptables-manual-data.php';
		// data exports
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/export/class-wptables-csv-export.php';
		// generic
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wptables-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wptables-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wptables-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wptables-public.php';
		$this->loader = new WPTables_Loader();
	}

	private function set_locale() {
		$plugin_i18n = new WPTables_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	private function define_admin_hooks() {
		$plugin_admin = new WPTables_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu' );
		$this->loader->add_action( 'admin_post_wpt_add_new_table', $plugin_admin, 'action_add_new_table' );
		$this->loader->add_action( 'admin_post_wpt_update_table', $plugin_admin, 'action_update_table' );
		$this->loader->add_action( 'admin_post_wpt_delete_table', $plugin_admin, 'action_delete_table');
		$this->loader->add_action( 'admin_post_wpt_export_csv', $plugin_admin, 'action_export_csv' );
		$this->loader->add_filter( 'mce_buttons', $plugin_admin, 'filter_mce_buttons' );
		$this->loader->add_filter( 'mce_external_plugins', $plugin_admin, 'filter_mce_external_plugins' );
		$this->loader->add_action( 'wp_ajax_wpt_tinymce_get_tables', $plugin_admin, 'ajax_tinymce_get_tables' );
	}

	private function define_public_hooks() {
		$plugin_public = new WPTables_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes');
		$this->loader->add_action( 'wp_ajax_wpt_load_data', $plugin_public, 'ajax_load_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_wpt_load_data', $plugin_public, 'ajax_load_data');
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

	private static function nonce($params) {
		if (isset($params['action']) && $params['action'] == 'wpt_delete_table') {
			return 'wpt-delete-table-'.$params['table'];
		} elseif (isset($params['action']) && $params['action'] == 'wpt_export_csv') {
			return 'wpt-export-csv-'.$params['table'];
		} elseif (isset($params['action']) && $params['action'] == 'wpt_load_data') {
			return 'wpt-load-data-'.$params['table'];
		}
	}

	public static function url( array $params = array(), $add_nonce = false, $target = '') {
		if (empty($target)) {
			$target = 'admin.php';
		} 
		$url = add_query_arg( $params, admin_url( $target ) );
		if ( $add_nonce ) {
			$url = wp_nonce_url( $url, WPTables::nonce($params) );
		}
		return $url;
	}

	public static function redirect( array $params = array(), $add_nonce = false ) {
		$redirect = self::url( $params );
		wp_redirect( $redirect );
		exit;
	}

	public static function shortcode_table($id) {
		return "[wp_table id={$id}/]";
	}
}
