=== FX Builder ===
Contributors: butterflymedia
Tags: page builder, drag, drop, sortable, columns
Requires at least: 2.0
Tested up to: 2.2.0
Requires PHP: 7.2
Stable tag: 1.3.0
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A simple page builder plugin. The one you can actually use.

== Description ==

**FX Builder** is a page builder plugin that provides visual columns in the post editor without using shortcodes. From the settings page, you can enable it for posts, pages, and custom post types. There is no vendor lock-in.

**Features:**

1. Super simple to use.
2. Visual columns in the post editor.
3. If you disable the plugin, you content is not lost.
4. Settings to enable/disable for each post type.
5. 1-4 columns supported.
6. Free, Open Source, GPL v3.0 or later license. Use it to make something cool :)

== Installation ==

1. Navigate to "Plugins > Add New" Page from your Admin.
2. To install from plugin .zip file, click "Upload Plugin" button in "Plugins > Add New" Screen. Browse the plugin .zip file, and click "Install Now" button.
3. Activate the plugin.
4. Navigate to "Settings > FX Builder" to configure.
5. Edit your content.

== Changelog ==

= 1.3.0 =
* Remove jQuery UI Sortable dependency and replace with SortableJS
* Remove old, unused files
* Update plugin branding to be consistent with "FX Builder"

= 1.2.3 =
* Fix author link
* Add system fonts to TinyMCE

= 1.2.2 =
* Fix undefined variable
* Add image styling to wraps to make sure images are not wider than content
* Remove "Wide" content width, as it's not relevant, and can be added as a class

= 1.2.1 =
* Add font size presets, custom font size and font weigh to TinyMCE
* Clean up row and item templates
* Clean up front-end styles
* Clean up readme.txt

= 1.2.0 =
* First public release for ClassicPress

= 1.0.2 - 13 Ags 2017 =
* Fix bug: WP 4.8 compat (thanks to nick6352683)

= 1.0.1 - 06 Jan 2017 =
* Fix bug: wp preview remove FX Builder data
* Fix CSS Bug: Tools & CSS button position
* Store _fxb_active in revisions
* Hide "Preview Changes" button in "Publish" meta box when FX Builder is active
* Fix "save_post" hook to fix fatal error bug

= 1.0.0 - 09 Dec 2016 =
* First stable Release

= 1.0.0.beta - 12 Oct 2016 =
* Browser compatibility (Tested with: Firefox, Chrome, IE11, Edge, Android Phone, Tablet)
* Use Admin Color
* UI and UX Improvement
* RTL Support
* Use HTML Class for Front End Output + Update Front CSS

= 1.0.0.alpha3 - 29 Sep 2016 =
* Revision: Revert to Revision now also revert to FX Builder data
* Remove disable editor feature. Simpler Settings
* Change settings option name. You need to re-configure settings
* Page is enabled by default
* Introduce Tools: Export / Import FX Builder data
* Auto focus to editor when open editor modal
* Fix Custom CSS textarea line break
* Fix several small-screen accessibility issue in modal box

= 1.0.0.alpha2 - 23 Sep 2016 =
* Add row with thumbnail for each layoout
* Welcome notice with link to admin page
* Improve overall builder design
* Fix overflow in editor modal
* Add post class "fx-builder-entry" when active
* Reset undo in editor modal
* Custom CSS for each posts
* Other small fixes and improvements

= 1.0.0.alpha - 21 Sep 2016 =
* Initial alpha release
