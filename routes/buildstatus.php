<?php 

class WPSPARKCONNECTOR_Route_Buildstatus{
    private static $instance;
    private $wpdb;
    private $table_name;
    private $token;

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
        add_action( 'rest_api_init', array($this, 'wpsparkconnector_routes') );
        $this->token = get_option('spark_app_token');
    }
    /**
     * Verify WordPress Sites
     * from wp spark app
     */ 
    public function wpsparkconnector_routes(){
        register_rest_route('spark', '/buildstatus', array(
            'methods' => 'get',
            'callback' => array($this, 'wpsparkconnector_get_build_status')
        ));
    }
    
    public function wpsparkconnector_get_build_status($request){
        $request_message = $_GET['message'];
        $request_status = $_GET['status'];

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
                
        $null_row = $this->wpdb->get_row( "SELECT * FROM {$this->table_name} WHERE token='$this->token' ORDER BY id DESC LIMIT 1" );
        
        if($null_row){
            if( ($null_row->status === 'null') || ($null_row->message == 'building') ) {
                $row_id = $null_row->id;
                $this->wpsparkconnector_build_data_update($row_id, $request_message, $this->token, $request_status);
                return 'database updated';
            }else{
                return 'Bad Request';
            }
        }

    }

    public function wpsparkconnector_build_data_update( $id, $message, $token, $status ) {
        $this->wpdb->update( $this->table_name, 
            array(
                'message' => $message,
                'token' => $token,
                'status' => $status
            ), 
            array( 'id' => $id ) 
        );
    }
    

/**
 * below brace is the end of this class
 */
}