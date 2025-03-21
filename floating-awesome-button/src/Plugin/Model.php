<?php

namespace Fab\Model;

! defined( 'WPINC ' ) or die;

/**
 * Initiate plugins
 *
 * @package    Fab
 * @subpackage Fab/Model
 */

use Fab\Wordpress\Model\Type;

class Model extends Type {

	// Load plugin traits.
	use \Fab\Plugin\Helper\Singleton;

	/**
	 * Plugin configuration object
	 *
	 * @var object
	 */
	protected $Plugin;

	/**
	 * Helper object
	 *
	 * @var object
	 */
	protected $Helper;

	/**
	 * WP object
	 *
	 * @var object
	 */
	protected $WP;

	/**
	 * Construct type
	 *
	 * @return void
	 * @var    object $plugin Plugin configuration
	 * @pattern prototype
	 */
	public function __construct() {
		$this->name           = substr( strrchr( get_called_class(), '\\' ), 1 );
		$this->name           = strtolower( $this->name );
		$this->Plugin         = \Fab\Plugin::getInstance();
		$this->Helper         = \Fab\Plugin\Helper::getInstance();
		$this->WP             = \Fab\Wordpress\Helper::getInstance();
		$this->taxonomies     = array();
		$this->hooks          = array();
		$this->metas          = array();
		$this->args           = array();
		$this->args['public'] = true;
		$this->args['labels'] = array( 'name' => ucwords( $this->name ) );
	}

	/**
	 * Overloading Method, for multiple arguments
	 *
	 * @method  loadModel           _ Load model @var string name
	 * @method  loadController      _ Load controller @var string name
	 */
	public function __call( $method, $arguments ) {
		if ( in_array( $method, array( 'loadModel', 'loadController' ) ) ) {
			$list = ( $method === 'loadModel' ) ? $this->Plugin->getModels() : array();
			$list = ( $method === 'loadController' ) ? $this->Plugin->getControllers() : $list;
			if ( count( $arguments ) === 1 ) {
				$this->{$arguments[0]} = $list[ $arguments[0] ]; }
			if ( count( $arguments ) === 2 ) {
				$this->{$arguments[0]} = $list[ $arguments[1] ]; }
		}
	}
}
