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
class Analytics extends Api implements Model_Interface {

    /**
     * Registers REST API routes for the Floating Awesome Button plugin.
     *
     * @return void
     */
    public function register_routes() {
        \register_rest_route(
           'fab/v1',
            '/clicked',
            array(
                array(
                    'methods'             => \WP_REST_Server::CREATABLE,
                    'permission_callback' => '__return_true',
                    'callback'            => array( $this, 'fab_clicked' ),
                ),
            )
        );
    }

    /**
     * Handles the REST API request for the clicked fab button.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
     */
    public function fab_clicked( $request ) {
        $nonce = $request->get_header('X-WP-Nonce');
        if ( ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new \WP_Error(
                'rest_cookie_invalid_nonce',
                __( 'Nonce verification failed', 'floating-awesome-button' ),
                array( 'status' => 403 )
            );
        }

        $post_id = $request->get_param('post_id');
        if ( ! $post_id || ! get_post( $post_id ) ) {
            return new \WP_Error(
                'invalid_post_id',
                __( 'Invalid post ID', 'floating-awesome-button' ),
                array( 'status' => 400 )
            );
        }

        do_action( 'fab_analytic_before_click', $post_id );

        // Record total clicks.
        $clicks = (int) get_post_meta( $post_id, 'fab_total_clicked', true );

        update_post_meta( $post_id, 'fab_total_clicked', $clicks + 1 );

        // Record daily clicks, default threshold_days is 7.
        $threshold_days = apply_filters( 'fab_analytic_threshold_days', 7) ;
        $daily_clicks = $this->_record_daily_clicks( $post_id, $threshold_days );

        do_action( 'fab_analytic_after_click', $post_id );

        return new \WP_REST_Response(
            array(
                'success' => true,
                'post_id' => $post_id,
                'clicks'  => $clicks + 1,
                'daily_clicks'  => $daily_clicks,
            ),
            200
        );
    }

    /**
     * Record daily clicks.
     *
     * @access private
     *
     * @param int $post_id Post ID.
     * @param int $threshold_days Threshold days. Default is set to be 7 (number of daily clicks required).
     * @return int Daily clicks.
     */
    private function _record_daily_clicks( $post_id, $threshold_days = 7 ) {
        $daily_clicks = get_post_meta( $post_id, 'fab_daily_clicked', true );
        if ( ! is_array( $daily_clicks ) ) {
            $daily_clicks = [];
        }

        $today = date( 'Y-m-d' );

        // Increment today's count or initialize it
        if ( isset( $daily_clicks[ $today ] ) ) {
            $daily_clicks[ $today ]++;
        } else {
            $daily_clicks[ $today ] = 1;
        }

        // Remove data older than threshold days
        $threshold_date = date( 'Y-m-d', strtotime( '-' . $threshold_days . ' days' ) );
        foreach ( $daily_clicks as $date => $count ) {
            if ( $date < $threshold_date ) {
                unset( $daily_clicks[ $date ] );
            }
        }

        update_post_meta( $post_id, 'fab_daily_clicked', $daily_clicks );

        return $daily_clicks;
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
