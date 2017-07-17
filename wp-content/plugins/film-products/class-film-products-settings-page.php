<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Film_Products_Settings_Page' ) ) :

class Film_Products_Settings_Page
{
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_options_page(
            __( 'Film Products Settings', 'film-products' ),
            __( 'Film Products Settings', 'film-products' ),
            'manage_options',
            'fp-setting-admin',
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        $this->options = get_option( 'fp_settings' );
        ?>
        <div class="wrap">
            <h1><?php _e( 'Film Products Settings', 'film-products' );?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'fp_settings' );
                do_settings_sections( 'fp-setting-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {
        register_setting(
            'fp_settings',
            'fp_settings'
        );

        add_settings_section(
            'setting_section_id',
            '',
            '',
            'fp-setting-admin'
        );

        add_settings_field(
            'user_login_redirect',
            __( 'User Login Redirect', 'film-products' ) ,
            array( $this, 'user_login_redirect_callback' ),
            'fp-setting-admin',
            'setting_section_id'
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function user_login_redirect_callback()
    {
        $pages = get_pages();
     ?>
        <select name="fp_settings[user_login_redirect_callback]">
            <option value =""><?php _e( 'Select page', 'film-products' );?></option>
            <?php foreach ($pages as $page) :?>
             <option value="<?php echo $page->ID;?>" <?php selected ($this->options['user_login_redirect_callback'], $page->ID );?>><?php echo $page->post_title;?></option>
            <?php endforeach;?>
        </select>
    <?php
    }
}

endif;?>