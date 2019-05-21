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
	Spark_Admin_Menu::init();
	TGC_Routes::init();
}
add_action('plugins_loaded', 'spark_core_load');

/**
 * create spark build table
 */
function spark_create_build_table(){
	require_once SPARK_CORE_ROOT. '/inc/build_table.php';
	Spark_Build::spark_create_build_table();
}
register_activation_hook( __FILE__, 'spark_create_build_table' );

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
	wp_enqueue_style('spark-uikit', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.1.5/css/uikit.min.css');

	// wp_enqueue_script('spark-uikit-js', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.1.5/js/uikit.min.js', array('jquery'), '1.0', true);
	// wp_enqueue_script('spark-uikit-icon', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.1.5/js/uikit-icons.min.js', array('jquery'), '1.0', true);
	// wp_enqueue_script('new-script', plugin_dir_url(__FILE__). 'assets/js/sparkScript.js', array('jquery'), '1.0', false);
	wp_enqueue_script('form_handle', plugin_dir_url(__FILE__). 'assets/js/sparkScript.js', array('jquery'), '1.0', false);
	wp_localize_script( 'form_handle', 'adminUrl', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'mysiteurl' =>  site_url(),
	));
}

