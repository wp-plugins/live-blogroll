=== Live Blogroll ===
Contributors: freediver
Donate link: https://www.networkforgood.org/donation/MakeDonation.aspx?ORGID2=510144434
Tags:  ajax, sidebar, links, blogroll, bookmarks, ajax, jquery
Requires at least: 2.5
Tested up to: 2.9.1
Stable tag: trunk

Shows a number of 'recent posts' for each link in your Blogroll in a popup box, using Ajax.


== Description == 

Live Blogroll will make your blogroll livelier. It will show a number of 'recent posts' for each link in your Blogroll using Ajax.

When the user hovers the mouse above the link, RSS feed from the site is automatically discovered and a number of recent posts is shown dynamically in a box.

Live BlogRoll uses internal caching for feed discovery and WordPress caching for RSS feeds to make sure everything is smooth for the user.

The looks of the hover box are fully customizable with CSS, and the position is editable in the options.

Plugin by Vladimir Prelovac. In need of <a href="http://www.prelovac.com/vladimir/services">WordPress Consulting Services</a>?

== Changelog ==

= 0.6.2 =
* Rewrite of the RSS engine (credits to Christopher G. Stach II http://ldsys.net/~cgs/)
* Fixes validation issues

= 0.5.2 =
* Updated to 2.8.4 support

= 0.5.1 =
* fixed an error in feed display

= 0.5 =
* Security update


== Installation ==

1. Upload the whole plugin folder to your /wp-content/plugins/ folder.
2. Go to the Plugins page and activate the plugin.
3. Use the Options page to change your options


== Screenshots ==

1. Live Blogroll on a blog


== License ==

This file is part of Live Blogroll.

Category Search is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

Category Search is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with Category Search. If not, see <http://www.gnu.org/licenses/>.


== Frequently Asked Questions ==

= How does it work? =

Live Blogroll uses Ajax to dynamically retrieve lasts posts sites in your Blogroll. The posts are then displayed in a popup hover box.

Live Blogroll will first try to search for rss feed link supplied in your blogroll data. If not found it will load the target page and try to autodiscover the feed. If found this feed will be filled in to your blogroll entry for later faster access.

= Live Blogroll does not show a preview for some of my sites, why is that?

The site may not have the RSS feed listed in it's HTML. Or simply it is unavailable at the moment. 

= Can I suggest an feature for the plugin? =

Of course, visit <a href="http://www.prelovac.com/vladimir/wordpress-plugins/live-blogroll#comments">Live Blogroll Home Page</a>

= I love your work, are you available for hire? =

Yes I am, visit my <a href="http://www.prelovac.com/vladimir/services">WordPress Services</a> page to find out more.