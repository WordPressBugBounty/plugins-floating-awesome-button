<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) || die;

use Fab\Metabox\FABMetaboxSetting;

/**
 * Latest Post.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class Latest_Post extends Controller {

    /**
     * Type.
     *
     * @var string
     */
    public $type = 'latest_post_link';

    /**
     * Add latest post data to FAB item
     *
     * @param object $instance FAB item instance.
     * @return void
     */
    public function add_fab_item_data( $instance ) {
        if ( $instance->getType() !== $this->type ) {
            return;
        }

        // Get the link.
        $post = wp_get_recent_posts(
            array(
                'numberposts' => '1',
                'post_status' => 'publish',
            )
        );
        if ( isset( $post[0] ) ) {
            $instance->setLink(
                get_permalink( $post[0]['ID'] )
            );
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
        add_action( 'fab_item_data', array( $this, 'add_fab_item_data' ), 10, 1 );
    }
}
