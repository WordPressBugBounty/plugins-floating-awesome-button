<?php

namespace Fab\Api;

use Fab\Interfaces\Model_Interface;
use Fab\Interfaces\Initiable_Interface;
use Fab\Interfaces\Activatable_Interface;

! defined( 'WPINC ' ) or die;

/**
* Initiate plugins
*
* @package    Fab
* @subpackage Fab/Api
*/

class Usages extends Api implements Model_Interface, Initiable_Interface, Activatable_Interface {

    /**
     * @var string $_usage_cron_action Cron action name
     */
    private $_usage_cron_action = 'fab_usage_tracking_cron';

    /**
     * @var string $_usage_cron_config Cron configuration key
     */
    private $_usage_cron_config = 'fab_usage_tracking_config';

    /**
     * @var string $_usage_last_checking Last check-in timestamp key
     */
    private $_usage_last_checking = 'fab_usage_tracking_last_checkin';

    /**
     * @var string $_usage_url Check-in API URL
     */
    private $_usage_url = 'https://usg.artistudio.xyz/v1/checkin';

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->_usage_url = defined('FAB_USAGE_TRACKING_URL') ? FAB_USAGE_TRACKING_URL : $this->_usage_url;
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare Data
    |--------------------------------------------------------------------------
     */

    /**
     * Gather the tracking data together
     *
     * @access public
     */
    private function _get_data() {
        $data = array();

        // Site URL.
        $data['siteurl'] = home_url();

        // Settings data.
        $this->_append_settings_data( $data );

        // Environment data.
        $this->_append_environment_data( $data );

        // Integrations data.
        $this->_append_integrations_data( $data );

        return $data;
    }

    /**
     * Append settings data.
     *
     * @access private
     *
     * @param array $data Usage data.
     */
    private function _append_settings_data( &$data ) {
        global $wpdb;

        $data['settings'] = array();

        // Retrieve settings with 'fab' in the option_name
        $results = $wpdb->get_results(
            "
            SELECT option_name, option_value
            FROM {$wpdb->options}
            WHERE option_name LIKE '%fab%'
            ",
        );

        // Check if results are returned
        if ($results) {
            foreach ( $results as $row ) {
                $data['settings'][ $row->option_name ] = is_serialized($row->option_value) ? unserialize( $row->option_value ) : $row->option_value ;
            }
        } else {
            $data['settings'] = [];
        }
    }

    /**
     * Append environment data.
     *
     * @access private
     *
     * @param array $data Usage data.
     */
    private function _append_environment_data( &$data ) {

        $data['env'] = array();

        // Get current theme info.
        $theme_data = wp_get_theme();

        // Get multisite data.
        $count_blogs = 1;
        if ( is_multisite() ) {
            if ( function_exists( 'get_blog_count' ) ) {
                $count_blogs = get_blog_count();
            } else {
                $count_blogs = 'Not Set';
            }
        }

        $data['env']['url']               = home_url();
        $data['env']['php_version']       = phpversion();
        $data['env']['wp_version']        = get_bloginfo( 'version' );
        $data['env']['server']            = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : ''; // phpcs:ignore
        $data['env']['multisite']         = is_multisite();
        $data['env']['sites']             = $count_blogs;
        $data['env']['usercount']         = function_exists( 'count_users' ) ? count_users() : 'Not Set';
        $data['env']['themename']         = $theme_data->Name;
        $data['env']['themeversion']      = $theme_data->Version;
        $data['env']['admin_email']       = get_bloginfo( 'admin_email' );
        $data['env']['usagetracking']     = get_option( $this->_usage_cron_config, false );
        $data['env']['timezoneoffset']    = wp_timezone_string();
        $data['env']['locale']            = get_locale();
        $data['env']['active_plugins']    = $this->_get_active_plugins_data();
        $data['env']['is_premium']        = $this->Helper->isPremiumPlan();
    }

    /**
     * Append integrations data.
     *
     * @access private
     *
     * @param array $data Usage data.
     */
    private function _append_integrations_data( &$data ) {
        global $wpdb;

        $data['integrations'] = array();

        // Retrieve the popular fab type.
        $popular_fab_type = $wpdb->get_row(
            "
            SELECT meta_value, COUNT(*) as count
            FROM {$wpdb->postmeta}
            WHERE meta_key = 'fab_setting_type'
            GROUP BY meta_value
            ORDER BY count DESC
            LIMIT 1
            "
        );

        if ($popular_fab_type) {
            $data['integrations']['popular_fab_type'] = $popular_fab_type->meta_value;
        } else {
            $data['integrations']['popular_fab_type'] = null; // or some default value
        }
    }

    /**
     * Get site's list of active plugins.
     *
     * @access private
     *
     * @return array List of active plugins.
     */
    private function _get_active_plugins_data() {
        $active_plugins         = get_option( 'active_plugins', array() );
        $network_active_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );

