<?php

/**
 * WordPress Tables plugin 
 * 
 * @package           WPTables
 *
 * @wptables
 * Plugin Name:       WordPress Tables
 * Description:       WordPress Tables plugin will add a support of interactive data tables to your WordPress site.
 * Version:           1.2.0
 * Author:            Ian Sadovy<ian.sadovy@gmail.com>
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wptables
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPT_BASE_URL', plugin_dir_url(__FILE__ ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_wptables() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wptables-activator.php';
	WPTables_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_wptables() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wptables-deactivator.php';
	WPTables_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wptables' );
register_deactivation_hook( __FILE__, 'deactivate_wptables' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wptables.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wptables() {
	$plugin = new WPTables();
	$plugin->run();

}
run_wptables();
