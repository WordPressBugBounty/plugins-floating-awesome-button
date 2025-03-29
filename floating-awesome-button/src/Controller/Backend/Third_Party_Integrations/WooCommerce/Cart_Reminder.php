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

class Cart_Reminder extends Base implements Model_Interface {

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
                'id'         => 'cart_reminder',
                'dependency' => 'woocommerce/woocommerce.php',
                'text'       => 'Cart Reminder',
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
            'fab_setting_woocommerce_cart_reminder'     => array('default' => ''),
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
            'woocommerce_cart_reminder' => array(
                'meta_key' => 'fab_setting_woocommerce_cart_reminder',
            ),
        );

        // Merge new post meta settings into the existing post metas.
        return array_merge($post_metas, $woocommerce_post_metas);
    }

    /**
     * Adds WooCommerce Features data to the FAB item instance.
     *
     * @param object $instance The FAB item instance.
     */
    public function add_fab_item_data( $instance ) {
        if ( 'cart_reminder' === $instance->getType() ) {
            $instance->setToBeDisplayed(false);
            $instance->setLink( wc_get_cart_url() );

            if(!empty($instance->getContent())){
                $instance->setTitle( $instance->getContent() );
            }

            if ( ! isset(WC()->session) || ! WC()->session instanceof \WC_Session ) {
                return;
            }

            if ( is_cart() || is_checkout() ) {
                return;
            }

            $cart_contents = WC()->session->get('cart');

            if ( ! empty( $cart_contents ) && is_array( $cart_contents ) ) {
                $instance->setToBeDisplayed(true);
            }
        }
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
        /** @backend - Add FAB setting types */
        add_filter('fab_setting_types', array($this, 'add_fab_setting_types'), 10, 1);

        // Prevent error if woocommerce plugin is not active.
        if( !is_plugin_active( FAB_WOOCOMMERCE_PLUGIN_FILE ) ){
            return;
        }

        /** @backend - Add FAB item data */
        add_action('fab_item_data', array($this, 'add_fab_item_data'), 10, 1);

        /** @backend - Add FAB setting input */
        add_filter('fab_setting_input', array($this, 'add_fab_setting_input'), 10, 1);

        /** @backend - Add FAB setting post metas */
        add_filter('fab_setting_post_metas', array($this, 'add_fab_setting_post_metas'), 10, 1);
    }

}
