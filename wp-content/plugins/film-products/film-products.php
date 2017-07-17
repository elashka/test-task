<?php
/**
 * Plugin Name: Film Products
 * Description: Plugin for test.
 * Version: 1.0
 * Author: Yevheniia Lashkevych
 *
 * Text Domain: film-productss
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Register function on activate plugin action.
register_activation_hook( __FILE__, array( 'Film_Products', 'install' ) );

if ( ! class_exists( 'Film_Products' ) ) :

    /**
     * Main Plugin Class.
     *
     * @class Film_Products
     * @version	1.0
     */
    class Film_Products {

        /**
         * Plugin version.
         *
         * @var string
         */
        public $version = '1.0';

        /**
         * The single instance of the class.
         *
         * @var Film_Products
         * @since 1.0
         */
        protected static $_instance = null;

        /**
         * Main Film_Products Instance.
         *
         * Ensures only one instance of plugin is loaded or can be loaded.
         *
         * @since 1.0
         * @static
         * @return Film_Products - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Film_Products Constructor.
         */
        public function __construct() {
            $this->define_constants();
            $this->includes();
            $this->init_hooks();

        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes()
        {
            // Plugin functions.
            include_once( FP_ABSPATH . '/functions.php');

            // Class settings page.
            include_once( FP_ABSPATH . '/class-film-products-settings-page.php');
        }


        /**
         * Install action. Triggering on plugin activation.
         *
         * @return void
         */
        public static function install()
        {
            // Create featured films page.
            $page_content = '[film_list featured=1]';
            $page_data = array(
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'post_author'    => 1,
                'post_name'      => 'feature-films',
                'post_title'     => __( 'Featured Films', 'film-products' ),
                'post_content'   => $page_content,
                'comment_status' => 'closed',
            );
            $page_id = wp_insert_post( $page_data );
            update_option( 'fp_settings', array( 'user_login_redirect_callback' => $page_id ) );

        }

        /**
         * Hook into actions and filters.
         * @since  1.0
         */
        private function init_hooks() {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            // Actions.
            add_action( 'init', array( $this, 'load_text_domain' ), 0 );
            add_action( 'init', array( $this, 'register_taxonomy' ), 0 );
            add_action( 'init', array( $this, 'register_post_types' ), 0 );
            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_film' ) );
            add_action( 'register_form', array( $this, 'register_form_fields' ) );
            add_action( 'user_register', array( $this, 'user_register' ) );
            add_action( 'show_user_profile', array( $this, 'user_fields' )  );
            add_action( 'edit_user_profile', array( $this, 'user_fields' ) );

            // Woocommerce actions and filters.
            if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ){
                add_action( 'fp_buy_now', array( $this, 'fp_setup_product_data' ), 99 );
                add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 3 );
                add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'product_add_to_cart_url' ), 10, 2 );
                add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'redirect_checkout_add_cart' ) );
                add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_items_from_session' ), 1, 3 );
                add_filter( 'woocommerce_cart_item_name', array( $this, 'add_film_info' ), 1, 3 );
                add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_values_to_order_item_meta' ), 1, 2 );
                add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'custom_cart_button_text' ) );
            }

            // Filters.
            add_filter( 'login_redirect', array( $this, 'login_redirect' ), 10, 3 );
            add_filter( 'single_template', array( $this, 'film_post_template' ) );

            if( is_admin() )
                new Film_Products_Settings_Page();

            // Shortcodes.
            add_shortcode( 'film_list', array( $this, 'film_list' ) );

        }

        /**
         * Define Constants.
         */
        private function define_constants() {

            $this->define( 'FP_PLUGIN_FILE', __FILE__ );
            $this->define( 'FP_ABSPATH', dirname( __FILE__ ) . '/' );
            $this->define( 'FP_TEMPLATE_PATH', dirname( __FILE__ ) . '/templates/' );
            $this->define( 'FP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'FP_VERSION', $this->version );
        }

        /**
         * Define constant if not already set.
         *
         * @param  string $name
         * @param  string|bool $value
         */
        private function define( $name, $value ) {
            if ( ! defined( $name ) ) {
                define( $name, $value );
            }
        }

        /**
         * Text domain.
         */
        public function load_text_domain() {

            // Set up localisation.
            load_plugin_textdomain( 'film-products', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
        }

        /**
         * Registers post types.
         *
         * @access public
         * @return void
         */
        public function register_post_types() {
            // Film post type.
            $labels = array(
                'name'               => _x( 'Film', 'post type general name', 'film-products' ),
                'singular_name'      => _x( 'Film', 'post type singular name', 'film-products' ),
                'add_new'            => _x( 'Add New', 'Film', 'film-products' ),
                'add_new_item'       => __( 'Add New Film', 'film-products' ),
                'edit_item'          => __( 'Edit Film', 'film-products' ),
                'new_item'           => __( 'New Film', 'film-products' ),
                'all_items'          => __( 'All Films', 'film-products' ),
                'view_item'          => __( 'View Film', 'film-products' ),
                'search_items'       => __( 'Search Film', 'film-products' ),
                'not_found'          => __( 'No Film found', 'film-products' ),
                'not_found_in_trash' => __( 'No Film found in the Trash', 'film-products' ),
                'parent_item_colon'  => '',
                'menu_name'          => __( 'Films', 'film-product' )
            );
            $args = array(
                'labels'        => $labels,
                'description'   => 'Film',
                'public'        => true,
                'menu_position' => 5,
                'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
                'has_archive'   => true,
                'taxonomies' => array('fp_category'),
                'show_in_nav_menus' => true
            );
            register_post_type( 'film', $args );
        }

        /**
         * Registers taxonomy for Films.
         *
         * @access public
         * @return void
         */
        public function register_taxonomy() {
            // Film category taxonomy.
            $labels = array(
                'name'              => _x( 'Categories', 'taxonomy general name', 'film-products' ),
                'singular_name'     => _x( 'Category', 'taxonomy singular name', 'film-products' ),
                'search_items'      => __( 'Search Category', 'film-products' ),
                'all_items'         => __( 'All Categories', 'film-products' ),
                'parent_item'       => __( 'Parent Category', 'film-products' ),
                'parent_item_colon' => __( 'Parent Category:', 'film-products' ),
                'edit_item'         => __( 'Edit Category', 'film-products' ),
                'update_item'       => __( 'Update Category', 'film-products' ),
                'add_new_item'      => __( 'Add New Category', 'film-products' ),
                'new_item_name'     => __( 'New Category Name', 'film-products' ),
                'menu_name'         => __( 'Category', 'film-products' ),
            );

            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'fp_category' ),
            );

            register_taxonomy( 'fp_category', array( 'film' ), $args );
        }

        /**
         * Add metaboxes.
         *
         * @since  1.0
         * @access public
         * @return void
         */
        public function add_meta_boxes() {
            add_meta_box('film_info', __( 'Film Info', 'film-products' ), array( $this, 'film_fields' ), 'film');
        }

        /**
         * Callback function for film fields.
         *
         * @access public
         * @param WP_Post $post Post object.
         * @return void
         */
        public function film_fields( $post ) {
            wp_nonce_field( 'fp_film_box_nonce', 'fp_film_box_nonce' );

            $sub_title = get_post_meta( $post->ID, 'film_subtitle', true );
            $featured = get_post_meta( $post->ID, 'film_featured', true );
            $film_product = get_post_meta( $post->ID, 'film_product', true );

            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1
            );
            $products = get_posts( $args );
            ?>
            <div class="inside">
                <label for="film[subtitle]">
                <?php _e( 'Film Subtitle', 'film-products' );?>
                </label>
                <p><input type="text" id="fp_subtitle" name="film[subtitle]" value="<?php echo esc_attr( $sub_title );?>" class="regular-text"/></p>
                <p><input type="checkbox" name="film[featured]" value="1" <?php checked($featured, 1);?>><?php _e( 'Featured', 'film-products' ) ;?></p>
                <label for="film[product]">
                <?php _e( 'Film Product', 'film-products' );?>
                </label>
                <p>
                    <select name="film[product]">
                        <option value=""><?php _e( 'Select Product', 'film-products' );?></option>
                        <?php foreach( $products as $product):?>
                            <option value="<?php echo $product->ID;?>"  <?php selected( $film_product, $product->ID );?>><?php echo $product->post_title;?></option>
                        <?php endforeach;?>
                    </select>
                </p>
            </div>
        <?php
        }

        /**
         * Save film callback.
         *
         * @access public
         * @param int $post_id ID.
         * @return void
         */
        public function save_film( $post_id ) {
            if ( ! isset( $_POST['fp_film_box_nonce'] ) )
                return $post_id;

            $nonce = $_POST['fp_film_box_nonce'];

            if ( ! wp_verify_nonce( $nonce, 'fp_film_box_nonce' ) )
                return $post_id;

            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
                return $post_id;

            if ( ! current_user_can( 'edit_post', $post_id ) )
                return $post_id;

            $data = $_POST['film'];

            foreach ( $data as $key => $field) {
                $value = sanitize_text_field( $field );
                update_post_meta( $post_id, 'film_' . $key, $value );
            }

            if (!isset($data['featured'])) {
                update_post_meta( $post_id, 'film_featured', 0 );
            }

        }

        /**
         * Add custom fields to registration form.
         * @access public
         * @return void
         */
        function register_form_fields() {
            $skype = ( ! empty( $_POST['skype'] ) ) ? trim( $_POST['skype'] ) : '';

            ?>
            <p>
                <label for="skype"><?php _e( 'Skype', 'film-productss' ) ?><br />
                <input type="text" name="skype" id="skype" class="input" value="<?php echo esc_attr( wp_unslash( $skype ) ); ?>" size="25" /></label>
            </p>
            <?php
        }

        /**
         * Save user custom fields.
         * @param $user_id
         * @access public
         */
        public function user_register( $user_id ) {
            if ( ! empty( $_POST['skype'] ) ) {
                update_user_meta( $user_id, 'skype', trim( $_POST['skype'] ) );
            }
        }

        /**
         * Add custom fields to user page.
         * @param $user
         * @access public
         */
        public function user_fields( $user ) { ?>

            <h3><?php _e( 'Extra profile information', 'film-products' );?></h3>

            <table class="form-table">
                <tr>
                    <th><label for="skype"><?php _e( 'Skype', 'film-products' );?></label></th>

                    <td>
                        <input type="text" name="skype" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'skype', $user->ID ) ); ?>" class="regular-text" /><br />
                        <span class="description"><?php _e( 'Please enter your skype', 'film-products' );?></span>
                    </td>
                </tr>

            </table>
        <?php }

        /**
         * [firm_list] shortcode callback.
         * Shortcode parameters:
         * num - amount of films
         * featured = 1 - to show only featured films.
         * @param $atts
         * @return string
         */
        public function film_list( $atts ) {
            $output = '';
            $atts['num'] = !empty( $atts['num'] ) ? $atts['num'] : 10;
            $args = array(
                'post_type'           => 'film',
                'post_status'         => 'publish',
                'posts_per_page'      => $atts['num'],
                'orderby'             => 'post_date',
                'order'               => 'DESC'
            );

            if( !empty( $atts['featured'] ) ) {
                $args['meta_key'] = 'film_featured';
                $args['meta_value'] = 1;
            }

            $films_query = new WP_Query( $args );

            ob_start();
            if ( $films_query->have_posts() ):
                do_action( 'fp_before_loop' );
                while ( $films_query->have_posts() ):  $films_query->the_post();
                    fp_get_template_part( 'film', 'list' );
                endwhile;
               do_action( 'fp_after_loop' );
            endif;
            $output = ob_get_clean();
            return $output;
        }

        /**
         * Redirect user after successful login.
         *
         * @param string $redirect_to URL to redirect to.
         * @param string $request URL the user is coming from.
         * @param object $user Logged user's data.
         * @return string
         */
        public function login_redirect( $redirect_to, $request, $user ) {
            //is there a user to check?
            if ( isset( $user->roles ) && is_array( $user->roles ) ) {
                //check for admins
                if ( in_array( 'administrator', $user->roles ) ) {
                    // redirect them to the default place
                    return $redirect_to;
                } else {
                    $fp_settings = get_option( 'fp_settings' );

                    if ( !empty( $fp_settings ) ) {
                        return get_page_link( $fp_settings['user_login_redirect_callback'] );
                    }
                    else {
                       return $redirect_to;
                    }
                }
            } else {
                return $redirect_to;
            }
        }

        /**
         * Single film template.
         * @param $single_template
         * @return string
         */
        public function film_post_template( $single_template ) {
            global $post;

            if ( $post->post_type == 'film' ) {
                $single_template = fp_get_template_part('single', 'film', false );
            }
            return $single_template;
        }

        /**
         * When the_post is called, put product data into a global.
         *
         */
        public function fp_setup_product_data( ) {
            global $post;

            if ( is_int( $post ) ) {
                $post = get_post( $post );
            }
            if ( empty( $post->post_type ) || ! in_array( $post->post_type, array( 'film' ) ) ) {
                return;
            }

            $product_id = get_post_meta( $post->ID, 'film_product', true );

            if( empty( $product_id ) ) {
                return;
            }

            $GLOBALS['product'] = wc_get_product( $product_id );
            if ( shortcode_exists( 'add_to_cart' ) ){
                echo do_shortcode( '[add_to_cart id=' . $product_id . ']' );
            }
        }

        /**
         * Skip woocommerce cart.
         * @param $url
         * @return false|string
         */
        public function redirect_checkout_add_cart( $url ) {
            $url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );
            return $url;
        }

        /**
         * Change add to cart button text.
         * @return mixed|string|void
         */
        public function custom_cart_button_text() {

            return __( 'Buy Now', 'film-product' );

        }

        /**
         * Add film id parameter.
         * @param $url
         * @param $product
         * @return string
         */
        public function product_add_to_cart_url( $url, $product ) {
            global $post;
            // Add film_id to request.
            $url  = add_query_arg( array( 'film_id' => $post->ID ), $url );
            return $url;
        }


        /**
         * Save film id.
         * @param $cart_item_data
         * @param $cart_item_key
         * @return mixed
         */
        public function add_cart_item_data( $cart_item_data, $cart_item_key ) {
            $film_id = $_REQUEST['film_id'];

            if ( !empty( $film_id ) ) {
                $cart_item_data['film_titles'][] = get_the_title( $film_id );
            }

            return $cart_item_data;
        }

        /**
         * Add film information to the cart.
         * @param $item
         * @param $values
         * @param $key
         * @return mixed
         */
        public function get_cart_items_from_session( $item, $values, $key ) {

            if (array_key_exists( 'film_titles', $values ) ) {
                $item['film_titles'] = $values['film_titles'];
            }

            return $item;
        }

        /**
         * Show film information on the cart.
         * @param $product_name
         * @param $values
         * @param $cart_item_key
         * @return string
         */
        function add_film_info($product_name, $values, $cart_item_key ) {
            if ( !empty( $values['film_titles'] ) ){
                $product_name = $product_name . "<br />" . implode( ', ', $values['film_titles'] );
            }

            return $product_name;

        }

        /**
         * Add film title to order.
         * @param $item_id
         * @param $values
         */
        public function add_values_to_order_item_meta($item_id, $values) {
            wc_add_order_item_meta( $item_id, 'film_titles', implode( ', ', $values['film_titles']) );

        }

    }

endif;

/**
 * Main instance of Film_Products.
 */
Film_Products::instance();
