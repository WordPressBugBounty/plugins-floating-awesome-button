<?php
namespace Fab\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstraction that provides contract relating to deactivation.
 * Any model that needs some sort of deactivation must implement this interface.
 */
interface Deactivatable_Interface {

    /**
     * Contract for deactivation.
     */
    public function deactivate();

}
