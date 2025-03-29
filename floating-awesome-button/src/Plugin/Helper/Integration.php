<?php

namespace Fab\Plugin\Helper;

!defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

trait Integration {

    /**
     * Check plugin integration
     *
     * @param string $plugin_file The plugin file.
     * @return string The plugin status.
     */
    public function check_plugin_integration($plugin_file) {
        // Check if plugin is installed
        if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            return 'not-installed';
        }

        // Check if plugin is inactive
        if (!is_plugin_active($plugin_file)) {
            return 'inactive';
        }

        // Check if integration option is enabled
        $integration_option = get_option('fab_integration_' . $plugin_file);
        if (!$integration_option || $integration_option === 'no') {
            return 'disabled';
        }

        return 'enabled';
    }

    /**
     * Autoload integration
     *
     * @param string $plugin_file The plugin file.
     * @return void
     */
    public function autoload_integration( $plugin_file ) {
        // Check if there's integration option
        $option_name = sprintf('fab_integration_%s', $plugin_file);
        $integration_option = get_option($option_name);
        if( ! $integration_option ){
            update_option($option_name, 'yes');
        }
    }

}
