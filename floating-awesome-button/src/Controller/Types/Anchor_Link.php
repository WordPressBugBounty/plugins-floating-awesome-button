<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) || die;

use Fab\Metabox\FABMetaboxSetting;

/**
 * FAB Module Link.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class Anchor_Link extends Controller {

    /**
     * Type.
     *
     * @var string
     */
    public $type = 'anchor_link';

    /**
     * Add link data to FAB item
     *
     * @param object $instance FAB item instance.
     * @return void
     */
    public function add_fab_item_data( $instance ) {
        if ( $instance->getType() !== $this->type ) {
            return;
        }

        // Set Link.
        $link = get_post_meta( $instance->getID(), FABMetaboxSetting::$post_metas['link']['meta_key'], true );
        $link = ( $link && is_string( $link ) ) ? $link : '';
        $instance->setLink( $link );

        // Set Link Behavior.
        $link_behavior = get_post_meta( $instance->getID(), FABMetaboxSetting::$post_metas['link_behavior']['meta_key'], true );
        $instance->setLinkBehavior( $link_behavior );
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
