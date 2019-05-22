<?php
add_action('wp_ajax_spark_get_connector_app_response', 'spark_get_connector_app_response');
add_action('wp_ajax_nopriv_spark_get_connector_app_response', 'spark_get_connector_app_response');

function spark_get_connector_app_response(){
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

	$db_time = current_time( 'mysql' );
	$message = 'Start building';
	$status = 'null';
	$insert_status = spark_insert_into_build_table($db_time, $message, $status);

	if(get_option('spark_build_count')){
		$today_build_number = get_option('spark_build_count');
		$today_build_number += $data;
		$update_status = update_option('spark_build_count', $today_build_number, 'yes');
		var_dump('update status', $update_status);
	}
	var_dump($build_count, $insert_status);
	die();
}

function spark_insert_into_build_table($time, $message, $status){
	global $wpdb;
	$table_name = $wpdb->prefix . 'spark_build';
	
	$data_insert_status = $wpdb->insert( 
		$table_name, 
		array( 
			'time' => $time, 
			'message' => $message, 
			'status' => $status, 
		) 
	);
	return $data_insert_status;

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

add_action('wp_ajax_spark_check_build_status', 'spark_check_build_status');
add_action('wp_ajax_nopriv_spark_check_build_status', 'spark_check_build_status');
function spark_check_build_status(){
	try {
		global $wpdb;
		$table_name = $wpdb->prefix . 'spark_build';
		$build_id = $_REQUEST['buildId'];
	
		$get_build_status_data = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id={$build_id}" );
		echo json_encode($get_build_status_data);
	}
	catch(Exception $e){
		echo 'Message: '.$e->getMessage();
	}
	
	
	die();
}

?>