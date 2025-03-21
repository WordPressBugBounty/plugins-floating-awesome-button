<?php

namespace Fab\Plugin\Helper;

!defined( 'WPINC ' ) or die;

/**
 * Singleton trait for Fab plugins
 * 
 * @package    Fab
 * @subpackage Fab\Helper
 */
trait Singleton {

    /**
     * Instance of this class
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Get instance of the class
     *
     * @return object Instance of the class
     */
    public static function getInstance() {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

}
