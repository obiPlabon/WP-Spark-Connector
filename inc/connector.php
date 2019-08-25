<?php
/**
 * save token to the wp_options table
 * if you need to add woocommerce key option than add below options 
 * inside wpsparkconnector_get_connector_app_response function
 * $key_status = add_option( 'tg_woo_key', $data['woocommerce_key'], '', 'yes');
 * $secret_status = add_option( 'tg_woo_secret', $data['woocommerce_secret'], '', 'yes' );
 */
add_action('wp_ajax_wpsparkconnector_get_connector_app_response', 'wpsparkconnector_get_connector_app_response');
add_action('wp_ajax_nopriv_wpsparkconnector_get_connector_app_response', 'wpsparkconnector_get_connector_app_response');
function wpsparkconnector_get_connector_app_response(){
	check_ajax_referer( 'wpsparkconnector_nonce', 'security' );
	if(current_user_can('administrator')):
		$data = sanitize_text_field($_POST['data']);
		$token = sanitize_text_field($_POST['token']);
		$token_status = add_option( 'spark_app_token', $token, '', 'yes');
	endif;
	die();
}


/**
 * receive the build request success data
 * update wp_spark_build table 
 * with token
 */
add_action('wp_ajax_wpsparkconnector_update_build_status', 'wpsparkconnector_update_build_status');
add_action('wp_ajax_nopriv_wpsparkconnector_update_build_status', 'wpsparkconnector_update_build_status');
function wpsparkconnector_update_build_status(){
	check_ajax_referer( 'wpsparkconnector_nonce', 'security' );
	if(current_user_can('administrator')):
		$data = sanitize_text_field($_POST['data']);
		$token = sanitize_text_field($_POST['token']);
		$data = (int) $data;
		$db_time = current_time( 'mysql' );
		$message = 'Start building';
		$status = 'null';
		$insert_status = wpsparkconnector_insert_into_build_table($db_time, $message, $status, $token);
	endif;
	die();
}
function wpsparkconnector_insert_into_build_table($time, $message, $status, $token){
	global $wpdb;
	$table_name = $wpdb->prefix . 'spark_build';
	
	$data_insert_status = $wpdb->insert( 
		$table_name, 
		array( 
			'time' => $time, 
			'message' => $message, 
			'token' => $token, 
			'status' => $status, 
		),
		array( 
			'%s',
			'%s',
			'%s',
			'%s'
		) 
	);

	return $data_insert_status;
}

/**
 * disconnect user from spark app 
 * delete the token from wp_options table
 */
add_action('wp_ajax_wpsparkconnector_remove_token', 'wpsparkconnector_remove_token');
add_action('wp_ajax_nopriv_wpsparkconnector_remove_token', 'wpsparkconnector_remove_token');
function wpsparkconnector_remove_token(){
	check_ajax_referer( 'wpsparkconnector_nonce', 'security' );
	if(current_user_can('administrator')):
		$toten_delete_status = delete_option('spark_app_token');
		$count_delete_status = delete_option('spark_build_count');
	endif;
	die();
}

/**
 * check the build status
 * this will query from wp_spark_build table 
 * and update the frontend table
 */
add_action('wp_ajax_wpsparkconnector_check_build_status', 'wpsparkconnector_check_build_status');
add_action('wp_ajax_nopriv_wpsparkconnector_check_build_status', 'wpsparkconnector_check_build_status');
function wpsparkconnector_check_build_status(){
	check_ajax_referer( 'wpsparkconnector_nonce', 'security' );
	if(current_user_can('administrator')):
		try {
			global $wpdb;
			$table_name = $wpdb->prefix . 'spark_build';
			$build_id = sanitize_text_field($_REQUEST['buildId']);
		
			$get_build_status_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %s",  $build_id ) );
			echo json_encode($get_build_status_data);
		}
		catch(Exception $e){
			echo 'Message: '.$e->getMessage();
		}
	endif;
	die();
}


function wpsparkconnector_deleted_function_spark_build_count(){
	$build_count = add_option( 'spark_build_count', $data, '', 'yes');
	if(get_option('spark_build_count')){
		$today_build_number = get_option('spark_build_count');
		$today_build_number += $data;
		$update_status = update_option('spark_build_count', $today_build_number, 'yes');
		var_dump('update status', $update_status);
	}
}

?>