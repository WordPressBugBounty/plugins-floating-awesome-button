<?php

namespace Fab\Plugin;

! defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */
class Config {

    // Load traits.
    use \Fab\Plugin\Helper\Singleton;

    /**
     * @access   protected
     * @var      object    $config  Config object
     */
    protected $config;

    /**
     * Constructor
     */
    public function __construct() {
        $this->Helper = \Fab\Plugin\Helper::getInstance();
        $this->config = $this->Helper->get_plugin_config();
    }

    /**
     * Initiate config
     *
     * @return void
     */
    public function initiate() {
        $this->config->options = get_option( 'fab_config' );
        $this->config->options = ( $this->config->options ) ? $this->config->options : new \stdClass();
    }

    /**
     * Run functions
     *
     * @return void
     */
    public function run(){
        $this->setDefaultOption();
    }

    /**
     * Activate functions
     *
     * @return void
     */
    public function activate(){
        $this->setDefaultOption();
    }

    /**
     * Set default config, if config not exists in db
     *
     * @return void
     */
    public function setDefaultOption(){
        $config_db = (array) $this->config->options;
        $config = $this->Helper->ArrayMergeRecursive(
            (array) $this->config->default,
            (array) $this->config->options
        );
        if(empty($config_db) || !$config_db){
            update_option( 'fab_config', (object) $config );
        }
        $this->config->options = apply_filters( 'fab_after_setup_options', (object) $config );
    }

    /**
     * @return object
     */
    public function getConfig() {
        return $this->config;
    }
}
