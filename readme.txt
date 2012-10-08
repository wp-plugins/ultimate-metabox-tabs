=== Ultimate Metabox Tabs ===
Contributors: SilbinaryWolf
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=jake_1012%40hotmail%2ecom&lc=AU&item_name=SilbinaryWolf&currency_code=AUD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: tabs, ultimate, metabox, tabs, sorting, clean, fast, easy, metabox tabs, admin, advanced, custom, fields, addons, easy, div, posts, pages, options, metaboxes
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 3.4.2
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

4. The tabs in action on the Advanced Custom Fields options page. Shows multiple boxes.

5. The second tab selected on the Advanced Custom Fields page, the other boxes are hidden, and now new boxes appear.

6. The admin editing page for the previous 2 screenshots again.

== Installation ==

1. Upload 'ultimate-metabox-tabs' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on the new menu item "Settings -> Metabox Tabs" and start sorting out your metaboxes.
4. You'll find the post types of which the metaboxes can be sorted on, as well as Global Options, which will affect ACF Options and all post types.
5. Edit the desired post type/options and procede to adding tab groups, to find out your metaboxes DIV ID, go to the page using it, and inspect element it.

= Tested on =
* Windows Chrome
* Windows Firefox
* Mac Chrome
* Mac Firefox
* Windows Internet Explorer 8

= Bug Submission =
Contact me at doogie1012@gmail.com

== Frequently Asked Questions ==

Q. The metabox tabs are buggy with certain plugins, either not appearing or having weird behaviour. Why?

A. As of now, on post types at least, Metabox tabs inserts itself just before the content, and pushes itself up via the CSS, this means things that attempt to hide the content may interfere. If you're a developer who knows a better filter/hook location, please contact me.

Q. Why have you allowed disabling and enabling ACF options and the hide content patch?

A. In the case where I stop supporting this plugin and they break ACF after an update, you can now toggle them off.

Q. How do I make the content only available on a certain tab?

A. Type "+the_content" without the quotations. This is a special custom function.

Q. Custom Functions? What if I want to write my own?

A. umt_custom_inactive-{slug} and umt_custom_active-{slug} hooks. I recommend you check out the ultimate-metabox-tabs.php file to see how I do it for "the_content". Just keep in mind the "+" represents a function, not a div.

Q. I want to write a custom metabox tab settings page. How?

A. Check out api.php and extensions/acf/acf_options_mod.php, If there is more demand for extensions, I'll write more thorough documentation.

Q. How do I change the ordering of the tabs between Global Options and certain post types?

A. Short answer is, you can't as of yet. Global options will always be in front of posts/option pages.

== Upgrade Notice ==
None necessary.

== Changelog ==

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