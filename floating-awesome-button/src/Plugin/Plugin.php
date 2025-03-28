<?php

namespace Fab;

use Fab\Feature\Upsell;

! defined( 'WPINC ' ) || die;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab\Includes
 */
class Plugin {

    // Load Traits.
    use \Fab\Plugin\Helper\Singleton;

    /**
     * Plugin name
     *
     * @var     string
     */
    protected $name;

    /**
     * Plugin slug
     *
     * @var     string
     */
    protected $slug;

    /**
     * Plugin version
     *
     * @var     string
     */
    protected $version;

    /**
     * Plugin stage (true = production, false = development)
     *
     * @var     boolean
     */
    protected $production;

    /**
     * Enable/Disable plugins hook (Action, Filter, Shortcode)
     *
     * @var     array   ['action', 'filter', 'shortcode']
     */
    protected $enableHooks;

    /**
     * Plugin path
     *
     * @var     array
     */
    protected $path;

    /**
     * Lists of plugin apis
     *
     * @var     array
     */
    protected $apis;

    /**
     * Lists of plugin controllers
     *
     * @var     array
     */
    protected $controllers;

    /**
     * Lists of plugin features
     *
     * @var     array
     */
    protected $features;

    /**
     * Lists of plugin models
     *
     * @var     array
     */
    protected $models;

    /**
     * Plugin configuration
     *
     * @var     object
     */
    protected $config;

    /**
     * @access   protected
     * @var      object    $helper  Helper object for controller
     */
    protected $Helper;

    /**
     * @access   protected
     * @var      object    $form  Form object for controller
     */
    protected $Form;

    /**
     * @access   protected
     * @var      object    $helper  Helper object for controller
     */
    protected $WP;

    /**
     * Define the core functionality of the plugin.
     *
     * @param   array $path     WordPress plugin path
     * @return void
     */
    public function __construct() {
        /** Initiate Plugin */
        $this->config      = \Fab\Plugin\Config::getInstance()->getConfig();
        $this->name        = $this->config->name;
        $this->slug        = strtolower( $this->config->name );
        $this->slug        = str_replace( ' ', '-', $this->slug );
        $this->version     = $this->config->version;
        $this->production  = $this->config->production;
        $this->enableHooks = $this->config->enableHooks;
        $this->controllers = array();
        $this->features    = array();
        $this->models      = array();
        $this->Helper      = \Fab\Plugin\Helper::getInstance();
        $this->Form        = new Form();
        $this->WP          = \Fab\Wordpress\Helper::getInstance();
        /** Init Config */
        $this->path = $this->WP->getPath( $this->config->path );
    }

    /**
     * Run the plugin
     * - Load plugin model
     * - Load plugin API
     * - Load plugin controller
     *
     * @return  void
     */
    public function run() {
        $this->loadModels();
        $this->loadFeatures();
        $this->loadHooks( 'Controller' );
        $this->loadHooks( 'Api' );
        $this->load_modules_hooks();
    }

    /**
     * Lifecycle Activate the plugin
     *
     * @return  void
     */
    public function activate() {
        $activatables = array_merge( $this->controllers, $this->apis );
        foreach ( $activatables as $activatable ) {
            if ( method_exists( $activatable, 'activate' ) ) {
                $activatable->activate();
            }
        }
    }

    /**
     * Lifecycle Deactivate the plugin
     *
     * @return  void
     */
    public function deactivate() {
        $deactivatables = array_merge( $this->controllers, $this->apis );
        foreach ( $deactivatables as $deactivatable ) {
            if ( method_exists( $deactivatable, 'deactivate' ) ) {
                $deactivatable->deactivate();
            }
        }
    }

    /**
     * Load registered models
     *
     * @return  void
     */
    public function loadModels() {
        $models = $this->Helper->getDirFiles( $this->path['plugin_path'] . 'src/Model' );
        $allow  = array( '.', '..', '.DS_Store', 'index.php' );
        foreach ( $models as $model ) {
            if ( in_array( basename( $model ), $allow ) ) {
                continue;
            }
            $name  = basename( $model, '.php' );
            $model = '\\Fab\\Model\\' . $name;
            $model = new $model( $this );

            // Build model.
            $args          = $model->getArgs();
            $args['build'] = ( isset( $args['build'] ) ) ? $args['build'] : true;
            if ( $args['build'] ) {
                $model->build();
            }

            // Run model run method.
            if ( method_exists( $model, 'run' ) ) {
                $model->run();
            }
        }
    }

    /**
     * Load registered hooks in a controller
     *
     * @return  void
     * @pattern bridge
     */
    private function loadFeatures() {
        $features = $this->Helper->getDirFiles( $this->path['plugin_path'] . 'src/Feature' );
        $allow    = array( '.', '..', '.DS_Store', 'index.php' );
        foreach ( $features as $feature ) {
            if ( in_array( basename( $feature ), $allow ) ) {
                continue;
            }
            $name                                 = basename( $feature, '.php' );
            $feature                              = '\\Fab\\Feature\\' . $name;
            $feature                              = new $feature();
            $this->features[ $feature->getKey() ] = $feature;
            if ( method_exists( $feature, 'run' ) ) {
                $feature->run();
            }
        }
        ksort( $this->features );
    }

