<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;
use Fab\Metabox\FABMetaboxSetting;

! defined( 'WPINC ' ) || die;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Frontend extends Base implements Model_Interface {

    /**
     * Enqueue scripts to @frontend
     *
     * @param string $hook_suffix The current admin page.
     * @return void
     */
    public function frontend_enequeue( $hook_suffix ) {
        global $post;

        // Default Variables.
        define( 'FAB_SCREEN', wp_json_encode( $this->WP->getScreen() ) );
        $default  = $this->Plugin->getConfig()->default;
        $config   = $this->Plugin->getConfig()->options;
        $options  = (object) ( $this->Helper->ArrayMergeRecursive( (array) $default, (array) $config ) );
        $fabTypes = array();

        // Get FAB for JS Manipulation.
        $fab_to_display = \Fab\Model\Fab::getInstance();
        $fab_to_display = $fab_to_display->get_lists_of_fab()['items'];
        foreach ( $fab_to_display as &$fab ) {
            $fabTypes[ $fab->getType() ] = $fab->getType();
            if ( $fab->getModal() ) {
                $fabTypes['modal'] = 'modal';
            }
            $fab = $fab->getVars();
        }

        // Get Features for JS Manipulation.
        $features = $this->Plugin->getFeatures();
        foreach ( $features as $key => &$feature ) {
            $feature = $feature->getOptions();
            if ( ! $feature ) {
                unset( $features[ $key ] );
            }
        }

        // Load Inline Script.
        $this->WP->wp_enqueue_script( 'fab-local', 'local/fab.js', array(), FAB_VERSION, true );
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_PLUGIN',
            array(
                'name'       => FAB_NAME,
                'version'    => FAB_VERSION,
                'screen'     => FAB_SCREEN,
                'path'       => FAB_PATH,
                'premium'    => $this->Helper->isPremiumPlan(),
                'rest_url'   => esc_url_raw( rest_url() ),
                'options'    => $options,
                'to_display' => $fab_to_display,
                'features'   => $features,
                'nonce'      => array(
                    'clicked' => wp_create_nonce( 'wp_rest' ),
                ),
            )
        );

        // Load WP Core jQuery.
        wp_enqueue_script( 'jquery' );

        // Load Vendors.
        if ( isset( $config->fab_animation->enable ) && $config->fab_animation->enable ) {
            $this->WP->wp_enqueue_style( 'animatecss', 'vendor/animatecss/animate.min.css' );
        }
        $this->WP->enqueue_assets( $config->fab_assets->frontend );

        // Load Plugin Assets.
        $this->WP->wp_enqueue_style_sass( 'fab', 'assets/css/frontend/style.scss' );
        $this->WP->wp_enqueue_script_typescript( 'fab', 'assets/ts/frontend/plugin.ts', array(), FAB_VERSION, true );

        // Load Components.
        foreach ( $fab_to_display as $component ) {
            $type = str_contains( $component['type'], 'toast' ) ? 'toast' : $component['type'];

            $this->WP->wp_enqueue_style( sprintf( 'fab-%s-component', $type ), sprintf( 'build/components/%s/bundle.css', $type ) );
            $this->WP->wp_enqueue_script( sprintf( 'fab-%s-component', $type ), sprintf( 'build/components/%s/bundle.js', $type ), array(), FAB_VERSION, true );
        }

        // Load Special Plugin Components.
        $components = array( 'fab', 'readingbar' );
        foreach ( $components as $component ) {
            $this->WP->wp_enqueue_style( sprintf( 'fab-%s-component', $component ), sprintf( 'build/components/%s/bundle.css', $component ) );
            $this->WP->wp_enqueue_script( sprintf( 'fab-%s-component', $component ), sprintf( 'build/components/%s/bundle.js', $component ), array(), FAB_VERSION, true );
        }

        // Special Template/Styles.
        if ( $options->fab_design->template->name === 'shape' ) {
            $this->WP->wp_enqueue_style_sass( 'fab-shapes', 'assets/css/fab-shapes/style.scss' );
        }
        if ( isset( $fabTypes['modal'] ) ) {
            $this->WP->wp_enqueue_style_sass( 'fab-modal', 'assets/css/fab-modal/style.scss' );
        }

        // Livereload.
        if ( function_exists( 'FAB_LoadComponentLiveReload' ) ) {
            FAB_LoadComponentLiveReload( $this );
        }
    }

    /**
     * Display the html element from view Frontend/float_button.php
     *
     * @return  void
     */
    public function fab_loader() {
        global $post;

        // Grab Data.
        $Fab            = \Fab\Model\Fab::getInstance();
        $args           = array(
            'filtercustommodule' => true,
        );
        $lists          = $Fab->get_lists_of_fab( $args );
        $fab_to_display = $lists['items'];

        // Show FAB Button.
        View::RenderStatic( 'Frontend.button' );

        // Show Modal - Only Default.
        if ( ! is_admin() && ( $fab_to_display ) ) {
            $args['builder'] = array( 'default' );
            $fab_to_display  = $Fab->get_lists_of_fab( $args )['items'];
            View::RenderStatic(
                'Frontend.modal',
                compact( 'post', 'fab_to_display' )
            );
        }
    }

    /**
     * Show FAB preview notice.
     *
     * @return void
     */
    public function fab_preview_notice() {
        if ( $this->Helper->isPremiumPlan() && $this->Helper->is_preview_page() ) {
            $notice = array(
                'id'      => 'fab-preview-notice',
                'message' => __( 'This is a preview of the FAB, all location rules will be ignored.', 'floating-awesome-button' ),
            );
            View::RenderStatic( 'Frontend.notice', compact( 'notice' ) );
        }
    }

    /**
     * Register widgets.
     *
     * @return void
     */
    public function fab_register_widget() {
        // Grab Widgets Type.
        $types       = FABMetaboxSetting::$types;
        $widgetsType = array();
        foreach ( $types as $type ) {
            if ( $type['text'] === 'Widget' ) {
                foreach ( $type['children'] as $child ) {
                    $widgetsType[] = $child['id'];
                }
            }
        }

        // Grab FAB with widget type.
        $Fab     = \Fab\Model\Fab::getInstance();
        $widgets = $Fab->get_lists_of_fab(
            array(
                'filterbyType' => $widgetsType,
            )
        )['items'];

        // Register Sidebar.
        foreach ( $widgets as $widget ) {
            register_sidebar(
                array(
                    'name'          => __( $widget->getTitle(), sprintf( 'fab-widget-%s', $widget->getSlug() ) ),
                    'id'            => sprintf( 'fab-widget-%s', $widget->getSlug() ),
                    'before_widget' => '<div id="%1$s" class="widget fab-container %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h3 class="widgettitle">',
                    'after_title'   => '</h3>',
                )
            );
        }
    }

    /**
     * Filter wpkses posts accept iframe, used to embed iframe (youtube, vimeo, etc) with FAB Content
     *
     * @param array  $tags The tags to filter.
     * @param string $context The context of the filter.
     * @return array The filtered tags.
     */
    public function filter_wpkses_posts( $tags, $context ) {
        $tags['iframe'] = array(
            'src'             => true,
            'height'          => true,
            'width'           => true,
            'frameborder'     => true,
            'allowfullscreen' => true,
        );
        return $tags;
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
        // Add table of content widget.
        add_action( 'widgets_init', array( $this, 'fab_register_widget' ) );

        // Enqueue scripts.
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enequeue' ), 20, 1 );

        // Display the html element from view Frontend/float_button.php.
        add_action( 'wp_footer', array( $this, 'fab_loader' ), 10, 0 );

        // Show FAB preview notice.
        add_action( 'wp_footer', array( $this, 'fab_preview_notice' ), 10, 0 );

        // Filter wpkses post.
        add_filter( 'wp_kses_allowed_html', array( $this, 'filter_wpkses_posts' ), 10, 2 );
    }
}
