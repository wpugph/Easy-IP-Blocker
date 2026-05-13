<?php
/**
 * Settings class file.
 *
 * @package Easy_IP_Blocker/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 */
class Easy_IP_Blocker_Settings {

	/**
	 * The single instance of Easy_IP_Blocker_Settings.
	 *
	 * @var Easy_IP_Blocker_Settings|null
	 */
	private static $instance = null;

	/**
	 * The main plugin object.
	 *
	 * @var Easy_IP_Blocker|null
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 *
	 * @var string
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Constructor function.
	 *
	 * @param Easy_IP_Blocker $parent Parent object.
	 */
	public function __construct( Easy_IP_Blocker $parent ) {
		$this->parent = $parent;
		$this->base   = 'eib_';

		add_action( 'init', array( $this, 'init_settings' ), 11 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		add_filter(
			'plugin_action_links_' . plugin_basename( $this->parent->file ),
			array( $this, 'add_settings_link' )
		);

		add_filter( $this->base . 'menu_settings', array( $this, 'configure_settings' ) );
	}

	/**
	 * Initialise settings.
	 *
	 * @return void
	 */
	public function init_settings(): void {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @return void
	 */
	public function add_menu_item(): void {

		$args = $this->menu_settings();

		if ( is_array( $args ) && isset( $args['location'] ) && function_exists( 'add_' . $args['location'] . '_page' ) ) {
			switch ( $args['location'] ) {
				case 'options':
				case 'submenu':
					$page = add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'] );
					break;
				case 'menu':
					$page = add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $args['function'], $args['icon_url'], $args['position'] );
					break;
				default:
					return;
			}
			add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
		}
	}

	/**
	 * Prepare default settings page arguments.
	 *
	 * @return array Settings page arguments.
	 */
	private function menu_settings(): array {
		return apply_filters(
			$this->base . 'menu_settings',
			array(
				'location'    => 'options',
				'parent_slug' => 'options-general.php',
				'page_title'  => __( 'Easy IP Block Settings', 'easy-ip-blocker' ),
				'menu_title'  => __( 'Easy IP Block Settings', 'easy-ip-blocker' ),
				'capability'  => 'manage_options',
				'menu_slug'   => $this->parent->token . '_settings',
				'function'    => array( $this, 'settings_page' ),
				'icon_url'    => '',
				'position'    => null,
			)
		);
	}

	/**
	 * Container for settings page arguments.
	 *
	 * @param array $settings Settings array.
	 * @return array
	 */
	public function configure_settings( array $settings = array() ): array {
		return $settings;
	}

	/**
	 * Load settings JS & CSS.
	 *
	 * @return void
	 */
	public function settings_assets(): void {

		wp_enqueue_style( 'farbtastic' );
		wp_enqueue_script( 'farbtastic' );
		wp_enqueue_media();

		wp_register_script( $this->parent->token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0', true );
		wp_enqueue_script( $this->parent->token . '-settings-js' );

		$css = '
			#easy_ip_blocker_settings { max-width: 800px; }

			.eib-header {
				background: #1d2327;
				border-radius: 8px 8px 0 0;
				padding: 24px 28px;
				margin: 20px 0 0;
			}
			.eib-header-inner {
				display: flex;
				align-items: center;
				gap: 16px;
			}
			.eib-header-icon {
				color: #f0c33c;
				flex-shrink: 0;
			}
			.eib-header h1 {
				color: #fff;
				font-size: 22px;
				font-weight: 600;
				margin: 0;
				padding: 0;
				line-height: 1.3;
			}
			.eib-version {
				color: #f0c33c;
				font-size: 13px;
				margin: 2px 0 0;
				opacity: 0.9;
			}

			.eib-card {
				background: #fff;
				border: 1px solid #c3c4c7;
				border-top: none;
				padding: 24px 28px;
			}
			.eib-card .form-table th {
				font-weight: 600;
				padding-top: 20px;
			}
			.eib-card .form-table td {
				padding-top: 16px;
			}
			.eib-card textarea {
				width: 100%;
				max-width: 100%;
				min-height: 180px;
				font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
				font-size: 13px;
				line-height: 1.6;
				padding: 12px;
				border: 1px solid #c3c4c7;
				border-radius: 4px;
				resize: vertical;
			}
			.eib-card textarea:focus {
				border-color: #2271b1;
				box-shadow: 0 0 0 1px #2271b1;
				outline: none;
			}
			.eib-card .description {
				color: #646970;
				font-style: normal;
				margin-top: 8px;
				display: block;
			}
			.eib-card .submit {
				padding-top: 8px;
				border-top: 1px solid #f0f0f1;
				margin-top: 20px;
			}

			.eib-cli-card {
				margin-top: 0;
				border-top: none;
			}
			.eib-cli-title {
				font-size: 14px;
				font-weight: 600;
				margin: 0 0 6px;
				color: #1d2327;
			}
			.eib-cli-desc {
				color: #646970;
				font-size: 13px;
				margin: 0 0 14px;
			}
			.eib-cli-table {
				width: 100%;
				border-collapse: collapse;
			}
			.eib-cli-table td {
				padding: 8px 12px;
				font-size: 13px;
				border-top: 1px solid #f0f0f1;
				vertical-align: middle;
			}
			.eib-cli-table tr:first-child td {
				border-top: none;
			}
			.eib-cli-table td:first-child {
				white-space: nowrap;
				width: 1%;
			}
			.eib-cli-table code {
				background: #f0f0f1;
				padding: 3px 8px;
				border-radius: 3px;
				font-size: 12px;
				font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, monospace;
			}

			.eib-tabs {
				margin: 0;
				border-bottom: 1px solid #c3c4c7;
				padding: 0;
			}
			.eib-tabs .nav-tab {
				border-bottom: none;
				margin-bottom: -1px;
			}
			.eib-tabs .nav-tab-active {
				background: #fff;
				border-bottom: 1px solid #fff;
			}

			.eib-tooltip-section {
				margin-top: 20px;
				padding-top: 16px;
				border-top: 1px solid #f0f0f1;
			}
			.eib-tooltip-title {
				font-size: 14px;
				font-weight: 600;
				margin: 0 0 12px;
				color: #1d2327;
				display: flex;
				align-items: center;
				gap: 4px;
			}
			.eib-tooltip-icon {
				color: #2271b1;
				font-size: 18px;
			}
			.eib-tooltip-list {
				margin: 0;
			}
			.eib-tooltip-list dt {
				font-weight: 600;
				font-size: 13px;
				color: #1d2327;
				margin: 12px 0 2px;
			}
			.eib-tooltip-list dt:first-child {
				margin-top: 0;
			}
			.eib-tooltip-list dd {
				margin: 0 0 0 0;
				padding: 0;
				color: #646970;
				font-size: 13px;
				line-height: 1.5;
			}

			.eib-detection-card {
				background: #fcf9e8;
				border-left: 4px solid #dba617;
				border-top: none;
				padding: 16px 28px;
			}
			.eib-detection-card code {
				background: #f0f0f1;
				padding: 2px 6px;
				border-radius: 3px;
				font-size: 12px;
			}
			.eib-detection-match {
				background: #edfaef;
				border-left-color: #00a32a;
			}

			.eib-footer {
				background: #f6f7f7;
				border: 1px solid #c3c4c7;
				border-top: none;
				border-radius: 0 0 8px 8px;
				padding: 16px 28px;
			}
			.eib-footer p {
				margin: 0;
				color: #646970;
				font-size: 13px;
			}
			.eib-footer a {
				color: #2271b1;
				text-decoration: none;
			}
			.eib-footer a:hover {
				color: #135e96;
				text-decoration: underline;
			}
		';

		wp_register_style( $this->parent->token . '-admin', false, array(), $this->parent->version );
		wp_enqueue_style( $this->parent->token . '-admin' );
		wp_add_inline_style( $this->parent->token . '-admin', $css );

		$js = '
			jQuery(function($) {
				var $select = $("#ip_source");
				var $customRow = $("#custom_header").closest("tr");
				function toggleCustom() {
					$customRow.toggle($select.val() === "custom");
				}
				$select.on("change", toggleCustom);
				toggleCustom();
			});
		';
		wp_add_inline_script( $this->parent->token . '-settings-js', $js );
	}

	/**
	 * Add settings link to plugin list table.
	 *
	 * @param  array $links Existing links.
	 * @return array Modified links.
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = '<a href="options-general.php?page=' . $this->parent->token . '_settings">' . __( 'Settings', 'easy-ip-blocker' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Build settings fields.
	 *
	 * @return array Fields to be displayed on settings page.
	 */
	private function settings_fields(): array {

		$settings['blocklist'] = array(
			'title'       => __( 'Blocklist', 'easy-ip-blocker' ),
			'description' => __( 'Block visitors by IP address, CIDR range, or wildcard pattern.', 'easy-ip-blocker' ),
			'fields'      => array(
				array(
					'id'          => 'blocked_ips',
					'label'       => __( 'IP Block list', 'easy-ip-blocker' ),
					'description' => __( 'Enter one rule per line. Supported formats: exact IP (192.168.1.1), CIDR range (192.168.1.0/24), or wildcard (10.0.0.*). Lines starting with # are ignored.', 'easy-ip-blocker' ),
					'type'        => 'textarea',
					'default'     => '',
					'placeholder' => __( "# Exact IP\n192.168.1.1\n\n# CIDR range\n10.0.0.0/24\n\n# Wildcard\n172.16.*.*", 'easy-ip-blocker' ),
				),
			),
		);

		$settings['settings'] = array(
			'title'       => __( 'Settings', 'easy-ip-blocker' ),
			'description' => __( 'Configure how the plugin detects visitor IP addresses.', 'easy-ip-blocker' ),
			'fields'      => array(
				array(
					'id'          => 'ip_source',
					'label'       => __( 'IP Detection Method', 'easy-ip-blocker' ),
					'description' => __( 'Select how the visitor IP address is determined. Choose your CDN or proxy, or use "Custom header" to specify your own.', 'easy-ip-blocker' ),
					'type'        => 'select',
					'options'     => array(
						'auto'          => __( 'Auto (legacy — trusts multiple headers)', 'easy-ip-blocker' ),
						'direct'        => __( 'Direct (no proxy) — REMOTE_ADDR only', 'easy-ip-blocker' ),
						'cloudflare'    => __( 'Cloudflare — CF-Connecting-IP', 'easy-ip-blocker' ),
						'fastly'        => __( 'Fastly — Fastly-Client-IP', 'easy-ip-blocker' ),
						'akamai'        => __( 'Akamai — True-Client-IP', 'easy-ip-blocker' ),
						'cloudfront'    => __( 'AWS CloudFront — CloudFront-Viewer-Address', 'easy-ip-blocker' ),
						'sucuri'        => __( 'Sucuri — X-Sucuri-ClientIP', 'easy-ip-blocker' ),
						'generic_proxy' => __( 'Generic proxy — X-Forwarded-For', 'easy-ip-blocker' ),
						'custom'        => __( 'Custom header', 'easy-ip-blocker' ),
					),
					'default'     => 'auto',
				),
				array(
					'id'          => 'custom_header',
					'label'       => __( 'Custom Header Name', 'easy-ip-blocker' ),
					'description' => __( 'Enter the HTTP header name your proxy sets (e.g. X-Real-IP). Only used when "Custom header" is selected above.', 'easy-ip-blocker' ),
					'type'        => 'text',
					'default'     => '',
					'placeholder' => 'X-Real-IP',
				),
			),
		);

		$settings = apply_filters( $this->parent->token . '_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		if ( ! is_array( $this->settings ) ) {
			return;
		}

		$current_section = '';

		if ( isset( $_POST['tab'] ) && isset( $_POST['_wpnonce'] ) ) {
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $this->parent->token . '_settings-options' ) ) {
				$current_section = sanitize_text_field( wp_unslash( $_POST['tab'] ) );
			}
		} elseif ( isset( $_GET['tab'] ) ) {
			$current_section = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}

		if ( ! $current_section ) {
			$keys            = array_keys( $this->settings );
			$current_section = $keys[0];
		}

		foreach ( $this->settings as $section => $data ) {

			if ( $current_section !== $section ) {
				continue;
			}

			add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), $this->parent->token . '_settings' );

