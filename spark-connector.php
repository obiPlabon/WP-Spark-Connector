<?php 
/**
 * Plugin Name: Spark App Connector
 * Plugin URI: https://wpspark.io/
 * Author: WP Spark
 * Author URI: https://wpspark.io/
 * Description: A Connector Plugin for WP Spark Applications
 * Version:1.0
 * License: GPLv2 or Later
 *  */ 

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * define the core root file
 */
define( 'SPARK_CORE_ROOT', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
* plugin activation library
*/
// require SPARK_CORE_ROOT. '/libs/class-tgm-plugin-activation.php';

/**
 * require files here
 */
// require SPARK_CORE_ROOT. '/inc/plugin_activation.php';
require SPARK_CORE_ROOT. '/inc/connector.php';
require SPARK_CORE_ROOT. '/inc/admin_menu.php';
require SPARK_CORE_ROOT. '/inc/routes.php';

function spark_core_load(){
	TGC_Admin_Menu::init();
	TGC_Routes::init();
}
add_action('plugins_loaded', 'spark_core_load');


/**
 * Flush rewrite rules on 
 * plugin activation/deactivation
 */
function spark_core_flush() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'spark_core_flush' );
register_deactivation_hook( __FILE__, 'spark_core_flush' );

/**
 * Load script to admin pages
 */
add_action('admin_enqueue_scripts', 'spark_load_script_to_admin');
function spark_load_script_to_admin(){
	wp_enqueue_style('tgc-core', plugin_dir_url(__FILE__). 'assets/css/style.css');

	wp_enqueue_script('new-script', plugin_dir_url(__FILE__). 'assets/js/newScript.js', array('jquery'), '1.0', false);
	wp_enqueue_script('form_handle', plugin_dir_url(__FILE__). 'assets/js/myScript.js', array('jquery'), '1.0', false);
	wp_localize_script( 'form_handle', 'adminUrl', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'mysiteurl' =>  site_url(),
	));
}

