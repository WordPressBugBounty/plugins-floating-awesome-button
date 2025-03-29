<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */

use Fab\Metabox\FABMetaboxSetting;

class Featured_Product extends Base implements Model_Interface {

    /**
     * AJAX handler to search for WooCommerce products based on a search term.
     *
     * This function listens for AJAX requests made to the `wp_ajax_fab_search_products` hook.
     * It sanitizes and processes the search term, queries WooCommerce products, and returns
     * the results as a JSON response. The results include the product ID and title.
     *
     * @return void Outputs a JSON response containing an array of products.
     */
    function ajax_search_products() {
        $search_term = isset($_GET['q']) ? sanitize_text_field( wp_unslash( $_GET['q']) ) : '';

        $search_term = sanitize_text_field($search_term);

        // Query WooCommerce products.
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => 10, // Limit the number of results
            's'              => $search_term, // Search term
            'post_status'    => 'publish', // Only published products
        ];

        $query = new \WP_Query($args);

        // Prepare the results
        $products = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product_title = get_the_title();
                $product_title = html_entity_decode($product_title, ENT_QUOTES, 'UTF-8');

                $products[] = [
                    'id'   => get_the_ID(),
                    'text' => $product_title,
                ];
            }
        }

        wp_reset_postdata();

        // Return the products as a JSON response
        wp_send_json($products);
    }

    /**
     * Add FAB setting types for WooCommerce.
     *
     * @param array $types Existing types passed through the filter.
     * @return array Modified types with the new WooCommerce settings type.
     */
    public function add_fab_setting_types( $types ) {
        // Define the new children elements to be added
        $new_children = array(
            array(
                'id'         => 'featured_product',
                'dependency' => FAB_WOOCOMMERCE_PLUGIN_FILE,
                'text'       => 'Featured Product',
            ),
        );

        // Check if 'WooCommerce' already exists in the types array
        foreach ( $types as &$type ) {
            if ( isset( $type['text'] ) && 'WooCommerce' === $type['text'] ) {
                // Merge the new children with the existing ones
                if ( isset( $type['children'] ) ) {
                    $type['children'] = array_merge( $type['children'], $new_children );
                } else {
                    $type['children'] = $new_children;
                }
                return $types; // Return early after merging
            }
        }

        $woocommerce_type = array(
            'text'     => 'WooCommerce',
            'children' => $new_children,
        );

        $types[] = $woocommerce_type; // Append the new type

        return $types;
    }

    /**
     * Add FAB setting inputs for WooCommerce features.
     *
     * @param array $input Existing input settings.
     * @return array Modified input settings with new WooCommerce settings.
     */
    public function add_fab_setting_input($input) {
        // Define new WooCommerce input settings.
        $woocommerce_settings = array(
            'fab_setting_woocommerce_featured_product' => array('default' => ''),
        );

        // Merge new settings into the existing inputs.
        return array_merge($input, $woocommerce_settings);
    }

    /**
     * Add FAB setting post meta for WooCommerce features.
     *
     * @param array $post_metas Existing post meta settings.
     * @return array Modified post meta settings with new WooCommerce meta.
     */
    public function add_fab_setting_post_metas($post_metas) {
        // Define new WooCommerce post meta settings.
        $woocommerce_post_metas = array(
            'woocommerce_featured_product' => array(
                'meta_key' => 'fab_setting_woocommerce_featured_product',
            ),
        );

        // Merge new post meta settings into the existing post metas.
        return array_merge($post_metas, $woocommerce_post_metas);
    }

    /**
     * Add localized settings for the WooCommerce.
     * This function modifies the localized settings data.
     *
     * @param array $data The existing localized settings data.
     * @return array The modified localized settings data with WooCommerce feature.
     */
    public function add_backend_enequeue_metabox_setting_localize( $data ) {
        // Label
        $data['labels']['setting']['woocommerce']['featured_product'] = array (
            'text' => __( 'Product', 'floating-awesome-button' ),
            'tooltip' => __( 'Featured product to be shown on the frontend.', 'floating-awesome-button' ),
        );

        // Data
        $product_id = $this->WP->get_post_meta( $data['data']['fab']['ID'], FABMetaboxSetting::get_post_metas()['woocommerce_featured_product']['meta_key'], true );
        $data['data']['fab']['woocommerce']['featured_product'] = array(
            'id' => $product_id,
            'text' => wc_get_product($product_id) ? wc_get_product($product_id)->get_name() : ''
        );

        return $data;
    }

    /**
     * Adds WooCommerce Features data to the FAB item instance.
     *
     * @param object $instance The FAB item instance.
     */
    public function add_fab_item_data( $instance ){

        if( 'featured_product' === $instance->getType()){
            $product_id = $product_id = $this->WP->get_post_meta( $instance->getID(), FABMetaboxSetting::get_post_metas()['woocommerce_featured_product']['meta_key'], true );

            $product_post = get_post( $product_id );

            if ( $product_post && 'product' === $product_post->post_type ) {

                // Fetch product details
                $product_name = $product_post->post_title;  // Get the product name (title)
                $product_image = get_the_post_thumbnail_url( $product_id );  // Get product image URL
                $product_url = get_permalink( $product_id );  // Get product URL

                // Get product meta fields for price data
                $regular_price = get_post_meta( $product_id, '_regular_price', true );
                $sale_price = get_post_meta( $product_id, '_sale_price', true );

                // Add the data to the $instance->obj['woocommerce']['featured_product']
                $instance->obj['woocommerce']['featured_product'] = array(
                    'product_name'    => $product_name,
                    'product_image'   => $product_image,
                    'product_url'     => $product_url,
                    'regular_price'   => wc_price( $regular_price ),
                    'sale_price'      => $sale_price ? wc_price( $sale_price ) : 0,
                );

                $instance->setLink( $product_url );

                if ( is_product() && get_the_ID() === (int) $product_id ) {
                    $instance->setToBeDisplayed(false);
                }

                // If on cart or checkout page, and the product is already in the cart, prevent display
                if ( is_cart() || is_checkout() ) {
                    // Check if the product is already in the cart
                    $product_in_cart = false;

                    foreach ( WC()->cart->get_cart() as $cart_item ) {
                        if ( $cart_item['product_id'] === (int) $product_id ) {
                            $product_in_cart = true;
                            break;
                        }
                    }

                    // If the product is in the cart, set to not be displayed
                    if ( $product_in_cart ) {
                        $instance->setToBeDisplayed(false);
                    }
                }
            }
        }
    }

    /**
     * Filter the FAB template to postmeta.
     *
     * @param array $postmeta The postmeta.
     * @param string $type The type of FAB.
     * @param object $data The data.
     */
    public function filter_template_postmeta($postmeta, $type, $data){
        // Check if woocommerce is active and type is featured product
        if(!class_exists('WooCommerce') || 'featured_product' !== $type){
            return $postmeta;
        }

        // Check if product id is set
        if( isset($data->product_id) ){
            $postmeta['fab_setting_woocommerce_featured_product'] = $data->product_id;
        } else {
            // Get product with most sales
            $args = array(
                'post_type' => 'product',
                'meta_key' => 'total_sales',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'posts_per_page' => 1,
            );
            $products = get_posts($args);

            // If there no sales, get recent product
            if(!$products){
                $args = array(
                    'post_type' => 'product',
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'posts_per_page' => 1,
                );
                $products = get_posts($args);
            }

            // Get product ID
            if(!empty($products)){
                $postmeta['fab_setting_woocommerce_featured_product'] = $products[0]->ID;
            }
        }

        return $postmeta;
    }

    /**
     * Add convert product to FAB action to FAB post type row actions
     *
     * @param array   $actions Array of row action links
     * @param WP_Post $post   The post object
     * @return array
     */
    public function add_fab_convert_product_action($actions, $post) {
        if ($post->post_type === 'product') {
            $actions['fab_convert_product'] = sprintf(
                '<a href="%s">%s</a>',
                wp_nonce_url(
                    admin_url('admin.php?action=fab_convert_product&product_id=' . $post->ID),
                    'fab_convert_product_' . $post->ID
                ),
                __('Turn into FAB', 'floating-awesome-button')
            );
        }
        return $actions;
    }

    /**
     * Handle the conversion of product to FAB
     *
     * @return void
     */
    public function convert_product_to_fab() {
        // Check if post ID is provided
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

        // Verify nonce
        if (!wp_verify_nonce($_GET['_wpnonce'], 'fab_convert_product_' . $product_id)) {
            wp_die(__('Security check failed', 'floating-awesome-button'));
        }

        // Add new fab
        $result = \Fab\Helper\FAB_Template::getInstance()->add_new_fab(
            (object) array( 'id' => 'woocommerce-featured-product', 'product_id' => $product_id )
        );

        // Check if there's an error
        if( is_wp_error($result) ){
            wp_die(esc_html($result->get_error_message()));
        }

        // Redirect to FAB edit page
        wp_redirect(admin_url('post.php?post=' . $result . '&action=edit'));
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute Class.
     *
     * @return void
     */
    public function run() {
        // @backend - Add fab setting types
        add_filter( 'fab_setting_types', array( $this, 'add_fab_setting_types' ), 10, 1 );

        // Prevent error if WooCommerce plugin is not active.
        if( !is_plugin_active( FAB_WOOCOMMERCE_PLUGIN_FILE ) ){
            return;
        }

        // @backend - Ajax search products
        add_action( 'wp_ajax_fab_search_products', array( $this, 'ajax_search_products' ) );

        // @backend - Add fab item data
        add_action( 'fab_item_data', array( $this, 'add_fab_item_data' ), 10, 1 );

        // @backend - Add fab setting input
        add_filter( 'fab_setting_input', array( $this, 'add_fab_setting_input' ), 10, 1 );

        // @backend - Add fab setting post metas
        add_filter( 'fab_setting_post_metas', array( $this, 'add_fab_setting_post_metas' ), 10, 1 );

        // @backend - Add fab backend enqueue metabox setting localize
        add_filter( 'fab_backend_enequeue_metabox_setting_localize', array( $this, 'add_backend_enequeue_metabox_setting_localize' ), 10, 1 );

        // @backend - Filter template postmeta
        add_filter( 'fab_template_postmeta', array( $this, 'filter_template_postmeta' ), 10, 3 );

        // Check if WooCommerce integration is enabled
        if( \Fab\Plugin\Helper::getInstance()->check_plugin_integration( FAB_WOOCOMMERCE_PLUGIN_FILE ) === 'enabled' ){
            // Add convert product to product row action
            add_filter('post_row_actions', array($this, 'add_fab_convert_product_action'), 10, 2);
            add_action('admin_action_fab_convert_product', array($this, 'convert_product_to_fab'));
        }
    }
}
