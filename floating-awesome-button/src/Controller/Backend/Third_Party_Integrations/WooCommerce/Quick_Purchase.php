<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\Metabox\FABMetaboxSetting;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Quick_Purchase extends Base implements Model_Interface {

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
                'id'         => 'quick_purchase',
                'dependency' => 'woocommerce/woocommerce.php',
                'text'       => 'Quick Purchase',
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
            'fab_setting_woocommerce_quick_purchase'     => array('default' => ''),
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
            'woocommerce_quick_purchase' => array(
                'meta_key' => 'fab_setting_woocommerce_quick_purchase',
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
        $data['labels']['setting']['woocommerce']['quick_purchase'] = array (
            'text' => __( 'Action', 'floating-awesome-button' ),
            'tooltip' => __( 'Action for quick purchase', 'floating-awesome-button' ),
            'options' => array(
                array(
                    'id' => 'fab_add_to_cart',
                    'text' => __( 'Add to cart', 'floating-awesome-button' ),
                ),
                array(
                    'id' => 'fab_buy_now',
                    'text' => __( 'Buy now', 'floating-awesome-button' ),
                )
            )
        );

        // Data
        $quick_purchase_id = $this->WP->get_post_meta( $data['data']['fab']['ID'], FABMetaboxSetting::get_post_metas()['woocommerce_quick_purchase']['meta_key'], true );
        $data['data']['fab']['woocommerce']['quick_purchase'] = $quick_purchase_id;

        return $data;
    }

    /**
     * Adds WooCommerce Features data to the FAB item instance.
     *
     * @param object $instance The FAB item instance.
     */
    public function add_fab_item_data( $instance ){

        if('quick_purchase' === $instance->getType()){
            $quick_purchase_id = $this->WP->get_post_meta( $instance->getID(), FABMetaboxSetting::get_post_metas()['woocommerce_quick_purchase']['meta_key'], true );
            $product_id = 0;
            $instance->setToBeDisplayed(false);

            if( is_product() ){
                $product_id = get_the_ID();
                $instance->setToBeDisplayed(true);
            }

            $instance->setLink( add_query_arg( $quick_purchase_id, $product_id, home_url( add_query_arg( null, null ) ) ) );

        }
    }

    /**
     * Handles quick purchase actions from URL parameters.
     *
     * This function checks for `fab_add_to_cart` or `fab_buy_now` parameters in the URL.
     * - If `fab_add_to_cart` is set, the product is added to the WooCommerce cart, and the user is redirected to the cart page.
     * - If `fab_buy_now` is set, the product is added to the WooCommerce cart, and the user is redirected to the checkout page.
     *
     * @return void
     */
    public function quick_purchase_from_url() {
        // Determine the parameter and its corresponding redirect URL
        $actions = [
            'fab_add_to_cart' => wc_get_cart_url(),
            'fab_buy_now' => wc_get_checkout_url()
        ];

        foreach ( $actions as $param => $redirect_url ) {
            if ( isset( $_GET[$param] ) && ! empty( $_GET[$param] ) ) {
                $product_id = sanitize_text_field( wp_unslash( $_GET[$param] ) );

                // If "fab_buy_now", empty the cart before adding the product
                if ( $param === 'fab_buy_now' ) {
                    WC()->cart->empty_cart();
                }

                // Add the product to the cart
                WC()->cart->add_to_cart( $product_id );

                // Redirect to the appropriate page
                wp_safe_redirect( $redirect_url );
                exit;
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

        // Get setting quick purchase
        if($type === 'quick_purchase'){
            $postmeta['fab_setting_woocommerce_quick_purchase'] = isset($data->settings->quick_purchase) ? $data->settings->quick_purchase : '';
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
        if( !is_plugin_active('woocommerce/woocommerce.php') ){
            return;
        }

        // @backend - Add fab item data
        add_action( 'fab_item_data', array( $this, 'add_fab_item_data' ), 10, 1 );

        // @backend - Quick purchase from URL
        add_action( 'template_redirect', array( $this, 'quick_purchase_from_url' ) );

        // @backend - Add fab setting input
        add_filter( 'fab_setting_input', array( $this, 'add_fab_setting_input' ), 10, 1 );

        // @backend - Add fab setting post metas
        add_filter( 'fab_setting_post_metas', array( $this, 'add_fab_setting_post_metas' ), 10, 1 );

        // @backend - Add fab backend enqueue metabox setting localize
        add_filter( 'fab_backend_enequeue_metabox_setting_localize', array( $this, 'add_backend_enequeue_metabox_setting_localize' ), 10, 1 );

        // @backend - Filter template postmeta
        add_filter( 'fab_template_postmeta', array( $this, 'filter_template_postmeta' ), 10, 3 );
    }
}
