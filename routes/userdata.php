<?php
class Spark_Route_Userdata{

    private static $instance;


    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        add_action( 'rest_api_init', array($this, 'spark_site_data_route') );
    }

    /**
     * Register your routes here
     * inside this functions
     */
    public function spark_site_data_route(){
        /**
         * return site favicon and logo
         * while you head this endpoint
         */
        register_rest_route('wp/v2', '/userdata', array(
            'methods' => 'get',
            'callback' => array($this, 'spark_pull_site_user_data')
        ));
        
    }

    public function spark_pull_site_user_data(){

        
        $site_data = [
            'favicon' => get_site_icon_url(),
        ];
        $site_url = get_site_url();
        $all_user = [];
        $users = get_users();
        foreach($users as $user){
            $user_meta_data = get_user_meta($user->data->ID);
            $user_id = $user->data->ID;
            $name = get_user_meta($user_id, 'nickname', true);
            $slug = get_user_meta($user_id, 'nickname', true);
            $description = get_user_meta($user_id, 'description', true);
            $user_email = $user->data->user_email;
            $user_gravater = rest_get_avatar_urls($user_email);
            $self_href = [];
            $collection_href = [];
            $self_href[] = (object) array(
                'href' => $site_url."/wp-json/wp/v2/users/". $user_id
            );
            $collection_href[] = (object) array(
                'href' => $site_url."/wp-json/wp/v2/users"
            );
            $links = (object) array(
                'self' => $self_href,
                'collection' => $collection_href,
            );
            $single_user = (object) array(
                'id' => (int)$user_id,
                'name' => $name,
                'slug' => $slug,
                'link' => $site_url."/author/".$slug.'/',
                'description' => $description,
                'avatar_urls' => $user_gravater,
                '_links' => $links
            );
            array_push($all_user, $single_user);
        }
        return new WP_REST_Response($all_user, 200);

    }

/**
 * below brace is the end of this class
 */
}

?>