<?php
namespace Fab\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstraction that provides contract relating to initialization.
 * Any model that needs some sort of initialization must implement this interface.
 */
interface Initiable_Interface {

    /**
     * Contract for initialization.
     *
     */
    public function initialize();

}
