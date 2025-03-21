<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;

! defined( 'WPINC ' ) or die;

/**
* Initiate plugins
*
* @package    Fab
* @subpackage Fab/Controller
*/
class BackendPage extends Base implements Model_Interface {

    /**
     * Adds the settings submenu page for the FAB plugin in the WordPress admin menu.
     *
     * @return void
     */
    public function admin_menu_setting() {
        add_submenu_page(
            'edit.php?post_type=fab',
            __( 'Settings', 'floating-awesome-button' ),
            __( 'Settings', 'floating-awesome-button' ),
            'manage_options',
            sprintf( '%s-setting', \Fab\Plugin::getInstance()->getSlug() ),
            array( $this, 'page_setting' ),
            9
        );
    }

    /**
     * Page Setting
     *
     * @backend @submenu setting
     * @return  void
     */
    public function page_setting() {
        /** Grab Data */
        $plugin        = \Fab\Plugin::getInstance();
        $slug          = sprintf( '%s-setting', $this->Plugin->getSlug() );
        $features      = $this->page_setting_features();

        /** Ignored setting in production */
        $ignored = array( 'core_asset' );
        foreach ( $ignored as $key ) {
            if ( $this->Plugin->getConfig()->production ) {
                unset( $features['features'][ $key ] );
            }
        }

        /** Handle form submission */
        $this->page_setting_submission( $slug, $features );

        $default       = $this->Plugin->getConfig()->default;
        $config        = $this->WP->get_option( 'fab_config' );
        $options       = (object) ( $this->Helper->ArrayMergeRecursive( (array) $default, (array) $config ) );
        $featureshooks = $plugin->getHelper()->FeatureHooksLists( $features['features'], $features['featureHooks'], $options );

        /** Section */
        $sections = array();

        /** Set View */
        View::RenderStatic( 'Backend.setting' );

        /** Data Normalization Before Send to Component */
        /** Section */
        if ( ! $this->Plugin->getConfig()->production ) {
            $sections['Backend.feature'] = array( 'name' => 'Feature' );
        }
        $sections['Backend.setting'] = array(
            'name'   => 'Setting',
            'active' => true,
        );
        $sections['Backend.module']  = array( 'name' => 'Module' );
        $sections['Backend.about']   = array( 'name' => 'About' );
        $nav                         = array();
        foreach ( $sections as $key => $section ) {
            $section['slug'] = str_replace( 'Backend.', '', $key );
            $nav[]           = $section;
        }
        /** Modules */
        $modules = array();
        foreach ( $plugin->getModules() as $module ) {
            $modules[] = $module->getVars(); }
        /** Features */
        foreach ( $features['features'] as &$feature ) {
            $feature = $feature->getVars(); }
        /** Get FAB for JS Manipulation */
        $fab_lists = $this->Plugin->getModels()['Fab'];
        $fab_lists = $fab_lists->get_lists_of_fab();
        foreach ( $fab_lists['items'] as &$fab ) {
            $fab = $fab->getVars(); }

        /** Localize Script */
        $this->WP->wp_enqueue_script( 'fab-local', 'local/fab.js', array(), '', true );
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_SETTING',
            array(
                'status'        => false,
                'config'        => $this->Plugin->getConfig(),
                'sections'      => $nav,
                'modules'       => $modules,
                'fab_lists'     => $fab_lists,
                'features'      => $features['features'],
                'featuresHooks' => $featureshooks,
                'nonce'         => array(
                    'clear'    => wp_create_nonce( 'clear-config' ),
                    'module'   => wp_create_nonce( 'module-config' ),
                    'setting'  => wp_create_nonce( 'wp_rest' ),
                ),
                'url'           => array(
                    'upgrade' => $this->Helper->getUpgradeURL(),
                ),
            )
        );

