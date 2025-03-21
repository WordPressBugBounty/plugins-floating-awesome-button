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
* Initiate plugins
*
* @package    Fab
* @subpackage Fab/Controller
*/
class MetaboxSetting extends Base implements Model_Interface {

    /**
     * Add Fab upsell metabox IDs.
     *
     * This method registers additional metabox IDs for Fab's upsell feature.
     *
     * @param array $metabox_ids The existing list of metabox IDs.
     * @return array The updated list of metabox IDs.
     */
    public function add_fab_upsell_metabox_ids( $metabox_ids ) {
        $metabox_ids[] = array('metabox_id' => 'fab-metabox-triggers');

        return $metabox_ids;
    }

    /**
     * Register metabox settings on custom post type Fab
     *
     * @return      void
     */
    public function metabox_settings() {
        $metabox = new MetaBox();
        $metabox->setScreen( 'fab' );
        $metabox->setId( 'fab-metabox-settings' );
        $metabox->setTitle( 'Setting' );
        $metabox->setCallback( array( $this, 'metabox_settings_callback' ) );
        $metabox->setCallbackArgs( array( 'is_display' => false ) );
        $metabox->build();
    }

    /**
     * Metabox Setting set view template
     *
     * @return      string              Html template string from view View/Template/backend/metabox_settings.php
     * @param       object $post      global $post object
     */
    public function metabox_settings_callback() {
        global $post;

        // Grab Data
        $fab = new FABItem( $post->ID );
        $fab = $fab->getVars();

        // Enqueue Script & component
        $this->enqueue_setting_script($fab);
        $this->enqueue_design_script($fab);
        $this->enqueue_trigger_script($fab);

        // Render View
        View::RenderStatic( 'Backend.Metabox.setting', compact('fab') );
    }

