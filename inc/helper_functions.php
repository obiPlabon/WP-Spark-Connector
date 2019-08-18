<?php
/**
 * Display User IP in WordPress
 */
function get_the_user_ip() {
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		/**
		 * check ip from share internet
		 */
		$ip = $_SERVER['HTTP_CLIENT_IP']; 
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		/**
		 * to check ip is pass from proxy
		 */
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR']; 
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	return apply_filters( 'wpb_get_ip', $ip );
}

add_shortcode('show_ip', 'get_the_user_ip');

/**
 * remove spark build data while remove the plugin
 */
function spark_delete_plugin_database_table(){
    global $wpdb;
    $table_name = $wpdb->prefix . 'spark_build';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_uninstall_hook(__FILE__, 'spark_delete_plugin_database_table');