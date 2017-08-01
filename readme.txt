=== EDD Favorites ===
Contributors: easydigitaldownloads, sumobi
Tags: easy digital downloads, digital downloads, e-downloads, edd, sumobi, purchase, wish list, wishlist, favorite, bookmark, e-commerce
Requires at least: 3.3
Tested up to: 4.8
Stable tag: 1.0.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Favorite/Unfavorite downloads in Easy Digital Downloads with just 1 click.

== Description ==

This plugin requires both [EDD Wish Lists](https://easydigitaldownloads.com/extensions/edd-wish-lists/?ref=166 "EDD Wish Lists") & [Easy Digital Downloads](http://wordpress.org/extend/plugins/easy-digital-downloads/ "Easy Digital Downloads"). <strong>It will NOT function without them.</strong>

EDD Favorites is a plugin for EDD Wish Lists that allows your customers to "favorite" downloads. With 1 click (and another click to remove) the download is instantly added to the user's favorites.

**What's the difference between EDD Wish Lists and EDD Favorites?**

With EDD Wish lists, you click the "add to wish list" link and choose a list (or create a new one) from the modal window. It also works with variable pricing. EDD favorites is for quickly adding/removing downloads to a single list with 1 click. It doesn't support variable pricing, meaning you cannot add a specific price to the favorites list, instead it adds the entire download.

**More plugins for Easy Digital Downloads**

You can find more EDD plugins (both free and commercial) on the [Easy Digital Downloads'](https://easydigitaldownloads.com) website.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin


OR you can just install it with WordPress by going to Plugins &rarr; Add New &rarr; and type this plugin's name

Setup the EDD Favorites page and edit page

1. Create a new page where users will view their favorites. Insert the [edd_favorites] shortcode
1. Create a new page where users will edit their favorites (optional). Insert the [edd_favorites_edit] shortcode
1. Select these 2 pages from the EDD settings in Downloads &rarr; Settings &rarr; Extensions

== Screenshots ==

[See more screenshots](https://easydigitaldownloads.com/extensions/edd-favorites/ "See more screenshots")

1. Favorite downloads
2. View your list of favorites
3. Edit your favorites list

== Changelog ==

= 1.0.8 =
* Fix: Replace deprecated function

= 1.0.7 =
* Fix: XSS vulnerability in query args

= 1.0.6 =
Requires: EDD Wish Lists v1.0.8
Fix: Modified query vars as per recent changes in EDD Wish Lists
Fix: EDD favorites now no longer deactivates when EDD is updated

= 1.0.5 =
Requires: EDD Wish Lists v1.0.6
Fix: Compatibility with EDD v1.9.9

= 1.0.4 =
Requires: EDD Wish Lists v1.0.4
Tweak: Added filter to remove CSS class responsible for ajax on view links when there is variable priced downloads in the favorites list

= 1.0.3 =
Requires: EDD Wish Lists v1.0.3
New: Modified the delete link text on edit page to say "Delete favorites". Filter included for changing
Tweak: removed unused code

= 1.0.2 =
Fix: When favorites link text is intentionally left empty in settings, text is no longer shown on front-end
New: edd_favorites_post_status filter for setting default post status when favorites list is created
New: Added documentation link to readme
Tweak: Changed edd_favorites_page_title filter to edd_favorites_post_title
Tweak: Removed unused constants

= 1.0.1 =
Fix: user who didn't have cookie was given a list that already existed

= 1.0 =
* Initial release
