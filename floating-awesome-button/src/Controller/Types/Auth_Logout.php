<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) || die;

/**
 * FAB Module Link.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class Auth_Logout extends Controller {

    /**
     * Type.
     *
     * @var string
     */
    public $type = 'auth_logout';

    /**
     * Add auth logout data to FAB item
     *
     * @param object $instance FAB item instance.
     * @return void
     */
    public function add_fab_item_data( $instance ) {
        if ( $instance->getType() !== $this->type ) {
            return;
        }

        // Set Link.
        $instance->setLink(
            wp_logout_url( home_url() )
        );
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
        add_action( 'fab_item_data', array( $this, 'add_fab_item_data' ), 10, 1 );
    }
}
