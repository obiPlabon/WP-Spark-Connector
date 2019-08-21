<?php 
class WPSPARKCONNECTOR_Rest_Field_Media{
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
        register_rest_field( 'post',  'spark_media', array(
            'get_callback'    => array($this, 'wpsparkconnector_add_spark_media'), 
            'update_callback' => null,
            'schema'          => null,
        ));
    }
    public function wpsparkconnector_add_spark_media($object, $field_name, $request){
        $featured_img_array = wp_get_attachment_image_src(
            $object['featured_media'], 
            'full',  
            false
        );
        
        if($featured_img_array){
            return $featured_img_array[0];
        }else{
            return null;
        }
    }
}
