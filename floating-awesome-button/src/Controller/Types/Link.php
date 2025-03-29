<?php

namespace Fab\Controller;

! defined( 'WPINC ' ) || die;

use Fab\Metabox\FABMetaboxSetting;

/**
 * Link.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class Link extends Controller {

    /**
     * Type.
     *
     * @var string
     */
    public $type = 'link';

    /**
     * Add link data to FAB item
     *
     * @param object $instance FAB item instance.
     * @return void
     */
    public function add_fab_item_data( $instance ) {
        if ( $instance->getType() !== $this->type ) {
            return;
        }

        // Set Link.
        $link = $this->grab_link( $instance, $instance->getID() );
        $instance->setLink( $link );

        // Set Link Behavior.
        $link_behavior = get_post_meta( $instance->getID(), FABMetaboxSetting::$post_metas['link_behavior']['meta_key'], true );
        $instance->setLinkBehavior( $link_behavior );
    }

    /**
     * Grab Link
     *
     * @param object $item Item.
     * @param int    $fab_id FAB ID.
     * @return string
     */
    public function grab_link( $item, $fab_id ) {
        $link = get_post_meta( $fab_id, FABMetaboxSetting::$post_metas['link']['meta_key'], true );
        $link = ( $link && is_string( $link ) ) ? $link : '';
        $link = $this->convert_whatsapp_link( $link, $item->getRawContent() );

        return $link;
    }

    /**
     * Add support for whatsapp link.
     *
     * @param string $link Link.
     * @param string $content Content.
     * @return string
     */
    public function convert_whatsapp_link( $link, $content ) {
        // If content is empty string, return link as is.
        if ( ! $content || '' === $content ) {
            return $link;
        }

        // Convert whatsapp link to api.whatsapp.com - due to unsupported characters in whatsapp link.
        if ( strpos( $link, 'https://wa.me/' ) !== false ) {
            $link = str_replace( 'https://wa.me/', 'https://api.whatsapp.com/send?phone=', $link );
        }

        // Don't add content parameter if we're in admin editing fab.
        if ( strpos( $link, 'https://api.whatsapp.com' ) !== false && ! is_admin() ) {
            $content = rawurlencode( $content );
            $link    = add_query_arg( 'text', $content, $link );
        }

        return $link;
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
        add_action( 'fab_item_data', array( $this, 'add_fab_item_data' ), 10, 1 );
    }
}