    /**
     * Enqueue Setting Script
     *
     * @param       object $fab      global $post object
     * @return      void
     */
    private function enqueue_setting_script($fab) {
        // Enqueue Localize Script
        $this->WP->wp_localize_script( 'fab-local', 'FAB_METABOX_SETTING', apply_filters( 'fab_backend_enequeue_metabox_setting_localize', array(
            'defaultOptions' => [
                'types' => FABMetaboxSetting::get_types(),
                'triggers' => FABMetaboxSetting::$triggers,
            ],
            'data' => compact('fab'),
            'labels' =>
            array(
                'setting' =>
                    array(
                        'type' => array(
                            'text' => __( 'Type', 'floating-awesome-button' ),
                            'tooltip' => __('Select the type of button, options include Bar & Button, Link, Modal, and Widget.', 'floating-awesome-button'),
                        ),
                        'link' => array(
                            'text' => __( 'Link Address', 'floating-awesome-button' ),
                            'info' => sprintf(
                                __( 'Use %1$s to target page or %2$s for anchor link', 'floating-awesome-button' ),
                                '<code>https://</code>',
                                '<code>#</code>'
                            ),
                            'tooltip' => __('Specify the link address for the button. This can be a full URL or an anchor link.', 'floating-awesome-button'),
                        ),
                        'anchor_link' => array(
                            'text' => __( 'Open link in new tab', 'floating-awesome-button' ),
                            'tooltip' => __('Enable this option to open the link in a new browser tab.', 'floating-awesome-button'),
                        ),
                        'print' => array(
                            'text' => __( 'Print Target', 'floating-awesome-button' ),
                            'info' => sprintf(
                                __('Please refer to %1$s for references. Use %2$s to print the whole document.', 'floating-awesome-button'),
                                '<a href="https://printjs.crabbly.com/" target="_blank"><code>printJS</code></a>',
                                '<code>body</code>'
                            ),
                            'tooltip' => __('Specify the target element for printing. This can be a specific element or the entire document.', 'floating-awesome-button'),
                        ),
                        'toast' => array(
                            'duration' => array(
                                'text' => __( 'Duration', 'floating-awesome-button' ),
                                'tooltip' => __( 'Duration to show the Toast message in milliseconds.', 'floating-awesome-button' ),
                            ),
                            'text_button' => array(
                                'text' => __( 'Text Button', 'floating-awesome-button' ),
                                'tooltip' => __( 'Text for the Toast button. Empty if you want to remove the button.', 'floating-awesome-button' ),
                            ),
                            'url_button' => array(
                                'text' => __( 'URL Button', 'floating-awesome-button' ),
                                'tooltip' => __( 'Destination URL for the Toast link. Empty if you want to remove the button.', 'floating-awesome-button' ),
                            ),
                            'window' => array(
                                'text' => __( 'New Window', 'floating-awesome-button' ),
                                'tooltip' => __( 'Open link in a new window.', 'floating-awesome-button' ),
                            ),
                            'closeable' => array(
                                'text' => __( 'Closeable', 'floating-awesome-button' ),
                                'tooltip' => __( 'Option to make the Toast message closeable.', 'floating-awesome-button' ),
                            ),
                            'remember_on_click' => array(
                                'text' => __( 'Remember On Click', 'floating-awesome-button' ),
                                'tooltip' => __( 'Option to make the Toast message remember on click, so the toast will not be shown again.', 'floating-awesome-button' ),
                            ),
                            'gravity' => array(
                                'text' => __( 'Gravity', 'floating-awesome-button' ),
                                'tooltip' => __( 'Toast display position: Top or Bottom.', 'floating-awesome-button' ),
                            ),
                            'position' => array(
                                'text' => __( 'Position', 'floating-awesome-button' ),
                                'tooltip' => __( 'Horizontal position: Left, Center, or Right.', 'floating-awesome-button' ),
                            ),
                            'background' => array(
                                'text' => __( 'Background', 'floating-awesome-button' ),
                                'tooltip' => __( 'Background color of the Toast.', 'floating-awesome-button' ),
                            ),
                            'text_color' => array(
                                'text' => __( 'Text Color', 'floating-awesome-button' ),
                                'tooltip' => __( 'Text color of the Toast.', 'floating-awesome-button' ),
                            ),
                            'bar_color' => array(
                                'text' => __( 'Bar Color', 'floating-awesome-button' ),
                                'tooltip' => __( 'Progress bar color for duration of the Toast.', 'floating-awesome-button' ),
                            ),
                        )
                    ),
            ),
        )));

        // Enqueue Script & component
        $this->WP->wp_enqueue_script( 'fab-setting', 'build/js/backend/metabox-setting.min.js', array(), '', true );
        $this->WP->wp_enqueue_script_component( 'metabox-setting-component', 'build/components/metabox-setting/bundle.js', array(), '1.0', true);
    }

