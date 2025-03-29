<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;
use Fab\Helper\FABItem;
use Fab\Feature\Design;
use Fab\Feature\Modal;
use Fab\Metabox\FABMetaboxSetting;
use Fab\Metabox\FABMetaboxTrigger;
use Fab\Wordpress\MetaBox;

! defined( 'WPINC ' ) or die;

/**
* WooCommerce Metabox Product
*
* @package    Fab
* @subpackage Fab/Controller
*/
class WC_Metabox_Product extends Base implements Model_Interface {

    /**
     * Register metabox product on custom post type WooCommerce Product
     *
     * @return      void
     */
    public function metabox_product() {
        // Check if WooCommerce integrataion is enabled
        if ( \Fab\Plugin\Helper::getInstance()->check_plugin_integration( FAB_WOOCOMMERCE_PLUGIN_FILE ) !== 'enabled' ) {
            return;
        }

        // Add metabox
        add_meta_box(
            'fab-metabox-product',
            __( 'Floating Awesome Button', 'floating-awesome-button' ),
            array( $this, 'metabox_product_callback' ),
            'product',
            'normal',
            'default'
        );
    }

    /**
     * Metabox Product set view template
     *
     * @return      string              Html template string from view View/Template/backend/metabox_product.php
     * @param       object $post      global $post object
     */
    public function metabox_product_callback() {
        global $post;

        // Supported Templates
        $supported_templates = array(
            "woocommerce-add-to-cart",
            "woocommerce-apply-coupon-cart-quantity",
            "woocommerce-apply-coupon-cart-subtotal",
            "woocommerce-apply-coupon-logged-in-customer",
            "woocommerce-apply-coupon",
            "woocommerce-buy-now",
            "woocommerce-cart-reminder",
            "woocommerce-featured-product",
        );
        foreach($supported_templates as &$template) {
            $template = \Fab\Plugin\Helper::getInstance()->get_template($template);

            // Transform template
            $template = array(
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'license' => $template->license,
                'requires' => $template->requires ?? [],
                'design' => array(
                    'color' => $template->design->color,
                    'icon' => array(
                        'color' => $template->design->icon->color,
                        'class' => $template->design->icon->class,
                    ),
                )
            );

            // Add product id to template
            $template_with_product_id = array(
                "woocommerce-featured-product"
            );
            if ( in_array( $template['id'], $template_with_product_id ) ) {
                $template['product_id'] = $post->ID;
            }
        }

        // Enqueue style
        $this->WP->wp_enqueue_style_sass( 'fab', 'assets/css/backend/style.scss' );
        $this->WP->wp_enqueue_style( 'fontawesome', apply_filters('fab_fontawesome_css', 'vendor/fontawesome/css/all.min.css') );

        // Enqueue Script & component
        $this->WP->wp_enqueue_script_component( 'wc-metabox-product-component', 'assets/components/wc-metabox-product/main.js', array(), FAB_VERSION, true);
        $this->WP->wp_localize_script( 'wc-metabox-product-component', 'FAB_WC_METABOX_PRODUCT', apply_filters( 'fab_backend_enqueue_wc_metabox_product_localize', array(
            'templates' => apply_filters('fab_wc_metabox_supported_templates', $supported_templates),
            'labels' => array(
                'templates' => __( 'Templates', 'floating-awesome-button' ),
                'introduction' => __( '<b>Floating Awesome Button (FAB)</b> helps you turn product actions—like "Add to Cart," "Buy Now," or "Featured Deal"—into eye-catching floating buttons that stay visible as customers scroll, nudging them to convert without leaving the page! Create sticky reminders, popups, or quick-access buttons in seconds (no coding needed) and watch abandoned carts drop while engagement soars. It\'s like a 24/7 sales assistant for your WooCommerce store—simple to set up, hardworking for results.', 'floating-awesome-button' ),
                'add_new' => __('Add New', 'floating-awesome-button'),
                'learn_more' => __('Learn More', 'floating-awesome-button'),
                'next' => __('Next', 'floating-awesome-button'),
                'previous' => __('Previous', 'floating-awesome-button'),
                'no_results' => __('No templates found matching your search.', 'floating-awesome-button'),
                'upgrade' => __('Required Upgrade', 'floating-awesome-button'),
                'show_all_descriptions' => __('Show All Descriptions', 'floating-awesome-button'),
                'hide_all_descriptions' => __('Hide All Descriptions', 'floating-awesome-button'),
            ),
        )));

        // Render View
        View::RenderStatic( 'Backend.WooCommerce.metabox-product' );
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
        // @backend - Add metabox to WooCommerce Product
        add_action( 'add_meta_boxes', array( $this, 'metabox_product' ), 10, 0 );
    }
}
