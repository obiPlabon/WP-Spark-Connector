<?php 

class Spark_Route_Shop{
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
        
        add_action( 'rest_api_init', array($this, 'spark_routes') );

    }
    /**
     * shop
     * receive request through rest api
     * to add product to cart
     *  */  
    public function spark_routes(){
        register_rest_route( 'spark', '/shop', array(
            'methods' => 'GET',
            'callback' => array($this, 'spark_shop_receive_api_request'),
        ) );
    }

    /**
     * add product to cart
     */
    public function add_product_to_cart($product_id, $product_quantity="1",  $variation_id="", $variations=[]){
        

        
        global $woocommerce;
        $product_cart_status = false;
        $product = wc_get_product( $product_id );
        $title = $product->get_title();

        var_dump('product status', $title);



        /**
         * check if product is in the cart
         */
        $in_cart = WC()->cart->find_product_in_cart( $product_id );
        

        /**
         * add product to cart 
         * if not is in the cart
         */
        var_dump('product id', $product_id);
        if($product_id ){
            $add_to_cart = $woocommerce->cart->add_to_cart($product_id, $product_quantity, $variation_id, $variations);
            var_dump('is added');
            return true;
        }else{
            var_dump( 'is not added. This may already is in cart');
            return false;
        }

    }

    /**
     * receive api request
     */
    public function spark_shop_receive_api_request ($request){
        global $woocommerce;
        session_start();
        $current_session = session_id();
        /**
         * if request for single product
         * then add that single product to cart
         */
        if($request['product_id']){
            $product_id = $request['product_id'];
            $product_add_status = [];

            $status = $this->add_product_to_cart($product_id);
            array_push($product_add_status, $status);

            /**
             * return checkout url
             */
            $cart_item = count($woocommerce->cart->get_cart());
            if($cart_item > 0 && !empty($product_add_status)){
                header('Location: ' . wc_get_cart_url());
                die();
            }
            
        }
        /**
         * multi product request
         */
        if($request['products']){

            $product_add_status = [];
            $product_list = $request['products'];
            $product_arr = explode(',', $product_list );

            foreach($product_arr as $product_id){

                /**
                 * if $product_id is string like below
                 * 13_292827_1_color-Blue_size-Medium
                 * 13_|29|28|27_1_color-Blue_size-Large
                 * that means it variation proudct
                 * it contains three data 
                 * that is 
                 * product id
                 * variation id
                 * product quantity
                 * product attributes
                 */
                if( strpos( $product_id, '_' ) !== false ) {

                    $variations = array();

                    $split_the_string = explode("_", $product_id); 
                    $product_id = $split_the_string[0];
                    $variation_id = $split_the_string[1];
                    $variation_id = explode("|", $variation_id)[1];
                    // var_dump('variation id', explode("|", $variation_id)[1]);
                    $product_quantity = $split_the_string[2];
                    $variations_str = array_slice($split_the_string, 3); 

                    foreach($variations_str as $var_str){
                        $variation_split_value = explode("-", $var_str); 
                        $key = $variation_split_value[0];
                        $value = $variation_split_value[1];
                        $variations[$key] = $value;
                    }

                    $status = $this->add_product_to_cart( $product_id, $product_quantity, $variation_id, $variations);
                    array_push($product_add_status, $status);
                                
                }else{
                    var_dump('product idx', $product_id);
                    $status = $this->add_product_to_cart($product_id);
                    array_push($product_add_status, $status);
                }
                /**
                 * unused code
                 * =============
                 * $status = $this->add_product_to_cart($product_id);
                 * array_push($product_add_status, $status);
                 */
                
            }
            /**
             * return checkout url
             */
            $cart_item = count($woocommerce->cart->get_cart());
            if($cart_item > 0 && !empty($product_add_status) ){
                header('Location: ' . wc_get_cart_url());
                die();
            }
            
        }
        
    }

/**
 * below brace is the end of this class
 */
}