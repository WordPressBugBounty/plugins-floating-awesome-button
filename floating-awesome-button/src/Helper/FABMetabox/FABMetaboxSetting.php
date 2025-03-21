<?php

namespace Fab\Metabox;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */

use Fab\Wordpress\Model\Metabox;

class FABMetaboxSetting extends Metabox {

    /**
     * WP object
     * @var object
     */
    protected $WP;

    /**
     * Params object
     * @var object
     */
    protected $params;

    /** FAB Metabox Settings */
    public static $types = array(
        array(
            'text' => 'Bar & Button',
            'children' => array(
                array(
                    'id'   => 'print',
                    'text' => 'Print',
                ),
                array(
                    'id'   => 'readingbar',
                    'text' => 'Reading Bar',
                ),
                array(
                    'id'   => 'scrolltotop',
                    'text' => 'Scroll To Top',
                ),
            )
        ),
        array(
            'text' => 'Link',
            'children' => array(
                array(
                    'id'   => 'link',
                    'text' => 'Link',
                ),
                array(
                    'id'   => 'anchor_link',
                    'text' => 'Anchor Link',
                ),
                array(
                    'id'   => 'latest_post_link',
                    'text' => 'Latest Post',
                ),
            )
        ),
        array(
            'text'     => 'Modal & Popup',
            'children' => array(
                array(
                    'id'   => 'auth_login',
                    'text' => 'Login',
                ),
                array(
                    'id'   => 'auth_logout',
                    'text' => 'Logout',
                ),
                array(
                    'id'   => 'modal',
                    'text' => 'Simple Modal',
                ),
                array(
                    'id'   => 'search',
                    'text' => 'Search',
                ),
            ),
        ),
        array(
            'text'     => 'Widget',
            'children' => array(
                array(
                    'id'   => 'modal_widget',
                    'text' => 'Modal + Widget',
                ),
                array(
                    'id'   => 'widget',
                    'text' => 'Widget',
                ),
            ),
        ),
        array(
            'text'     => 'Toast',
            'children' => array(
                array(
                    'id'   => 'toast',
                    'text' => 'Toast',
                ),
            ),
        ),
    );

    /** FAB Metabox Settings */
    public static $triggers = array(
        array(
            'id'   => 'exit_intent',
            'text' => 'Exit Intent',
        ),
        array(
            'id'   => 'time_delay',
            'text' => 'Time Delay',
        ),
    );

    /** $_POST input */
    public static $input = array(
        'fab_setting_type'          => array( 'default' => '' ),

        /** Link */
        'fab_setting_link'          => array( 'default' => '' ),
        'fab_setting_link_behavior' => array( 'default' => '' ),

        /** Print */
        'fab_setting_print_target' => array( 'default' => '' ),

        /** Toast */
        'fab_setting_toast'       => array(
            'default' => array(
                'duration' => 3000,
                'button_text' => '',
                'button_url' => '',
                'window' => false,
                'closeable' => false,
                'remember_on_click' => false,
                'gravity' => 'top',
                'position' => 'left',
                'background' => '#1e73be',
                'text_color' => '#ffffff',
                'bar_color' => '#ff5722',
            )
        ),
    );

    /** FAB Metabox Post Metas */
    public static $post_metas = array(
        'type'          => array( 'meta_key' => 'fab_setting_type' ),

        /** Link */
        'link'          => array( 'meta_key' => 'fab_setting_link' ),
        'link_behavior' => array( 'meta_key' => 'fab_setting_link_behavior' ),

        /** Print */
        'print_target' => array( 'meta_key' => 'fab_setting_print_target' ),

        /** Toast */
        'toast' => array('meta_key' => 'fab_setting_toast'),
    );

    /** Constructor */
    public function __construct() {
        $plugin   = \Fab\Plugin::getInstance();
        $this->WP = $plugin->getWP();
    }

    /** Sanitize */
    public function sanitize() {
        $input = apply_filters('fab_setting_input', self::$input);

        /** Sanitized input */
        $params = array();
        foreach ( $_POST as $key => $value ) {
            if ( isset( $input[ $key ] ) && $value ) {
                $params[ $key ] = $value;
            }
        }

        $this->params = $params;
    }

    /** SetDefaultInput */
    public function setDefaultInput() {
        /** Default Input Function */
        $input = apply_filters('fab_setting_input', self::$input);

        foreach ( $input as $key => $value ) {
            $this->params[ $key ] = isset( $this->params[ $key ] ) ? $this->params[ $key ] : $value['default'];

            if ( is_array( $this->params[ $key ] ) ) {
                $this->params[ $key ] += $value['default'];
            }
        }

        /** Transform Data */
        $this->params['fab_setting_link'] = ( $this->params['fab_setting_link'] ) ? $this->params['fab_setting_link'] : '#';
        $this->params['fab_setting_link_behavior'] = \Fab\Plugin\Helper::getInstance()->transformBooleanValue($this->params['fab_setting_link_behavior']);
        $this->params['fab_setting_link_behavior'] = ( $this->params['fab_setting_type'] === 'link' ) ? $this->params['fab_setting_link_behavior'] : 0;
        $this->params['fab_setting_toast']['window'] = ( $this->params['fab_setting_toast']['window'] === 'true' || $this->params['fab_setting_toast']['window'] == 1 ) ? 1 : 0;
        $this->params['fab_setting_toast']['closeable'] = ( $this->params['fab_setting_toast']['closeable'] === 'true' || $this->params['fab_setting_toast']['closeable'] == 1 ) ? 1 : 0;
        $this->params['fab_setting_toast']['remember_on_click'] = ( $this->params['fab_setting_toast']['remember_on_click'] === 'true' || $this->params['fab_setting_toast']['remember_on_click'] == 1 ) ? 1 : 0;

        do_action('fab_setting_set_default_input', $this->params);
    }

    /** Save data to database */
    public function save() {
        global $post;

        foreach ( $this->params as $key => $value ) {
            $this->WP->update_post_meta( $post->ID, $key, $value );
        }
    }

    /** Get types, check the dependency and enable the type */
    public static function get_types() {
        $types_with_dependencies = apply_filters('fab_setting_types', self::$types);

        foreach ($types_with_dependencies as &$type) {
            if (isset($type['children']) && is_array($type['children'])) {
                foreach ($type['children'] as &$child) {
                    $child['is_enable'] = true;

                    if (isset($child['dependency'])) {
                        $child['is_enable'] = is_plugin_active($child['dependency']);
                    }
                }
            }
        }

        return $types_with_dependencies;
    }

    /** Get input with filters */
    public static function get_input(){
        $input = apply_filters('fab_setting_input', self::$input);

        return $input;
    }

    /** Get post metas with filters */
    public static function get_post_metas(){
        $post_metas = apply_filters('fab_setting_post_metas', self::$post_metas);

        return $post_metas;
    }
}
