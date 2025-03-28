<?php

namespace Fab\Module;

! defined( 'WPINC ' ) || die;

/**
 * FAB Module Anchor Link.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class FABModuleAnchorLink extends FABModule {

    /**
     * Type.
     *
     * @var string
     */
    public static $type = 'anchor_link';

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->key         = 'module_linkanchor';
        $this->name        = 'Anchor Link';
        $this->description = 'Anchor Link Module Configuration';

        /** Initialize Options */
        $this->options = array(
            'duration' => array(
                'text'  => 'Duration',
                'type'  => 'number',
                'value' => 400,
                'info'  => 'Window scroll duration in miliseconds',
            ),
        );
        $options       = $this->WP->get_option( sprintf( 'fab_%s', $this->key ) );
        $this->options = ( is_array( $options ) ) ? $this->Helper->ArrayMergeRecursive( $this->options, $options ) : $this->options;
    }
}
