<?php 
/**
 * Plugin Name: TG App Connector
 * Plugin URI: https://themesgrove.com
 * Author: Themesgrove
 * Author URI: https://themesgrove.com
 * Description: A Connector Plugin for Themesgrove Applications
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
define( 'TGC_CORE_ROOT', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * require files here
 */
require TGC_CORE_ROOT. '/inc/connector.php';
require TGC_CORE_ROOT. '/inc/admin_menu.php';

function tgc_core_load(){
	TGC_Admin_Menu::init();
}
add_action('plugins_loaded', 'tgc_core_load');


/**
 * Flush rewrite rules on 
 * plugin activation/deactivation
 */
function tgc_core_flush() {
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'tgc_core_flush' );
register_deactivation_hook( __FILE__, 'tgc_core_flush' );

/**
 * Load script to admin pages
 */
add_action('admin_enqueue_scripts', 'tgc_load_script_to_admin');
function tgc_load_script_to_admin(){
	wp_enqueue_style('tgc-core', plugin_dir_url(__FILE__). 'assets/css/style.css');

	wp_enqueue_script('new-script', plugin_dir_url(__FILE__). 'assets/js/newScript.js', array('jquery'), '1.0', false);
	wp_enqueue_script('form_handle', plugin_dir_url(__FILE__). 'assets/js/myScript.js', array('jquery'), '1.0', false);
	wp_localize_script( 'form_handle', 'adminUrl', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'mysiteurl' =>  site_url(),
	));
}

