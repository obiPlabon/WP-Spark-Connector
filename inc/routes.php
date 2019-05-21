<?php
class TGC_Routes{

    private static $instance;
    private $wpdb;
    private $table_name;


    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        global $wpdb;
		$this->wpdb = $wpdb;

		$this->table_name = $this->wpdb->prefix . 'spark_build';
        add_action( 'rest_api_init', array($this, 'tgc_routes') );

    }

    public function tgc_routes(){
        register_rest_route('spark', '/sitedata', array(
            'methods' => 'get',
            'callback' => array($this, 'tgc_pull_site_meta_data')
        ));

        register_rest_route('spark', '/buildstatus', array(
            'methods' => 'POST',
            'callback' => array($this, 'spark_get_build_status')
        ));
    }

    public function tgc_pull_site_meta_data(){

        $logo_url = $this->custom_logo_url(get_custom_logo());
        
        $site_data = [
            // 'url' => site_url(),
            // 'info' => get_bloginfo('name'),
            // 'description' => get_bloginfo('description'),
            // 'version' => get_bloginfo('version'),
            'favicon' => get_site_icon_url(),
            'logo' => $logo_url,
        ];

        return new WP_REST_Response($site_data, 200);

    }

    public function custom_logo_url ( $html ) {

        $custom_logo_id = get_theme_mod( 'custom_logo' );
        // $url = network_site_url();
        // $html = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
        //         esc_url( $url  ),
        //         wp_get_attachment_image( $custom_logo_id, 'full', false, array(
        //             'class'    => 'custom-logo',
        //         ) )
        //     );
        $html = wp_get_attachment_url($custom_logo_id);
        return $html;    
    }

    public function spark_get_build_status($request){
        $request_message = $request['message'];
        $request_status = $request['status'];
        /**
         * build message status
         * building - 201
         * published - 200
         * failed - 500
         * =================
         * At first check if there is any data saved 
         * Against build status 
         * if not then create a new record against that data
         * if exist then update that record 
         * add_option($option, $value, $deprecated, $autoload)
         * get_option($option, $default)
         * delete_option($option)
         * update_option($option, $value, $autoload)
         */
        $build_message_in_db = get_option('spark_build_message');
        $build_status_in_db = get_option('spark_build_status');
        $null_row = $this->wpdb->get_row( "SELECT * FROM {$this->table_name} WHERE status='null'" );
        
        if($build_message_in_db && $build_status_in_db){
            if($null_row){
                $null_id = $null_row->id;
                $this->spark_build_data_update($null_id, $request_message, $request_status);
            }
            $update_build_message = update_option('spark_build_message', $request_message, 'yes');
            $update_build_status = update_option('spark_build_status', $request_status, 'yes');
            return 'update build message to db - '. $update_build_message .' - update build status to db - '. $update_build_status;
        }else{
            if($null_row){
                $null_id = $null_row->id;
                $this->spark_build_data_update($null_id, $request_message, $request_status);
            }
            $add_build_message = add_option('spark_build_message', $request_message, '', 'yes');
            $add_build_status = add_option('spark_build_status', $request_status, '', 'yes');
            return 'add build message to db - '.$add_build_message .' - add build status to db - '. $add_build_status;
        }
        
    }

    public function spark_build_data_update( $id, $message, $status ) {
        $this->wpdb->update( $this->table_name, 
            array(
                'message' => $message,
                'status' => $status
            ), 
            array( 'id' => $id ) 
        );
	}


}

?>