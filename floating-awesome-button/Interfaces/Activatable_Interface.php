<?php
namespace Fab\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstraction that provides contract relating to activation.
 * Any model that needs some sort of activation must implement this interface.
 */
interface Activatable_Interface {

    /**
     * Contract for activation.
     *
     */
    public function activate();

}
