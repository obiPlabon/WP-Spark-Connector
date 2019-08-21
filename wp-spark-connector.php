<?php
/**
 * Plugin Name: WP Spark Connector
 * Plugin URI: https://wpspark.io/
 * Author: Themesgrove
 * Author URI: https://themesgrove.com/
 * Description: A Connector Plugin for WP Spark Applications
 * Version:1.0
 * License: GPLv2 or Later
 * Text Domain: wpsparkconnector
 *  */

/**
 * If this file is called directly, abort.
 */
if (!defined('WPINC')) {
    die;
}

/**
 * define the core root file
 */
define('WPSPARKCONNECTOR_CORE_ROOT', untrailingslashit(plugin_dir_path(__FILE__)));


/**
 * require all files from routes directory
 */
$routes = glob(WPSPARKCONNECTOR_CORE_ROOT. '/routes/*.php');
foreach($routes as $route){
    require $route;
}
/**
 * require all files from inc directory
 */
$files = glob(WPSPARKCONNECTOR_CORE_ROOT. '/inc/*.php');
foreach($files as $file){
    require $file;
}

function wpsparkconnector_core_load(){
	WPSPARKCONNECTOR_Admin_Menu::init();
    WPSPARKCONNECTOR_Rest_Field_Media::init();
    WPSPARKCONNECTOR_Rest_Field_User::init();
    WPSPARKCONNECTOR_Route_Shop::init();
    WPSPARKCONNECTOR_Route_VerifyWP::init();
    WPSPARKCONNECTOR_Route_Buildstatus::init();
    WPSPARKCONNECTOR_Route_Sitedata::init();
    WPSPARKCONNECTOR_Route_Userdata::init();
}
add_action('plugins_loaded', 'wpsparkconnector_core_load');

/**
 * create spark build table
 */
function wpsparkconnector_create_build_table()
{
    require_once WPSPARKCONNECTOR_CORE_ROOT . '/db/create_build_table.php';
    WPSPARKCONNECTOR_Build::wpsparkconnector_create_build_table();
}
register_activation_hook(__FILE__, 'wpsparkconnector_create_build_table');
/**
 * remove spark build table data 
 * while remove the plugin
 */
function wpsparkconnector_delete_plugin_database_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'spark_build';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_uninstall_hook(__FILE__, 'wpsparkconnector_delete_plugin_database_table');

/**
 * Flush rewrite rules on
 * plugin activation/deactivation
 */
function wpsparkconnector_core_flush()
{
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wpsparkconnector_core_flush');
register_deactivation_hook(__FILE__, 'wpsparkconnector_core_flush');

/**
 * Load js only inside spark admin page
 */
add_action('current_screen', 'wpsparkconnector_get_page_slug');
function wpsparkconnector_get_page_slug()
{
    $current_screen = get_current_screen()->id;
    if ($current_screen == 'toplevel_page_spark') {
        add_action('admin_enqueue_scripts', 'wpsparkconnector_load_script_to_admin');
    }
}

/**
 * enqueue spark scripts
 */
function wpsparkconnector_load_script_to_admin()
{
    wp_enqueue_style('wpsparkconnector-core', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_style('uikit-style', plugin_dir_url(__FILE__) . 'assets/css/uikit.min.css');
    wp_enqueue_script('wpsparkconnector_script', plugin_dir_url(__FILE__) . 'assets/js/wpSparkConnectorScript.js', ['jquery'], '1.0', false);
    wp_localize_script('wpsparkconnector_script', 'adminUrl', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'mysiteurl' => site_url(),
        'gifurl' => plugin_dir_url(__FILE__) . 'assets/images/ajax-loader.gif'
    ]);
}

add_action(
    'rest_api_init',
    function () {
        if (!function_exists('use_block_editor_for_post_type')) {
            require ABSPATH . 'wp-admin/includes/post.php';
        }
        // Surface all Gutenberg blocks in the WordPress REST API
        $post_types = get_post_types_by_support(['editor']);
        foreach ($post_types as $post_type) {
            if (use_block_editor_for_post_type($post_type)) {
                register_rest_field(
                    $post_type,
                    'blocks',
                    [
                        'get_callback' => function (array $post) {
                            return parse_blocks($post['content']['raw']);
                        },
                    ]
        );
            }
        }
    }
);
