=== Easy IP Blocker ===
Contributors: carl-alberto
Donate link: https://wordpress.org/plugins/easy-ip-blocker/
Tags: security, ip blocker, firewall, block ip, traffic
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 8.4
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quickly block unwanted IPs in your WP site

== Description ==

Easy IP Blocker is a lightweight security plugin that lets you block unwanted IP addresses directly from your WordPress dashboard. Protect your site from malicious traffic, brute-force login attempts, and spam by maintaining a blocklist of IPs with just a few clicks.

**Features:**

* Block individual IPs, CIDR ranges, or wildcard patterns from the admin panel
* Lightweight and fast — no external dependencies or API calls
* Helps prevent brute-force attacks, spam, and unauthorized access
* Comment support — use # to annotate your blocklist
* Modern, clean admin interface

**Supported blocking formats:**

* **Exact IP** — `192.168.1.1`
* **CIDR range** — `192.168.1.0/24` (blocks 192.168.1.0 through 192.168.1.255)
* **Wildcard** — `10.0.0.*` or `172.16.*.*` (matches any value in place of *)
* **Comments** — lines starting with `#` are ignored, useful for notes

== Installation ==

Installing "Easy IP Blocker" can be done either by searching for "Easy IP Blocker" via the "Plugins > Add New" screen in your WordPress dashboard, or by using the following steps:

1. Download the plugin via WordPress.org
1. Upload the ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What is the plugin used for? =

Easy IP Blocker allows you to block unwanted IP addresses from accessing your WordPress site. Navigate to Settings > Easy IP Block Settings, and add entries using exact IPs (192.168.1.1), CIDR ranges (10.0.0.0/24), or wildcards (172.16.*.*). Use lines starting with # to add comments to your blocklist.

== Changelog ==

= 2.0.0 =
* 2026-05-12
* Version bump to WordPress 6.8 and PHP 8.4 compatibility
* Added CIDR range blocking (e.g. 192.168.1.0/24)
* Added wildcard pattern blocking (e.g. 10.0.0.*)
* Added comment support (lines starting with #)
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
