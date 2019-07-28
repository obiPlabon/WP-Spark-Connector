<?php
class Spark_Admin_Menu
{
    private static $instance;
    private $wpdb;
    private $table_name;
    public static function init(){
        if(null == self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        global $wpdb;
		$this->wpdb = $wpdb;

		$this->table_name = $this->wpdb->prefix . 'spark_build';
        add_action('admin_menu', array($this, 'spark_admin_menu_init'));
        add_action("admin_init", array($this, "spark_display_options"));
        add_action('admin_bar_menu', array($this, "spark_add_toolbar_items"), 80);

    }


    public function spark_admin_menu_init()
    {
        # code...
        // add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position)
        add_menu_page('Spark', 'Spark', 'manage_options', 'spark', array($this, 'tg_connector_admin_menu'), plugin_dir_url(__DIR__). '/assets/images/wpspark-icon-25x.png', 2);
        

    }
    public function tg_connector_admin_menu(){
        ?>
        <div class="tg-app-connector uk-padding">
            <div class="wrap">
                <div id="icon-options-general" class="icon32"></div>

                <div class="registration">
                    <div class="uk-card uk-card-default uk-card-body uk-background-muted">
                        <div class="uk-child-width-expand@s uk-flex uk-flex-middle">
                            <div class="left logo">
                                <img src="<?php echo plugin_dir_url(__DIR__). '/assets/images/wpspark-logo.png';?>" width="200px" alt="">
                            </div>
                            <div class="right uk-text-right status">
                                <?php if(get_option('spark_app_token')):?>
                                    
                                    <?php 
                                        $build_data = $this->spark_get_build_data(get_option('spark_app_token'));
                                        $last_build_data = $this->get_last_build_row();
                                    ?>
                                    <p class="uk-form-horizontal">
                                        <button 
                                        type="submit" 
                                        name="spark-build" 
                                        id="spark-build" 
                                        <?php echo ($last_build_data->status == '') || ($last_build_data->status == 'null')  || ($last_build_data->status == '201') ? 'disabled=true' : '' ;  ?>
                                        class="uk-button uk-button-primary uk-button-medium" 
                                        >Build</button>
                                    </p>
                                <?php else:?>
                                    <p>
                                        <span class="uk-label uk-label-danger uk-padding-small">Not connected</span>
                                    </p>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>

                    

                    <div class="uk-card uk-card-default uk-card-body">
                        <?php 
                        $token = get_option('spark_app_token');
                        if(! empty($token)): ?>
                            <div class="uk-child-width-expand@s uk-grid" id="spark_auth_state" uk-grid>

                                <div class="uk-padding">
                                    <input 
                                    id="spark-app-token"
                                    class="uk-input uk-form-width-large" 
                                    type="text" readonly placeholder="form-success" 
                                    value="<?php echo get_option('spark_app_token'); ?>">
                                    <button href="#" id="disconnect_application" class="uk-button uk-button-danger uk-button-medium">Disconnecte</button>
                                    
                                    <div class="build-status" id="build-status">
                                        <div class="uk-alert-primary uk-alert uk-margin-small-top" style="display:none">
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>You build request has been sent. Please wait for a while ..... </p>
                                        </div>

                                        <div class="uk-alert-success uk-alert uk-margin-small-top" style="display:none">
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Congrutulatio! Your site has been successfully build for the new change.</p>
                                        </div>

                                        <div class="uk-alert-danger uk-alert uk-margin-small-top" style="display:none">
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>There are some problem occurs while build process is happenning. Please contact with support.</p>
                                        </div>
                                    </div>

                                    
                                    <?php if($build_data): ?>
                                    <table class="uk-table uk-table-small uk-table-middle uk-table-hover uk-table-divider uk-table-striped ">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th class="uk-width-small">Time</th>
                                                <th class="uk-width-small">Token</th>
                                                <th>Message</th>
                                                <th>Status Code</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($build_data as $data):?>
                                            <tr class="build-data-row-<?php echo $data->id; ?> ">
                                                <td class="build-id"><?php echo $data->id; ?></td>
                                                <td class="build-time"><?php echo $data->time; ?></td>
                                                <td class="build-token"><?php echo $data->token; ?></td>
                                                <td class="build-message">
                                                    <span class="
                                                        <?php 
                                                            if($data->status == '200'): 
                                                                echo 'uk-text-success';
                                                            elseif($data->status == '201'):
                                                                echo 'uk-text-warning';
                                                            elseif($data->status == '500'):
                                                                echo 'uk-text-danger';
                                                            else:
                                                                echo 'uk-text-primary';
                                                            endif;

                                                        ?>
                                                    ">
                                                        <?php echo ucwords($data->message); ?>
                                                    </span>
                                                </td>
                                                <td class="build-status"><?php echo ucwords($data->status); ?></td>
                                                <?php if( $data->status == '200'):?>
                                                    <td class="check-status-button uk-text-truncate"><span class="uk-text-success">Success</span></td>
                                                <?php elseif($data->status == '500'): ?>
                                                    <td class="check-status-button uk-text-truncate"><span class="uk-text-danger">Build Failed</span></td>
                                                <?php else: ?>
                                                    <td class="check-status-button uk-text-truncate"><span id="check-build-status" class="check-build-status uk-button uk-button-default uk-alert-primary" type="button">Check Status</span></td>
                                                <?php endif;?>
                                            </tr>
                                            <?php endforeach;?>
                                        </tbody>
                                    </table>
                                    <?php endif;?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="uk-child-width-expand@s uk-grid" id="spark_annonymus" uk-grid>
                                <div class="uk-padding uk-width-2-3">
                                    <p class="uk-text-large uk-text-bold">
                                        <?php esc_html_e('Sign-up for API key', 'spark');?>
                                    </p>
                                    
                                    <p class="uk-h4">
                                        In order to get access to build you will need an API key from <a href="http://wpspark.io" target="_blank">WpSpark</a>
                                    </p>
                                    
                                    <ol class="">
                                        <li>Login to our portal</li>
                                        <li>Register your domain</li>
                                        <li>Get an API Key</li>
                                    </ol>
                                </div>
                                <div class="uk-padding uk-padding-remove-right uk-flex uk-flex-middle">
                                    <div class="uk-width-1-1">
                                        <div class="uk-margin" id="email-for-register" style="display:none;">
                                            <input class="uk-input uk-form-large" type="email" placeholder="Your email address to register"/>
                                        </div>
                                        <a href="http://app.wpspark.io/register" target="_blank" id="register-input" class="uk-width-1-1 uk-button uk-button-danger uk-button-large uk-margin-small-bottom">Register For API keys</a>
                                        <br/>
                                        <a href="#" id="already-has-token" class="uk-width-1-1 uk-button uk-button-primary uk-button-large">Already have API keys</a>
                                    </div>
                                </div>
                            </div>

                            <div class="uk-child-width-expand@s uk-grid" id="spark_auth_state" uk-grid style="display:none;">
                                
                                <div class="uk-padding">
                                    <ul class="uk-breadcrumb">
                                        <li><a class="show_resgistration_state" href="#">Register Account</a></li>
                                        <li><a href="#">Connect Account</a></li>
                                    </ul>
                                    <form method="post" action="options.php" class="connect-app-form">
                                        <?php

                                            /**
                                             * add_settings_section callback is displayed here. 
                                             * For every new section we need to call settings_fields.
                                             * settings_fields($option_group)
                                             */
                                            // settings_fields("header_section");
                                            
                                            /**
                                             * all the add_settings_field callbacks is displayed here
                                             * do_settings_fields($page, $section)
                                             * do_settings_sections($page)
                                             */                                            
                                            do_settings_sections("spark");
                                            
                                            /**
                                             * submit_button($text, $type, $name, $wrap, $other_attributes)
                                             */
                                            submit_button('Connect App'); 
                                            
                                        ?>          
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                    
                </div>

                    <!-- <h1> Options</h1> -->
                    
                </div>
            </div>
        </div>
        <?
    }

    /**
     * 1. Define the section heading to describe your section by add_settings_section() function
     * 2. Add your settings field name by add_settings_field()
     * 3. Register settings fields to that settings fields by register_setting()
     */
    public function spark_display_options()
    {
        /**
         * section name, display name, callback to print description of section, page to which section is attached.
         * add_settings_section($id, $title, $callback, $page)
         */
        add_settings_section("header_section", "", array($this, "display_header_options_content"), "spark");

        /**
         * setting name, display name, callback to print form element, page in which field is displayed, section to which it belongs.
         * last field section is optional.
         * add_settings_field($id, $title, $callback, $page, $section, $args);
         */
        add_settings_field("spark_app_token", "Token", array($this, "spark_token"), "spark", "header_section");
        // add_settings_field("spark_woo_token", "WooCommerce Key", array($this, "spark_woo_token"), "spark", "header_section");
        // add_settings_field("spark_woo_secret", "WooCommerce Secret", array($this, "spark_woo_secret"), "spark", "header_section");

        /**
         * section name, form element name, callback for sanitization
         * register_setting($option_group, $option_name, $sanitize_callback)
         */
        register_setting("header_section", "spark_app_token");
        register_setting("header_section", "tg_woo_key");
        register_setting("header_section", "tg_woo_secret");
    }
    
    /**
     * for heading section 
     * Heading title and
     * description text 
     */
    public function display_header_options_content(){echo "";}

    /**
     * For settings body fields
     */
    public function spark_token()
    {
        //id and name of form element should be same as the setting name.
        ?>
        <input type="text" 
            name="spark_app_token" 
            <?php echo get_option('spark_app_token') ? 'readonly': ''; ?> 
            id="spark_app_token" class="uk-input uk-form-width-large" style="width:60%" value="<?php echo get_option('spark_app_token'); ?>" 
        />
        <?php
    }
    public function spark_woo_token()
    {   
        ?>
        <input type="text" name="tg_woo_key" id="tg_woo_key" readonly style="width:60%" value="<?php echo get_option('tg_woo_key'); ?>" />
        <?php
    }
    
    public function spark_woo_secret()
    {   
        ?>
        <input type="text" name="tg_woo_secret" id="tg_woo_secret" readonly style="width:60%" value="<?php echo get_option('tg_woo_secret'); ?>" />
        <?php
    }


    public function spark_add_toolbar_items($admin_bar){
        if(get_option('spark_app_token')){
            $admin_bar->add_menu( array(
                'id'    => 'tg-connector-build',
                'title' => 'Build',
                'href'  => '#',
                'meta'  => array(
                    'title' => __('Build'),    
                    'class' => __('spark-build-button')
                ),
            ));
            // $admin_bar->add_menu( array(
            //     'id'    => 'my-sub-item',
            //     'parent' => 'my-item',
            //     'title' => 'My Sub Menu Item',
            //     'href'  => '#',
            //     'meta'  => array(
            //         'title' => __('My Sub Menu Item'),
            //         'target' => '_blank',
            //         'class' => 'my_menu_item_class'
            //     ),
            // ));
            // $admin_bar->add_menu( array(
            //     'id'    => 'my-second-sub-item',
            //     'parent' => 'my-item',
            //     'title' => 'My Second Sub Menu Item',
            //     'href'  => '#',
            //     'meta'  => array(
            //         'title' => __('My Second Sub Menu Item'),
            //         'target' => '_blank',
            //         'class' => 'my_menu_item_class'
            //     ),
            // ));
        }
    }

    public function spark_get_build_data($token){
        return $this->wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE token='$token' ORDER BY id DESC  ");
    }
    public function get_last_build_row(){
        return $this->wpdb->get_row( "SELECT * FROM {$this->table_name} ORDER BY id DESC LIMIT 1");
    }
    

}




?>