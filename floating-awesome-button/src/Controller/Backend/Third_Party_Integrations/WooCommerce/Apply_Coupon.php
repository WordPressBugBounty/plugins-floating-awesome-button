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

class Apply_Coupon extends Base implements Model_Interface {

    /**
     * AJAX handler to search for WooCommerce coupons based on a search term.
     *
     * This function listens for AJAX requests made to the `wp_ajax_fab_search_coupons` hook.
     * It sanitizes and processes the search term, queries WooCommerce coupons, and returns
     * the results as a JSON response. The results include the coupon ID and title.
     *
     * @return void Outputs a JSON response containing an array of coupons.
     */
    function ajax_search_coupons() {
        // Get the search term from the request.
        $search_term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

        // Query WooCommerce coupons.
        $args = [
            'post_type'      => 'shop_coupon',
            'posts_per_page' => 10, // Limit the number of results.
            's'              => $search_term, // Search term.
            'post_status'    => 'publish', // Only published coupons.
        ];

        $query = new \WP_Query( $args );

        // Prepare the results.
        $coupons = [];

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $coupon_title = get_the_title();
                $coupon_title = html_entity_decode( $coupon_title, ENT_QUOTES, 'UTF-8' );

                $coupons[] = [
                    'id'   => get_the_ID(),
                    'text' => $coupon_title,
                ];
            }
        }

        wp_reset_postdata();

        // Return the coupons as a JSON response.
        wp_send_json( $coupons );
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
                'id'         => 'apply_coupon',
                'dependency' => 'woocommerce/woocommerce.php',
                'text'       => 'Apply Coupon',
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
            'fab_setting_woocommerce_apply_coupon'     => array('default' => ''),
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
            'woocommerce_apply_coupon' => array(
                'meta_key' => 'fab_setting_woocommerce_apply_coupon',
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
        $data['labels']['setting']['woocommerce']['apply_coupon'] = array (
            'text' => __( 'Coupon', 'floating-awesome-button' ),
            'tooltip' => __( 'Coupon to be applied on cart or checkout page.', 'floating-awesome-button' ),
        );

        // Data
        $coupon_id = $this->WP->get_post_meta( $data['data']['fab']['ID'], FABMetaboxSetting::get_post_metas()['woocommerce_apply_coupon']['meta_key'], true );
        $data['data']['fab']['woocommerce']['apply_coupon'] = array(
            'id' => $coupon_id,
            'text' => get_the_title( $coupon_id )
        );

        return $data;
    }

    /**
     * Adds WooCommerce Features data to the FAB item instance.
     *
     * @param object $instance The FAB item instance.
     */
    public function add_fab_item_data( $instance ){

        if('apply_coupon' === $instance->getType()){
            $coupon_id = $this->WP->get_post_meta( $instance->getID(), FABMetaboxSetting::get_post_metas()['woocommerce_apply_coupon']['meta_key'], true );
            $coupon_code = strtolower ( get_the_title($coupon_id) );
            $applied_coupons = array();

            if( is_cart() || is_checkout() ){
                $applied_coupons = WC()->cart->get_applied_coupons();
            }

            if ( (!is_cart() && !is_checkout()) || WC()->cart->get_cart_contents_count() === 0 || in_array($coupon_code, $applied_coupons) ) {
                $instance->setToBeDisplayed(false);

                return;
            }

            $instance->setLink( add_query_arg( 'fab_coupon_code', $coupon_code, home_url( add_query_arg( null, null ) ) ) );

        }
    }

    /**
     * Automatically applies a coupon from the URL parameter.
     *
     * This function checks if a coupon code is passed via the URL parameter `fab_coupon_code`.
     * If a valid coupon code is found and the coupon is not already applied to the cart,
     * it applies the coupon to the WooCommerce cart.
     *
     * @return void
     */
    public function apply_coupon_from_url(){
        if ( isset($_GET['fab_coupon_code']) && !empty($_GET['fab_coupon_code']) ) {
            $coupon_code = sanitize_text_field( wp_unslash( $_GET['fab_coupon_code'] ) );

            // Apply the coupon if it's valid and the cart is not empty
            if (!WC()->cart->has_discount($coupon_code)) {
                WC()->cart->add_discount($coupon_code);
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
        // Check if woocommerce is active
        if(!class_exists('WooCommerce')){
            return $postmeta;
        }

        // Check if type is apply coupon
        if($type === 'apply_coupon'){
            // Get recent coupon
            $coupons = get_posts(array(
                'post_type' => 'shop_coupon',
                'posts_per_page' => 1,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            // If coupon exists, set the postmeta
            if(!empty($coupons)){
                $postmeta['fab_setting_woocommerce_apply_coupon'] = $coupons[0]->ID;
            }
        }

        return $postmeta;
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

        // Prevent error if woocommerce plugin is not active.
        if( !is_plugin_active( FAB_WOOCOMMERCE_PLUGIN_FILE ) ){
            return;
        }

        // @backend - Ajax search coupons
        add_action( 'wp_ajax_fab_search_coupons', array( $this, 'ajax_search_coupons' ) );

        // @backend - Add fab item data
        add_action( 'fab_item_data', array( $this, 'add_fab_item_data' ), 10, 1 );

        // @backend - Apply coupon from URL
        add_action( 'template_redirect', array( $this, 'apply_coupon_from_url' ) );

        // @backend - Add fab setting input
        add_filter( 'fab_setting_input', array( $this, 'add_fab_setting_input' ), 10, 1 );

        // @backend - Add fab setting post metas
        add_filter( 'fab_setting_post_metas', array( $this, 'add_fab_setting_post_metas' ), 10, 1 );

        // @backend - Add fab backend enequeue metabox setting localize
        add_filter( 'fab_backend_enequeue_metabox_setting_localize', array( $this, 'add_backend_enequeue_metabox_setting_localize' ), 10, 1 );

        // @backend - Filter template postmeta
        add_filter( 'fab_template_postmeta', array( $this, 'filter_template_postmeta' ), 10, 3 );
    }
}
