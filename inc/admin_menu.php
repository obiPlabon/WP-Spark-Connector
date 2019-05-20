<?php
class TGC_Admin_Menu
{
    private static $instance;
    public static function init(){
        if(null == self::$instance){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
        // add_shortcode($this->name, array($this, 'valley_adventure'));
        add_action('admin_menu', array($this, 'tgc_admin_menu_init'));
        add_action("admin_init", array($this, "display_options"));
        add_action('admin_bar_menu', array($this, "tgc_add_toolbar_items"), 80);

    }


    public function tgc_admin_menu_init()
    {
        # code...
        // add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position)
        add_menu_page('Spark', 'Spark', 'manage_options', 'spark', array($this, 'tg_connector_admin_menu'), 'dashicons-admin-plugins', 2);
        

    }
    public function tg_connector_admin_menu(){
        ?>
        <div class="tg-app-connector">
            <div class="wrap">
                <div id="icon-options-general" class="icon32"></div>

                <div class="registration">
                    <div class="uk-card uk-card-default uk-card-body uk-background-muted">
                        <div class="uk-child-width-expand@s uk-flex uk-flex-middle">
                            <div class="left logo">
                                <img src="<?php echo plugin_dir_url(__DIR__). '/assets/images/wpspark-logo.png';?>" width="200px" alt="">
                            </div>
                            <div class="right uk-text-right status">
                                <?php if(get_option('tg_app_token')):?>
                                    <button href="#" id="register-input" class="uk-button uk-button-primary uk-button-medium">Connected</button>
                                <?php else:?>
                                    <button href="#" id="register-input" class="uk-button uk-button-danger uk-button-medium">Not connected</button>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>

                    

                    <div class="uk-card uk-card-default uk-card-body">
                        <?php 
                        $token = get_option('tg_app_token');
                        if(! empty($token)): ?>
                            <div class="uk-child-width-expand@s uk-grid" id="spark_auth_state" uk-grid>
                                <div class="uk-padding">
                                    <input 
                                    id="spark-app-token"
                                    class="uk-input uk-form-width-large" 
                                    type="text" readonly placeholder="form-success" 
                                    value="<?php echo get_option('tg_app_token'); ?>">
                                    <button href="#" id="disconnect_application" class="uk-button uk-button-danger uk-button-medium">Disconnecte</button>
                                    <p class="uk-form-horizontal">
                                        <input type="submit" name="tgc-build" id="tgc-build" class="button button-primary" value="Build "  />
                                        <input type="button" name="tgc-build-count" id="tgc-build-count" readonly class="button button-primary" value=<?php echo get_option('tg_app_build_count') ? get_option('tg_app_build_count') : '0' ; ?>  />
                                    </p>
                                    <div class="build-status" id="build-status">
                                        <div class="uk-alert-primary uk-alert" style="display:none">
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>You build request has been sent. Please wait for a while ..... </p>
                                        </div>

                                        <div class="uk-alert-success uk-alert" style="display:none">
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>Congrutulatio! Your site has been successfully build for the new change.</p>
                                        </div>

                                        <div class="uk-alert-danger uk-alert" style="display:none">
                                            <a class="uk-alert-close" uk-close></a>
                                            <p>There are some problem occurs while build process is happenning. Please contact with support.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="uk-child-width-expand@s uk-grid" id="spark_annonymus" uk-grid>
                                <div class="uk-padding">
                                    <p class="uk-text-large uk-text-bold">
                                        <?php esc_html_e('Sign-up for API key', 'spark');?>
                                    </p>
                                    <p class="uk-text-secondary">Lorem ipsum dolor sit amet consectetur adipisicing elit. Modi error pariatur neque labore sit temporibus alias nulla ipsam quo aperiam dolore, harum eum eveniet nisi inventore ad eos veritatis est.</p>
                                </div>
                                <div class="uk-padding">
                                    <a href="http://app.wpspark.io/register" target="_blank" id="register-input" class="uk-button uk-button-danger uk-button-large uk-width-1-1 uk-margin-small-bottom">Register For API keys</a>
                                    <a href="#" id="already-has-token" class="uk-button uk-button-primary uk-button-large uk-width-1-1">I already have an API keys</a>
                                </div>
                            </div>

                            <div class="uk-child-width-expand@s uk-grid" id="spark_auth_state" uk-grid style="display:none;">
                                
                                <div class="uk-padding">
                                    <ul class="uk-breadcrumb">
                                        <li><a class="show_resgistration_state" href="#">Register Account</a></li>
                                        <li><a href="#">Connect Account</a></li>
                                    </ul>
                                    <form method="post" action="options.php">
                                        <?php
                                        
                                            //add_settings_section callback is displayed here. For every new section we need to call settings_fields.
                                            // settings_fields("header_section");
                                            
                                            // all the add_settings_field callbacks is displayed here
                                            do_settings_sections("spark");
                                            // Add the submit button to serialize the options
                                            
                                            if(get_option('tg_app_token')){
                                                ?>
                                                <p>
                                                    <input type="submit" name="submit" id="submit" class="button button-secondary" disabled="true" value="Connected"  />
                                                    <input type="submit" name="tgc-build" id="tgc-build" class="button button-primary" value="Build "  />
                                                    <input type="button" name="tgc-build-count" id="tgc-build-count" readonly class="button button-primary" value=<?php echo get_option('tg_app_build_count') ? get_option('tg_app_build_count') : '0' ; ?>  />
                                                </p>
                                                <?php
                                            }else{
                                                submit_button('Connect App'); 
                                            }
                                            
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
    public function display_options()
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
        add_settings_field("tg_app_token", "Token", array($this, "tgc_token"), "spark", "header_section");
        // add_settings_field("tgc_woo_token", "WooCommerce Key", array($this, "tgc_woo_token"), "spark", "header_section");
        // add_settings_field("tgc_woo_secret", "WooCommerce Secret", array($this, "tgc_woo_secret"), "spark", "header_section");

        /**
         * section name, form element name, callback for sanitization
         * register_setting($option_group, $option_name, $sanitize_callback)
         */
        register_setting("header_section", "tg_app_token");
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
    public function tgc_token()
    {
        //id and name of form element should be same as the setting name.
        ?>
        <input type="text" 
            name="tg_app_token" 
            <?php echo get_option('tg_app_token') ? 'readonly': ''; ?> 
            id="tg_app_token" class="uk-input uk-form-width-large" style="width:60%" value="<?php echo get_option('tg_app_token'); ?>" 
        />
        <?php
    }
    public function tgc_woo_token()
    {   
        ?>
        <input type="text" name="tg_woo_key" id="tg_woo_key" readonly style="width:60%" value="<?php echo get_option('tg_woo_key'); ?>" />
        <?php
    }
    
    public function tgc_woo_secret()
    {   
        ?>
        <input type="text" name="tg_woo_secret" id="tg_woo_secret" readonly style="width:60%" value="<?php echo get_option('tg_woo_secret'); ?>" />
        <?php
    }


    public function tgc_add_toolbar_items($admin_bar){
        if(get_option('tg_app_token')){
            $admin_bar->add_menu( array(
                'id'    => 'tg-connector-build',
                'title' => 'Build',
                'href'  => '#',
                'meta'  => array(
                    'title' => __('Build'),    
                    'class' => __('tgc-build-button')
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
    

}




?>