<?php

namespace Fab\Controller;

use Fab\Interfaces\Model_Interface;

! defined( 'WPINC ' ) or die;

/**
 * Plugin hooks in a backend
 * setComponent
 *
 * @package    Fab
 * @subpackage Fab/Controller
 */
class Contact_Form_7 extends Integration implements Model_Interface {

    /**
     * Plugin file
     *
     * @var string
     */
    protected $plugin_file = 'contact-form-7/wp-contact-form-7.php';

    /**
     * Add integration to FAB
     *
     * @param array $integrations The integrations.
     * @return array The integrations.
     */
    public function add_integration($integrations){
        $integrations['contact-form-7'] = array(
            'name' => __('Contact Form 7', 'floating-awesome-button'),
            'description' => __('Contact Form 7 is a powerful and flexible contact form plugin for WordPress.', 'floating-awesome-button'),
            'url' => 'https://wordpress.org/plugins/contact-form-7/',
            'icon_url' => 'https://ps.w.org/contact-form-7/assets/icon.svg',
            'banner_url' => 'https://ps.w.org/contact-form-7/assets/banner-1544x500.png',
            'plugin_file' => $this->plugin_file,
            'status' => \Fab\Plugin\Helper::getInstance()->check_plugin_integration($this->plugin_file),
        );
        return $integrations;
    }

    /**
     * Filter the FAB template content.
     *
     * @param string $content The content.
     * @param string $type The type of FAB.
     * @param object $data The data.
     */
    public function filter_template_content($content, $type, $data){
        // Prevent error if contact form 7 plugin is not active.
        if( !is_plugin_active($this->plugin_file) || !isset($data->requires) || !in_array($this->plugin_file, $data->requires) ){
            return $content;
        }

        // Check if form id is set
        if( isset($data->cf7id) ){
            $form = get_post( $data->cf7id );
        } else {
            // Get latest contact form 7 form
            $forms = get_posts(array(
                'post_type' => 'wpcf7_contact_form',
                'numberposts' => 1,
            ));

            // Check if any forms exist
            if (!empty($forms)) {
                // Get form
                $form = $forms[0];
            }
        }

        // Check if form is valid
        if( $form && $form->post_type === 'wpcf7_contact_form' ){
            // Change content to contact from 7 shortcode
            $content = sprintf('[contact-form-7 id="%d" title="%s"]', $form->ID, $form->post_title);
        }

        return $content;
    }

    /**
     * Enqueue scripts
     *
     * @return void
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();

        // Check if screen is CF7 edit page and integration is enabled
        if ( $screen->id === 'toplevel_page_wpcf7' && \Fab\Plugin\Helper::getInstance()->check_plugin_integration($this->plugin_file) === 'enabled' ) {
            $this->WP->wp_enqueue_script_typescript( 'fab-integration-contact-form-7', 'assets/ts/integration/contact-form-7.ts', array(), FAB_VERSION, true );
            $this->WP->wp_localize_script( 'fab-integration-contact-form-7', 'FAB_TEMPLATE', array(
                'id' => 'contact-form-7-popup',
                'label' => array(
                    'button' => __( 'Turn into Popup', 'floating-awesome-button' ),
                )
            ) );
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
        // Add integration to FAB// Add integration to FAB
        add_filter( 'fab_plugin_integrations', array( $this, 'add_integration' ) );

        // Prevent error if plugin is not active.
        if( !is_plugin_active($this->plugin_file) ){
            return;
        }

        // Autoload integration
        add_action( 'admin_init', array( $this, 'autoload_integration' ) );

        // Filter template content
        add_filter( 'fab_template_content', array( $this, 'filter_template_content' ), 10, 3 );

        // Add custom script to CF7 edit page
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }
}
