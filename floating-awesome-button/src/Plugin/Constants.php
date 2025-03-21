<?php

namespace Fab\Plugin;

! defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */
class Constants {

    // Load traits.
    use \Fab\Plugin\Helper\Directory;
    use \Fab\Plugin\Helper\Singleton;

    /**
     * Constructor
     */
    public function __construct() {
        $this->config = \Fab\Plugin\Config::getInstance()->getConfig();
        $this->wp = \Fab\Wordpress\Helper::getInstance();
    }

    /**
     * Add constants on rest api
     *
     * @return void
     */
    public function rest_api_constants() {
        define( 'FAB_REST_API_USER_IS_ADMIN', current_user_can('manage_options') );
    }

    /**
     * Initiate constants
     * Define const which will be used within the plugin
     *
     * @return void
     */
    public function initiate() {
        $path = $this->wp->getPath( $this->config->path );
        define( 'FAB_NAME', $this->config->name );
        define( 'FAB_VERSION', $this->config->version );
        define( 'FAB_PRODUCTION', $this->config->production );
        define( 'FAB_PATH', json_encode( $path ) );
        define( 'FAB_PLUGIN_PATH', $path['plugin_path'] );
        define( 'FAB_SLUG', \Fab\Plugin::getInstance()->getSlug() );
        define( 'FAB_POST_TYPE_NAME', \Fab\Model\Fab::getInstance()->getName() );
    }

    /**
     * Run functions
     *
     * @return void
     */
    public function run() {
        // Add constants on rest api
        add_action( 'rest_api_init', array( $this, 'rest_api_constants' ) );
    }
}
