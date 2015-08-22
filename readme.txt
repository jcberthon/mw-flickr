=== Magical World Flickr Plugin ===
Contributors: jcberthon
Tags: gallery, flickr, widget
Requires at least: 2.8
Tested up to: 4.3
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Display photos for Flickr (filtering possible) in a widget, one can place in a compatible Theme.

== Description ==

Display photos for Flickr (filtering possible) in a widget, one can place in a compatible Theme.

== Installation ==

1. Upload `jcb_flickr_widget.php` and `index.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. You can now use the widget in themes that support those

== Changelog ==

= 1.1 =
* Try to be smarter with preconnect when using the older API (Flickr Badge)
* New API, directly connect to Flickr API (work-in-progress)
 * Only pseudo-random is developed with square images
 * Optimise page load by using asynchronous javascript execution (HTML5)

= 1.0 =
* Initial release
* Widget that displays photos from Flickr.
 * Only user photos are supported,
 * Possible to filter by tag,
 * Customisable: number of photo, random/latest, size