			foreach ( $data['fields'] as $field ) {

				$validation = '';
				if ( isset( $field['callback'] ) ) {
					$validation = $field['callback'];
				}

				$option_name = $this->base . $field['id'];
				register_setting( $this->parent->token . '_settings', $option_name, $validation );

				add_settings_field(
					$field['id'],
					$field['label'],
					array( $this->parent->admin, 'display_field' ),
					$this->parent->token . '_settings',
					$section,
					array(
						'field'  => $field,
						'prefix' => $this->base,
					)
				);
			}
		}
	}

	/**
	 * Settings section.
	 *
	 * @param array $section Array of section ids.
	 * @return void
	 */
	public function settings_section( array $section ): void {
		echo '<p>' . wp_kses_post( $this->settings[ $section['id'] ]['description'] ) . '</p>' . "\n";
	}

	/**
	 * Load settings page content.
	 *
	 * @return void
	 */
	public function settings_page(): void {

		$tab = '';
		if ( isset( $_GET['tab'] ) ) {
			$tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		}

		$html  = '<div class="wrap" id="' . esc_attr( $this->parent->token ) . '_settings">' . "\n";
		$html .= '<div class="eib-header">' . "\n";
		$html .= '<div class="eib-header-inner">' . "\n";
		$html .= '<svg class="eib-header-icon" viewBox="0 0 24 24" width="28" height="28" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" stroke="currentColor" stroke-width="2" fill="none"/><circle cx="12" cy="12" r="4.5" stroke="currentColor" stroke-width="2" fill="none"/><line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>' . "\n";
		$html .= '<div>' . "\n";
		$html .= '<h1>' . esc_html__( 'Easy IP Blocker', 'easy-ip-blocker' ) . '</h1>' . "\n";
		$html .= '<p class="eib-version">' . sprintf(
			/* translators: %s: plugin version number */
			esc_html__( 'Version %s', 'easy-ip-blocker' ),
			esc_html( $this->parent->version )
		) . '</p>' . "\n";
		$html .= '</div>' . "\n";
		$html .= '</div>' . "\n";
		$html .= '</div>' . "\n";

		$active_tab = $tab;
		if ( ! $active_tab && is_array( $this->settings ) ) {
			$keys       = array_keys( $this->settings );
			$active_tab = $keys[0];
		}

		if ( is_array( $this->settings ) && 1 < count( $this->settings ) ) {

			$html .= '<h2 class="nav-tab-wrapper eib-tabs">' . "\n";

			foreach ( $this->settings as $section => $data ) {

				$class = 'nav-tab';
				if ( $section === $active_tab ) {
					$class .= ' nav-tab-active';
				}

				$tab_link = add_query_arg( array( 'tab' => $section ) );
				if ( isset( $_GET['settings-updated'] ) ) {
					$tab_link = remove_query_arg( 'settings-updated', $tab_link );
				}

				$html .= '<a href="' . esc_url( $tab_link ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $data['title'] ) . '</a>' . "\n";
			}

			$html .= '</h2>' . "\n";
		}

		if ( 'settings' === $active_tab ) {
			$detection    = $this->detect_cdn();
			$current_src  = get_option( 'eib_ip_source', 'auto' );
			$is_match     = ( $current_src === $detection['detected'] );
			$card_class   = $is_match ? 'eib-detection-card eib-detection-match' : 'eib-detection-card';

			$html .= '<div class="eib-card ' . esc_attr( $card_class ) . '">' . "\n";
			$html .= '<p><strong>' . esc_html__( 'CDN / Proxy Detection', 'easy-ip-blocker' ) . ':</strong> ';

			if ( $is_match && 'auto' !== $current_src ) {
				$html .= esc_html__( 'Your current setting matches the detected configuration.', 'easy-ip-blocker' );
			} elseif ( 'direct' === $detection['detected'] ) {
				$html .= esc_html__( 'No CDN or proxy headers detected. If your server connects directly to visitors, select "Direct (no proxy)" below.', 'easy-ip-blocker' );
			} else {
				$html .= sprintf(
					/* translators: 1: header name, 2: CDN/proxy name */
					esc_html__( 'We detected the %1$s header, which indicates %2$s. We recommend selecting "%2$s" as your IP Detection Method below.', 'easy-ip-blocker' ),
					'<code>' . esc_html( $detection['header'] ) . '</code>',
					'<strong>' . esc_html( $detection['label'] ) . '</strong>'
				);
			}

			$html .= '</p>' . "\n";
			$html .= '</div>' . "\n";
		}

		$html .= '<div class="eib-card">' . "\n";
		$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

		ob_start();
		settings_fields( $this->parent->token . '_settings' );
		do_settings_sections( $this->parent->token . '_settings' );
		$html .= ob_get_clean();

		if ( 'settings' === $active_tab ) {
			$html .= $this->render_ip_source_tooltips();
		}

		$html .= '<p class="submit">' . "\n";
		$html .= '<input type="hidden" name="tab" value="' . esc_attr( $tab ) . '" />' . "\n";
		$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr__( 'Save Settings', 'easy-ip-blocker' ) . '" />' . "\n";
		$html .= '</p>' . "\n";
		$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		if ( 'blocklist' === $active_tab || '' === $tab ) {
			$html .= '<div class="eib-card eib-cli-card">' . "\n";
			$html .= '<h3 class="eib-cli-title">' . esc_html__( 'WP-CLI Commands', 'easy-ip-blocker' ) . '</h3>' . "\n";
			$html .= '<p class="eib-cli-desc">' . esc_html__( 'Manage your blocklist from the terminal for faster workflows and automation.', 'easy-ip-blocker' ) . '</p>' . "\n";
			$html .= '<table class="eib-cli-table">' . "\n";
			$html .= '<tr><td><code>wp eib add &lt;ip&gt;...</code></td><td>' . esc_html__( 'Add one or more IPs, CIDR ranges, or wildcards to the blocklist', 'easy-ip-blocker' ) . '</td></tr>' . "\n";
			$html .= '<tr><td><code>wp eib remove &lt;ip&gt;...</code></td><td>' . esc_html__( 'Remove entries from the blocklist', 'easy-ip-blocker' ) . '</td></tr>' . "\n";
			$html .= '<tr><td><code>wp eib delete &lt;ip&gt;...</code></td><td>' . esc_html__( 'Alias for remove', 'easy-ip-blocker' ) . '</td></tr>' . "\n";
			$html .= '<tr><td><code>wp eib list</code></td><td>' . esc_html__( 'Show all blocked IPs and rules', 'easy-ip-blocker' ) . '</td></tr>' . "\n";
			$html .= '<tr><td><code>wp eib clear --yes</code></td><td>' . esc_html__( 'Clear the entire blocklist', 'easy-ip-blocker' ) . '</td></tr>' . "\n";
			$html .= '</table>' . "\n";
			$html .= '<p class="eib-cli-desc" style="margin-top:12px;">' . esc_html__( 'All commands accept multiple entries in a single call, e.g.:', 'easy-ip-blocker' ) . ' <code>wp eib add 192.168.1.1 10.0.0.0/24 172.16.0.*</code></p>' . "\n";
			$html .= '</div>' . "\n";
		}

		$html .= '<div class="eib-footer">' . "\n";
		$html .= '<p>' . wp_kses(
			sprintf(
				/* translators: 1: opening link tag for WP support, 2: closing link tag, 3: opening link tag for GitHub, 4: closing link tag */
				__( 'Need help? Visit the %1$sWordPress.org support forum%2$s or %3$sopen an issue on GitHub%4$s.', 'easy-ip-blocker' ),
				'<a href="https://wordpress.org/support/plugin/easy-ip-blocker/" target="_blank" rel="noopener noreferrer">',
				'</a>',
				'<a href="https://github.com/wpugph/Easy-IP-Blocker/issues" target="_blank" rel="noopener noreferrer">',
				'</a>'
			),
			array(
				'a' => array(
					'href'   => array(),
					'target' => array(),
					'rel'    => array(),
				),
			)
		) . '</p>' . "\n";
		$html .= '</div>' . "\n";

		$html .= '</div>' . "\n";

		$allowed_html = $this->parent->admin->allowed_htmls;
		echo wp_kses( $html, $allowed_html );
	}

	/**
	 * Render tooltip reference table explaining each IP detection method.
	 *
	 * @return string HTML output.
	 */
	private function render_ip_source_tooltips(): string {
		$methods = array(
			array(
				'label' => __( 'Auto (legacy)', 'easy-ip-blocker' ),
				'desc'  => __( 'Checks HTTP_CLIENT_IP, X-Forwarded-For, then REMOTE_ADDR in order. Easy to set up but less secure — attackers can spoof headers to bypass blocking.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Direct (no proxy)', 'easy-ip-blocker' ),
				'desc'  => __( 'Uses REMOTE_ADDR only. The most secure option when your server connects directly to visitors with no CDN or reverse proxy in between.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Cloudflare', 'easy-ip-blocker' ),
				'desc'  => __( 'Reads the CF-Connecting-IP header set by Cloudflare. This contains the true visitor IP. Only trust this if your site is actually behind Cloudflare.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Fastly', 'easy-ip-blocker' ),
				'desc'  => __( 'Reads the Fastly-Client-IP header. Fastly sets this to the downstream client IP at the edge. Use this if Fastly is your CDN.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Akamai', 'easy-ip-blocker' ),
				'desc'  => __( 'Reads the True-Client-IP header. Akamai sets this at the edge server. Note: Cloudflare Enterprise also supports this header.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'AWS CloudFront', 'easy-ip-blocker' ),
				'desc'  => __( 'Reads the CloudFront-Viewer-Address header. This includes a port suffix (e.g. 1.2.3.4:54321) which is automatically stripped.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Sucuri', 'easy-ip-blocker' ),
				'desc'  => __( 'Reads the X-Sucuri-ClientIP header. Use this if your site is behind the Sucuri WAF/CDN firewall.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Generic proxy', 'easy-ip-blocker' ),
				'desc'  => __( 'Reads X-Forwarded-For, which is the standard header for proxies and load balancers. Takes the first IP in the chain. Can be spoofed — only use when you trust the proxy.', 'easy-ip-blocker' ),
			),
			array(
				'label' => __( 'Custom header', 'easy-ip-blocker' ),
				'desc'  => __( 'Specify any HTTP header name your proxy sets (e.g. X-Real-IP). Use this for Nginx, HAProxy, or any non-standard proxy configuration.', 'easy-ip-blocker' ),
			),
		);

		$html  = '<div class="eib-tooltip-section">' . "\n";
		$html .= '<h3 class="eib-tooltip-title">';
		$html .= '<span class="dashicons dashicons-info-outline eib-tooltip-icon"></span> ';
		$html .= esc_html__( 'IP Detection Methods Explained', 'easy-ip-blocker' );
		$html .= '</h3>' . "\n";
		$html .= '<dl class="eib-tooltip-list">' . "\n";

		foreach ( $methods as $method ) {
			$html .= '<dt>' . esc_html( $method['label'] ) . '</dt>' . "\n";
			$html .= '<dd>' . esc_html( $method['desc'] ) . '</dd>' . "\n";
		}

		$html .= '</dl>' . "\n";
		$html .= '</div>' . "\n";

		return $html;
	}

	/**
	 * Detect CDN/proxy by checking for known headers in the current request.
	 *
	 * @return array{detected: string, header: string}
	 */
	private function detect_cdn(): array {
		$checks = array(
			'cloudflare'    => 'HTTP_CF_CONNECTING_IP',
			'fastly'        => 'HTTP_FASTLY_CLIENT_IP',
			'akamai'        => 'HTTP_TRUE_CLIENT_IP',
			'cloudfront'    => 'HTTP_CLOUDFRONT_VIEWER_ADDRESS',
			'sucuri'        => 'HTTP_X_SUCURI_CLIENTIP',
			'generic_proxy' => 'HTTP_X_FORWARDED_FOR',
		);

		$labels = array(
			'cloudflare'    => 'Cloudflare',
			'fastly'        => 'Fastly',
			'akamai'        => 'Akamai',
			'cloudfront'    => 'AWS CloudFront',
			'sucuri'        => 'Sucuri',
			'generic_proxy' => 'Generic proxy',
		);

		foreach ( $checks as $key => $server_key ) {
			if ( ! empty( $_SERVER[ $server_key ] ) ) {
				$header_name = str_replace( '_', '-', substr( $server_key, 5 ) );
				return array(
					'detected' => $key,
					'header'   => $header_name,
					'label'    => $labels[ $key ],
				);
			}
		}

		return array(
			'detected' => 'direct',
			'header'   => 'REMOTE_ADDR',
			'label'    => 'Direct (no proxy)',
		);
	}

	/**
	 * Main Easy_IP_Blocker_Settings Instance.
	 *
	 * Ensures only one instance of Easy_IP_Blocker_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @param Easy_IP_Blocker $parent Object instance.
	 * @return Easy_IP_Blocker_Settings Settings instance.
	 */
	public static function instance( Easy_IP_Blocker $parent ): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $parent );
		}
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning of Easy_IP_Blocker_Settings is forbidden.', 'easy-ip-blocker' ), esc_attr( $this->parent->version ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of Easy_IP_Blocker_Settings is forbidden.', 'easy-ip-blocker' ), esc_attr( $this->parent->version ) );
	}
}