    /**
     * Enqueue Design Script
     *
     * @param       object $fab      global $post object
     * @return      void
     */
    private function enqueue_design_script($fab) {
        $this->WP->wp_localize_script( 'fab-local', 'FAB_METABOX_DESIGN', array(
            'defaultOptions' => [
                'size' => array( 'type' => Design::$size['type'] ),
                'theme' => Modal::$theme,
                'layout' => Modal::$layout,
                'template' => Design::$template,
            ],
            'data' => compact('fab'),
            'labels' =>
                array(
                    'button' =>
                        array(
                            'color' => array(
                                'text' => __( 'Color', 'floating-awesome-button' ),
                                'tooltip' => __('Select the color for the button. This color will be applied to the button background.', 'floating-awesome-button'),
                            ),
                            'shape' => array(
                                'text' => __( 'Shape', 'floating-awesome-button' ),
                                'tooltip' => __('Choose the shape of the button. Options include rounded, square, or custom shapes.', 'floating-awesome-button'),
                                'info' => sprintf(
                                    __('Please refer to %s to see the shape', 'floating-awesome-button'),'<code><a href="https://bennettfeely.com/clippy/" target="_blank">Clippy</a></code>'
                                ),
                            ),
                            'responsive' => array(
                                'text' => __( 'Show In', 'floating-awesome-button' ),
                                'tooltip' => __('Determine where the button should be visible. Options include mobile, tablet, and desktop views.', 'floating-awesome-button'),
                                'switchs' => array(
                                    'mobile' => __( 'Mobile', 'floating-awesome-button' ),
                                    'tablet' => __( 'Tablet', 'floating-awesome-button' ),
                                    'desktop' => __( 'Desktop', 'floating-awesome-button' ),
                                )
                            ),
                            'standalone' => array(
                                'text' => __( 'Standalone', 'floating-awesome-button' ),
                                'tooltip' => __('Enable this option to separate the button from other groups, making it an individual element.', 'floating-awesome-button'),
                                'info' => __( 'Seperates button from groups', 'floating-awesome-button' ),
                                'enable' => __( 'Enable', 'floating-awesome-button' ),
                            ),
                            'hotkey' => array(
                                'text' => __( 'Hotkey', 'floating-awesome-button' ),
                                'tooltip' => __('Configure hotkeys for the button.', 'floating-awesome-button'),
                                'info' => sprintf(
                                    __('Please refer to %s to see supported hotkey', 'floating-awesome-button'),'<code><a href="https://rawgit.com/jeresig/jquery.hotkeys/master/test-static-01.html" target="_blank">jQuery Hotkeys</a></code>'
                                ),
                            ),
                            'icon' => array(
                                'text' => __( 'Icon', 'floating-awesome-button' ),
                                'tooltip' => __('Select the icon for the button.', 'floating-awesome-button'),
                                'info' => __( 'Icon Configuration', 'floating-awesome-button' ),
                                'class' => __( 'Class', 'floating-awesome-button' ),
                                'color' => __( 'Color', 'floating-awesome-button' ),
                                'tooltip_color' => __('Select the color for the icon.', 'floating-awesome-button'),
                                'picker' => array(
                                    'title' => __('Icon Selector','floating-awesome-button'),
                                    'search_placeholder'  => __('Search icon...','floating-awesome-button'),
                                    'delete' => __('Delete','floating-awesome-button'),
                                    'cancel' => __('Cancel','floating-awesome-button'),
                                    'select' => __('Select','floating-awesome-button'),
                                )
                            ),
                            'tooltip' => array(
                                'text' => __( 'Tooltip', 'floating-awesome-button' ),
                                'always_display' =>  __( 'Always Display', 'floating-awesome-button' ),
                                'tooltip' => __('Choose whether to always display or not', 'floating-awesome-button'),
                                'info' => __( 'Tooltip Configuration', 'floating-awesome-button' ),
                                'enable' => __( 'Enable', 'floating-awesome-button' ),
                                'font_color' => __( 'Font Color', 'floating-awesome-button' ),
                                'tooltip_font_color' => __('Specify the color for the text button.', 'floating-awesome-button'),
                            ),
                        ),
                    'modal' =>
                        array(
                            'theme' => array(
                                'text' => __( 'Theme', 'floating-awesome-button' ),
                                'tooltip' => __('Select the theme for the modal.', 'floating-awesome-button'),
                            ),
                            'layout' => array(
                                'text' => __( 'Layout', 'floating-awesome-button' ),
                                'tooltip' => __('Choose the layout for the modal content. This includes how elements are arranged inside the modal.', 'floating-awesome-button'),
                            ),
                            'size' => array(
                                'text' => __( 'Size', 'floating-awesome-button' ),
                                'tooltip' => __('Select the predefined size of the modal, such as small, medium, or large.', 'floating-awesome-button'),
                            ),
                            'custom_size' => array(
                                'text' => __( 'Custom Size', 'floating-awesome-button' ),
                                'tooltip' => __('Specify a custom size for the modal. You can use units like %, px, or em.', 'floating-awesome-button'),
                                'placeholder' => __('Custom Size %, px, em', 'floating-awesome-button'),
                            ),
                            'navigation' => array(
                                'text' => __( 'Navigation', 'floating-awesome-button' ),
                                'tooltip' => __('Configure the navigation elements within the modal.', 'floating-awesome-button'),
                            ),
                            'background_color' => array(
                                'text' => __( 'Background Color', 'floating-awesome-button' ),
                                'tooltip' => __('Set the background color of the modal.', 'floating-awesome-button'),
                            ),
                            'animation' => array(
                                'text' => __( 'Animation', 'floating-awesome-button' ),
                                'info' => sprintf(
                                    __( 'To see animation reference you can go to <code><a href="%s" target="_blank">Animate.css</a></code>', 'floating-awesome-button' ),
                                    'https://daneden.github.io/animate.css/'
                                ),
                                'in' => array(
                                    'text' => __( 'In', 'floating-awesome-button' ),
                                    'tooltip'  => __('Select the animation for how the modal appears.', 'floating-awesome-button'),
                                ),
                                'out' => array(
                                    'text' => __( 'Out', 'floating-awesome-button' ),
                                    'tooltip'  => __('Select the animation for how the modal disappears.', 'floating-awesome-button'),
                                ),
                            ),
                            'overlay' => array(
                                'text' => __( 'Overlay', 'floating-awesome-button' ),
                                'info' =>  __( 'Modal Overlay', 'floating-awesome-button' ),
                                'background_color' => array(
                                    'text' => __( 'Background Color', 'floating-awesome-button' ),
                                    'tooltip'  => __('Set the background color of the overlay that appears behind the modal.', 'floating-awesome-button'),
                                ),
                                'opacity' => array(
                                    'text' => __( 'Opacity', 'floating-awesome-button' ),
                                    'tooltip'  => __('Adjust the transparency of the overlay background.', 'floating-awesome-button'),
                                ),
                            ),
                            'spacing' => array(
                                'text' => __( 'Content Spacing', 'floating-awesome-button' ),
                                'info' =>  __( 'Modal Overlay', 'floating-awesome-button' ),
                                'padding' => array(
                                    'text' => __( 'Padding', 'floating-awesome-button' ),
                                    'tooltip'  => __('Set the padding inside the modal content area.', 'floating-awesome-button'),
                                ),
                                'margin' => array(
                                    'text' => __( 'Margin', 'floating-awesome-button' ),
                                    'tooltip'  => __('Set the margin around the modal content.', 'floating-awesome-button'),
                                ),
                            ),
                            'no_modal' => array(
                                'text' => __( 'None modal/popup type action button!', 'floating-awesome-button' ),
                                'info' =>  __( 'Please change the type to modal/popup to access the setting.', 'floating-awesome-button' ),
                            ),
                        ),
                ),
            )
        );

        // Enqueue Script & component
        $this->WP->wp_enqueue_script( 'fab-design', 'build/js/backend/metabox-design.min.js', array(), '', true );
    }