        /** Load Component */
        $this->WP->wp_enqueue_style( 'fab-setting-component', 'build/components/setting/bundle.css' );
        $this->WP->wp_enqueue_script( 'fab-setting-component', 'build/components/setting/bundle.js', array(), '1.0', true );
    }

    /*** Handle Page Submission */
    public function page_setting_submission( $slug, $features ) {
        if ( isset( $_GET['page'] ) && $_GET['page'] == $slug && $_POST ) {
            if ( isset( $_POST['clear-config'] ) ) { /** Clear Config */
                $this->page_setting_submission_clearconfig();
            } elseif ( isset( $_POST['module-config'] ) ) { /** Module Config */
                $this->page_setting_submission_module();
            } else { /** Save Setting */
                $this->page_setting_submission_setting( $features );
            }
        }
    }

    /** Clear Config */
    public function page_setting_submission_clearconfig() {
        $plugin  = \Fab\Plugin::getInstance();
        $modules = $plugin->getModules();
        $this->WP->delete_option( 'fab_config' );
        $this->WP->update_option( 'fab_config', $this->Plugin->getConfig()->default );
        foreach ( $modules as $module ) {
            $this->WP->delete_option( sprintf( 'fab_%s', $module->getKey() ) );
        }
        $features = $this->page_setting_features()['features'];
        foreach ( $features as $feature ) {
            $this->WP->delete_option( sprintf( 'fab_%s', $feature->getKey() ) );
        }
    }

    /** Save Module Config */
    public function page_setting_submission_module() {
        $params = $_POST;
        foreach ( $params as $key => $options ) {
            if ( strpos( $key, 'fab_' ) === false ) {
                continue; }
            $this->WP->update_option( $key, $options );
        }
    }

    /** Save Plugin Setting */
    public function page_setting_submission_setting( $features, $params = array() ) {
        $default = $this->Plugin->getConfig()->default;
        $options = $this->Plugin->getConfig()->options;

        // Use POST data if provided, otherwise use the provided $params
        $params = !empty($params) ? $params : $_POST;

        /** Sanitize & Transform Animation */
        if ( isset( $params['fab_animation'] ) ) {
            $feature = $features['features']['core_animation'];
            $feature->sanitize();
            $featureOption = ( isset( $options->fab_animation ) ) ? $options->fab_animation : $default->fab_animation;
            $feature->setOptions( $featureOption );
            $options->fab_animation = $feature->transform();
        }

        /** Sanitize & Transform Assets */
        if ( isset( $params['fab_assets'] ) && isset( $features['features']['core_asset'] ) ) {
            $feature = $features['features']['core_asset'];
            $feature->sanitize();
            $featureOption = ( isset( $options->fab_assets ) ) ? $options->fab_assets : $default->fab_assets;
            $feature->setOptions( $featureOption );
            $options->fab_assets = $feature->transform();
        }

        /** Sanitize & Transform Order */
        if ( isset( $params['fab_design'] ) ) {
            $feature       = $features['features']['core_design'];
            $featureOption = ( isset( $options->fab_design ) ) ? $options->fab_design : $default->fab_design;
            $feature->setOptions( $featureOption );
            $feature->sanitize();
            // merge with default template to prevent error on frontend.
            $options->fab_design = json_decode(json_encode(array_replace_recursive(json_decode(json_encode($default->fab_design), true), $params['fab_design'])));
        }

        /** Sanitize & Transform Order */
        if ( isset( $params['fab_order'] ) ) {
            $feature = $features['features']['core_order'];
            $feature->sanitize();
            $options->fab_order = $feature->transform();
        }

        /** Sanitize & Transform Feature */
        if ( isset( $params['fab_hooks'] ) ) {
            $feature = $features['features']['core_hooks'];
            $feature->sanitize();
            $options->fab_hooks = $feature->transform();
        }

        /** Save config */
        $this->WP->update_option( 'fab_config', $options );

        /**
         * Modular Configuration Features Update
         */

        /** Sanitize & Transform Feature */
        if ( isset( $params['fab_core_miscellaneous'] ) ) {
            $feature = $features['features']['core_miscellaneous'];
            $feature->sanitize();
            $this->WP->update_option(
                'fab_core_miscellaneous',
                (array) $feature->transform()
            );
        }

        return wp_send_json_success( array(
            'status'  => 'success',
            'message' => __( 'Settings updated successfully.', 'floating-awesome-button' ),
        ) );
    }

    /**
     * Get Lists of registered features, controller, & APIs
     */
    public function page_setting_features() {
        /** Transform features */
        $features     = $this->Plugin->getFeatures();
        $featureHooks = array();

        /** Map Controller */
        foreach ( $this->Plugin->getControllers() as $name => $controller ) {
            foreach ( $controller->getHooks() as $hook ) {
                $feature                    = ( $hook->getFeature() ) ? $hook->getFeature()->getKey() : 'others';
                $featureHooks[ $feature ][] = $hook;
            }
        }
        /** Map APIs */
        $APIs = $this->Plugin->getApis();
        if ( $APIs ) {
            foreach ( $APIs as $name => $controller ) {
                foreach ( $controller->getHooks() as $hook ) {
                    $feature                    = ( $hook->getFeature() ) ? $hook->getFeature()->getKey() : 'others';
                    $featureHooks[ $feature ][] = $hook;
                }
            }
        }

        return array(
            'features'     => $features,
            'featureHooks' => $featureHooks,
        );
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
        /** @backend - Add custom admin page under settings */
        add_action( 'admin_menu', array( $this, 'admin_menu_setting' ) );
    }
}
