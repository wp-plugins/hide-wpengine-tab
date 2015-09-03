=== Plugin Name ===

Contributors: Aaron Vanderzwan
Donate link: http://www.aaronvanderzwan.com/
Tags: wpengine, staging, hide, tab
Requires at least: 3.0
Tested up to: 4.3
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


== Description ==

WPEngine is a fantastic Wordpress hosting provider with an absolutely fantastic function - the one click staging environment.  This plugin is built to make it super easy to limit access to that button so that people don't accidentally overwrite the staging environment.


== Installation ==

1. Upload 'hide-wpengine-tab' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the new menu item "Hide WPEngine Tab" under settings!
4. Add a comma separated list of users that should have access to the tab.  (Note: wpengine gets added automatically.)


== Frequently Asked Questions ==

None yet.  This plugin is pretty self explanatory.


== Screenshots ==

1. Settings page


== Changelog ==

= 1.1.2 =
* Updated to run on Wordpress 4.3

= 1.1.1 =
* Added urlencoding so that special characters can be used in lock messages.

= 1.1 =
* Added ability to lock the staging environment so that it cannot be rebuild without unlocking. This is done by capturing the click on the "Create staging area" button and displaying a friendly message letting the user know it's locked, who locked it, and when.

= 1.0.1 =
* Updated descriptions

= 1.0 =
* Wrote plugin


== Upgrade Notice ==

Nothing yet.