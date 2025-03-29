<?php

namespace Fab\Plugin\Helper;

!defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

trait Template {

    /**
     * Get template from /templates/$name.json
     *
     * @param string $name
     * @return object
     */
    public function get_template($name) {
        $template = file_get_contents(FAB_PLUGIN_PATH . 'templates/' . $name . '.json');
        return json_decode($template);
    }

}
