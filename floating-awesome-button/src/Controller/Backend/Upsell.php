<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */

class Upsell extends Base implements Model_Interface {

    /**
     * Eneque upsell scripts @backend
     *
     * @return  void
     * @var     array   $hook_suffix     The current admin page
     */
    public function backend_enequeue_upsell_metabox_trigger( $hook_suffix ) {
        global $post;
        $screen = $this->WP->getScreen();
        $allowedPage = ['post.php', 'post-new.php'];
        if ( !isset( $post->post_type ) || $post->post_type !== 'fab' || !in_array($screen->pagenow, $allowedPage) ) {
            return;
        }

        // Add metaboxes / container for upsell.
        $metabox_ids = apply_filters('fab_upsell_metabox_ids', array());

        /** Add Inline Script */
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_METABOX_UPSELL',
            array(
                'upsells' => $metabox_ids,
                'logo' => json_decode( FAB_PATH )->plugin_url . '/assets/img/icon.png',
                'title' => __( 'Upgrade To Get Floating Awesome Button Premium Features', 'floating-awesome-button' ),
                'content' => sprintf(
                    '<p>%s</p><p>%s</p><p>%s</p>',
                    __( 'Upgrade to our Premium version to gain full control over when and where your Floating Awesome Button appears.', 'floating-awesome-button' ),
                    __( 'You can create rules based to precisely target where your button appears. Additionally, choose from advanced triggers like Adblock, Exit Intent, Time Delay, or customize your own trigger behavior.', 'floating-awesome-button' ),
                    __( 'Upgrade today to enhance engagement and maximize conversions with targeted button placement!', 'floating-awesome-button' )
                ),
                'button' => '<a target="_blank" href="' . esc_url( $this->Helper->getUpgradeURL() ) . '" class="button-class">' . __( 'Upgrade Now →', 'floating-awesome-button' ) . '</a>'
            )
        );

        $this->WP->wp_enqueue_script( 'upsell-component', 'build/components/upsell/bundle.js', array(), '', true );
    }

    /*
    |--------------------------------------------------------------------------
    | Upsell WoCommerce Integration
    |--------------------------------------------------------------------------
     */

    /**
     * Add FAB setting types for WooCommerce.
     *
     * @param array $types Existing types passed through the filter.
     * @return array Modified types with the new WooCommerce settings type.
     */
    public function add_fab_setting_types( $types ) {
        // Define the new children elements to be added
        $new_children = array(
            array(
                'id'         => 'scarcity_toast_upsell', // add upsell suffix to implement upsell on specific setting type field.
                'dependency' => 'woocommerce/woocommerce.php',
                'text'       => 'Scarcity Toast (Premium)',
            ),
        );

        // Check if 'WooCommerce' already exists in the types array
        foreach ( $types as &$type ) {
            if ( isset( $type['text'] ) && 'WooCommerce' === $type['text'] ) {
                // Merge the new children with the existing ones
                if ( isset( $type['children'] ) ) {
                    $type['children'] = array_merge( $type['children'], $new_children );
                } else {
                    $type['children'] = $new_children;
                }
                return $types; // Return early after merging
            }
        }

        $woocommerce_type = array(
            'text'     => 'WooCommerce',
            'children' => $new_children,
        );

        $types[] = $woocommerce_type; // Append the new type

        return $types;
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
        if ( $this->Helper->isPremiumPlan() ) {
            return;
        }

        /** Enqueue backend upsell metabox trigger */
        add_action( 'admin_enqueue_scripts', array( $this, 'backend_enequeue_upsell_metabox_trigger' ) );

        /** Register the upsell setting field */
        add_filter( 'fab_setting_types', array( $this, 'add_fab_setting_types' ) );
    }
}
