<?php

namespace Fab\Feature;

! defined( 'WPINC ' ) || die;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

class Upsell extends Feature {

    /**
     * Feature construect
     *
     * @return void
     * @var    object   $plugin     Feature configuration
     * @pattern prototype
     */
    public function __construct() {
        parent::__construct();
        $this->key         = 'upsell';
        $this->name        = 'Upsell';
        $this->description = 'Upsell feature';
    }

}
