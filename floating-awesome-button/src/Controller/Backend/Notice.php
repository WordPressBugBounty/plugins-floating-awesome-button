<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Notice extends Base implements Model_Interface {

    /**
     * Override freemium notice.
     *
     * @return bool false to force disable default freemium notice.
     */
    public function fs_show_admin_notice( $show, $msg ) {

        add_action( 'admin_footer', array( 'FS_Admin_Notice_Manager', '_add_sticky_dismiss_javascript' ) );
        View::RenderStatic( 'Backend.admin-notice', $msg );

        return false;
    }

    /**
     * Show admin upgrade notice.
     *
     * @return void
     */
    public function fab_show_admin_upgrade_notice() {
        if ( function_exists( 'current_user_can' )
            && ! current_user_can( 'manage_options' )
        ) {
            // Only show messages to admins.
            return;
        }

        // Check if the user is on a premium plan.
        if ( $this->Helper->isPremiumPlan() ) {
            return;
        }

        $screen = get_current_screen();

        // Check if the admin notice promo upgrade option is set and if the current screen is not the FAB settings page.
        if ( get_option( 'fs_fab_admin_notice_promo_upgrade' ) && $screen->id !== 'fab_page_floating-awesome-button-setting' ) {
            return;
        }

        // Determine if the notice should be dismissible.
        $dismissible = $screen->id !== 'fab_page_floating-awesome-button-setting';

        // Prepare the data for the admin notice.
        $data = array(
            'message'     => __( 'You\'re currently on our free plan. Elevate your experience by upgrading now and unlock exclusive features and benefits!', 'floating-awesome-button' ),
            'dismissible' => $dismissible,
            'sticky'      => true,
            'manager_id'  => 'floating-awesome-button',
            'id'          => 'fab_upgrade_premium',
            'plugin'      => 'Floating Awesome Button',
            'type'        => 'promotion',
            'buttons'     => array(
                array(
                    'message' => 'Click here to upgrade →',
                    'url'     => $this->Helper->getUpgradeURL(),
                    'classes' => 'button button-primary',
                ),
            ),
        );

        // Add the JavaScript for making the notice sticky and dismissible.
        add_action( 'admin_footer', array( 'FS_Admin_Notice_Manager', '_add_sticky_dismiss_javascript' ) );

        View::RenderStatic( 'Backend.admin-notice', $data );
    }

    /**
     * Ajax callback for dismissing the notice.
     */
    public function dismiss_notice_ajax_callback() {

        if ( isset( $_POST['message_id'] ) && $_POST['message_id'] === 'fab_upgrade_premium' ) {
            update_option( 'fs_fab_admin_notice_promo_upgrade', true );
        }

        if ( isset( $_POST['message_id'] ) && $_POST['message_id'] === 'fab_usage_tracking' ) {
            update_option(
                'fs_fab_admin_notice_usage_tracking',
                isset( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : 'no'
            );

            $fab_core_misc = get_option( 'fab_core_miscellaneous', array() );

            // Ensure the retrieved data is properly unserialized.
            $fab_core_misc = is_serialized( $fab_core_misc ) ? unserialize( $fab_core_misc ) : ( is_array( $fab_core_misc ) ? $fab_core_misc : array() );

            // Ensure the array structure exists.
            $fab_core_misc['captcha']['children']['detectlocation']['value']     ??= 0;
            $fab_core_misc['usage_tracking']['children']['usagetracking']['value'] =
                ( sanitize_text_field( wp_unslash( $_POST['value'] ) ) ?? '0' ) === 'yes' ? '1' : sanitize_text_field( wp_unslash( $_POST['value'] ?? '0' ) );

            // Save the option without double serialization.
            update_option( 'fab_core_miscellaneous', $fab_core_misc );

        }
    }

    /**
     * Show admin usage tracking notice.
     *
     * @return void
     */
    public function fab_show_admin_usage_tracking_notice() {
        if ( function_exists( 'current_user_can' )
            && ! current_user_can( 'manage_options' )
        ) {
            // Only show messages to admins.
            return;
        }

        // Check if the user is allow the usage tracking.
        if ( \Fab\Api\Usages::getInstance()->is_tracking_allowed() ) {
            return;
        }

        $screen = get_current_screen();

        // Check if the admin notice usage tracking option is set and if the current screen is not the FAB settings page.
        if ( get_option( 'fs_fab_admin_notice_usage_tracking' ) === 'no' && $screen->id !== 'fab_page_floating-awesome-button-setting' ) {
            return;
        }

        // Determine if the notice should be dismissible.
        $dismissible = $screen->id !== 'fab_page_floating-awesome-button-setting';

        // Prepare the data for the admin notice.
        $data = array(
            'message'      => __( 'Allow FAB to track plugin usage? Opt-in lets us track usage data so we know with WordPress configurations, themes, and plugins we should test with.', 'floating-awesome-button' ),
            'dismissible'  => $dismissible,
            'sticky'       => true,
            'manager_id'   => 'floating-awesome-button',
            'id'           => 'fab_usage_tracking',
            'plugin'       => 'Floating Awesome Button',
            'suffix_title' => 'USAGE TRACKING PERMISSION',
            'type'         => 'error',
            'buttons'      => array(
                array(
                    'id'      => 'fab_allow_usage_tracking',
                    'value'   => 'yes',
                    'message' => 'Allow tracking',
                    'classes' => 'button button-primary',
                ),
            ),
        );

        // Check the option and conditionally add the "Do not allow" button.
        if ( get_option( 'fs_fab_admin_notice_usage_tracking' ) !== 'no' && $screen->id !== 'fab_page_floating-awesome-button-setting' ) {
            $data['buttons'][] = array(
                'id'      => 'fab_disallow_usage_tracking',
                'value'   => 'no',
                'message' => 'Maybe later',
                'classes' => 'button hover:bg-red-600 hover:text-white',
            );
        }

        // Add the JavaScript for making the notice sticky and dismissible.
        add_action( 'admin_footer', array( 'FS_Admin_Notice_Manager', '_add_sticky_dismiss_javascript' ) );

        View::RenderStatic( 'Backend.admin-notice', $data );
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
        // Handle notice promo premium upgrade.
        add_action( 'admin_notices', array( $this, 'fab_show_admin_upgrade_notice' ) );

        // Handle dismiss notice ajax.
        add_action( 'wp_ajax_fs_dismiss_notice_action_floating-awesome-button', array( $this, 'dismiss_notice_ajax_callback' ), 1 );

        // Handle freemium notice.
        fab_freemius()->add_filter( 'show_admin_notice', array( $this, 'fs_show_admin_notice' ), 10, 2 );

        // Handle notice usage tracking GDPR.
        add_action( 'admin_notices', array( $this, 'fab_show_admin_usage_tracking_notice' ) );
    }
}
