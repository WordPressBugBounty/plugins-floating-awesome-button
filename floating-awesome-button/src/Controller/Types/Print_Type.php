<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) || die;

use Fab\Metabox\FABMetaboxSetting;

/**
 * Print Type
 * - We call this class Print_Type because Print is already a class in PHP/WordPress.
 * - Getting error when using Print as class name.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class Print_Type extends Controller {

    /**
     * Type.
     *
     * @var string
     */
    public $type = 'print';

    /**
     * Add print data to FAB item
     *
     * @param object $instance FAB item instance.
     * @return void
     */
    public function add_fab_item_data( $instance ) {
        if ( $instance->getType() !== $this->type ) {
            return;
        }

        // Set print target.
        $extra_options          = $instance->getExtraOptions();
        $extra_options['print'] = array(
            'target' => get_post_meta( $instance->getID(), FABMetaboxSetting::$post_metas['print_target']['meta_key'], true ),
        );
        $instance->setExtraOptions( $extra_options );
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
