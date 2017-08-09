<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
?>
<?php 
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
	include(plugin_dir_path( dirname( __FILE__ ) ).'partials/view-wptables-edit-table.php');
} else {
	include(plugin_dir_path( dirname( __FILE__ ) ).'partials/view-wptables-list-tables.php');
}