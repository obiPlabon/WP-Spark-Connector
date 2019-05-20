<?php
class TGC_Routes{

    private static $instance;
    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
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
        
        $site_data[] = [
            // 'url' => site_url(),
            // 'info' => get_bloginfo('name'),
            // 'description' => get_bloginfo('description'),
            // 'version' => get_bloginfo('version'),
            'icon' => get_site_icon_url(),
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
        var_dump($request);
        return 'I am request';
    }


}

?>