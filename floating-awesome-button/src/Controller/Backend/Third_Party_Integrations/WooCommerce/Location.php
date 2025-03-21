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
class Location extends Base implements Model_Interface {
    /**
     * Dependency for the plugin to work correctly.
     * @var string $dependency'.
     */
    private $_dependency = 'woocommerce/woocommerce.php';

    /**
     * Add localized location for the WooCommerce.
     * This function modifies the localized location data.
     *
     * @param array $data The existing localized location data.
     * @return array The modified localized location data with WooCommerce feature.
     */
    public function add_backend_enequeue_metabox_location_localize( $data ) {
        // Label
        $data['objects'] = array (
            'text' => __( 'WooCommerce', 'floating-awesome-button' ),
            'children' => array(
                array(
                    'id' => 'fab_wc_cart_quantity',
                    'text' => __( 'Cart Quantity', 'floating-awesome-button' ),
                    'disabled' => ! is_plugin_active($this->_dependency),
                    'operator' => array(
                        array(
                            'id'   => '==',
                            'text' => 'is equal to',
                        ),
                        array(
                            'id'   => '!=',
                            'text' => 'is not equal to',
                        ),
                        array(
                            'id'   => '>',
                            'text' => 'more than',
                        ),
                        array(
                            'id'   => '<',
                            'text' => 'less than',
                        ),
                    )
                ),
                array(
                    'id' => 'fab_wc_cart_subtotal',
                    'text' => __( 'Cart Subtotal', 'floating-awesome-button' ),
                    'disabled' => ! is_plugin_active($this->_dependency),
                    'operator' => array(
                        array(
                            'id'   => '==',
                            'text' => 'is equal to',
                        ),
                        array(
                            'id'   => '!=',
                            'text' => 'is not equal to',
                        ),
                        array(
                            'id'   => '>',
                            'text' => 'more than',
                        ),
                        array(
                            'id'   => '<',
                            'text' => 'less than',
                        ),
                    )
                )
            )
        );

        return $data;
    }

    /**
     * Determines if a given rule matches specific conditions.
     *
     * This function evaluates whether a rule satisfies the criteria
     * for a specific condition or location. It modifies the current evaluation
     * status based on the rule's type, value, and operator.
     *
     * @param bool  $current_passed The current status of rule evaluation.
     *                  Defaults to the value from the previous rule check.
     *
     * @param array $rule An associative array representing the rule to evaluate.
     *                    Expected keys include:
     *                    - 'type' (string): The type of rule (e.g., 'fab_wc_cart_quantity', 'fab_wc_cart_subtotal').
     *                    - 'value' (mixed): The value to be matched.
     *                    - 'operator' (string): The operator to use for comparison (e.g., '>', '<', '=').
     *
     * @return bool Returns true if the rule matches the specified conditions
     *              and the current evaluation status allows it, otherwise false.
     */
    public function add_match_rule( $current_passed, $rule ) {
        if ( 'fab_wc_cart_quantity' === $rule['type'] ) {

            if ( ! is_cart() && ! is_checkout() ) {
                return false;
            }

            $cart_quantity = WC()->cart->get_cart_contents_count();
            $current_passed = $this->Helper->match_operator_and_value( $rule['operator'], $cart_quantity, $rule['value'] );

        } elseif ( 'fab_wc_cart_subtotal' === $rule['type'] ) {

            if ( ! is_cart() && ! is_checkout() ) {
                return false;
            }

            $cart_subtotal = WC()->cart->get_subtotal() + WC()->cart->get_subtotal_tax();
            $current_passed = $this->Helper->match_operator_and_value( $rule['operator'], $cart_subtotal, $rule['value'] );

        }

        return $current_passed;
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
        // @backend - Add fab backend enequeue metabox location localize.
        add_filter( 'fab_backend_enequeue_metabox_location_localize', array( $this, 'add_backend_enequeue_metabox_location_localize' ), 10, 1 );

        // Prevent error if woocommerce plugin is not active.
        if( !is_plugin_active('woocommerce/woocommerce.php') ){
            return;
        }

        // @backend - Add fab backend match rule.
        add_filter( 'fab_match_rule', array( $this, 'add_match_rule' ), 10, 2 );
    }

}