    /**
     * Load Modules Hook
     *
     * @return void
     */
    public function load_modules_hooks() {
        $modules = array();
        $allow   = array( '.', '..', '.DS_Store', 'index.php' );
        foreach ( $this->Helper->getDirFiles( $this->path['plugin_path'] . 'src/Helper/FABModule' ) as $module ) {
            if ( in_array( basename( $module ), $allow ) ) {
                continue;
            }
            $name   = basename( $module, '.php' );
            $module = '\\Fab\\Module\\' . $name;
            $module = new $module();

            if ( method_exists( $module, 'run' ) ) {
                $module->run();
            }
        }
    }

    /**
     * Get FAB Modules
     */
    public function getModules() {
        $modules = array();
        $allow   = array( '.', '..', '.DS_Store', 'index.php' );
        foreach ( $this->Helper->getDirFiles( $this->path['plugin_path'] . 'src/Helper/FABModule' ) as $module ) {
            if ( in_array( basename( $module ), $allow ) ) {
                continue;
            }
            $name             = basename( $module, '.php' );
            $class            = '\\Fab\\Module\\' . $name;
            $modules[ $name ] = new $class();
        }
        return $modules;
    }

    /**
     * Load registered hooks in a controller
     *
     * @return  void
     * @var     string  $dir   plugin hooks directory (API, Controller)
     * @pattern bridge
     */
    private function loadHooks( $dir ) {
        $controllers = $this->Helper->getDirFiles( $this->path['plugin_path'] . 'src/' . $dir );
        $allow       = array( '.', '..', '.DS_Store', 'index.php' );
        foreach ( $controllers as $controller ) {
            if ( in_array( basename( $controller ), $allow ) ) {
                continue;
            }
            $name       = basename( $controller, '.php' );
            $controller = '\\Fab\\' . ucwords( $dir ) . '\\' . $name;
            $controller = new $controller();
            if ( $dir === 'Controller' ) {
                $this->controllers[ $name ] = $controller;
            }
            if ( $dir === 'Api' ) {
                $this->apis[ $name ] = $controller;
            }

            if ( method_exists( $controller, 'initialize' ) ) {
                $controller->initialize();
            }

            if ( method_exists( $controller, 'run' ) ) {
                $controller->run();
            }
        }
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName( string $name ): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug( $slug ): void {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getVersion(): string {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion( string $version ): void {
        $this->version = $version;
    }

    /**
     * @return bool
     */
    public function isProduction(): bool {
        return $this->production;
    }

    /**
     * @param bool $production
     */
    public function setProduction( bool $production ): void {
        $this->production = $production;
    }

    /**
     * @return array
     */
    public function getEnableHooks(): array {
        return $this->enableHooks;
    }

    /**
     * @param array $enableHooks
     */
    public function setEnableHooks( array $enableHooks ): void {
        $this->enableHooks = $enableHooks;
    }

    /**
     * @return array
     */
    public function getPath(): array {
        return $this->path;
    }

    /**
     * @param array $path
     */
    public function setPath( array $path ): void {
        $this->path = $path;
    }

    /**
     * @return array
     */
    public function getApis() {
        return $this->apis;
    }

    /**
     * @param array $apis
     */
    public function setApis( $apis ) {
        $this->apis = $apis;
    }

    /**
     * @return array
     */
    public function getControllers(): array {
        return $this->controllers;
    }

    /**
     * @param array $controllers
     */
    public function setControllers( array $controllers ): void {
        $this->controllers = $controllers;
    }

    /**
     * @return array
     */
    public function getFeatures() {
        return $this->features;
    }

    /**
     * @param array $features
     */
    public function setFeatures( $features ) {
        $this->features = $features;
    }

    /**
     * @return array
     */
    public function getModels(): array {
        return $this->models;
    }

    /**
     * @param array $models
     */
    public function setModels( array $models ): void {
        $this->models = $models;
    }

    /**
     * @return object
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @param object $config
     */
    public function setConfig( $config ): void {
        $this->config = $config;
    }

    /**
     * @return object
     */
    public function getHelper() {
        return $this->Helper;
    }

    /**
     * @param object $Helper
     */
    public function setHelper( $Helper ) {
        $this->Helper = $Helper;
    }

    /**
     * @return object
     */
    public function getForm(): Form {
        return $this->Form;
    }

    /**
     * @param object $Form
     */
    public function setForm( Form $Form ): void {
        $this->Form = $Form;
    }

    /**
     * @return object
     */
    public function getWP(): Wordpress\Helper {
        return $this->WP;
    }

    /**
     * @param object $WP
     */
    public function setWP( Wordpress\Helper $WP ): void {
        $this->WP = $WP;
    }
}
