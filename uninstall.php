<?php
/**
 *
 * Uninstallation functions.
 *
 * @package Easy IP Blocker/Uninstall
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'eib_blocked_ips' );
delete_option( 'eib_ip_source' );
delete_option( 'eib_custom_header' );
delete_option( 'easy_ip_blocker_version' );
