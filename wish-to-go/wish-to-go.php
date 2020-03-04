<?php

/**
 * @package           WishToGo
 *
 * Plugin Name:       Wish To Go
 * Plugin URI:        https://wish-to-go.com
 * Description:       The Travel Wish List
 * Version:           0.1.0
 * Author:            Josep Seto
 * Author URI:        http://github.com/jseto
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wish-to-go
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {                                                           // cSpell: disable-line
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WISH_TO_GO_VERSION', '0.1.0' );

class WishToGo {
  function __construct() {
    // $endPoints = new EndPoints();
    // add_action( 'rest_api_init', array( $endPoints, 'createEndpoints' ) );
  }

  function enqueueAdminScripts() {
    //  wp_enqueue_style( 'a-style', plugins_url( '/backend/css/a.css', __FILE__) );
    //  wp_enqueue_script( 'a-js', plugins_url( '/backend/js/a.js', __FILE__) );
  }

	function enqueueFrontEndScripts() {
		wp_register_script( 'wish-to-go-js', 'https://wish-to-go.com/wish-to-go.main.js', null, null, true );
		wp_enqueue_script( 'wish-to-go-js' );
  }

  function register() {
    add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'enqueueFrontEndScripts' ) );
  }

  function activate(){
    // Database::createDB();
    flush_rewrite_rules();
  }

  function deactivate(){
    flush_rewrite_rules();
  }
}

$wishToGo = new WishToGo();
$wishToGo->register();
register_activation_hook( __FILE__, array( $wishToGo, 'activate' ) );
register_deactivation_hook( __FILE__, array( $wishToGo, 'deactivate' ) );