    /**
     * Enqueue Trigger Script
     *
     * @param       object $fab      global $post object
     * @return      void
     */
    private function enqueue_trigger_script($fab) {
        /** Add Inline Script */
        $this->WP->wp_localize_script(
            'fab-local',
            'FAB_METABOX_TRIGGER', array(
                'defaultOptions' => array(
                    'types' => FABMetaboxTrigger::$types,
                ),
                'data' => compact('fab'),
                'labels' =>
                array(
                    'trigger' =>
                        array(
                            'type' => array(
                                'text' => __( 'Type', 'floating-awesome-button' ),
                                'tooltip' => __('Select the type of trigger that will activate the modal or button action.', 'floating-awesome-button'),
                            ),
                            'delay' => array(
                                'text' => __( 'Delay', 'floating-awesome-button' ),
                                'tooltip' => __('Set the delay in miliseconds (ms) before the trigger activates the modal or button action after the specified event occurs.', 'floating-awesome-button'),
                            ),
                        ),
                    'cookie' =>
                        array(
                            'expiration' => array(
                                'text' => __( 'Expiration (Days)', 'floating-awesome-button' ),
                                'tooltip' => __('Specify the number of days before the cookie expires. This determines how long the user’s interaction with the button or modal is remembered.', 'floating-awesome-button'),
                            ),
                        ),
                ),
            )
        );

        // Enqueue Script & component
        $this->WP->wp_enqueue_script( 'fab-trigger', 'build/js/backend/metabox-trigger.min.js', array(), '', true );
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
        // @backend - Add Setting metabox to Fab CPT
        add_action( 'add_meta_boxes', array( $this, 'metabox_settings' ), 10, 0 );

        // @backend - Filter to add triggers metabox ID to Fab Upsell
        add_filter('fab_upsell_metabox_ids', array($this, 'add_fab_upsell_metabox_ids'));
    }
}
