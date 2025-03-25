<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;
use Fab\View;
use Fab\Helper\FAB_Template;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Templates extends Base implements Model_Interface {

    /**
     * Register submenu page
     *
     * @return void
     */
    public function register_submenu_page() {
        add_submenu_page(
            'edit.php?post_type=fab',
            __( 'Add New FAB', 'floating-awesome-button' ),
            __( 'Add New FAB', 'floating-awesome-button' ),
            'manage_options',
            sprintf( '%s-templates', \Fab\Plugin::getInstance()->getSlug() ),
            array( $this, 'page_template' ),
            2
        );
    }

    /**
     * Remove add new fab submenu
     *
     * @return void
     */
    function remove_add_new_fab_submenu() {
        global $submenu;

        // Check if the 'fab' post type submenu exists
        if (isset($submenu['edit.php?post_type=fab'])) {
            // Loop through the submenu items to find the "Add New" link
            foreach ($submenu['edit.php?post_type=fab'] as $key => $item) {
                // Check if the submenu item is the "Add New" link
                if ($item[2] === 'post-new.php?post_type=fab') {
                    // Remove the "Add New" submenu item
                    unset($submenu['edit.php?post_type=fab'][$key]);
                    break; // Exit the loop once the item is found
                }
            }
        }
    }

    /**
     * Page Template
     *
     * @return void
     */
    public function page_template() {
        // Get all json files in templates folder
        $templates = glob( FAB_PLUGIN_PATH . 'templates/*.json' );
        $templates = array_reduce(array_map(function($template) {
            $data = json_decode(file_get_contents($template));

            // Check if all required plugins are active
            if( isset($data->requires) && !\Fab\Plugin\Helper::getInstance()->isArrayInArray($data->requires, get_option('active_plugins')) ){
                return [];
            }

            // Only return required data
            return array($data->id => array(
                'id' => $data->id,
                'name' => $data->name,
                'description' => $data->description,
                'license' => $data->license,
                'requires' => $data->requires ?? [],
                'design' => array(
                    'color' => $data->design->color,
                    'icon' => array(
                        'color' => $data->design->icon->color,
                        'class' => $data->design->icon->class,
                    ),

                )
            ));
        }, $templates), 'array_merge', []);

        // Localize templates
        $this->WP->wp_enqueue_script_component('fab-templates-component', 'assets/components/templates/main.js', array(), FAB_VERSION, false);
        $this->WP->wp_localize_script('fab-templates-component', 'FAB_TEMPLATES', array(
            'templates' => apply_filters('fab_templates', $templates),
            'labels' => array(
                'add_new' => __('Add New', 'floating-awesome-button'),
                'add_integration' => __('Add Integration', 'floating-awesome-button'),
                'next' => __('Next', 'floating-awesome-button'),
                'previous' => __('Previous', 'floating-awesome-button'),
                'no_results' => __('No templates found matching your search.', 'floating-awesome-button'),
                'upgrade' => __('Required Upgrade', 'floating-awesome-button'),
            )
        ));

        // Render template
        View::RenderStatic( 'Backend.templates' );
    }

    /**
     * Register templates endpoint
     *
     * @return void
     */
    public function register_templates_endpoint() {
        register_rest_route( 'fab/v1', '/add_new_fab', array(
            'methods' => 'POST',
            'permission_callback' => function() {
                return defined('FAB_REST_API_USER_IS_ADMIN') && FAB_REST_API_USER_IS_ADMIN;
            },
            'callback' => array( $this, 'add_new_fab' ),
        ) );
    }

    /**
     * Add new fab
     *
     * @return void
     */
    public function add_new_fab() {
        $data = json_decode(file_get_contents('php://input'));

        // Read template file
        $template = file_get_contents(FAB_PLUGIN_PATH . 'templates/' . $data->id . '.json');
        $data = $this->Helper->ArrayMergeRecursive( json_decode($template), $data );

        // Transform template data
        try {
            // Insert post
            $post_id = wp_insert_post(array(
                'post_type' => 'fab',
                'post_title' => $data->name,
                'post_content' => FAB_Template::getInstance()->get_content($data),
                'post_status' => 'publish',
            ));

            // Add postmeta
            $postmeta = FAB_Template::getInstance()->transform_template_to_postmeta($data);
            foreach ($postmeta as $key => $value) {
                update_post_meta($post_id, $key, $value);
            }

            return rest_ensure_response( array( 'message' => 'Success', 'post_id' => $post_id ) );
        } catch (Exception $e) {
            return rest_ensure_response( array( 'message' => 'Error: ' . $e->getMessage() ) );
        }
    }

    /**
     * Custom fab page title action url
     *
     * @return void
     */
    public function custom_fab_page_title_action_url($url, $path, $blog_id) {
        // Check if we're on the edit.php page for the 'fab' post type
        if ($path === 'post-new.php?post_type=fab') {
            $url = admin_url('edit.php?post_type=fab&page='. \Fab\Plugin::getInstance()->getSlug() . '-templates');
        }
        return $url;
    }

    /**
     * Redirect fab post new to template page
     *
     * @return void
     */
    public function redirect_fab_post_new_to_template_page() {
        // Check if the current page is 'post-new.php' and the post type is 'fab'
        if (
            isset($_GET['post_type'], $_SERVER['SCRIPT_NAME']) &&
            $_GET['post_type'] === 'fab' &&
            basename(sanitize_text_field(wp_unslash($_SERVER['SCRIPT_NAME']))) === 'post-new.php'
        ) {
            // Redirect to the templates page
            wp_redirect(admin_url('edit.php?post_type=fab&page=' . \Fab\Plugin::getInstance()->getSlug() . '-templates'));
            exit; // Always call exit after wp_redirect
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
        // Register new submenu page
        add_action('admin_menu', array( $this, 'register_submenu_page' ) );

        // Change submenu add new fab page title action url
        add_action('admin_menu', array( $this, 'remove_add_new_fab_submenu' ) );

        // Templates wp json api endpoint
        add_action( 'rest_api_init', array( $this, 'register_templates_endpoint' ) );

        // Custom add new fab page title action url
        add_filter('admin_url', array( $this, 'custom_fab_page_title_action_url' ), 10, 3);

        // Redirect post-new.php?post_type=fab to templates page
        add_action('admin_init', array( $this, 'redirect_fab_post_new_to_template_page' ) );
    }
}
