<?php

namespace Fab\Plugin\Helper;

! defined( 'WPINC ' ) || die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */
trait Page {

    /**
     * Get FAB preview URL
     *
     * @param int|null $id The post ID.
     * @return string|false
     */
    public function get_preview_url( $id = null ) {
        global $post;

        // Check if the post is a FAB post type.
        if ( ! $post || 'fab' !== $post->post_type ) {
            return false;
        }

        // Get FAB preview URL.
        $fab  = $id ? get_post( $id ) : $post;
        $type = get_post_meta( $fab->ID, 'fab_setting_type', true );
        $url  = apply_filters( 'fab_post_row_action_preview_url', home_url(), $type, $fab );
        $url  = add_query_arg( 'fab_preview', $fab->ID, $url );
        $url  = wp_nonce_url( $url, 'view_fab_' . $fab->ID );

        return $url;
    }

    /**
     * Get FAB clone URL
     *
     * @param int|null $id The post ID.
     * @return string|false
     */
    public function get_clone_url( $id = null ) {
        global $post;

        // Check if the post is a FAB post type.
        if ( ! $post || 'fab' !== $post->post_type ) {
            return false;
        }

        // Get FAB clone URL.
        $fab = $id ? get_post( $id ) : $post;
        $url = apply_filters( 'fab_post_row_action_clone_url', admin_url( 'admin.php' ), 'fab', $fab );
        $url = add_query_arg( 'action', 'fab_clone', $url );
        $url = add_query_arg( 'post', $fab->ID, $url );
        $url = wp_nonce_url( $url, 'clone_fab_' . $fab->ID );

        return $url;
    }

    /**
     * Check if the current page is a preview page
     *
     * @return bool
     */
    public function is_preview_page() {
        if ( isset( $_GET['fab_preview'] ) &&
            isset( $_GET['_wpnonce'] ) &&
            wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'view_fab_' . sanitize_text_field( wp_unslash( $_GET['fab_preview'] ) ) )
        ) {
            return true;
        }
        return false;
    }
}
