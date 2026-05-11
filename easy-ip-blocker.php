<?php
/**
 * Plugin Name: Easy IP Blocker
 * Version: 2.0.2
 * Plugin URI: https://wordpress.org/plugins/easy-ip-blocker/
 * Description: Quickly block unwanted IPs in your WP site
 * Author: Carl Alberto
 * Author URI: https://carlalberto.code.blog/
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 *
 * Text Domain: easy-ip-blocker
 * Domain Path: /lang/
 *
 * @package Easy_IP_Blocker
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

// Register WP-CLI commands.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once 'includes/class-easy-ip-blocker-cli.php';
	WP_CLI::add_command( 'eib', 'Easy_IP_Blocker_CLI' );
}

/**
 * Returns the main instance of Easy_IP_Blocker to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return Easy_IP_Blocker Plugin instance.
 */
function easy_ip_blocker() {
	$instance = Easy_IP_Blocker::instance( __FILE__, '2.0.2' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Easy_IP_Blocker_Settings::instance( $instance );
	}

	return $instance;
}

easy_ip_blocker();
