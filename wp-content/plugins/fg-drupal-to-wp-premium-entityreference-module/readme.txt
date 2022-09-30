=== FG Drupal to WordPress Premium Entity Reference module ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-drupal-to-wordpress/
Tags: drupal, wordpress, importer, migrator, converter, import, entity reference, nodes relationships, taxonomies relationships
Requires at least: 4.5
Tested up to: 5.9.1
Stable tag: 1.13.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to migrate the Entity Reference data (nodes/taxonomies relationships) from Drupal to WordPress
Needs the plugin «FG Drupal to WordPress Premium» to work
Needs the Toolset Types plugin

== Description ==

This is the Entity Reference module. It works only if the plugin FG Drupal to WordPress Premium is already installed.
It has been tested with **Drupal versions 7 and 8** and **WordPress 5.9**. It is compatible with multisite installations.

Major features include:

* migrates node/node relationships
* migrates node/taxonomy relationships
* migrates users relationships

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

= 1.13.0 =
New: Import the taxonomy term relationships to ACF
Tested with WordPress 5.9

= 1.12.0 =
New: Import the users relationships to ACF

= 1.11.0 =
New: Import the User entity reference fields to ACF
Tested with WordPress 5.8

= 1.10.1 =
Fixed: Missing relationships (regression from 1.10.0)

= 1.10.0 =
New: Import the users custom fields defined as Entity Reference
Tested with WordPress 5.6

= 1.9.0 =
New: Add WP-CLI and CRON support

= 1.8.1 =
Fixed: Taxonomy not imported if the view name is different from the taxonomy name (Drupal 8)
Tested with WordPress 5.5

= 1.8.0 =
Tweak: Compatibility with FG Drupal to WordPress Premium 2.16.0

= 1.7.0 =
New: Get the relationships by their view display name

= 1.6.1 =
Fixed: Some views were not considered as relationships

= 1.6.0 =
Tweak: Refactoring of unserialized data
Tested with WordPress 5.4

= 1.5.0 =
New: Add "Many to many" relationships

= 1.4.1 =
Fixed: Warning: unserialize() expects parameter 1 to be string, array given
Fixed: Notice: Undefined index: base_table

= 1.4.0 =
New: Compatibility with Drupal 8.5
Tested with WordPress 5.0

= 1.3.0 =
New: Import the entity references defined in "Views" mode

= 1.2.0 =
New: Compatibility with FG Drupal to WordPress Premium 1.61.0
Tested with WordPress 4.9

= 1.1.0 =
New: Compatibility with Drupal 8

= 1.0.0 =
Initial version
