<?php
/**
 * WP-CLI commands for Easy IP Blocker.
 *
 * @package Easy_IP_Blocker/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage the Easy IP Blocker blocklist.
 */
class Easy_IP_Blocker_CLI {

	/**
	 * Validate that an entry is a valid IP, CIDR, wildcard, or comment.
	 *
	 * @param string $entry Entry to validate.
	 * @return bool True if valid.
	 */
	private function is_valid_entry( string $entry ): bool {
		if ( '#' === $entry[0] ) {
			return true;
		}

		if ( filter_var( $entry, FILTER_VALIDATE_IP ) ) {
			return true;
		}

		if ( preg_match( '/^\d{1,3}(\.\d{1,3}){3}\/\d{1,2}$/', $entry ) ) {
			return true;
		}

		if ( preg_match( '/^[\d.*]{3,15}$/', $entry ) && false !== strpos( $entry, '*' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add one or more entries to the IP blocklist.
	 *
	 * Accepts exact IPs, CIDR ranges, wildcards, or comments.
	 *
	 * ## OPTIONS
	 *
	 * <entry>...
	 * : One or more IPs, CIDR ranges (192.168.1.0/24), or wildcard patterns (10.0.0.*) to block.
	 *
	 * ## EXAMPLES
	 *
	 *     wp eib add 192.168.1.1
	 *     wp eib add 10.0.0.0/24 172.16.0.*
	 *     wp eib add "# Spammer network" 203.0.113.0/24
	 *
	 * @param array $args Positional arguments.
	 * @return void
	 */
	public function add( array $args ): void {
		$current = get_option( 'eib_blocked_ips', '' );
		$entries = array_map( 'trim', explode( PHP_EOL, $current ) );
		$added   = array();

		foreach ( $args as $entry ) {
			$entry = trim( $entry );
			if ( '' === $entry ) {
				continue;
			}

			if ( ! $this->is_valid_entry( $entry ) ) {
				WP_CLI::warning( sprintf( 'Invalid entry skipped: %s', $entry ) );
				continue;
			}

			if ( in_array( $entry, $entries, true ) ) {
				WP_CLI::warning( sprintf( 'Already in blocklist: %s', $entry ) );
				continue;
			}

			$entries[] = $entry;
			$added[]   = $entry;
		}

		if ( empty( $added ) ) {
			WP_CLI::success( 'No new entries to add.' );
			return;
		}

		$updated = implode( PHP_EOL, array_filter( $entries, 'strlen' ) );
		update_option( 'eib_blocked_ips', $updated );

		foreach ( $added as $entry ) {
			WP_CLI::log( sprintf( 'Added: %s', $entry ) );
		}

		WP_CLI::success( sprintf( '%d entry(s) added to blocklist.', count( $added ) ) );
	}

	/**
	 * Remove one or more entries from the IP blocklist.
	 *
	 * ## OPTIONS
	 *
	 * <entry>...
	 * : One or more entries to remove (must match exactly).
	 *
	 * ## EXAMPLES
	 *
	 *     wp eib remove 192.168.1.1
	 *     wp eib remove 10.0.0.0/24
	 *
	 * @param array $args Positional arguments.
	 * @return void
	 */
	public function remove( array $args ): void {
		$current = get_option( 'eib_blocked_ips', '' );
		$entries = array_map( 'trim', explode( PHP_EOL, $current ) );
		$removed = array();

		foreach ( $args as $entry ) {
			$entry = trim( $entry );
			$key   = array_search( $entry, $entries, true );

			if ( false === $key ) {
				WP_CLI::warning( sprintf( 'Not found in blocklist: %s', $entry ) );
				continue;
			}

			unset( $entries[ $key ] );
			$removed[] = $entry;
		}

		if ( empty( $removed ) ) {
			WP_CLI::success( 'No entries removed.' );
			return;
		}

		$updated = implode( PHP_EOL, array_filter( $entries, 'strlen' ) );
		update_option( 'eib_blocked_ips', $updated );

		foreach ( $removed as $entry ) {
			WP_CLI::log( sprintf( 'Removed: %s', $entry ) );
		}

		WP_CLI::success( sprintf( '%d entry(s) removed from blocklist.', count( $removed ) ) );
	}

	/**
	 * Delete one or more entries from the IP blocklist (alias for remove).
	 *
	 * ## OPTIONS
	 *
	 * <entry>...
	 * : One or more entries to delete (must match exactly).
	 *
	 * ## EXAMPLES
	 *
	 *     wp eib delete 192.168.1.1
	 *     wp eib delete 10.0.0.0/24 172.16.0.*
	 *
	 * @param array $args Positional arguments.
	 * @return void
	 */
	public function delete( array $args ): void {
		$this->remove( $args );
	}

	/**
	 * List all entries in the IP blocklist.
	 *
	 * @subcommand list
	 *
	 * ## OPTIONS
	 *
	 * [--format=<format>]
	 * : Output format.
	 * ---
	 * default: list
	 * options:
	 *   - list
	 *   - csv
	 *   - count
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp eib list
	 *     wp eib list --format=count
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 */
	public function list_( array $args, array $assoc_args ): void {
		$current = get_option( 'eib_blocked_ips', '' );

		if ( empty( trim( $current ) ) ) {
			WP_CLI::log( 'Blocklist is empty.' );
			return;
		}

		$entries = array_filter( array_map( 'trim', explode( PHP_EOL, $current ) ), 'strlen' );
		$format  = WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'list' );

		switch ( $format ) {
			case 'count':
				WP_CLI::log( (string) count( $entries ) );
				break;
			case 'csv':
				WP_CLI::log( implode( ',', $entries ) );
				break;
			default:
				foreach ( $entries as $entry ) {
					WP_CLI::log( $entry );
				}
				break;
		}
	}

	/**
	 * Clear all entries from the IP blocklist.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Skip confirmation prompt.
	 *
	 * ## EXAMPLES
	 *
	 *     wp eib clear --yes
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 */
	public function clear( array $args, array $assoc_args ): void {
		WP_CLI::confirm( 'Are you sure you want to clear the entire blocklist?', $assoc_args );
		update_option( 'eib_blocked_ips', '' );
		WP_CLI::success( 'Blocklist cleared.' );
	}
}
