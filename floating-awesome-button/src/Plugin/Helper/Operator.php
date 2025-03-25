<?php

namespace Fab\Plugin\Helper;

!defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

trait Operator {

    /**
     * Get operators by type
     *
     * @param string $type The type of operator to get.
     * @return array The operators.
     */
    public function get_default_operators_by_type( $type ) {
        $operators = array();

        if(in_array($type, array('bool', 'number'))){
            $operators[] = array(
                'id'   => '==',
                'text' => __( 'is equal to', 'floating-awesome-button' ),
            );
            $operators[] = array(
                'id'   => '!=',
                'text' => __( 'is not equal to', 'floating-awesome-button' ),
            );
        }

        if(in_array($type, array('number'))){
            $operators[] = array(
                'id'   => '<',
                'text' => __( 'is less than', 'floating-awesome-button' ),
            );
            $operators[] = array(
                'id'   => '>',
                'text' => __( 'is greater than', 'floating-awesome-button' ),
            );
        }

        return $operators;
    }

    /**
     * Match locations setting when current displayed content is compared against a value.
     *
     * @param string $operator The comparison operator ('==', '!=', '<', '>', '<=', '>=').
     * @param mixed  $source_value The value to compare from the source (e.g., cart quantity or subtotal).
     * @param mixed  $compared_value The value to compare against.
     *
     * @return bool Returns true if the comparison satisfies the operator, otherwise false.
     */
    public function match_operator_and_value( $operator, $source_value, $compared_value ) {
        // Perform the comparison based on the operator
        if ( '==' === $operator ) {
            return $source_value == $compared_value;
        } elseif ( '!=' === $operator ) {
            return $source_value != $compared_value;
        } elseif ( '<' === $operator ) {
            return $source_value < $compared_value;
        } elseif ( '>' === $operator ) {
            return $source_value > $compared_value;
        } elseif ( '<=' === $operator ) {
            return $source_value <= $compared_value;
        } elseif ( '>=' === $operator ) {
            return $source_value >= $compared_value;
        }

        // Return false for unsupported operators
        return false;
    }

}
