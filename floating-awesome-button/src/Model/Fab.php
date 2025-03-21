<?php
/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab/Model
 */

namespace Fab\Model;

! defined( 'WPINC ' ) || die;

use Fab\Metabox\FABMetaboxDesign;
use Fab\Metabox\FABMetaboxLocation;
use Fab\Metabox\FABMetaboxSetting;
use Fab\Metabox\FABMetaboxTrigger;
use Fab\Helper\FABItem;

class Fab extends Model {

    /**
     * @var array   WordPress global $post variable.
     */
    protected $post;

    /**
     * Constructor
     *
     * @param \Fab\Plugin $plugin
     */
    public function __construct() {
        parent::__construct();

        /** Create a post type */
        $this->args['labels'] = [
            'name'                  => strtoupper( $this->name ),
            'add_new_item'          => sprintf( __('Add New %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'edit_item'             => sprintf( __('Edit %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'new_item'              => sprintf( __('New %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'view_item'             => sprintf( __('View %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'view_items'            => sprintf( __('View %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'search_items'          => sprintf( __('Search %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'not_found'             => sprintf( __('No %s found', 'floating-awesome-button'), strtolower($this->name) ),
            'not_found_in_trash'    => sprintf( __('No %s found in Trash', 'floating-awesome-button'), strtolower($this->name) ),
            'all_items'             => sprintf( __('All %s', 'floating-awesome-button'), strtoupper($this->name) ),
            'archives'              => sprintf( __('%s Archives', 'floating-awesome-button'), strtoupper($this->name) ),
            'insert_into_item'      => sprintf( __('Insert into %s', 'floating-awesome-button'), strtolower($this->name) ),
            'uploaded_to_this_item' => sprintf( __('Uploaded to this %s', 'floating-awesome-button'), strtolower($this->name) ),
        ];
        $this->args['public'] = true;
        $this->args['publicly_queryable'] = true; /** Needed to enable Elementor */
        $this->args['menu_icon'] = json_decode( FAB_PATH )->plugin_url . '/assets/img/icon.png';
        $this->args['has_archive'] = false;
        $this->args['show_in_rest'] = true;
        $this->args['supports'] = array('title', 'editor', 'thumbnail');

        // @backend - Save FAB Metabox Data
        add_action( 'save_post', array( $this, 'metabox_save_data' ) );

        // @backend - Redirect FAB Post Type Public Access
        add_action( 'template_redirect', array( $this, 'redirect_public_access' ) );
    }

    /**
     * Save metabox data when post is saving
     *
     * @return void
     */
    public function metabox_save_data() {
        global $post;

        /** Check Correct Post Type, Ignore Trash */
        if ( ! isset( $post->ID ) || $post->post_type !== 'fab' || $post->post_status === 'trash' ) {
            return;
        }

        /** Save Metabox Setting */
        if ( $this->checkInput( FABMetaboxSetting::get_input() ) ) {
            $metabox = new FABMetaboxSetting();
            $metabox->sanitize();
            $metabox->setDefaultInput();
            $metabox->save();
        }

        /** Save Metabox Design */
        if ( $this->checkInput( FABMetaboxDesign::$input ) ) {
            $metabox = new FABMetaboxDesign();
            $metabox->sanitize();
            $metabox->setDefaultInput();
            $metabox->save();
        }

        /** Save Metabox Location */
        if ( $this->checkInput( FABMetaboxLocation::$input ) ) {
            $metabox = new FABMetaboxLocation();
            $metabox->sanitize();
            $metabox->setDefaultInput();
            $metabox->save();
        } else {
            $this->WP->delete_post_meta( $post->ID, FABMetaboxLocation::$post_metas['locations']['meta_key'] );
        }

        /** Save Metabox Trigger */
        if ( $this->checkInput( FABMetaboxTrigger::$input ) ) {
            $metabox = new FABMetaboxTrigger();
            $metabox->sanitize();
            $metabox->setDefaultInput();
            $metabox->save();
        }
    }

    /**
     * Return fabs item and fabs order
     *
     * @param array $args   Arguments.
     * @return array
     */
    public function get_lists_of_fab( $args = array() ) {
        /** Data */
        $order = array();
        $items = array();
        $custom = array(
            'readingbar' => false,
            'scrolltotop' => false
        );
        $fab_preview = isset ( $args['fab_preview'] ) ? $args['fab_preview']: false;

        /** Grab Data - Ordered Data */
        $fab_order = $this->Plugin->getConfig()->options->fab_order;
        if ( $fab_order ) {
            $order = $fab_order;
            foreach ( $fab_order as $value ) {
                $items[] = get_post( $value );
            }
        }
        $order = array_flip( $order );

        /** Grab Data - Unordered */
        $items = array_merge(
            $items,
            get_posts(
                array(
                    'posts_per_page' => -1,
                    'post_type'      => $this->getName(),
                    'post_status'    => array( 'publish' ),
                    'post__not_in'   => empty( $fab_order ) ?
                        array( 'empty' ) : $fab_order,
                    'orderby'        => 'post_date',
                    'order'          => 'DESC',
                )
            )
        );

        /** Add preview if exists */
        if ( $fab_preview ){
            $items[] = get_post($fab_preview->ID);
        }

        /** Filter by Location */
        $tmp = array();
        foreach ( $items as &$item ) {
            /** Data Validation */
            if ( ! isset( $item->ID ) ) { continue; }

            /** FAB Item */
            $item = new FABItem( $item->ID ); // Grab FAB Item.

            /** FAB Item Args Validation */
            if ( $item->getStatus() !== 'publish' && (!$fab_preview || $item->getID() !== $fab_preview->ID) ) {
                continue;
            }

            if ( !$item->isToBeDisplayed() ) {
                continue;
            }

            if ( isset( $args['builder'] ) && ! in_array( $item->getBuilder(), $args['builder'] ) ) { continue; }

            /** FAB Item Grab Custom Module */
            if( in_array($item->getType(), array_keys($custom)) ){
                $custom[ $item->getType() ] = $item;
                if ( isset( $args['filtercustommodule'] ) ) { continue; }
            }

            /** FAB Item Location */
            if ( isset( $args['validateLocation'] ) &&
                ! empty( $item->getLocations() ) &&
                ! $item->isToBeDisplayed()
            ) {
                continue; // Check location rules.
            }

            /** Order */
            if ( ! isset( $order[ $item->getID() ] ) ) {
                $order[ $item->getID() ] = count( $order ); }

            /** Grab Location */
            $tmp[] = $item;
        }
        unset( $item );
        $items = $tmp;

        /** Filter by Type */
        if ( isset( $args['filterbyType'] ) ) {
            $tmp = array();
            foreach ( $items as $item ) {
                if ( in_array( $item->getType(), $args['filterbyType'] ) ) {
                    $tmp[] = $item;
                }
            }
            $items = $tmp;
        }

        return array(
            'order' => array_flip( $order ),
            'items' => $items,
            'custom' => $custom
        );
    }

    /** Redirect public access */
    public function redirect_public_access() {
        global $post;
        if ( isset( $post->post_type ) && $post->post_type === 'fab' ) {
            $user  = ( is_user_logged_in() ) ? wp_get_current_user() : array();
            $roles = ( isset( $user->roles ) ) ? (array) $user->roles : array();
            if ( ! in_array( 'administrator', $roles ) ) {
                $url = sprintf( '%spost.php?post=%s&action=edit', admin_url(), $post->ID );
                wp_redirect( $url );
            }
        }
    }

    /** Check Input Exists */
    private function checkInput( $input, $input_exists = false ) {
        /** Get Parameters */
        $params = $_POST;

        /** Check Input Exists */
        foreach ( $input as $key => $value ) {
            if ( isset( $params[ $key ] ) ) {
                $input_exists = true;
                break; }
        }

        return $input_exists;
    }

}
