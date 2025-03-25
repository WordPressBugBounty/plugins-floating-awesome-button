<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Utils extends Base implements Model_Interface {

    /**
     * Add clone action to FAB post type row actions
     *
     * @param array   $actions Array of row action links
     * @param WP_Post $post   The post object
     * @return array
     */
    public function add_fab_clone_action($actions, $post) {
        if ($post->post_type === 'fab') {
            $actions['clone'] = sprintf(
                '<a href="%s">%s</a>',
                wp_nonce_url(
                    admin_url('admin.php?action=clone_fab&post=' . $post->ID),
                    'clone_fab_' . $post->ID
                ),
                __('Clone', 'floating-awesome-button')
            );
        }
        return $actions;
    }

    /**
     * Handle the cloning of FAB post
     *
     * @return void
     */
    public function clone_fab_post() {
        // Check if post ID is provided
        $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;

        // Verify nonce
        if (!wp_verify_nonce($_GET['_wpnonce'], 'clone_fab_' . $post_id)) {
            wp_die(__('Security check failed', 'floating-awesome-button'));
        }

        // Get the post to clone
        $post = get_post($post_id);

        // Verify post exists and is FAB type
        if (null === $post || 'fab' !== $post->post_type) {
            wp_die(__('FAB post not found', 'floating-awesome-button'));
        }

        // Create new post data array
        $new_post_args = array(
            'post_type'    => $post->post_type,
            'post_title'   => $post->post_title . ' ' . __('(Clone)', 'floating-awesome-button'),
            'post_content' => $post->post_content,
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id()
        );

        // Insert the new post
        $new_post_id = wp_insert_post($new_post_args);

        // Copy post meta
        $post_meta = get_post_meta($post_id);
        if ($post_meta) {
            foreach ($post_meta as $meta_key => $meta_values) {
                foreach ($meta_values as $meta_value) {
                    add_post_meta($new_post_id, $meta_key, maybe_unserialize($meta_value));
                }
            }
        }

        // Redirect to the edit screen of the new post
        wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
        exit;
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
        add_filter('post_row_actions', array($this, 'add_fab_clone_action'), 10, 2);
        add_action('admin_action_clone_fab', array($this, 'clone_fab_post'));
    }
}
