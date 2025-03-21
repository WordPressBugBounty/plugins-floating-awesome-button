<?php
namespace Fab\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Abstraction that provides contract relating to plugin models.
 * All "regular models" should implement this interface.
 */
interface Model_Interface {

    /**
     * Contract for running the model.
     *
     */
    public function run();

}
