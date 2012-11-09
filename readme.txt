=== SmartBroker ===
Contributors: phuvf
Tags: yachts, boats, brokerage, brokers
Donate link: http://www.smart-broker.co.uk
Requires at least: 3.3.0
Tested up to: 3.4.1
Stable tag: 1.2.4

This plugin embeds yacht listings from the SmartBroker service into your WordPress site. Requires a SmartBroker subscription.

== Description ==

This plugin embeds yacht brokerage listings from the SmartBroker service 
([http://www.smart-broker.co.uk](http://www.smart-broker.co.uk/ "A web-based sales tool for yacht and boat brokers"))
into your WordPress site.

In order to use this plugin, you'll require a paid-for account with SmartBroker - please see our 
[pricing page](http://www.smart-broker.co.uk/?page_id=95 "Pricing information for SmartBroker") for more details.

SmartBroker is a system for creating, managing and sharing yacht sales listings.

If you're installing this plugin for testing purposes, there's a demo server with dummy data available - see installation instructions for further details.

== Installation ==

Either install the plugin via Admin->Plugins->Add New, or  

* Upload `smartbroker.zip` to the `/wp-content/plugins/` directory
* Un-zip the file. This will create the smartbroker directory and all the necessry sub-directories

Then:

* Activate the plugin through the 'Plugins' menu in WordPress
* Set the address of your SmartBroker server in `Settings->SmartBroker`
* Create a page for your SmartBroker search - and add the shortcode `[sb_search_page]` to it
* Create a page for the SmartBroker boat listings - and add the shortcode `[sb_listing]` to it
* Update `page-id` values for *search page* and  *listing page* in `Settings->SmartBroker`

> If you want to use a demo server to test your installation, set the server address to `http://demo.smart-broker.co.uk`.
> This is the address of the server used for the *Live Demo* section of the SmartBroker site.
> The authentication token for this server is *vjrxhvmkq67wb14639v5*.

Once you have followed the installation instructions, you can also add the following shortcodes anywhere on your site:

* [sb_featured] - a scrolling carousel feature highlighting some of your listings
* [sb_search_box] - a stand-alone listings search box (similar to that found on the left hand side of the search page)
* [sb_search_box_small] - a simple size-type search box
* [sb_search_by_ref] - a small search-by-reference-number box

**Options**

The following options can be set with [sb_search_box] and [sb_search_box_small]:

* size_low - the pre-set value of the lower size slider, in feet (default: 28, integers only)
* size_high - the pre-set value of the upper size slider, in feet (default: 45, integers only)

For example, the following shortcode will produced a small search box with a pre-set size range of 30-40ft:

*[sb_search_box_small size_low="30" size_high="40"]*

In addition, the following options can be set for [sb_search_box] only:

* price_low - the pre-set value of the lower price slider, in GBP (default: 30,000, integers only)
* price_high - the pre-set value of the upper price slider, in GBP (default: 150,000, integers only)

For example, the following shortcode will produced a search box with a pre-set size range of 40-50ft and a price range of 100,000 - 200,000 GBP:

*[sb_search_box size_low="40" size_high="50" price_low="100000" price_high="200000"]*

The options available for [sb_search_box] can also be used on [sb_listing] (see installation instructions) to set default values if none are passed to it from elsewhere (e.g. from a get request).

**Theming**

This plugin uses the *jQuery UI* themeing framework, and the full set of themes are available to use. For theme samples, please go to the [jQuery UI theme gallery](http://jqueryui.com/themeroller/#themegallery "The jQuery UI theme gallery").

The theme is set in Admin->Settings->SmartBroker.

== Frequently Asked Questions ==

= Huh, what, SmartBroker? - never heard of it! =

A brief overview of the service is available at [http://www.smart-broker.co.uk](http://www.smart-broker.co.uk/ "A web-based sales tool for yacht and boat brokers") 

== Screenshots ==

1. The search box and results page using theme 'start'.
2. A sample boat listing with broker's notes, specifications, photos, videos and contact form, using theme 'start'.
3. The featured boats shortcode using theme 'ui-darkness'.
4. A drop-in search box, using theme 'ui-darkness'.
5. A small search box, using theme 'ui-darkness'.
6. A search-by-reference number box, using theme 'ui-darkness'

== Changelog ==
= 1.2.4 =
* Switched jQuery to noConflict mode to avoid clashes with other libraries
* Fixed error with photo counting where no photos exist

= 1.2.3 =
* Updated instructions on how to find the authentication token

= 1.2.2 =
* Updated verification token details for demo.smartbroker.co.uk in Admin->Settings->SmartBroker

= 1.2.1 =
* Fixed photo issue caused when boat model contains backslash (/) (requires SmartBroker v1.1.3 or above to function correctly)
* Added pagination for search results where > 10 results returned
* Re-aligned boat specification table to vertical-align: top

= 1.2 =
* Plugin will now use cURL to load XML files if allow_url_fopen = false
* Updated XML links to reflect new back-end file structure

= 1.1.2 =
* Fixed error relating to 'VAT paid' & 'VAT not paid' messages
* Removed authentication requirement for demo.smart-broker.co.uk server

= 1.1.1 =

* Fixed JavaScript error caused if page has no shortcodes at all
* Switched to using googleapis.com theme repository

= 1.1 =

* Changed xml requests to use https
* Added [sb_search_box_small] & [sb_search_by_ref] shortcodes
* Added optional settings for [sb_search_box] and [sb_search_box_small]

= 1.0 =
* Initial release

== Upgrade Notice ==
= 1.2 =
This upgrade is required to update links to new SmartBroker file structure.

= 1.1 =
This upgrade secures the data feed from WordPress to SmartBroker. Users of v1.0 should upgrade as soon as possible.

= 1.0 =
Initial release