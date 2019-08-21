<?php 

class WPSPARKCONNECTOR_Route_Sitedata{
    private static $instance;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        global $wpdb;
		$this->wpdb = $wpdb;

        add_action( 'rest_api_init', array($this, 'wpsparkconnector_routes') );
    }
    
    public function wpsparkconnector_routes(){
        register_rest_route('spark', '/sitedata', array(
            'methods' => 'get',
            'callback' => array($this, 'wpsparkconnector_pull_site_meta_data')
        ));
    }
    
    public function wpsparkconnector_pull_site_meta_data(){
        /**
         * get_custom_logo() is a built in function or wordpress
         * Returns a custom logo, linked to home.
         */
        $logo_url = $this->wpsparkconnector_custom_logo_url(get_custom_logo());
        
        $site_data = [
            'favicon' => get_site_icon_url(),
            'logo' => $logo_url,
        ];

        return new WP_REST_Response($site_data, 200);

    }

    public function wpsparkconnector_custom_logo_url ( $html ) {

        $custom_logo_id = get_theme_mod( 'custom_logo' );
        $html = wp_get_attachment_url($custom_logo_id);
        return $html;    
    }
    

/**
 * below brace is the end of this class
 */
}