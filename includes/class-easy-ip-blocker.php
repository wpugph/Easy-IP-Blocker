<?php
/**
 * Main plugin class file.
 *
 * @package Easy_IP_Blocker/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Easy_IP_Blocker {

	/**
	 * The single instance of Easy_IP_Blocker.
	 *
	 * @var Easy_IP_Blocker|null
	 */
	private static $instance = null;

	/**
	 * Local instance of Easy_IP_Blocker_Admin_API.
	 *
	 * @var Easy_IP_Blocker_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object.
	 *
	 * @var Easy_IP_Blocker_Settings|null
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var string
	 */
	public $version;

	/**
	 * The token.
	 *
	 * @var string
	 */
	public $token;

	/**
	 * The main plugin file.
	 *
	 * @var string
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var string
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var string
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var string
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var string
	 */
	public $script_suffix;

	/**
	 * Main Easy_IP_Blocker Instance.
	 *
	 * Ensures only one instance of Easy_IP_Blocker is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file    File instance.
	 * @param string $version Version parameter.
	 * @return Easy_IP_Blocker Plugin instance.
	 */
	public static function instance( string $file = '', string $version = '2.0.0' ): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $file, $version );
		}

		return self::$instance;
	}

	/**
	 * Constructor function.
	 *
	 * @param string $file    File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( string $file = '', string $version = '2.0.0' ) {
		$this->version = $version;
		$this->token   = 'easy_ip_blocker';

		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		if ( is_admin() ) {
			$this->admin = new Easy_IP_Blocker_Admin_API();
		}

		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		add_action( 'init', array( $this, 'eib_blocklist' ), 0 );
	}

	/**
	 * Check the blocklist and deny access to blocked IPs.
	 *
	 * @return bool True if IP is not blocked.
	 */
	public function eib_blocklist(): bool {

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return true;
		}

		$blocked_ips = get_option( 'eib_blocked_ips' );

		if ( empty( $blocked_ips ) ) {
			return true;
		}

		$visitor_ip = $this->eib_get_ip();

		if ( empty( $visitor_ip ) ) {
			return true;
		}

		$entries = array_map( 'trim', explode( PHP_EOL, $blocked_ips ) );

		foreach ( $entries as $entry ) {
			if ( '' === $entry || '#' === $entry[0] ) {
				continue;
			}

			if ( $this->eib_ip_matches( $visitor_ip, $entry ) ) {
				wp_die( esc_html__( 'Access Denied!', 'easy-ip-blocker' ), 403 );
			}
		}

		return true;
	}

	/**
	 * Check if an IP matches a rule (exact, CIDR, or wildcard).
	 *
	 * @param string $ip   Visitor IP address.
	 * @param string $rule Blocking rule.
	 * @return bool
	 */
	private function eib_ip_matches( string $ip, string $rule ): bool {
		if ( $ip === $rule ) {
			return true;
		}

		if ( false !== strpos( $rule, '/' ) ) {
			return $this->eib_cidr_match( $ip, $rule );
		}

		if ( false !== strpos( $rule, '*' ) ) {
			$pattern = '/^' . str_replace( array( '.', '*' ), array( '\\.', '\\d{1,3}' ), $rule ) . '$/';
			return (bool) preg_match( $pattern, $ip );
		}

		return false;
	}

	/**
	 * Check if an IP is within a CIDR range.
	 *
	 * @param string $ip   IP address to check.
	 * @param string $cidr CIDR notation (e.g. 192.168.1.0/24).
	 * @return bool
	 */
	private function eib_cidr_match( string $ip, string $cidr ): bool {
		list( $subnet, $mask ) = explode( '/', $cidr, 2 );

		$mask = (int) $mask;
		if ( $mask < 0 || $mask > 32 ) {
			return false;
		}

		$ip_long     = ip2long( $ip );
		$subnet_long = ip2long( $subnet );

		if ( false === $ip_long || false === $subnet_long ) {
			return false;
		}

		$mask_long = -1 << ( 32 - $mask );

		return ( $ip_long & $mask_long ) === ( $subnet_long & $mask_long );
	}

	/**
	 * Get the visitor's IP address.
	 *
	 * @return string IP address.
	 */
	public function eib_get_ip(): string {
		$ip = '';

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		$ip = explode( ',', $ip );
		return trim( $ip[0] );
	}

	/**
	 * Load plugin localisation.
	 *
	 * @return void
	 */
	public function load_localisation(): void {
		load_plugin_textdomain( 'easy-ip-blocker', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		$domain = 'easy-ip-blocker';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Easy_IP_Blocker is forbidden.', 'easy-ip-blocker' ), esc_attr( $this->version ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Easy_IP_Blocker is forbidden.', 'easy-ip-blocker' ), esc_attr( $this->version ) );
	}

	/**
	 * Installation. Runs on activation.
	 *
	 * @return void
	 */
	public function install(): void {
		$this->log_version_number();
	}

	/**
	 * Log the plugin version number.
	 *
	 * @return void
	 */
	private function log_version_number(): void {
		update_option( $this->token . '_version', $this->version );
	}
}
