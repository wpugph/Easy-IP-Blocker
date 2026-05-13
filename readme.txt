=== Easy IP Blocker ===
Contributors: carl-alberto
Donate link: https://wordpress.org/plugins/easy-ip-blocker/
Tags: security, ip blocker, firewall, block ip, traffic
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quickly block unwanted IPs in your WP site with CDN/proxy-aware IP detection

== Description ==

Easy IP Blocker is a lightweight security plugin that lets you block unwanted IP addresses directly from your WordPress dashboard. Protect your site from malicious traffic, brute-force login attempts, and spam by maintaining a blocklist with just a few clicks.

Your entire blocklist is stored as a single plain-text option — one entry per line. There is no database table, no import/export workflow, and no complicated UI. Just copy, paste, and save. Back up your list by copying the textarea contents to a text file, or restore it by pasting one back in. It is that simple.

Need to block IPs faster? Use the built-in WP-CLI commands to add, remove, or list entries without ever opening the dashboard.

**Features:**

* Block individual IPs, CIDR ranges, or wildcard patterns
* CDN/proxy-aware IP detection — supports Cloudflare, Fastly, Akamai, AWS CloudFront, Sucuri, and custom headers
* Auto-detects your CDN/proxy and recommends the correct setting
* Plain-text flat list — copy and paste to back up, migrate, or restore
* WP-CLI support (`wp eib add/remove/list/clear`) for scripting and automation
* Lightweight and fast — no external dependencies, API calls, or extra database tables
* Compatible with PHP 7.4 through 8.4
* Helps prevent brute-force attacks, spam, and unauthorized access
* Comment support — use # to annotate and organize your blocklist
* Modern, clean admin interface with tabbed layout

**Supported CDN / proxy providers:**

* **Cloudflare** — reads `CF-Connecting-IP`
* **Fastly** — reads `Fastly-Client-IP`
* **Akamai** — reads `True-Client-IP`
* **AWS CloudFront** — reads `CloudFront-Viewer-Address`
* **Sucuri** — reads `X-Sucuri-ClientIP`
* **Generic proxy** — reads `X-Forwarded-For`
* **Custom header** — specify any header name your proxy sets
* **Direct** — uses `REMOTE_ADDR` when no proxy is in use

The plugin auto-detects which CDN or proxy is in front of your site and recommends the correct setting. You can also choose manually from the Settings tab.

**Supported blocking formats:**

* **Exact IP** — `192.168.1.1`
* **CIDR range** — `192.168.1.0/24` (blocks 192.168.1.0 through 192.168.1.255)
* **Wildcard** — `10.0.0.*` or `172.16.*.*` (matches any value in place of *)
* **Comments** — lines starting with `#` are ignored, useful for notes

**WP-CLI commands:**

* `wp eib add <ip>...` — Append one or more entries to the blocklist
* `wp eib remove <ip>...` — Remove entries from the blocklist
* `wp eib delete <ip>...` — Alias for remove
* `wp eib list` — Show all blocked IPs and rules
* `wp eib clear` — Clear the entire blocklist

All commands that accept IPs support multiple entries in a single call. For example:

`wp eib add 192.168.1.1 10.0.0.0/24 172.16.0.*`
`wp eib remove 192.168.1.1 10.0.0.0/24`

== Installation ==

Installing "Easy IP Blocker" can be done either by searching for "Easy IP Blocker" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
1. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What is the plugin used for? =

Easy IP Blocker allows you to block unwanted IP addresses from accessing your WordPress site. Navigate to Settings > Easy IP Block Settings, and add entries using exact IPs (192.168.1.1), CIDR ranges (10.0.0.0/24), or wildcards (172.16.*.*). Use lines starting with # to add comments to your blocklist.

= My site is behind Cloudflare / a CDN — will blocking work? =

Yes. Go to Settings > Easy IP Block Settings > Settings tab and select your CDN or proxy from the "IP Detection Method" dropdown. The plugin will read the correct header to get the real visitor IP. If you are unsure, the plugin auto-detects your CDN and shows a recommendation on the Settings tab.

= What is the "Auto (legacy)" IP detection method? =

This is the default for existing installations. It checks multiple headers in order (HTTP_CLIENT_IP, X-Forwarded-For, REMOTE_ADDR). While convenient, it is less secure because attackers can spoof headers. We recommend selecting your specific CDN or "Direct" if you have no proxy.

= Can I use a custom header? =

Yes. Select "Custom header" from the IP Detection Method dropdown and enter the header name your proxy sets (e.g. X-Real-IP). This works with Nginx, HAProxy, or any non-standard proxy configuration.

== Changelog ==

= 2.1.0 =
* 2026-05-13
* Added CDN/proxy-aware IP detection — supports Cloudflare, Fastly, Akamai, AWS CloudFront, Sucuri, generic proxy, and custom headers
* Added auto-detection of CDN/proxy headers with admin recommendation notice
* Added configurable IP Detection Method dropdown in a new Settings tab
* Added Custom Header Name field for non-standard proxy configurations
* Added "IP Detection Methods Explained" tooltip reference on the Settings tab
* Reorganized admin UI into tabbed layout — Blocklist (main) and Settings tabs
* Existing installations default to "Auto (legacy)" mode for backward compatibility
* Updated plugin description and FAQ
* Bumped "Tested up to" to WordPress 6.9

= 2.0.0 =
* 2026-05-12
* Version bump to WordPress 6.8 and PHP 8.4 compatibility
* Added CIDR range blocking (e.g. 192.168.1.0/24)
* Added wildcard pattern blocking (e.g. 10.0.0.*)
* Added comment support (lines starting with #)
* Added WP-CLI commands (wp eib add/remove/list/clear)
* Modernized admin settings UI
* Updated plugin assets and deployment workflow

= 1.0.4 =
* 2022-10-24
* Version bump to WordPress versino 6.0.3 compatibility

= 1.0.3 =
* 2021-08-9
* Version bump to 5.8.1

= 1.0 =
* 2020-08-10
* Initial release
