<?php

namespace Fab\Feature;

! defined( 'WPINC ' ) || die;

use Fab\View;
use Fab\Interfaces\Model_Interface;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */
class Miscellaneous extends Feature implements Model_Interface {
    /**
     * Feature construect
     *
     * @return void
     * @var    object   $plugin     Feature configuration
     * @pattern prototype
     */
    public function __construct() {
        parent::__construct();
        $this->key         = 'core_miscellaneous';
        $this->name        = 'Miscellaneous';
        $this->description = 'Plugin extra options and configuration';

        /** Initialize Options */
        $this->options = array(
            'captcha' => array(
                'text' => __('Captcha', 'floating-awesome-button'),
                'children' => array(
                    'detectlocation' => array(
                        'text' =>  __('Auto Detect location', 'floating-awesome-button'),
                        'label' => array( 'text' => 'Enable/Disable' ),
                        'type' => 'switch',
                        'value' => '',
                        'info' => __('Auto Show/Hide google reCaptcha v3 based on form location', 'floating-awesome-button')
                    ),
                )
            ),
            'usage_tracking' => array (
                'text' => __('Usage Tracking', 'floating-awesome-button'),
                'children' => array(
                    'usagetracking' => array(
                        'text' => __('Allow Usage Tracking', 'floating-awesome-button'),
                        'label' => array( 'text' => 'Enable/Disable' ),
                        'type' => 'switch',
                        'value' => '',
                        'info' => __('By allowing us to track usage data we can better help you because we know with which WordPress configurations, themes and plugins we should test', 'floating-awesome-button')
                    ),
                )
            )
        );
        $options = $this->WP->get_option( sprintf('fab_%s', $this->key) );

        // Force enable allow tracking on UI.
        if ( ( ! get_option('fs_fab_admin_notice_usage_tracking', false) && \Fab\Api\Usages::getInstance()->is_tracking_allowed() ) || get_option('fs_fab_admin_notice_usage_tracking', false) ) {
            $options = is_array($options) ? $options : array();
            $options['usage_tracking'] = array(
                'children' => array(
                    'usagetracking' => array(
                        'value' => 1
                    )
                )
            );
        }

        $this->options = (is_array($options)) ? $this->Helper->ArrayMergeRecursive($this->options, $options) : $this->options;
    }



    /** Google Recaptcha v3 Inactive */
    public function recaptchaAutoDetectInactive(){
        View::RenderStatic( 'Frontend/Miscellaneous/Captcha/AutoDetectInactive' );
    }

    /**
     * Sanitize input
     */
    public function sanitize() {
        /** Grab Data */
        $this->params = $_POST;
        $this->params = $this->params['fab_core_miscellaneous'];

        /** Sanitize Text Field */
        $this->params = (object) $this->WP->sanitizeTextField( $this->params );
    }

    /**
     * Transform data before save
     */
    public function transform() {
        /** Revalidate */
        $plugin   = \Fab\Plugin::getInstance();
        $this->params->captcha = $plugin->getHelper()->transformBooleanValue( $this->params->captcha );
        return $this->params;
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
    public function run(){
        /** @backend - Auto Detect v3 Recaptcha Active or Not */
        if($this->options['captcha']['children']['detectlocation']['value']){
            add_action('wp_footer', 'recaptchaAutoDetectInactive', 10, 0);
        }
    }

}
