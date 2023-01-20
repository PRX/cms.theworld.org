=== RSS Feed Pro ===

Contributors: Artiosmedia, sindhwal
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=E7LS2JGFPLTH2
Tags: rss, podcast, feed, streaming, rss feed, itunes, MP3, Castos
Requires at least: 3.0.1
Tested up to: 6.0
Version: 1.1.1
Stable tag: 1.1.1
Requires PHP: 5.6.2
License: GPLv3 or later license and included
URI: http://www.gnu.org/licenses/gpl-3.0.html

Display an RSS Feed in a widget, on a page or post by shortcode using any number of parameters. Provide users a means to sort your RSS feed archive by Category, Year, and by Author Name.

== Description ==

Wordpress comes with a default site-wide RSS feed but it isn’t sufficient when it comes to podcasts, where it only shares blog posts. RSS Feed Pro is a full feature podcasting plugin designed for both beginners and power-users. You setup your preferred podcast hosting, the plugin delivers to your Wordpress build.  

The plugin is an upgraded and advanced revision of Frank Bültge's RSSImport. RSS Feed Pro will display feeds in your blog, using PHP, a widget or shortcode. If you uninstall RSSImport and install RSS Feed Pro, you should not loose any of your current configurations. The plugin uses only standard WordPress functionality therefore no external libraries are required. As with all other content you publish, make sure you are allowed to stream the content of the feeds you are going to import.

To create a shortcode for sorting your RSS feeds archive catalog, please activate the plugin then click on RSS Archives > Add New Under "Shortcode Details". Add the feed URL then the user can select their sort mode (By Year, By Category and By Author Name)

To use the RSS Feed Pro widget, please go to widgets and find a widget called "RSS Feed Pro". Check the boxes for the shortcodes that you'd like to use for your widget. See Installations tab for usage examples and all the shortcodes. Includes English, French and Spanish languages.

= Acknowledgements =

