<?php

namespace Fab\Plugin;

!defined( 'WPINC ' ) or die;

/**
 * Helper library for Fab plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */

class Helper {

    /** Load Trait */
    use Helper\Directory;
    use Helper\Integration;
    use Helper\Option;
    use Helper\Operator;
    use Helper\Plan;
    use Helper\Singleton;
    use Helper\Template;
    use Helper\Text;

    /**
     * Convert html relative path into absolute path
     * @var     string  $path   Wordpress base path
     * @var     string  $html   Html string
     * @return  void
     */
    public function convertImagesRelativetoAbsolutePath($path, $html){
        $pattern = "/<img([^>]*) " .
            "src=\"([^http|ftp|https][^\"]*)\"/";
        $replace = "<img\${1} src=\"" . $path . "\${2}\"";
        return preg_replace($pattern, $replace, $html);
    }

    /**
     * Extract templates from config files
     * @var     array   $config         Lists of config templates
     * @var     array   $templates      Lists of templates, to return
     */
    public function getTemplatesFromConfig($config, $templates = []){
        foreach($config as $template){
            foreach($template->children as $children){
                $templates[$children->id] = $children;
            }
        }
        return $templates;
    }

}
