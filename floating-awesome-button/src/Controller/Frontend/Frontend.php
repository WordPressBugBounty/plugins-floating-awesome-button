<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;
use Fab\Metabox\FABMetaboxSetting;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Frontend extends Base implements Model_Interface {

    /**
     * Eneque scripts to @frontend
     *
     * @return  void
     * @var     array   $hook_suffix     The current admin page
     */
    public function frontend_enequeue( $hook_suffix ) {
        global $post;

        /** Default Variables */
        define( 'FAB_SCREEN', json_encode( $this->WP->getScreen() ) );
        $default = $this->Plugin->getConfig()->default;
        $config  = $this->Plugin->getConfig()->options;
        $options = (object) ( $this->Helper->ArrayMergeRecursive( (array) $default, (array) $config ) );
        $fabTypes = array();

        /** Get FAB for JS Manipulation */
        $fab_to_display = $this->Plugin->getModels()['Fab'];
        $fab_to_display = $fab_to_display->get_lists_of_fab( array(
            'validateLocation' => true,
            'fab_preview' => $post->post_status === 'draft' && $post->post_type === 'fab' ? $post : false,
        ) )['items'];
        foreach($fab_to_display as &$fab){
            $fabTypes[$fab->getType()] = $fab->getType();
            if($fab->getModal()) { $fabTypes['modal'] = 'modal'; }
            $fab = $fab->getVars();
        }

        /** Get Features for JS Manipulation */
        $features = $this->Plugin->getFeatures();
        foreach($features as $key => &$feature){
            $feature = $feature->getOptions();
            if(!$feature) { unset($features[$key]); }
        }

        /** Load Inline Script */
        $this->WP->wp_enqueue_script( 'fab-local', 'local/fab.js', array(), '', true );
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_PLUGIN',
            array(
                'name'              => FAB_NAME,
                'version'           => FAB_VERSION,
                'screen'            => FAB_SCREEN,
                'path'              => FAB_PATH,
                'premium'           => $this->Helper->isPremiumPlan(),
                'rest_url'          => esc_url_raw( rest_url() ),
                'options'           => $options,
                'to_display'        => $fab_to_display,
                'features'          => $features,
                'nonce'         => array(
                    'clicked'  => wp_create_nonce( 'wp_rest' ),
                ),
            )
        );

        /** Load WP Core jQuery */
        wp_enqueue_script( 'jquery' );

        /** Load Vendors */
        if ( isset( $config->fab_animation->enable ) && $config->fab_animation->enable ) {
            $this->WP->wp_enqueue_style( 'animatecss', 'vendor/animatecss/animate.min.css' );
        }
        $this->WP->enqueue_assets( $config->fab_assets->frontend );

        /** Load Plugin Assets */
        $this->WP->wp_enqueue_style( 'fab', 'build/css/frontend.min.css' );
        $this->WP->wp_enqueue_script( 'fab', 'build/js/frontend/plugin.min.js', array(), '', true );

        /** Load Components */
        foreach($fab_to_display as $component){
            $type = str_contains($component['type'], 'toast') ? 'toast' : $component['type'];

            $this->WP->wp_enqueue_style( sprintf('fab-%s-component', $type), sprintf('build/components/%s/bundle.css',$type) );
            $this->WP->wp_enqueue_script(sprintf('fab-%s-component', $type), sprintf('build/components/%s/bundle.js', $type), array(), '1.0', true);
        }

        /** Load Special Plugin Components */
        $components = ['fab', 'readingbar'];
        foreach($components as $component){
            $this->WP->wp_enqueue_style( sprintf('fab-%s-component', $component), sprintf('build/components/%s/bundle.css', $component) );
            $this->WP->wp_enqueue_script(sprintf('fab-%s-component', $component), sprintf('build/components/%s/bundle.js', $component), array(), '1.0', true);
        }

        /** Special Template/Styles */
        if($options->fab_design->template->name==='shape'){ $this->WP->wp_enqueue_style( 'fab-shapes', sprintf('build/css/fab-shapes.min.css', $component) ); }
        if(isset($fabTypes['modal'])){ $this->WP->wp_enqueue_style( 'fab-modal', sprintf('build/css/fab-modal.min.css', $component) ); }

        /** Livereload */
        if(function_exists('FAB_LoadComponentLiveReload')) { FAB_LoadComponentLiveReload($this); }
    }

    /**
     * Display the html element from view Frontend/float_button.php
     *
     * @return  void
     */
    public function fab_loader() {
        global $post;

        /** Ignore in Pages */
        if ( is_singular() && isset( $post->post_type ) && $post->post_type === 'fab' && $post->post_status !== 'draft') {
            return;
        }

        /** Grab Data */
        $Fab = $this->Plugin->getModels()['Fab'];
        $args = array(
            'validateLocation' => true,
            'filtercustommodule' => true,
            'fab_preview' => $post->post_status === 'draft' ? $post : false,
        );
        $lists = $Fab->get_lists_of_fab( $args );
        $fab_to_display = $lists['items'];

        /** Show FAB Button */
        View::RenderStatic('Frontend.button');

        /** Show Modal - Only Default */
        if ( ! is_admin() && ( $fab_to_display ) ) {
            $args['builder'] = array( 'default' );
            $fab_to_display  = $Fab->get_lists_of_fab( $args )['items'];
            View::RenderStatic('Frontend.modal',
                compact( 'post', 'fab_to_display' )
            );
        }
    }

    /** Register widgets */
    public function fab_register_widget() {
        /** Grab Widgets Type */
        $types       = FABMetaboxSetting::$types;
        $widgetsType = array();
        foreach ( $types as $type ) {
            if ( $type['text'] === 'Widget' ) {
                foreach ( $type['children'] as $child ) {
                    $widgetsType[] = $child['id'];
                }
            }
        }

        /** Grab FAB with widget type */
        $Fab     = $this->Plugin->getModels()['Fab'];
        $widgets = $Fab->get_lists_of_fab(
            array(
                'filterbyType' => $widgetsType,
            )
        )['items'];

        /** Register Sidebar */
        foreach ( $widgets as $widget ) {
            register_sidebar(
                array(
                    'name'          => __( $widget->getTitle(), sprintf( 'fab-widget-%s',  $widget->getSlug() ) ),
                    'id'            => sprintf( 'fab-widget-%s',  $widget->getSlug() ),
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

    public function fab_preview_content( $template ) {
        if ( is_preview() && get_post_type() === 'fab' ) {

            // Get the path to the plugin root directory
            $plugin_dir = dirname( plugin_dir_path( __FILE__ ), 3 ); // Move up 3 directories
            $readme_path = $plugin_dir . '/readme.txt';

            // Check if the readme.txt file exists
            if ( file_exists( $readme_path ) ) {
                // Read the content of the readme.txt file
                $readme_content = file_get_contents( $readme_path );

                // Extract content after '== Description =='
                if ( preg_match( '/== Description ==\s+(.*?)(?:==|\Z)/s', $readme_content, $matches ) ) {
                    $markdown_content = trim( $matches[1] );
                } else {
                    $markdown_content = 'Description not found in readme.txt.';
                }

                // Parse markdown manually
                $custom_content = $this->Helper->parse_readme_to_html( $markdown_content );
            } else {
                $custom_content = '<p>Readme.txt file not found.</p>';
            }

            // Hook into the rendering process.
            add_filter( 'the_content', function ( $content ) use ( $custom_content ) {
                return $custom_content;
            });

            return $template;
        }
        return $template;
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
        /** @frontend - Add Table Of Content Widget */
        add_action( 'widgets_init', array( $this, 'fab_register_widget' ) );

        /** @frontend - Eneque scripts */
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enequeue' ), 20, 1 );

        /** @frontend - Display the html element from view Frontend/float_button.php */
        add_action( 'wp_footer', array( $this, 'fab_loader' ), 10, 0 );

        /** @frontend - Filter wpkses post */
        add_filter( 'wp_kses_allowed_html', array( $this, 'filter_wpkses_posts' ), 10, 2 );

        /** @frontend - Display preview content fab */
        add_filter( 'template_include', array( $this, 'fab_preview_content' ), 10, 1 );
    }
}
