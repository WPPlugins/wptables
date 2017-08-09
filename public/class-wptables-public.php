<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_Public {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wptables-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jsgrid', WPT_BASE_URL . 'public/js/jsgrid/jsgrid.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jsgrid-theme', WPT_BASE_URL . 'public/js/jsgrid/jsgrid-theme-wptables.min.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'jsgrid', WPT_BASE_URL . 'public/js/jsgrid/jsgrid.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wptables-public.js', array( 'jquery', 'jsgrid' ), $this->version, false );

	}

	public function register_shortcodes() {
		add_shortcode('wp_table', array($this, 'shortcode_table'));
	}

	public function shortcode_table($atts) {
		$a = shortcode_atts(array( 
			'id' => '',
			'width'	=> '100%',
			'height' => null
		), $atts );
		$id = "wpt-table-".time().rand(100, 999);
		$meta_fields = get_post_meta($a['id'], 'wpt_fields', true);
		$meta_options = get_post_meta($a['id'], 'wpt_options', true);
		$fields = json_decode($meta_fields, true);
		$options = json_decode($meta_options, true);

		$config = array(
			'_id_div'	=> $id,
			'_ctrl_url'	=> WPTables::url(array('action' => 'wpt_load_data', 'table' => $a['id']), true, 'admin-ajax.php'),
			'width'		=> $a['width'],
			'height'	=> $a['height'],
			'sorting'	=> isset($options['sorting']) ? $options['sorting'] : false,
			'selecting'	=> isset($options['selecting']) ? $options['selecting'] : true,
			'heading'	=> isset($options['heading']) ? $options['heading'] : true,
			'paging'	=> isset($options['paging']) ? $options['paging'] : false,
			'pageSize'	=> isset($options['pageSize']) ? $options['pageSize'] : 20,
			'autoload'	=> true,
			'fields'	=> $fields
		);

		$html = "<div id='{$id}' class='{$options['theme']}'>here will be a table</div>";
		$html .= "<script type='text/javascript'>";
		$html .= "(function($) {";
		$html .= "wpt.createTable(".json_encode($config).");";
		$html .= "})(jQuery);";
		$html .= "</script>";
		return $html;
	}

	public function ajax_load_data() {
		header("Content-Type: application/json");
		$post_id = $_GET['table'];
		check_ajax_referer('wpt-load-data-'.$post_id);
		$post = get_post($post_id);
		if ($post->post_mime_type == 'mysql/db-table') {
			$table_name = json_decode($post->post_content);
			$fields = json_decode(get_post_meta($post->ID, 'wpt_fields', true), true);
			$loader = new WPTables_MySqlLoader();
			echo json_encode($loader->load_table($table_name, $fields));
		} elseif ($post->post_mime_type == 'mysql/sql-query') {
			$query = json_decode($post->post_content);
			$loader = new WPTables_MySqlLoader();
			echo json_encode($loader->load_query($query));
		} else {
			echo $post->post_content;
		}
		die();
	}

}