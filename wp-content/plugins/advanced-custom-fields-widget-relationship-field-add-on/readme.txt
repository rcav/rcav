=== Advanced Custom Fields - Widget Relationship Field add-on ===
Contributors: djbokka
Tags: advanced custom fields, widget, widget management, widget filter, widget relationship
Requires at least: 3.3
Tested up to: 3.5.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is an add-on for Advanced Custom Fields. It allows you to use an ACF "relationship" field to choose widgets at a page level.

== Description ==

NOW ACF 3 & 4 COMPATIBLE!

This plugin is an add-on for Advanced Custom Fields. It allows you to use an ACF "relationship" field to choose widgets at a page level.

Inherit widgets from parent post or menu items. Drag and drop to change widget display order.

= Documentation =
https://bitbucket.org/djbokka/widget-relationship-field-add-on-for-advanced-custom-fields

= Bug Submission and Support =
https://bitbucket.org/djbokka/widget-relationship-field-add-on-for-advanced-custom-fields

= Rate this plugin =

If this plugin helps you, please give it a good rating. If you have any problems, please ask me. I'm happy to help.


== Installation ==

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. In `sidebar.php`, replace `dynamic_sidebar()` function with the new `dynamic_widgets()` method.
`if ( ! acf_Widget::dynamic_widgets( 'Side Bar' ) ) {

   //fallback to default function if you like
   dynamic_sidebar( 'Side Bar' );

}`



== Frequently asked questions ==

https://bitbucket.org/djbokka/widget-relationship-field-add-on-for-advanced-custom-fields


== Screenshots ==

1. Configuration on ACF settings

2. Usage at the page level



== Changelog ==

= 1.1 =
* Added ACF 4 structure with backward compatibility to ACF 3.

= 1.0.2 =
* Fixed paging bug (thanks for Dylan Kuhn for pointing it out and providing the solution).

= 1.0.1 =
* Changed javascript functions to account for ACF version 3.5.8 upgrade. Still backward compatible.

= 1.0 =
* Initial Commit.

