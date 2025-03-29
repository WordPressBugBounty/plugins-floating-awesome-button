<?php

/*
 * Plugin Name:       Floating Awesome Button
 * Plugin URI:        https://artistudio.xyz
 * Description:       Floating Awesome Button (FAB): Elevate engagement with customizable action buttons. Showcase modals,shortcodes, widgets & links effortlessly.
 * Version:           2.3.0
 * Author:            Artistudio
 * Author URI:        https://brain.artistudio.xyz/artistudio/WordPress-floating-awesome-button
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Text Domain: floating-awesome-button
 * Domain Path: /languages/
 *
  *
 * SOFTWARE LICENSE INFORMATION
 *
 * Copyright 2021 Artistudio, all rights reserved.
 *
 * For detailed information regarding to the licensing of
 * this software, please review the license.txt
*/
!defined( 'WPINC ' ) || die;
/** Load Composer Vendor */
if ( !class_exists( 'Fab\\Plugin' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}
// Load plugin files freemius, helper, and referral.
$plugin_files = array('freemius', 'helper', 'referral');
foreach ( $plugin_files as $file ) {
    if ( file_exists( plugin_dir_path( __FILE__ ) . $file . '.php' ) ) {
        require_once plugin_dir_path( __FILE__ ) . $file . '.php';
    }
}
// Load config & constants.
$objects = array(\Fab\Plugin\Config::getInstance(), \Fab\Plugin\Constants::getInstance(), \Fab\Plugin::getInstance());
foreach ( $objects as $object ) {
    // Initiate functions
    if ( method_exists( $object, 'initiate' ) ) {
        $object->initiate();
    }
    // Run functions
    if ( method_exists( $object, 'run' ) ) {
        $object->run();
    }
    // Activation hook
    if ( method_exists( $object, 'activate' ) ) {
        register_activation_hook( __FILE__, array($object, 'activate') );
    }
    // Deactivation hook
    if ( method_exists( $object, 'deactivate' ) ) {
        register_deactivation_hook( __FILE__, array($object, 'deactivate') );
    }
}
/** Uninstall Hook */
register_uninstall_hook( __FILE__, 'uninstall_fab_plugin' );
if ( !function_exists( 'uninstall_fab_plugin' ) ) {
    function uninstall_fab_plugin() {
        delete_option( 'fab_config' );
    }

}