<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
define("WPT_POST_TYPE_TABLE", "wptables_table");

class WPTables_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wptables-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jsgrid', WPT_BASE_URL . 'public/js/jsgrid/jsgrid.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jsgrid-theme', WPT_BASE_URL . 'public/js/jsgrid/jsgrid-theme-wptables.min.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wptables-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'wpt_consts', array( 
			'url_add_new_table' => WPTables::url(array('page' => 'wptables-add-new')) 
		));
		wp_enqueue_script( 'jsgrid', WPT_BASE_URL . 'public/js/jsgrid/jsgrid.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-public', WPT_BASE_URL . 'public/js/wptables-public.js', array( 'jquery', 'jsgrid' ), $this->version, false );
		wp_enqueue_script( "jquery-ui-core", array('jquery'));
		wp_enqueue_script( "jquery-ui-sortable", array('jquery','jquery-ui-core'));
	}

	public function add_menu() {
		$page = add_menu_page(
			__('Tables', 'wptables'), 
			__('WPTables', 'wptables'), 
			'manage_options', 
			'wptables',
			array($this, 'render_admin_view'),
			'dashicons-grid-view',
			26
		);
		add_action('load-'.$page, array($this, 'main_add_help'));
		$page = add_submenu_page(
			'wptables',
			__('Add New Table', 'wptables'),
			__('Add New', 'wptables'),
			'manage_options',
			'wptables-add-new',
			array($this, 'render_add_new_table_view' )
		);
		add_action('load-'.$page, array($this, 'main_add_help'));
		$page = add_submenu_page(
			'wptables',
			__('About WordPress Tables', 'wptables'),
			__('About', 'wptables'),
			'manage_options',
			'wptables-about',
			array($this, 'render_about' )
		);
		add_action('load-'.$page, array($this, 'main_add_help'));
	}

	public function render_admin_view() {
		include("partials/view-wptables-admin.php");
	}

	public function render_add_new_table_view() {
		include("partials/view-wptables-add-new-table.php");
	}

	public function render_about() {
		include("partials/view-wptables-about.php");
	}

	public function main_add_help() {
		$screen = get_current_screen();
	    $screen->add_help_tab(array(
	        'id'	=> 'wptables-help',
	        'title'	=> __('WordPress Tables'),
	        'content'	=> 
	        '<p>' . sprintf(__( 'Please visit <a href="%s" target="_blank">WordPress Tables page</a> to find more information about the plugin.', 'wptables'), WPTables::URL_PLUGIN_PAGE). '</p>'
	        .'<p>' . sprintf(__( 'You are welcome to leave your feedbacks and report issues on the <a href="%s" target="_blank">Support Forum</a>.', 'wptables'), WPTables::URL_SUPPORT) . '</p>'
	        .'<p>' . __( 'Also, any ideas and possible improvements are highly appreciated!', 'wptables' ) . '</p>'
	        .'<p>' . sprintf(__( 'Follow us on <a href="%1$s" target="_blank">Facebook</a> and <a href="%2$s" target="_blank">Twitter</a> to get the latest news and tutorials.', 'wptables'), WPTables::URL_FACEBOOK, WPTables::URL_TWITTER ). '</p>'
	    ));
	    $screen->set_help_sidebar( 
	    	'<p><strong>' . __( 'For more information:', 'wptables' ) . '</strong></p>'
	    	."<p><a href='".WPTables::URL_PLUGIN_PAGE."' target='_blank'>Documentation</a></p>"
	    	."<p><a href='".WPTables::URL_SUPPORT."' target='_blank'>Support Forum</a></p>"
	    	."<p><a href='".WPTables::URL_FACEBOOK."' target='_blank'>Facebook</a></p>"
	    	."<p><a href='".WPTables::URL_TWITTER."' target='_blank'>Twitter</a></p>"
	    );
	}

	public function action_add_new_table() {
		check_admin_referer('wpt-add-new-table');
		$title = isset($_POST['title']) && !empty($_POST['title']) ? $_POST['title'] : 'New Table';
		$format = $_POST['format'];
		if ($format == 'manual') {
			$type = "data";
			$cols = $_POST['input-cols'];
			$rows = $_POST['input-rows'];
			$parser = new WPTables_ManualData();
			$data = $parser->create_data($cols, $rows);
		}
		if ($format == 'csv') {
			$type = $_POST['input-type-csv'];
			$parser = new WPTables_CsvParser();
			if ($type == 'file') {
				$data = $parser->parse_file($_FILES['data-file']['tmp_name']);
			} elseif ($type == 'url') {
				$data = $parser->parse_file($_POST['data-url']);
			} elseif ($type == 'text') {
				$data = $parser->parse_text($_POST['data-text']);
			}
		}
		if ($format == 'mysql') {
			$type = $_POST['input-type-mysql'];
			$parser = new WPTables_MySqlParser();
			if ($type == 'db-table') {
				$data = $parser->parse_table($_POST['data-db-table']);
			} elseif ($type == 'sql-query') {
				//$data = $parser->parse_query($_POST['data-query']);
			}
		}
		if (isset($data) && $data !== false) {
			$post_id = wp_insert_post(array(
				'post_title' => $title,
				'post_type'	=> WPT_POST_TYPE_TABLE,
				'post_content' => json_encode($data['data']),
				'post_mime_type' => "{$format}/{$type}"
			));
			if (is_wp_error($post_id)){
			   $error_msg = $post_id->get_error_message();
			} else {
				update_post_meta($post_id, 'wpt_fields', json_encode($data['fields']));
				update_post_meta($post_id, 'wpt_options', json_encode($this->default_options()));
				WPTables::redirect(array('page' => 'wptables', 'action' => 'edit', 'table' => $post_id));
			}
		} else {
			$error_msg = __('Error: Please specify valid data to import.', 'wptables');
		}

		if (isset($error_msg) && !empty($error_msg)) {
			WPTables::redirect(array(
				'page' => 'wptables-add-new',
				'title' => urlencode($title),
				'error_msg'	=> urlencode($error_msg)
			));
		}
	}

	private function default_options() {
		return array(
			'sorting' 	=> true,
			'selecting' => true,
			'heading'	=> true,
			'paging'	=> true,
			'pageSize'	=> 20
		);
	}

	public function action_update_table() {
		$post_id = $_POST['table'];
		check_admin_referer( 'wpt-update-table-'.$post_id);

		var_dump($_POST['data']);

		// update post
		$new_post = array();
		$new_post['ID'] = $post_id;
		$new_post['post_title'] = isset($_POST['title']) && !empty($_POST['title']) ? $_POST['title'] : __('New Table', 'wptables');
		if (isset($_POST['data'])) {
			$new_post['post_content'] = json_encode($_POST['data']);
		}
		$result = wp_update_post($new_post);
		if (is_wp_error($result)) {
			$error_msg = $result->get_error_message();
		} else {
			// update fields
			$fields = array();
			foreach ($_POST['fields'] as $name => $item) {
				$field = array(
					'name'		=> $name,
					'title'		=> $item['title'],
					'type'		=> $item['type']
				);
				$field['visible'] = isset($item['visible']) && $item['visible'] == 'on';
				if (isset($item['width']) && !empty($item['width'])) {
					$field['width'] = $item['width'];
				}
				if (isset($item['align']) && !empty($item['align'])) {
					$field['align'] = $item['align'];
				}
				if (isset($item['css']) && !empty($item['css'])) {
					$field['css'] = $item['css'];
				}
				$fields[] = $field; 
			}
			update_post_meta($post_id, 'wpt_fields', json_encode($fields));

			// update options
			$options = array();
			$config = $_POST['config'];
			var_dump($config);
			if (isset($config['width']) && !empty($config['width'])) {
				$options['width'] = $config['width'];
			}
			if (isset($config['width-u']) && !empty($config['width-u'])) {
				$options['width-u'] = $config['width-u'];
			}
			if (isset($config['height']) && !empty($config['height'])) {
				$options['height'] = $config['height'];
			}
			if (isset($config['height-u']) && !empty($config['height-u'])) {
				$options['height-u'] = $config['height-u'];
			}
			$options['sorting'] = isset($config['sorting']) && $config['sorting'] == 'on';
			$options['selecting'] = isset($config['selecting']) && $config['selecting'] == 'on';
			$options['heading'] = isset($config['heading']) && $config['heading'] == 'on';
			$options['paging'] = isset($config['paging']) && $config['paging'] == 'on';
			if (isset($config['pageSize']) && !empty($config['pageSize'])) {
				$options['pageSize'] = $config['pageSize'];
			}
			if (isset($config['theme'])) {
				$options['theme'] = $config['theme'];
			}
			update_post_meta($post_id, 'wpt_options', json_encode($options));
		}
		$redirect = array('page' => 'wptables', 'action' => 'edit', 'table' => $post_id);
		if (isset($error_msg) && !empty($error_msg)) {
			$redirect['error_msg'] = $error_msg;
		}
		WPTables::redirect($redirect);
	}

	public function action_delete_table() {
		$post_id = $_GET['table'];
		check_admin_referer('wpt-delete-table-'.$post_id);
		delete_post_meta($post_id, 'wpt_fields');
		delete_post_meta($post_id, 'wpt_options');
		wp_delete_post($post_id, true);
		WPTables::redirect(array('page' => 'wptables'));
	}

	public function filter_mce_buttons($buttons) {
		array_push( $buttons, 'WPTables_insert_table' );
   		return $buttons;
	}

	public function filter_mce_external_plugins() {
		$plugin_array['wptables_tinymce'] = WPT_BASE_URL.'admin/js/wptables-tinymce-plugin.js';
   		return $plugin_array;
	}

	public function ajax_tinymce_get_tables() {
		$output = array();
		$query = new WP_Query( array( 'post_type' => WPT_POST_TYPE_TABLE ) );
		while ( $query->have_posts() ) { 
			$query->the_post();
			$output[] = array(
				'text' 	=> get_the_title(), 
				'value'	=> WPTables::shortcode_table(get_the_ID())
			);
		}
		echo json_encode($output);
		die();
	}

	public function action_export_csv() {
		if (isset($_GET['table'])) {
			$post_id = $_GET['table'];
			check_admin_referer('wpt-export-csv-'.$post_id);
			$post = get_post($post_id);
			$title = $post->post_title;
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename={$title}.csv");
			$csv = new WPTables_CsvExport($post_id);
			echo $csv->export();
		}
		die();
	}
}