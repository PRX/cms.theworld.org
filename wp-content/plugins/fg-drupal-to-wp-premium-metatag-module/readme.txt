=== FG Drupal to WordPress Premium Metatag module ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-drupal-to-wordpress/
Tags: drupal, wordpress, importer, migrator, converter, import, metas, metatag, meta description, meta keywords, meta title, nodewords, page_title, simplemeta
Requires at least: 4.5
Tested up to: 6.0
Stable tag: 1.8.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to migrate the meta tags from Drupal to WordPress
Needs the plugin «FG Drupal to WordPress Premium» to work
Needs the Yoast SEO plugin to manage the meta tags in WordPress

== Description ==

This is the Metatag module. It works only if the plugin FG Drupal to WordPress Premium is already installed.
It has been tested with **Drupal version 6, 7 and 8** and **WordPress 6.0**. It is compatible with multisite installations.

Major features include:

* migrates meta title to Yoast SEO
* migrates meta description to Yoast SEO
* migrates meta keywords to Yoast SEO
* supports the Drupal Metatag module
* supports the Drupal Nodewords module
* supports the Drupal Page Title module
* supports the Drupal Simplemeta module

== Installation ==

1.  Prerequesite: Buy and install the plugin «FG Drupal to WordPress Premium»
2.  Extract plugin zip file and load up to your wp-content/plugin directory
3.  Activate Plugin in the Admin => Plugins Menu
4.  Run the importer in Tools > Import > Drupal

== Translations ==
* French (fr_FR)
* English (default)
* other can be translated

== Changelog ==

= 1.8.0 =
New: Import the Drupal 8 meta stored in the node__field_metatags table
Tested with WordPress 6.0

= 1.7.1 =
Fixed: Notice: Undefined index: body_summary

= 1.7.0 =
New: Import the Drupal 8 meta stored in the node__field_meta table
Tested with WordPress 5.7

= 1.6.0 =
New: Add WP-CLI and CRON support
Tested with WordPress 5.5

= 1.5.0 =
New: Converts the metatag shortcodes
Tested with WordPress 5.4

= 1.4.0 =
New: Supports the Drupal Simplemeta module
Tested with WordPress 5.3

= 1.3.1 =
Fixed: [ERROR] Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'm.revision_id' in 'order clause'

= 1.3.0 =
New: Compatibility with Drupal 8
Tested with WordPress 5.0

= 1.2.0 =
New: Compatibility with old version of Nodewords

= 1.1.0 =
New: Supports the Drupal Nodewords module
New: Supports the Drupal Page Title module
Tested with WordPress 4.9

= 1.0.0 =
Initial version
