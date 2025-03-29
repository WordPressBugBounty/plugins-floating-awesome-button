<?php

namespace Fab\Module;

! defined( 'WPINC ' ) || die;

use FAB\Plugin;
use Fab\View;

/**
 * FAB Module Auth Login.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class FABModuleAuthLogin extends FABModule {
    /**
     * Type.
     *
     * @var string
     */
    public static $type = 'auth_login';

    /**
     * Module construect
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->key         = 'module_auth_login';
        $this->name        = 'Auth Login';
        $this->description = 'Popup Auth Login';
    }

    /**
     * Render Module.
     *
     * @return void
     */
    public function render() {
        View::RenderStatic( 'Frontend.Module.login' );
    }
}