        return array_unique( array_merge( $active_plugins, $network_active_plugins ) );
    }

    /*
    |--------------------------------------------------------------------------
    | Schedules
    |--------------------------------------------------------------------------
     */

    /**
     * Schedule when we should send tracking data
     *
     * @access public
     */
    public function schedule_send() {
        $main_usage_tracking_scheduled = wp_next_scheduled( $this->_usage_cron_action );

        // Return if schedules are already set.
        if ( $main_usage_tracking_scheduled ) {
            return;
        }

        $tracking = array();
        $tracking['day']      = rand( 0, 6 );
        $tracking['hour']     = rand( 0, 23 );
        $tracking['minute']   = rand( 0, 59 );
        $tracking['second']   = rand( 0, 59 );
        $tracking['offset']   = ( $tracking['day'] * DAY_IN_SECONDS ) +
            ( $tracking['hour'] * HOUR_IN_SECONDS ) +
            ( $tracking['minute'] * MINUTE_IN_SECONDS ) +
            $tracking['second'];
        $tracking['initsend'] = strtotime( 'next sunday' ) + $tracking['offset'];

        if ( ! $main_usage_tracking_scheduled ) {
            // Schedule the main usage tracking event.
            wp_schedule_event( $tracking['initsend'], 'weekly', $this->_usage_cron_action );
            update_option( $this->_usage_cron_config, $tracking );
        } else {
            // Use the existing scheduled time.
            $tracking['initsend'] = $main_usage_tracking_scheduled;
        }
    }

    /**
     * Add the cron schedule
     *
     * @access public
     * @param array $schedules The schedules array from the filter.
     */
    public function add_schedules( $schedules = array() ) {
        // Adds once weekly to the existing schedules.
        $schedules['weekly'] = array(
            'interval' => 604800,
            'display'  => __( 'Once Weekly', 'floating-awesome-button' ),
        );
        return $schedules;
    }

    /**
     * Send the checkin.
     *
     * @access public
     * @param bool $override            Flag to override if tracking is allowed or not.
     * @param bool $ignore_last_checkin Flag to ignore that last checkin time check.
     * @return bool Whether the checkin was sent successfully.
     */
    public function send_checkin( $override = false, $ignore_last_checkin = false ) {

        // Check if tracking is allowed on this site.
        if ( ! $this->is_tracking_allowed() && ! $override ) {
            return false;
        }

        // Send a maximum of once per week.
        $last_send = get_option( $this->_usage_last_checking );
        $ignore_last_checkin = defined('FAB_USAGE_TRACKING_IGNORE_LAST_CHECKIN') ? FAB_USAGE_TRACKING_IGNORE_LAST_CHECKIN : $ignore_last_checkin;
        if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! ( $ignore_last_checkin ) ) {
            return false;
        }

        $response = wp_remote_post(
            $this->_usage_url,
            array(
                'method'      => 'POST',
                'timeout'     => 60,
                'redirection' => 5,
                'httpversion' => '1.1',
                'blocking'    => false,
                'body'        => json_encode( $this->_get_data() ),
                'headers'     => array(
                    'Content-Type' => 'application/json',
                ),
                'user-agent'  => 'FAB/' . $this->Plugin->getVersion() . '; ' . get_bloginfo( 'url' ),
            )
        );

        // If we have completed successfully, recheck in 1 week.
        update_option( $this->_usage_last_checking, time() );
        return true;
    }

    /**
     * Check if tracking is allowed.
     *
     * @access public
     *
     * @return bool True if allowed, false otherwise.
     */
    public function is_tracking_allowed() {
        // Check if freemius is allowed.
        if (
            fab_freemius()->is_registered() && // Checks if user opted-in (or activated a license).
            fab_freemius()->is_tracking_allowed() // Checks if user didn't opt out from tracking.
        ) {
                return true;
        }

        // Check if the user is allow the usage tracking.
        $allow_usage = get_option( 'fs_fab_admin_notice_usage_tracking', 'no' );

        // If premium then force to allow usage tracking.
        if ( 'no' === $allow_usage && $this->Helper->isPremiumPlan() ) {
            update_option( 'fs_fab_admin_notice_usage_tracking', 'yes' );
            return true;
        }

        return ( 'yes' === $allow_usage );
    }

    /**
     * Sends a check-in request if no previous check-in exists.
     *
     * This function checks whether a last check-in record exists.
     * If it is empty, it forces a new check-in.
     *
     * @return void
     */
    private function _maybe_send_checkin() {
        $last_send = get_option($this->_usage_last_checking, false);
        if (!$last_send) {
            // Force a check-in.
            $this->send_checkin(true, true);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Fulfill implemented interface contracts
    |--------------------------------------------------------------------------
     */

    /**
     * Execute codes that needs to run plugin activation.
     *
     * @return void
     */
    public function activate() {
        $this->schedule_send();
    }

    /**
     * Execute codes that needs to run plugin init.
     *
     * @return void
     */
    public function initialize() {
        $this->_maybe_send_checkin();
    }

    /**
     * Execute Class.
     *
     * @return void
     */
    public function run() {
        /** Add usage tracking schedule */
        add_filter('cron_schedules', array($this, 'add_schedules'));

        /** Schedule usage tracking check-in */
        add_action($this->_usage_cron_action, array($this, 'send_checkin'));
    }
}
