<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Elementor extends Base implements Model_Interface {

    /** Add support elementor builder fab */
    public function elementor_support_post_type_fab() {
        add_post_type_support( 'fab', 'elementor' );
    }

    /** Make elementor editor full width */
    public function post_class_fab( $classes, $class, $ID ) {
        global $post;
        if ( isset( $post->post_type ) && $post->post_type == 'fab' ) {
            $classes[] = 'fab-fullwidth';
        }
        return $classes;
    }

    /** Register elementor widgets init */
    public function fab_register_widget() {
        /** Register Sidebar */
        register_sidebar(
            array(
                'name' => __( 'FAB Elementor Modal' ),
                'id'   => 'fab-widget-elementor',
            )
        );
    }

    /** Display the elementor modal */
    public function fab_display_widget() {
        dynamic_sidebar( 'fab-widget-elementor' );
    }

    /**
     * [fab_elementor] Initiate elementor in page
     */
    public function fab_elementor() {
        global $post;

        /** Ignore in Pages */
        if ( is_single() && isset( $post->post_type ) && $post->post_type === 'fab' ) {
            return;
        }

        /** Grab Data */
        $Fab            = \Fab\Model\Fab::getInstance();
        $fab_to_display = $Fab->get_lists_of_fab(
            array(
                'validateLocation' => true,
                'builder'          => array( 'elementor' ),
            )
        )['items'];

        /** Show FAB Button */
        if ( ! is_admin() && $fab_to_display ) {
            View::RenderStatic(
                'Frontend.modal',
                compact( 'post', 'fab_to_display' )
            );
        }
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
        /** Only Execute Hook when Elementor Plugin is Active */
        if ( is_plugin_active( 'elementor/elementor.php' ) ) {
            // @backend - Add support elementor builder post type fab
            add_action( 'elementor/init', array( $this, 'elementor_support_post_type_fab' ) );

            // @frontend - Setup elementor init widget
            add_action( 'widgets_init', array( $this, 'fab_register_widget' ) );

            // @frontend - Display the elementor modal
            add_action( 'wp_footer', array( $this, 'fab_display_widget' ) );

            // @frontend - Display the elementor modal
            add_action( 'wp_footer', array( $this, 'fab_display_widget' ) );

            // @backend - Make elementor editor full width - post class
            add_filter( 'post_class', array( $this, 'post_class_fab' ), 10, 3 );

            // @frontend - [fab_elementor] Initiate Modal Init
            add_shortcode( 'fab_elementor', array( $this, 'fab_elementor' ) );
        }
    }
}
