=== Widget Builder ===
Contributors: ModernTribe, codearachnid, peterchester, jbrinley
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R8H3DD84PWAQ2
Tags: widget, featured image, simple, sidebar, admin, custom post type, CPT, widget-only, dashboard, dashboard widget
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.6.1

== Description ==

Widget Builder uses native WordPress editing interface to provide a unique tool to build custom widgets for your site(s).

* MU Compatible
* Create admin dashboard widgets __NEW!__
* Link the image
* Title and Description
* Customize "Read More" link text
* Very versatile. All fields are optional.
* Supports override of template so that you can override the template for your theme!

Tested on PHP 5.2.17, 5.3.14 & 5.4.4 and WP 3.3 & 3.4.

This plugin is actively supported and we will do our best to help you. In return we simply as 3 things:

1. Help Out. If you see a question on the forum you can help with or have a great idea and want to code it up and submit a patch, that would be just plain awesome and we will shower you with praise. Might even be a good way to get to know us and lead to some paid work if you freelance.  Also, we are happy to post translations if you provide them.
1. Donate - if this is generating enough revenue to support our time it makes all the difference in the world
https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R8H3DD84PWAQ2
1. Support us by buying our Premium plugins. In particular, check out our Events Calendar Pro http://tri.be/wordpress-events-calendar-pro/

Note: gear vector art used in the WordPress.org banner were created by http://www.opengraphicdesign.com

== Installation ==

= Install =

1. In your WordPress administration, go to the Plugins page
1. Activate the Widget Builder plugin and a subpage for the plugin will appear
   in your Appearance menu.
1. Go to the Appearance > Widget Builder and create the widget
1. Go to the Appearance > Widgets page and activate the new widget you created!

If you find any bugs or have any ideas, please mail us.

Please visit the forum for questions or comments: http://WordPress.org/tags/widget-builder/

= Requirements =

* PHP 5.1 or above
* WordPress 3.0 or above

== Documentation ==

= Dashboard Widgets =

Select 'Available As Dashboard Widget' in the widget editor to enable a widget as a dashboard widget. If you do not want this widget showing in the available widgets list for sidebar placement, select 'Disable Sidebar Widget'.

= Default vs. Custom Templates =

The built in template can be overridden by files within your template.

The Widget Builder comes with a default template for the widget output. If you would like to alter the widget display code, create a new folder called "tribe_widget_builder" in your template directory and copy over the "views/widget.php" file.

Edit the new file to your hearts content. Please do not edit the one in the plugin folder as that will cause conflicts when you update the plugin to the latest release.

Alternatively you can point to a path of your choosing using the filter 'tribe_widget_builder_widget.php'.

= Filter widget query args =

Filter your query arguments or get_posts altogether for granular fine tuning your listing of widgets or in the case of MU install restricting the builder to one site.

The following filters are available for override
`'tribe_widget_builder_get_posts_args' // customize the widget query parameters
'tribe_widget_builder_get_posts' // change the get_posts() query`

== Changelog ==

= 1.6.1 =

* Fix missing data bug when using in especially convoluted multisite arrangments

= 1.6 =

* Added option to specify link targets

= 1.4 =

* Added dashboard widget availability
* Added disable as sidebar widget option
* Created new widget_dashboard.php view template

= 1.3 =

* Added option in widget to turn off display of the header title (props to empathik)
* Resolved get_template_hierarchy trimming file extension
* Resolved issue with translation file not being loaded in the /lang folder
* Added es_ES lang translation thanks to elarequi

= 1.2 =

* Integrate into the existing $wp_widget_factory global, respecting the singleton intent
* Add caching to prevent extra queries on every page load
* Misc bug fixes

= 1.1 =

* CPT admin: remove the preview changes button.
* CPT admin: remove 'view post' link in update message in the yellow after saving a widget post.
* Remove publish date or visibility for the widget.
* Add language support so that people can contribute translations (Please feel free to send us translations)
* Misc bug fixes.

= 1.0 =

* Initial plugin release

== Screenshots ==

1. Widget Builder create widget screen.
1. List Widget Builder editable widgets.
1. Highlighting custom widgets in the Widgets/Sidebar page.
1. How the default display template looks in presentation.
1. How the default display template looks in the dashboard (when enabled).

== Frequently Asked Questions ==

= Where do I go to file a bug or ask a question? =

Please visit the forum for questions or comments: http://WordPress.org/tags/widget-builder/
