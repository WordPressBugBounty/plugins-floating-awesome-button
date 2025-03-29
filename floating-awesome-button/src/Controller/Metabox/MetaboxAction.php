<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) || die;

use Fab\Interfaces\Model_Interface;
use Fab\View;
use Fab\Helper\FABItem;
use Fab\Feature\Design;
use Fab\Feature\Modal;
use Fab\Metabox\FABMetaboxSetting;
use Fab\Metabox\FABMetaboxTrigger;
use Fab\Wordpress\MetaBox;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class MetaboxAction extends Base implements Model_Interface {

    /**
     * Add metabox action
     *
     * @return void
     */
    public function metabox_action() {
        add_meta_box(
            'fab-metabox-action',
            __( 'Action', 'floating-awesome-button' ),
            array( $this, 'metabox_action_callback' ),
            'fab',
            'side',
            'default'
        );
    }

    /**
     * Metabox action callback
     *
     * @return void
     */
    public function metabox_action_callback() {
        View::RenderStatic(
            'Backend.Metabox.action',
            array(
                'clone_url'   => $this->Helper->get_clone_url(),
                'preview_url' => $this->Helper->get_preview_url(),
            )
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
        // @backend - Add Setting metabox to Fab CPT
        add_action( 'add_meta_boxes', array( $this, 'metabox_action' ), 10, 0 );
    }
}
