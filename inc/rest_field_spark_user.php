<?php 
class WPSPARKCONNECTOR_Rest_Field_User{
    private static $instance;

    public static function init(){
        if(null === self::$instance ){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        add_action( 'rest_api_init', array($this, 'wpsparkconnector_rest_fields') );
    }
    /**
     * create spark media field in wp rest api
     */
    public function wpsparkconnector_rest_fields(){
        register_rest_field( 'post',  'spark_user', array(
            'get_callback'    => array($this, 'wpsparkconnector_add_spark_user'), 
            'update_callback' => null,
            'schema'          => null,
        ));
    }
    
    public function wpsparkconnector_add_spark_user($object, $field_name, $request){

        $user_name = get_the_author_meta( 'nicename', $object['author'] );
        $user_slug = get_the_author_meta( 'nicename', $object['author'] );
        $user_description = get_the_author_meta( 'description', $object['author'] );
        $user_avatar_url = get_avatar_url( $object['author'] );
        $user_avatar = (object) array('wordpress_96' => $user_avatar_url);
        $all_data = (object) array(
            'name' => $user_name, 
            'slug' => $user_slug, 
            'description' => $user_description, 
            'avatar_urls' => $user_avatar
        );

        if($user_name){
            return $all_data;
        }else{
            return null;
        }
    }
}
