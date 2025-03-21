<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\Feature\Design;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Backend extends Base implements Model_Interface {

    /**
     * Handle plugin upgrade
     *
     * @return void
     */
    public function upgrade_plugin( $upgrader_object, $options ) {
        $current_plugin_path_name = plugin_basename( $this->Plugin->getConfig()->path );
        if ( $options['action'] === 'update' && $options['type'] === 'plugin' ) {
            foreach ( $options['plugins'] as $each_plugin ) {
                if ( $each_plugin == $current_plugin_path_name ) {
                    /** Update options */
                    $this->WP->update_option(
                        'fab_config',
                        (object) (
                        (array) $this->Plugin->getConfig()->options + (array) $this->Plugin->getConfig()->default )
                    );
                }
            }
        }
    }

    /**
     * Eneque scripts @backend
     *
     * @return  void
     */
    public function backend_enequeue() {
        /** Load Data */
        define( 'FAB_SCREEN', json_encode( $this->WP->getScreen() ) );
        $default = $this->Plugin->getConfig()->default;
        $config  = $this->Plugin->getConfig()->options;
        $screen  = $this->WP->getScreen();
        $screens = array(
            sprintf( 'fab_page_%s-setting', \Fab\Plugin::getInstance()->getSlug() ),
            sprintf( 'fab_page_%s-templates', \Fab\Plugin::getInstance()->getSlug() )
        );
        $types   = array( 'fab' );

        /** Load Core Vendors */
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_style( 'wp-color-picker' );

        /** Load Inline Script */
        $options = (object) ( $this->Helper->ArrayMergeRecursive( (array) $default, (array) $config ) );

        $this->WP->wp_enqueue_script( 'fab-local', 'local/fab.js', array(), '', true );
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_PLUGIN',
            array(
                'name'           => FAB_NAME,
                'version'        => FAB_VERSION,
                'screen'         => FAB_SCREEN,
                'path'           => json_decode(FAB_PATH),
                'premium'        => $this->Helper->isPremiumPlan(),
                'production'     => $this->Plugin->getConfig()->production,
                'description'    => $this->Plugin->getConfig()->description,
                'fontsJsonUrl'   => plugin_dir_url(__DIR__ . '/../../../..'). 'fonts.json',
                'options'        => $options,
                'defaultOptions' => array(
                    'layout' => Design::$layout,
                    'template' => Design::$template,
                    'font'  => Design::$font,
                ),
            )
        );

        /** Load Vendors */
        if ( isset( $config->fab_animation->enable ) && $config->fab_animation->enable ) {
            $this->WP->wp_enqueue_style( 'animatecss', 'vendor/animatecss/animate.min.css' );
        }
        if ( in_array( $screen->base, $screens )
            || ( isset( $screen->post->post_type )
            && in_array( $screen->post->post_type, $types ) ) ) {

            $this->WP->enqueue_assets( $config->fab_assets->backend );
        }

        /** Load Plugin Assets */
        $this->WP->wp_enqueue_style( 'fab', 'build/css/backend.min.css' );
        $this->WP->wp_enqueue_script( 'fab', 'build/js/backend/plugin.min.js', array( 'wp-color-picker' ), '', true );

        // Load vite
        if ( defined( 'HMR_DEV' ) && HMR_DEV ) {
            $this->WP->wp_enqueue_script( 'fab-vite', sprintf('http://localhost:%s/@vite/client', HMR_DEV_PORT), array(), '', true );
        }

        /** Livereload */
        if(function_exists('FAB_LoadComponentLiveReload')) {
            FAB_LoadComponentLiveReload($this);
        }
    }

    /**
     * Add setting link in plugin page
     *
     * @backend
     * @return  void
     * @var     array   $links     Plugin links
     */
    public function plugin_setting_link( $links ) {
        $slug = sprintf( '%s-setting', $this->Plugin->getSlug() );
        $text = __('Settings', 'floating-awesome-button');
        return array_merge( $links, array( '<a href="edit.php?post_type=fab&page=' . $slug . '">' . $text . '</a>' ) );
    }

    /**
     * Plugin row meta references
     */
    public function plugin_row_meta_references( $plugin_meta, $plugin_file, $plugin_data, $status ) {
        if ( strpos( $plugin_file, sprintf( '%s.php', $this->Plugin->getSlug() ) ) !== false ) {
            $new_links = array(
                'doc'    => sprintf('<a href="https://www.youtube.com/watch?v=MMuhc9pcYew&list=PLnwuifVLRkaXBV9IBTPZeLtduzCdt5cFh" target="_blank">%s</a>', __('Documentation', 'floating-awesome-button')),
                'tutorial' => sprintf('<a href="https://www.youtube.com/watch?v=CkSspyM9yjQ&list=PLnwuifVLRkaXH9I-QAAReVoEv9DClViPG" target="_blank">%s</a>', __('Tutorial', 'floating-awesome-button')),
                'support' => sprintf('<a href="https://wordpress.org/support/plugin/floating-awesome-button" target="_blank">%s</a>', __('Community Support', 'floating-awesome-button')),
            );

            $plugin_meta = array_merge( $plugin_meta, $new_links );
        }
        return $plugin_meta;
    }

    /**
     * Add script tag attributes, for module scripts
     * - This is vite HMR
     *
     * @param string $tag
     * @param string $handle
     * @return string
     */
    public function add_script_tag_attributes( $tag, $handle ) {
        // Add module scripts
        $handles = apply_filters( 'fab_module_scripts', array( 'fab-vite' ) );

        // Add type="module" to script tag
        if ( in_array( $handle, $handles ) ) {
            $tag = str_replace( ' src=', ' type="module" src=', $tag );
        }

        return $tag;
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
        /** @backend - Handle plugin upgrade */
        add_action( 'upgrader_process_complete', array($this, 'upgrade_plugin'), 10, 2 );

        /** @backend - Enqueue backend plugins assets */
        add_action( 'admin_enqueue_scripts', array($this, 'backend_enequeue'), 10, 0 );

        /** @backend - Add setting link for plugin in plugins page */
        add_action( sprintf( 'plugin_action_links_%s/%s.php', FAB_SLUG, FAB_SLUG ), array($this, 'plugin_setting_link'), 10, 1 );

        /** @backend - Add references links Documentations & Tutorials */
        add_filter('plugin_row_meta', array($this, 'plugin_row_meta_references'), 10, 4);

        // Customize script loader to load vite HMR
        add_filter( 'script_loader_tag', array( $this, 'add_script_tag_attributes' ), 10, 2 );
    }
}
