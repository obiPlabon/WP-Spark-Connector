<?php
class Spark_Routes{

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

    /**
     * Register your routes here
     * inside this functions
     */
    public function tgc_routes(){
        /**
         * return site favicon and logo
         * while you head this endpoint
         */
        register_rest_route('spark', '/sitedata', array(
            'methods' => 'get',
            'callback' => array($this, 'tgc_pull_site_meta_data')
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

    
    

/**
 * below brace is the end of this class
 */
}

?>