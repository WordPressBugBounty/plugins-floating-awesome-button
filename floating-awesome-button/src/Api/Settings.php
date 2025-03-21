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
class Settings extends Api implements Model_Interface {

    /**
     * Registers REST API routes for the Floating Awesome Button plugin.
     *
     * @return void
     */
    public function register_routes() {
        \register_rest_route(
           'fab/v1',
            '/setting',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'permission_callback' => array( $this, 'get_admin_permissions_check' ),
                    'callback'            => array( $this, 'api_settings' ),
                ),
            )
        );

        \register_rest_route(
            'fab/v1',
             '/clear-setting',
             array(
                 array(
                     'methods'             => \WP_REST_Server::CREATABLE,
                     'permission_callback' => array( $this, 'get_admin_permissions_check' ),
                     'callback'            => array( $this, 'api_clear_settings' ),
                 ),
             )
         );
    }

    /**
     * Checks if a given request has access to read list of settings options.
     *
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
     */
    public function get_admin_permissions_check( $request ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new \WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed access to this endpoint.', 'floating-awesome-button' ), array( 'status' => \rest_authorization_required_code() ) );
        }

        return apply_filters( 'fab_get_admin_permissions_check', true, $request );
    }

    /**
     * Handles the REST API request for saving plugin settings.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function api_settings( $request ) {
        $features = $this->Plugin->getControllers()['BackendPage']->page_setting_features();

        /** Ignored setting in production */
        $ignored = array( 'core_asset' );
        foreach ( $ignored as $key ) {
            if ( $this->Plugin->getConfig()->production ) {
                unset( $features['features'][ $key ] );
            }
        }

        // If usage tracking is allowed then set the usage tracking option to 'yes'
        if($request->get_params()['fab_core_miscellaneous']['usage_tracking']['children']['usagetracking']['value'] == 1){
            update_option('fs_fab_admin_notice_usage_tracking', 'yes');
        } else if($request->get_params()['fab_core_miscellaneous']['usage_tracking']['children']['usagetracking']['value'] == 0){
            update_option('fs_fab_admin_notice_usage_tracking', '');
        }

        $this->Plugin->getControllers()['BackendPage']->page_setting_submission_setting( $features, $request->get_params() );
    }

    /**
     * Handles the REST API request for reset plugin settings.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function api_clear_settings( $request ) {
        $this->Plugin->getControllers()['BackendPage']->page_setting_submission_clearconfig();

        return wp_send_json_success( array(
            'status'  => 'success',
            'message' => __( 'Settings reset successfully.', 'floating-awesome-button' ),
        ) );
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
        /** @backend - Register Routes */
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }
}
