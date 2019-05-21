<?php
add_action('wp_ajax_get_connector_app_response', 'get_connector_app_response');
add_action('wp_ajax_nopriv_get_connector_app_response', 'get_connector_app_response');

function get_connector_app_response(){
	$data = $_POST['data'];
	$token = $_POST['token'];
	$token_status = add_option( 'spark_app_token', $token, '', 'yes');
	// $key_status = add_option( 'tg_woo_key', $data['woocommerce_key'], '', 'yes');
	// $secret_status = add_option( 'tg_woo_secret', $data['woocommerce_secret'], '', 'yes' );
	var_dump($data, $token);	
    die();
}

add_action('wp_ajax_update_build_status', 'update_build_status');
add_action('wp_ajax_nopriv_update_build_status', 'update_build_status');
function update_build_status(){
	$data = $_POST['data'];
	$data = (int) $data;
	$build_count = add_option( 'spark_build_count', $data, '', 'yes');

	if(get_option('spark_build_count')){
		$today_build_number = get_option('spark_build_count');
		$today_build_number += $data;
		$update_status = update_option('spark_build_count', $today_build_number, 'yes');
		var_dump('update status', $update_status);
	}
	var_dump($build_count);
	die();
}


add_action('wp_ajax_spark_remove_token', 'spark_remove_token');
add_action('wp_ajax_nopriv_spark_remove_token', 'spark_remove_token');
function spark_remove_token(){
	$toten_delete_status = delete_option('spark_app_token');
	$count_delete_status = delete_option('spark_build_count');

	// if(get_option('spark_build_count')){
	// 	$today_build_number = get_option('spark_build_count');
	// 	$today_build_number += $data;
	// 	$update_status = update_option('spark_build_count', $today_build_number, 'yes');
	// 	var_dump('update status', $update_status);
	// }
	// return ['token_deleted'=> $toten_delete_status, 'count_deleted'=> $count_delete_status];
	var_dump('token_deleted', $toten_delete_status, 'count_deleted', $count_delete_status);
	die();
}


?>