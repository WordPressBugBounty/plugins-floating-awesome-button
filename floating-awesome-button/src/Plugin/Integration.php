<?php

namespace Fab\Controller;

!defined( 'WPINC ' ) or die;

/**
 * Plugin integration
 *
 * @package    Fab
 * @subpackage Fab/Plugin
 */

class Integration extends Controller {

    /**
     * Plugin file
     *
     * @var string
     */
    protected $plugin_file;

    /**
     * Autoload integration
     *
     * @return void
     */
    public function autoload_integration() {
        // Check if there's integration option
        $integration_option = get_option('fab_integration_' . $this->plugin_file);
        if( ! $integration_option ){
            update_option('fab_integration_' . $this->plugin_file, 'yes');
        }
    }

}
