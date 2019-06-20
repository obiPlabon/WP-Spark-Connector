<?php
/**
 * Plugin Name: Spark
 * Plugin URI: https://wpspark.io/
 * Author: Themesgrove
 * Author URI: https://themesgrove.com/
 * Description: A Connector Plugin for WP Spark Applications
 * Version:1.0
 * License: GPLv2 or Later
 * Text Domain: spark
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
define('SPARK_CORE_ROOT', untrailingslashit(plugin_dir_path(__FILE__)));

/**
* plugin activation library
*/
// require SPARK_CORE_ROOT. '/libs/class-tgm-plugin-activation.php';

/**
 * require files here
 */
// require SPARK_CORE_ROOT. '/inc/plugin_activation.php';
require SPARK_CORE_ROOT . '/inc/connector.php';
require SPARK_CORE_ROOT . '/inc/admin_menu.php';
require SPARK_CORE_ROOT . '/inc/routes.php';

function spark_core_load()
{
    Spark_Admin_Menu::init();
    TGC_Routes::init();
}
add_action('plugins_loaded', 'spark_core_load');

/**
 * create spark build table
 */
function spark_create_build_table()
{
    require_once SPARK_CORE_ROOT . '/inc/build_table.php';
    Spark_Build::spark_create_build_table();
}
register_activation_hook(__FILE__, 'spark_create_build_table');

/**
 * Flush rewrite rules on
 * plugin activation/deactivation
 */
function spark_core_flush()
{
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'spark_core_flush');
register_deactivation_hook(__FILE__, 'spark_core_flush');

add_action('current_screen', 'spark_get_page_slug');

function spark_get_page_slug()
{
    $current_screen = get_current_screen()->id;
    if ($current_screen == 'toplevel_page_spark') {
        add_action('admin_enqueue_scripts', 'spark_load_script_to_admin');
    }
}

/**
 * Load script to admin pages
 */
function spark_load_script_to_admin()
{
    wp_enqueue_style('spark-core', plugin_dir_url(__FILE__) . 'assets/css/style.css');
    wp_enqueue_style('uikit', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.1.5/css/uikit.min.css');
    wp_enqueue_script('spark_script', plugin_dir_url(__FILE__) . 'assets/js/sparkScript.js', ['jquery'], '1.0', false);
    wp_localize_script('spark_script', 'adminUrl', [
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