Based on Frank Bültge RSSImport plugin that was no longer supported. Thanks to [Dave Wolf](http://www.davewolf.net, "Dave Wolf") for the original idea, to [Thomas Fischer](http://www.securityfocus.de "Thomas Fischer") and [Gunnar Tillmann](http://www.gunnart.de "Gunnar Tillmann") for code enhancements in the original build up to version 4.6.1 and Ilya Shindyapin, http://skookum.com for the idea and solution of pagination before version 4.6.1.

= License =

This advance fee plugin is public domain. Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. However, if you have gain value from this plugin, you can thank us by leaving a [donation](https://www.paypal.com/donate/?cmd=_s-xclick&hosted_button_id=E7LS2JGFPLTH2 "Support the Needy") which will support the needy globally.

== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/plugin-name' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Access the 'Admin Panel > RSS Shortcodes' to create any shortcode you might need. We suggest Sort by Author, Sort by Category, Sort by Year.

You can insert the following code into a PHP plugin or in a template, for example `sidebar.php` or `single.php`:

_Example:_
`&lt;?php rssfp_import(10, 'https://yourdomain.com/feed/'); ?&gt;`

This is the minimal code needed for using the plugin. The plugin accepts a number of parameters for customizing the feed content import. See below for the full list of available parameters. You can also use any of the parameters with Shortcode in posts and pages.

_Example Shortcode:_
`[RSSImport display="5" feedurl="https://yourdomain.com/feed/" use_simplepie="true"]`

For all (bool) parameters you can either use the strings `true` and `false` or the integer values `1` and `0`.

1. `display` - The number of items to display. Default is `5`.
2. `feedurl` - The feed address. Default is `https://yourdomain.com/feed/`.
3. `before_desc` - The HTML or string to insert before the description. Default is `empty`. You can use some variables which will be replaced, see below.
4. `displaydescriptions` - (bool) When set to true, the description for each entry will be displayed. Default is `false`.
5. `after_desc` - The HTML or string to insert after the description. Default is `empty`. You can use some variables which will be replaced, see below.
6. `html` - (bool) When set to true, the description can include HTML tags. Default is `false`.
7. `truncatedescchar` - The maximum number of characters allowed in descriptions. If the description is longer than this length, it will be truncated to the given length. Default is `200`, set the value to empty quotes `''` to never truncate descriptions.
8. `truncatedescstring` - The HTML or string to insert at the end of a description after it has been truncated. Default is ` ... `
9. `truncatetitlechar` - The maximum number of characters allowed in titles. If the title is longer than this value, it will be truncated to the given length. Default is `''`, which means never truncate titles.
10. `truncatetitlestring` - The HTML or string to insert at the end of a title after it has been truncated. Default is `' ... '`.
11. `before_date` - The HTML or string to insert before the date. Default is ` <small>`.
12. `date` - (bool) If true, display the date of the item. Default is `false`.
13. `after_date` - The HTML or string to insert after the date. Default is `</small>`.
14. `date_format`- The date format string. Leave empty to use the format of your WordPress installation. If a different date format is desired, specify a PHP date string, for example: `F j, Y`. See also [the date and time formatting page in the WordPress Codex](https://codex.wordpress.org/Formatting_Date_and_Time).
15. `before_creator` - The HTML or string to insert before the creator of the item. Default is ` <small>`.
16. `creator` - (bool) If true, display the creator of the item. Default is `false`.
17. `after_creator` - The HTML or string to insert after creator of the item. Default is `</small>`.
18. `start_items` - The HTML or string to insert before the list of items. Default is `<ul>`.
19. `end_items` - The HTML or string to insert after the list of items. Default is `</ul>`.
20. `start_item` - The HTML or string to insert before each item. Default is `<li>`. You can use some variables which will be replaced, see below.
21. `end_item` - The HTML or string to insert after each item. Default is `</li>`. You can use some variables which will be replaced, see below.
22. `target` - The string to use for the `target` attribute on links. Default is `empty`. Valid options are `blank`, `self`, `parent`, `top`.
23. `rel` - The string to use for the `rel` attribute on links. Default is `empty`. Valid options are `nofollow` and `follow`.
24. `desc4title` - The description to use in the `title` attribute on item title links. Default is `false`.
25. `charsetscan` - (bool) If true, scan the feed content for the correct character set. This may cause the content to load more slowly. Use this option if you're having problems with feed content being displayed with stranged characters. Default is `false`.
26. `debug` - (bool) If true, activate debug-mode, which will echo the Magpie object as an array. Default is `false`. Only use this option for debugging.
27. `before_noitems` - The HTML or string to insert before the no items message. Default is `<p>`.
28. `noitems`- The message to display when the feed is empty. Default is `No items, feed is empty.`.
29. `after_noitems` - The HTML or string to insert before the no items message. Default is `</p>`.
30. `before_error` - The HTML or string to insert before the error message. Default is `<p>`.
31. `error` - Error message displayed when there is an error loading or displaying the feed. Default is `Error: Feed has an error or is not valid`.
32. `after_error` - The HTML or string to insert before the error message. Default is `</p>`.
33. `paging` - (bool) If true, enable pagination. Default is `false`.
34. `prev_paging_link` - The name of the previous page link. Default is `&laquo; Previous`.
35. `next_paging_link` - The name next page link. Default is `Next &raquo;`.
36. `prev_paging_title` - The title attribute of the previous page link. Default is `more items`.
37. `next_paging_title` - The title attribute of the next page link. Default is `more items`.
38. `use_simplepie` - (bool) If true, use SimplePie to parse the feed. SimplePie is included in WordPress 2.8 and newer and can parse both RSS and ATOM feeds. Default is `false` if used with Shortcode, `true` if used with the PHP function.
39. `view` - (bool) If true, calling the `rssfp_import()` function will print the rendered HTML directly to the output. If false, the rendered HTML will be returned by the function as a string value and nothing will be output. Default when using PHP code is `true`. Default when using Shortcode is `false`.
40. `random_sort` - (bool) If true, Items will be displayed in random order. Default when using Shortcode is `false`.
41. `order` - (string) Order of the filds Date, Title, Creator, Description. Use a comma separated string for your order. Default is `date,title,creator,description`

The parameters `before_desc`, `after_desc`, `start_item` and `end_item` accepts the following variables which will be replaced:

1. `%title%` for the title of the entry
2. `%href%` for the entry's URL
3. `%picture_url%` for the URL of a thumbnail image for the entry if available. To use this variable, SimplePie is required to be enabled (`use_simplepie="true"`)


If pagination is enabled, it adds a `div` with the class `rsspaging` to enable easier styling with CSS. You can also style the previous and next links, which have the classes: `rsspaging_prev` and `rsspaging_next`.

You can use any of the parameters in the php function `rssfp_import` in your templates or with the Shortcode `[RSSImport]` in posts and pages.

= Examples =
_Using the PHP function with many parameters:_

	rssfp_import(
		$display = 5, $feedurl = 'https://bueltge.de/feed/', 
		$before_desc = '', $displaydescriptions = false, $after_desc = '', $html = false, $truncatedescchar = 200, $truncatedescstring = ' ... ', 
		$truncatetitlechar = '', $truncatetitlestring = ' ... ', 
		$before_date = ' <small>', $date = false, $after_date = '</small>', 
		$before_creator = ' <small>', $creator = false, $after_creator = '</small>', 
		$start_items = '<ul>', $end_items = '</ul>', 
		$start_item = '<li>', $end_item = '</li>'
	);

Please note that for the PHP function the parameters are expected in the order in which they are defined in the above list. Thus if you skip one parameter, you will also have to skip all of the subsequent parameters.

_Using Shortcode with several parameters:_

	[RSSImport display="10" feedurl="https://your_feed_url/" 
	displaydescriptions="true" html="true" 
	start_items="<ol>" end_items="</ol>" paging="true" use_simplepie="true"]

_Add a "more" link to the output:_

	rssfp_import(
		$display = 5,
		$feedurl = 'https://yourdomain.com/feed/', 
		$before_desc = '',
		$displaydescriptions = true,
		$after_desc = ' <a href="%href%" target="_blank">show more</a>'
	);

or

	[RSSImport feedurl="https://wordpress.org/news/feed/" after_desc=" <a href='%href%' target='_blank'>show more</a>" displaydescriptions="true" use_simplepie="true"]

_Enable Thumbnail Pictures:_

	rssfp_import(
		$display = 5,
		$feedurl = 'https://bueltge.de/feed/',
		$before_desc = '<img src="%picture_url%" alt="">',
		$displaydescriptions = true
	);

or

	[RSSImport feedurl="https://wordpress.org/news/feed/" displaydescriptions="true" before_desc="<div><img src='%picture_url%' width='50px' alt='' style='float:left;' />" after_desc="</div>" use_simplepie="true"]

== Technical Details for Release 1.1.1 ==

Load time: 0.271 s; Memory usage: 3.41 MiB
PHP up to tested version: 8.1.5
MySQL up to tested version: 8.0.29
cURL up to tested version: 7.83.1, OpenSSL/3.0.3
PHP 5.6, 7.3, 7.4, and 8.0 compliant.

== Frequently Asked Questions ==

= Is this plugin frequently updated to Wordpress compliance? =
Yes, attention is given on a staged installation with many other plugins via debug mode.

= Is the plugin as simple to use as it looks? =
Yes. No other plugin exists that adds a RSS stream easily with so many options.

= Has there ever any compatibility issues? =
To date, none have ever been reported.

= What podcast hosting services does this plugin work with? =
Updated especially for Castos, it works with Buzzsprout, Captivate, Transistor, Podbean, Simplecast, and Resonate just to name a few. 

= Is the code in the plugin proven stable? =
Please click the following link to check the current stability of this plugin:
<a href="https://plugintests.com/plugins/rss-feed-pro/latest" rel="nofollow ugc">https://plugintests.com/plugins/rss-feed-pro/latest</a>

== Screenshots ==

1. RSS Archive Shortcode Settings Table
2. Archive Shortcodes Added with Block Editor and Results
3. RSS Widget Fields Shown in Order, Left to Right Part 1
4. RSS Widget Fields Shown in Order, Left to Right Part 2
5. RSS Widget Fields Shown in Order, Left to Right Part 3

== Upgrade Notice ==

None to report as of the release version

== Changelog ==

1.1.1 05/23/22
- Text edits along with translations
- Assure compliance with WordPress 6.0

1.1.0 02/08/2022
- Update: Added 'remove_link' parameter to RSSImport shortcode.
- Update: Setting remove_link='1' will remove permalink from feed
- Update: Compatibility for Wordpress 5.9
- Update: All language files
- Fixed: Fixed error that prevent some feeds from working properly
- Fixed: Fixed some PHP and WordPress warning
- Fixed: Update some old code to prevent conditional error
- Assure current stable PHP 8.1.1 use

1.0.9 05/14/2021
- Update: Edit for compatibility with Wordpress 5.7.2

1.0.8 09/24/20
- Fix a JavaScript error
- Fix a PHP generated warning

1.0.7 09/23/20
- Fix sanitization functions to escape
- Change unique function names to avert conflicts
- Add a nounce check for trigger submission checks

1.0.6 09/22/20
- Add Shortcode table with settings
- Add date, category and author archive options
- Add archive results modal popup within feed page 
- Add language support including Spanish and French
- Update: Compliance with PHP 7.4.8 backward to 5.6.2
- Update: Edit for compatibility with Wordpress 5.5.1

1.0 08/15/20
- Rebuild RSSImport 4.6.1
