<?php

namespace Fab\Api;

use Fab\Interfaces\Model_Interface;

! defined( 'WPINC ' ) or die;

/**
* Initiate plugins
*
* @package    Fab
* @subpackage Fab/Api
*/
class Plugin extends Api implements Model_Interface {

    /**
     * Register REST API endpoints
     */
    public function register_integration_endpoint() {
        register_rest_route('fab/v1', '/integration', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_integration_action'),
            'permission_callback' => function() {
                return defined('FAB_REST_API_USER_IS_ADMIN') && FAB_REST_API_USER_IS_ADMIN;
            },
            'args' => array(
                'file' => array(
                    'required' => true,
                    'type' => 'string',
                    'validate_callback' => function($param) {
                        return preg_match('/^[a-z0-9-]+\/[a-z0-9-]+\.php$/', $param);
                    }
                ),
                'status' => array(
                    'required' => true,
                    'type' => 'string',
                    'enum' => array('not-installed', 'inactive', 'enabled', 'disabled')
                )
            )
        ));
    }

    /**
     * Handle integration action
     */
    public function handle_integration_action($request) {
        $plugin_file = $request->get_param('file');
        $status = $request->get_param('status');

        try {
            // Install & Activate plugin if status is not-installed or inactive
            $allowed = array('not-installed', 'inactive');
            if (in_array($status, $allowed)) {
                $this->handle_plugin_action($plugin_file, 'install-activate');
            }

            // Update integration option
            if ($status === 'enabled') {
                update_option('fab_integration_' . $plugin_file, "no");
            } else {
                update_option('fab_integration_' . $plugin_file, "yes");
            }
        } catch (\Exception $e) {
            return new \WP_Error('plugin_action_error', $e->getMessage(), array('status' => 400));
        }

        return rest_ensure_response(array(
            'success' => true,
            'message' => $status === 'enabled' ? 'Integration removed successfully' : 'Integration added successfully'
        ));
    }

    /**
     * Handle plugin installation and activation
     *
     * @param string $plugin_file The plugin file.
     * @param string $action The action to perform.
     * @return WP_Error|bool True if the action was successful, otherwise a WP_Error object.
     */
    public function handle_plugin_action($plugin_file, $action) {
        // Validate inputs
        if (empty($plugin_file) || empty($action)) {
            return new \WP_Error('invalid_input', 'Plugin file and action are required', array('status' => 400));
        }

        // Extract plugin slug from plugin file
        $plugin_slug = explode('/', $plugin_file)[0];

        // Install plugin if action is install or install-activate
        if ($action === 'install' || $action === 'install-activate') {
            if (!file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
                include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
                include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
                include_once ABSPATH . 'wp-admin/includes/file.php';

                // Get plugin info
                $api = plugins_api('plugin_information', array(
                    'slug' => $plugin_slug,
                    'fields' => array('sections' => false)
                ));

                if (is_wp_error($api)) {
                    return new \WP_Error('plugin_api_error', $api->get_error_message(), array('status' => 400));
                }

                $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());
                $result = $upgrader->install($api->download_link);

                if (is_wp_error($result) || !$result) {
                    return new \WP_Error('plugin_install_error', 'Failed to install plugin', array('status' => 400));
                }
            }

            // Activate after install if requested
            if ($action === 'install-activate') {
                $activated = activate_plugin($plugin_file);
                if (is_wp_error($activated)) {
                    return new \WP_Error('plugin_activation_error', $activated->get_error_message(), array('status' => 400));
                }
            }
        }
        // Handle standalone activate
        else if ($action === 'activate') {
            $activated = activate_plugin($plugin_file);
            if (is_wp_error($activated)) {
                return new \WP_Error('plugin_activation_error', $activated->get_error_message(), array('status' => 400));
            }
        }
        // Handle deactivate
        else if ($action === 'deactivate') {
            if (!is_plugin_active($plugin_file)) {
                return new \WP_Error('plugin_not_active', 'Plugin is not active', array('status' => 400));
            }

            deactivate_plugins($plugin_file);
            if (is_plugin_active($plugin_file)) {
                return new \WP_Error('plugin_deactivation_error', 'Failed to deactivate plugin', array('status' => 400));
            }
        }
        else {
            return new \WP_Error('invalid_action', 'Invalid action specified', array('status' => 400));
        }

        return true;
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
        // Add REST API endpoints
        add_action('rest_api_init', array($this, 'register_integration_endpoint'));
    }
}
