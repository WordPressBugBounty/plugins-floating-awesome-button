<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) or die;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */

class Controller {

    // Load plugin traits.
    use \Fab\Plugin\Helper\Singleton;

    /**
     * Plugin configuration object
     * @var object
     */
    protected $Plugin;

    /**
     * Helper object
     * @var object
     */
    protected $Helper;

    /**
     * WP object
     * @var object
     */
    protected $WP;

    /**
     * @access   protected
     * @var      array    $hook    Lists of hooks to register within controller
     */
    protected $hooks;

    /**
     * Admin constructor
     *
     * @return void
     * @param    object $plugin     Plugin configuration
     * @pattern prototype
     */
    public function __construct() {
        $this->Plugin = \Fab\Plugin::getInstance();
        $this->Helper = \Fab\Plugin\Helper::getInstance();
        $this->WP     = \Fab\Wordpress\Helper::getInstance();
        $this->hooks  = array();
    }

    /**
     * Overloading Method, for multiple arguments
     *
     * @method  loadModel           _ Load model @var string name
     * @method  loadController      _ Load controller @var string name
     */
    public function __call( $method, $arguments ) {
        if ( in_array( $method, array( 'loadModel', 'loadController' ) ) ) {
            $list = ( $method == 'loadModel' ) ? $this->Plugin->getModels() : array();
            $list = ( $method == 'loadController' ) ? $this->Plugin->getControllers() : $list;
            if ( count( $arguments ) == 1 ) {
                $this->{$arguments[0]} = $list[ $arguments[0] ];
            }
            if ( count( $arguments ) == 2 ) {
                $this->{$arguments[0]} = $list[ $arguments[1] ];
            }
        }
    }

    /**
     * @return array
     */
    public function getHooks() {
        return $this->hooks ?? array();
    }

    /**
     * @param array $hooks
     */
    public function setHooks( $hooks ) {
        $this->hooks = $hooks;
    }

    /**
     * Get the Plugin property
     * @return object
     */
    public function getPlugin() {
        return $this->Plugin;
    }

    /**
     * Get the WP property
     * @return object
     */
    public function getWP() {
        return $this->WP;
    }

}
