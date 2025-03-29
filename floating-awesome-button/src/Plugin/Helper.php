<?php

namespace Fab\Plugin;

! defined( 'WPINC ' ) || die;

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
    use Helper\Page;
    use Helper\Plan;
    use Helper\Singleton;
    use Helper\Template;
    use Helper\Text;

    /**
     * Convert html relative path into absolute path
     *
     * @param     string $path   WordPress base path.
     * @param     string $html   Html string.
     * @return  string
     */
    public function convertImagesRelativetoAbsolutePath( $path, $html ) {
        $pattern = '/<img([^>]*) ' .
            'src="([^http|ftp|https][^"]*)"/';
        $replace = '<img${1} src="' . $path . '${2}"';
        return preg_replace( $pattern, $replace, $html );
    }

    /**
     * Extract templates from config files
     *
     * @param     array $config Lists of config templates.
     * @param     array $templates Lists of templates, to return.
     * @return  array
     */
    public function getTemplatesFromConfig( $config, $templates = array() ) {
        foreach ( $config as $template ) {
            foreach ( $template->children as $children ) {
                $templates[ $children->id ] = $children;
            }
        }
        return $templates;
    }
}
