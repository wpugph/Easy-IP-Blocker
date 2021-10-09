<?php
/**
 * Plugin Name: Easy IP Blocker
 * Version: 1.0.3
 * Plugin URI: https://wordpress.org/plugins/easy-ip-blocker/
 * Description: Easily blocks IPs
 * Author: Carl Alberto
 * Author URI: https://carlalberto.code.blog/
 * Requires at least: 5.0
 * Tested up to: 5.8.1
 *
 * Text Domain: easy-ip-blocker
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Carl Alberto
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-easy-ip-blocker.php';
require_once 'includes/class-easy-ip-blocker-settings.php';

// Load plugin libraries.
require_once 'includes/lib/class-easy-ip-blocker-admin-api.php';

/**
 * Returns the main instance of Easy_IP_Blocker to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Easy_IP_Blocker
 */
function easy_ip_blocker() {
	$instance = Easy_IP_Blocker::instance( __FILE__, '1.0.3' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Easy_IP_Blocker_Settings::instance( $instance );
	}

	return $instance;
}

easy_ip_blocker();
