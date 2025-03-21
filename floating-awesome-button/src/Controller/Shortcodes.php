<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\Module\FABModuleSearch;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Shortcodes extends Base implements Model_Interface {

    /**
     * Set View for [fab_search] shortcode
     *
     * @return      string              Html template string from view View/Frontend/search.php
     */
    public function fab_search() {
        $module = new FABModuleSearch();
        $module->render();
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
        /** @frontend - [fab_search] shortcode Floating Awesome Button - Search */
        add_shortcode( 'fab_search', array( $this, 'fab_search' ) );
    }
}
