=== Ultimate Metabox Tabs ===
Contributors: SilbinaryWolf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jake_1012%40hotmail%2ecom&lc=AU&item_name=SilbinaryWolf&currency_code=AUD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: tabs, ultimate, metabox, tabs, sorting, clean, fast, easy, metabox tabs, admin, advanced, custom, fields, addons, easy, div, posts, pages, options, metaboxes
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 0.9.9
License: GPLv2 or later

Ultimate Metabox Tabs allows for keeping your post/page and ACF options user friendly for your client with the use of tabs.

== Description ==

Ultimate Metabox Tabs adds metabox tabs to your posts, pages and the Advanced Custom Fields options page.
They allow you to easily allow tabbable and sortable metaboxes on your editing page so that your client can do less scrolling and more editing.
They're also inserted at server-time, meaning no ugly javascript forcing the metaboxes into the page.

== Screenshots ==

1. The tabs in action. This illustrates their insertion just underneath the "Edit Post" heading.

2. The second tab is clicked, it now shows that there is a metabox available for editing and that the content can be hidden.

3. The admin editing page for the previous 2 screenshots. It shows that you need the Div ID of the metaboxes and that you can also hide the content with +the_content

== Installation ==

1. Upload 'ultimate-metabox-tabs' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the new menu item "Settings -> Metabox Tabs" and start sorting out your metaboxes.
4. You'll find the post types of which the metaboxes can be sorted on, as well as Global Options, which will affect ACF Options and all post types.
5. Edit the desired post type/options and procede to adding tab groups, to find out your metaboxes DIV ID, go to the page using it, and inspect element it. In the latest update, you can now just select an ACF post in a select box.

= Tested on =
* Windows Chrome
* Windows Firefox
* Windows Safari
* Mac Chrome
* Mac Firefox
* Windows Internet Explorer 8

= Bug Submission =
Contact me at doogie1012@gmail.com

== Frequently Asked Questions ==

Q. The metabox tabs are buggy with certain plugins, either not appearing or having weird behaviour. Why?

A. As of now, on post types at least, Metabox tabs inserts itself just before the content, and pushes itself up via the CSS, this means things that attempt to hide the content may interfere. This can be averted by going to the "Patches" menu and applying the Edit Advanced Forms patch.

Q. Why have you allowed disabling and enabling ACF options and the hide content patch?

A. In the case where I stop supporting this plugin and they break ACF after an update, you can now toggle them off.

Q. How do I make the content only available on a certain tab?

A. Type "+the_content" without the quotations. This is a special custom function. You can now also use the select box to just select it.

Q. Custom Functions? What if I want to write my own?

A. Check out umt_add_custom_command() in api.php (root folder of the plugin)

Q. I want to write a custom metabox tab settings page. How?

A. Check out api.php and extensions/acf/acf_options_mod.php, If there is more demand for extensions, I'll write more thorough documentation.

== Upgrade Notice ==
* From 0.9.4 onward, I suggest you delete the 'addons' folder, as it has been renamed to 'extensions' and that is used from now on. It's just a waste of a few kb, it won't harm anything.

== Changelog ==

= 0.9.9 =
* Added Shopp support.
* Added new filter to metabox_validate. (umt_filter_metabox_screen)
* Fixed Options Page extension to work with ACF v4

= 0.9.8 =
* Added is_admin() check to avoid loading Metabox Tabs if not in admin menu
* Small UI changes made to accomdate for translated editions of Metabox Tabs.

= 0.9.7 =
* Added a patching mechanism that edits the core Wordpress files, allow for greater plugin compatibility. (qTranslate is supported with the patch enabled)
* Changed the UI, its much neater and easier to look at now.

= 0.9.6 =
* Fixed a bug with the ACF post list. (Wouldn't show all posts)
* Slight interface bug with moving DIV IDs

= 0.9.5 =
* Added a select box extension, for easy configuration with ACF.
* Added a new extension API command, so that custom metabox selections can be created.

= 0.9.4 =
* Added extensions API, which will allow for custom settings pages.
* Added a patch extension (toggeable) which allows ACF's "Hide Content" option to work.
* Allowed the toggling of the ACF Options Page metatabs, in case of users not wanting them there or future ACF update breaks.

= 0.9.3 =
* Fixed a bug in the umt-post.js that caused saving to work oddly.

= 0.9.2 =
* Fixed a bug in the javascript that stopped Firefox from working.

= 0.9.1 = 
* Fixed invalid script/style hooks in ACF Options Page.

= 0.9.0 =
* Internal Beta release.