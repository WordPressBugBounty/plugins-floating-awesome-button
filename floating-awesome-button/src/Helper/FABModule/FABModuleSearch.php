<?php

namespace Fab\Module;

! defined( 'WPINC ' ) || die;

use FAB\Plugin;
use Fab\View;

/**
 * FAB Module Search.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class FABModuleSearch extends FABModule {

    /**
     * Type.
     *
     * @var string
     */
    public static $type = 'search';

    /**
     * Module construect
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->key         = 'module_search';
        $this->name        = 'Search';
        $this->description = 'Modal Search Configuration';

        /** Initialize Options */
        $this->options = array(
            'label'      => array(
                'text'  => 'Search Label',
                'type'  => 'text',
                'value' => 'Search...',
            ),
            'pagination' => array(
                'text'     => 'Pagination',
                'children' => array(
                    'enable'   => array(
                        'text'  => 'Enable Pagination',
                        'label' => array( 'text' => 'Enable/Disable' ),
                        'type'  => 'switch',
                        'value' => 1,
                    ),
                    'per_page' => array(
                        'text'  => 'Per Page',
                        'type'  => 'text',
                        'value' => '10',
                        'info'  => 'Maximum number of items to be returned in result set.',
                    ),
                ),
            ),
        );
        $options       = $this->WP->get_option( sprintf( 'fab_%s', $this->key ) );
        $this->options = ( is_array( $options ) ) ? $this->Helper->ArrayMergeRecursive( $this->options, $options ) : $this->options;
    }

    /**
     * Render Module.
     *
     * @return void
     */
    public function render() {
        View::RenderStatic( 'Frontend.Module.search' );
    }
}
