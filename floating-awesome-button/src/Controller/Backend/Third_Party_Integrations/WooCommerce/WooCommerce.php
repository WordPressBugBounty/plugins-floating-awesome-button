<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) or die;

use Fab\Interfaces\Model_Interface;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class WooCommerce extends Base implements Model_Interface {

    /**
     * Autoload integration
     *
     * @return void
     */
    public function autoload_integration() {
        $this->Helper->autoload_integration( FAB_WOOCOMMERCE_PLUGIN_FILE );
    }

    /**
     * Add integration to FAB
     *
     * @param array $integrations The integrations.
     * @return array The integrations.
     */
    public function add_integration($integrations){
        $integrations['woocommerce'] = array(
            'name' => __('WooCommerce', 'floating-awesome-button'),
            'description' => __('WooCommerce is a powerful and flexible eCommerce plugin for WordPress.', 'floating-awesome-button'),
            'url' => 'https://wordpress.org/plugins/woocommerce/',
            'icon_url' => 'https://ps.w.org/woocommerce/assets/icon.svg',
            'banner_url' => 'https://ps.w.org/woocommerce/assets/banner-1544x500.png',
            'plugin_file' => FAB_WOOCOMMERCE_PLUGIN_FILE,
            'status' => \Fab\Plugin\Helper::getInstance()->check_plugin_integration(FAB_WOOCOMMERCE_PLUGIN_FILE),
        );
        return $integrations;
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
        // Add integration to FAB// Add integration to FAB
        add_filter( 'fab_plugin_integrations', array( $this, 'add_integration' ) );

        // Prevent error if plugin is not active.
        if( !is_plugin_active( FAB_WOOCOMMERCE_PLUGIN_FILE ) ){
            return;
        }

        // Autoload integration
        add_action( 'admin_init', array( $this, 'autoload_integration' ) );
    }
}
