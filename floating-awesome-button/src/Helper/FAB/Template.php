<?php

namespace Fab\Helper;

! defined( 'WPINC ' ) or die;

/**
 * FAB Helper Template
 * - This class is used to transform the template data into FAB post type
 *
 * @package    Fab
 * @subpackage Fab/Helper
 */
class Template {

    // Load Traits
    use \Fab\Plugin\Helper\Singleton;

    /***
     * Get Content
     * - This function is used to get the content of the template based on the type
     * @param object $data
     * @return string
     */
    public function get_content($data){
        switch($data->settings->type) {
            case 'toast' && isset($data->settings->message):
                return $data->settings->message;
            case 'modal' && isset($data->settings->content):
                return $data->settings->content;
            default:
                return '';
        }
    }

    /**
     * FAB setting type
     * - This could be 'button', 'link', 'modal', 'toast', 'popup', etc
     * @var string
     */
    private $type;

    /**
     * Get FAB setting
     *
     * @param object $data
     * @param array $postmeta
     * @return array
     */
    private function get_fab_setting($data, $postmeta = array()){
        $postmeta['fab_setting_type'] = $this->type;

        // Get setting type and link
        if($this->type === 'link'){
            $postmeta['fab_setting_link'] = isset($data->settings->link) ? $data->settings->link : '';
            $postmeta['fab_setting_link_behavior'] = isset($data->settings->open_in_new_tab) ? 1 : 0;
        }

        // Get setting print type
        if($this->type === 'print'){
            $postmeta['fab_setting_print_target'] = isset($data->settings->target) ? $data->settings->target : 'body';
        }

        // Get setting toast
        if($this->type === 'toast'){
            $postmeta['fab_setting_toast'] = array(
                'duration' => isset($data->settings->duration) ? $data->settings->duration : '',
                'text_button' => isset($data->settings->text_button) ? $data->settings->text_button : '',
                'url_button' => isset($data->settings->url_button) ? $data->settings->url_button : '',
                'window' => isset($data->settings->open_in_new_tab) ? 1 : 0,
                'gravity' => isset($data->design->gravity) ? $data->design->gravity : '',
                'position' => isset($data->design->position) ? $data->design->position : '',
                'background' => isset($data->design->background) ? $data->design->background : '',
                'text_color' => isset($data->design->text_color) ? $data->design->text_color : '',
                'bar_color' => isset($data->design->bar_color) ? $data->design->bar_color : '',
            );
        }

        return $postmeta;
    }

    /**
     * Get FAB design
     *
     * @param object $data
     * @param array $postmeta
     * @return array
     */
    private function get_fab_design($data, $postmeta = array()){
        // Ignore icon class if type is toast
        $ignored = array('toast', 'popup');
        if(!in_array($this->type, $ignored)){
            $postmeta['fab_design_icon_class'] = isset($data->design->icon->class) ? $data->design->icon->class : '';
            $postmeta['fab_design_standalone'] = isset($data->design->standalone) ? $data->design->standalone : '';
            $postmeta['fab_design_template'] = array(
                'color' => isset($data->design->color) ? $data->design->color : '',
                'icon' => array(
                    'color' => isset($data->design->icon->color) ? $data->design->icon->color : '',
                ),
                'shape' => isset($data->design->shape) ? $data->design->shape : '',
                'grouped' => isset($data->design->grouped) ? $data->design->grouped : '',
            );
        }

        return $postmeta;
    }

    /**
     * Get FAB modal
     *
     * @param object $data
     * @param array $postmeta
     * @return array
     */
    private function get_fab_modal($data, $postmeta = array()){
        $allowed = array('modal', 'auth_login', 'auth_logout', 'search');
        if(in_array($this->type, $allowed)){
            $postmeta['fab_modal_layout'] = $data->modal->layout;
        }

        return $postmeta;
    }

    /**
     * Get FAB responsive
     *
     * @param object $data
     * @param array $postmeta
     * @return array
     */
    private function get_fab_responsive($data, $postmeta = array()){
        $postmeta['fab_responsive_template'] = array(
            'device' => array(
                'mobile' => isset($data->design->responsive->device->mobile) ? $data->design->responsive->device->mobile : '',
                'tablet' => isset($data->design->responsive->device->tablet) ? $data->design->responsive->device->tablet : '',
                'desktop' => isset($data->design->responsive->device->desktop) ? $data->design->responsive->device->desktop : '',
            ),
        );

        return $postmeta;
    }

    /**
     * Get FAB trigger & cookies
     *
     * @param object $data
     * @param array $postmeta
     * @return array
     */
    private function get_fab_trigger($data, $postmeta = array()){
        // Only for popup, toast & modal
        $allowed = array('popup', 'toast', 'modal');
        if(in_array($this->type, $allowed)){
            $postmeta['fab_trigger'] = array(
                'type' => isset($data->trigger->type) ? $data->trigger->type : '',
                'delay' => isset($data->trigger->delay) ? $data->trigger->delay : '',
                'cookies' => array(
                    'expiration' => isset($data->cookies->expiration) ? $data->cookies->expiration : '',
                )
            );
        }

        return $postmeta;
    }

    /**
     * Transform template data
     *
     * @param object $data
     * @return array
     */
    public function transform_template_to_postmeta(object $data) {
        $this->type = isset($data->settings->type) ? $data->settings->type : '';

        // Transform template data
        $postmeta = array();
        $postmeta = $this->get_fab_setting($data, $postmeta);
        $postmeta = $this->get_fab_design($data, $postmeta);
        $postmeta = $this->get_fab_modal($data, $postmeta);
        $postmeta = $this->get_fab_responsive($data, $postmeta);
        $postmeta = $this->get_fab_trigger($data, $postmeta);

        return apply_filters('fab_template_postmeta', $postmeta, $this->type, $data);
    }
}
