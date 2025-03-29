<?php

namespace Fab\Module;

! defined( 'WPINC ' ) || die;

use FAB\Plugin;
use Fab\View;

/**
 * FAB Module Auth Logout.
 *
 * @package    Fab
 * @subpackage Fab/Module
 */
class FABModuleAuthLogout extends FABModule {

    /**
     * Type.
     *
     * @var string
     */
    public static $type = 'auth_logout';

    /**
     * Module construect
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->key         = 'module_auth_logout';
        $this->name        = 'Auth Logout';
        $this->description = 'Popup Auth Logout';
    }

    /**
     * Render Module.
     *
     * @return void
     */
    public function render() {
        View::RenderStatic( 'Frontend.Module.logout' );
    }
}
