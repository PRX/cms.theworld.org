=== FG Drupal to WordPress Premium Media Provider module ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-drupal-to-wordpress/
Tags: drupal, wordpress, importer, migrator, converter, import, media provider, custom fields
Requires at least: 4.5
Tested up to: 5.8
Stable tag: 1.2.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to import the media field links (YouTube video, Vimeo video, SoundCloud podcasts)
Needs the plugin «FG Drupal to WordPress Premium» to work

== Description ==

This is the Media Provider module. It works only if the plugin FG Drupal to WordPress Premium is already installed.
It has been tested with **Drupal version 7** and **Wordpress 5.8**. It is compatible with multisite installations.

Major features:

* imports the SoundCloud fields
* imports the YouTube fields
* imports the Vimeo fields
* converts the S3 URLs

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

= 1.2.1 =
Fixed: S3 files without "amazons3_bucket" were not imported
Tested with WordPress 5.8

= 1.2.0 =
New: Add WP-CLI and CRON support
Tested with WordPress 5.5

= 1.1.0 =
New: Compatible with Vimeo
Tested with WordPress 5.3

= 1.0.1 =
Fixed: Some images were not imported as featured images
Tested with WordPress 4.9

= 1.0.0 =
Initial version
