<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\Metabox\FABMetaboxLocation;
use Fab\View;
use Fab\Helper\FABItem;
use Fab\Wordpress\MetaBox;

! defined( 'WPINC ' ) or die;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class MetaboxLocation extends Base implements Model_Interface {

    /**
     * Add Fab upsell metabox IDs.
     *
     * This method registers additional metabox IDs for Fab's upsell feature.
     *
     * @param array $metabox_ids The existing list of metabox IDs.
     * @return array The updated list of metabox IDs.
     */
    public function add_fab_upsell_metabox_ids( $metabox_ids ) {
        $metabox_ids[] = array('metabox_id' => 'fab-metabox-location');

        return $metabox_ids;
    }

    /**
     * Eneque scripts @backend
     *
     * @return  void
     * @var     array   $hook_suffix     The current admin page
     */
    public function backend_enequeue_metabox_location( $hook_suffix ) {
        /** Grab Data */
        global $post, $wp_roles;
        $screen = $this->WP->getScreen();
        $allowedPage = ['post.php', 'post-new.php'];
        if ( !isset( $post->post_type ) || $post->post_type !== 'fab' || !in_array($screen->pagenow, $allowedPage) ) {
            return;
        }

        /** Grab Locations */
        $fab       = new FABItem( $post->ID );
        $locations = $fab->getLocations();

        foreach ( $locations as &$location ) {
            // Loop through the 'rules' array to find 'type' and handle accordingly
            if ( isset($location['rules']) && is_array($location['rules']) ) {
                foreach ( $location['rules'] as &$rule ) {
                    // Check if 'type' is set and is a string before using strpos
                    if ( isset($rule['type']) && is_string($rule['type']) ) {
                        if ( strpos($rule['type'], 'single_') !== false ) {
                            $rule['value'] = get_post($rule['value']);
                        } elseif ( strpos($rule['type'], 'taxonomy_') !== false ) {
                            $rule['value'] = get_term($rule['value']);
                        }
                    } else {
                        // Handle the case where 'type' is not set or not a string
                        $rule['value'] = null; // Or apply any other default behavior
                    }
                }
            }
        }

        /** Add Inline Script */
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_METABOX_LOCATION', apply_filters( 'fab_backend_enequeue_metabox_location_localize',
                array(
                    'rest_url'            => esc_url_raw( rest_url() ),
                    'post_types'          => get_post_types(
                        array(
                            'public'       => true,
                            'show_in_rest' => true,
                        ),
                        'objects'
                    ),
                    'post_taxonomies' => get_taxonomies(
                        array(
                            'public' => true,
                            'show_in_rest' => true,
                        ),
                    'objects'
                    ),
                    'excludes_post_types' => array( 'attachment' ),
                    'data'                => compact( 'locations' ),
                    'defaultOptions'      => array(
                        'operator' => FABMetaboxLocation::$operator,
                        'logic' => FABMetaboxLocation::$logic,
                        'user' => [
                            'roles' => array_keys($wp_roles->roles)
                        ],
                    ),
                    'objects' => array()
                )
            ),
        );

        /** Enqueue */
        $this->WP->wp_enqueue_script( 'fab-location', 'build/js/backend/metabox-location.min.js', array(), '', true );

        /** Load Component */
        $component = 'metabox-location';
        $this->WP->wp_enqueue_style( sprintf('%s-component', $component), sprintf('build/components/%s/bundle.css', $component) );
        $this->WP->wp_enqueue_script(sprintf('%s-component', $component), sprintf('build/components/%s/bundle.js', $component), array(), '1.0', true);
    }

    /**
     * Register metabox location on custom post type Fab
     *
     * @return      void
     */
    public function metabox_location() {
        $metabox = new MetaBox();
        $metabox->setScreen( 'fab' );
        $metabox->setId( 'fab-metabox-location' );
        $metabox->setTitle( 'Location'. ( !$this->Helper->isPremiumPlan() ? ' (Premium)':'') );
        $metabox->setCallback( array( $this, 'metabox_location_callback' ) );
        $metabox->setCallbackArgs( array( 'is_display' => false ) );
        $metabox->build();
    }

    /**
     * Metabox Location set view template
     *
     * @return      string              Html template string from view View/Template/backend/metabox_location.php
     */
    public function metabox_location_callback() {
        /** Set View */
        $view = new View( $this->Plugin );
        $view->setTemplate( 'backend.blank' );
        $view->setSections(
            array(
                'Backend.Metabox.location' => array(
                    'name'   => '',
                    'active' => true,
                ),
            )
        );
        $view->build();
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
        /** @backend - Enqueue backend metabox location */
        add_action( 'admin_enqueue_scripts', array( $this, 'backend_enequeue_metabox_location' ), 10, 1 );

        /** @backend - Add Location metabox to Fab CPT */
        add_action( 'add_meta_boxes', array( $this, 'metabox_location' ), 10, 0 );

        /** @backend - Filter to add triggers metabox ID to Fab Upsell */
        add_filter('fab_upsell_metabox_ids', array($this, 'add_fab_upsell_metabox_ids'));
    }
}
