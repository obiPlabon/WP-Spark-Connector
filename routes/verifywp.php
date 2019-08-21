<?php 

class WPSPARKCONNECTOR_Route_VerifyWP{
    private static $instance;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        add_action( 'rest_api_init', array($this, 'wpsparkconnector_routes') );
    }
    /**
     * Verify WordPress Sites
     * from wp spark app
     */ 
    public function wpsparkconnector_routes(){
        register_rest_route('spark', '/verifywp', array(
            'methods' => 'get',
            'callback' => array($this, 'wpsparkconnector_verify_wp_site')
        ));
    }
    /**
     * Verify wordpress site 
     * from wp spark app
     * what will be happen inside here 
     * ----
     * user will press the verify button 
     * inside wpwpark dashboard 
     * then wpspark app will send a request to 
     * wp site through this routes. 
     * this function is responsible to veriry 
     * this wordpress site with wpspark token
     * which is sent through the request
     */
    public function wpsparkconnector_verify_wp_site($request){
        if(! $request['token']){die("You are not allowed baby !!!");}
        $requested_token = $_GET['token'];
        $saved_token = get_option('spark_app_token');
        if($saved_token === $requested_token){
            return 'Verified';
        }else{
            return 'Token Mismatch';
        }
    }
    

/**
 * below brace is the end of this class
 */
}