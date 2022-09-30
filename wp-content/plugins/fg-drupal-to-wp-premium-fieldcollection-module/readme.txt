=== FG Drupal to WordPress Premium Field Collection module ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-drupal-to-wordpress/
Tags: drupal, wordpress, importer, migrator, converter, import, field collection
Requires at least: 4.5
Tested up to: 6.0
Stable tag: 2.3.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to migrate the Field Collection data  from Drupal to WordPress
Needs the plugin «FG Drupal to WordPress Premium» to work
Needs either the Toolset Types plugin or the ACF plugin

== Description ==

This is the Field Collection module. It works only if the plugin FG Drupal to WordPress Premium is already installed.
It has been tested with **Drupal version 7** and **WordPress 6.0**. It is compatible with multisite installations.

Major features include:

* migrates all the posts collection fields
* migrates all the users collection fields

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

= 2.3.0 =
New: Import the users collection fields
Tested with WordPress 6.0

= 2.2.0 =
New: Import the node_reference fields contained in the field collections
Tested with WordPress 5.9

= 2.1.1 =
Fixed: Entity reference fields not imported

= 2.1.0 =
New: Import the entityreference fields contained in the field collections
Tested with WordPress 5.8

= 2.0.3 =
Fixed: ACF repeater fields and their subfields not displayed on the front-end

= 2.0.2 =
Fixed: PHP Fatal error: Uncaught InvalidArgumentException: Invalid type provided

= 2.0.1 =
Fixed: Collection fields with long names were not imported

= 2.0.0 =
New: Compatible with ACF
Tested with WordPress 5.7

= 1.2.0 =
New: Add WP-CLI and CRON support

= 1.1.7 =
Fixed: Fatal error: Uncaught InvalidArgumentException: The element to connect with doesn't belong to the relationship definition provided
Tested with WordPress 5.5

= 1.1.6 =
Fixed: Notice: Undefined index: module

= 1.1.5 =
Fixed: Notice: register_post_type was called incorrectly. Post type names must be between 1 and 20 characters in length
Fixed: Fields with duplicate names were imported but unusable in WordPress

= 1.1.4 =
Tested with WordPress 5.4

= 1.1.3 =
Tweak: Compatible with FG Drupal to WordPress Premium 1.89.0
Tested with WordPress 5.2

= 1.1.2 =
Tweak: Refactoring

= 1.1.1 =
Fixed: Repeating field groups and collections were not deleted when deleting the imported data
Tested with WordPress 5.1

= 1.1.0 =
New: Migrate all the Drupal 7 collection field types

= 1.0.0 =
Initial version
Migrate the Drupal 7 images collection fields
