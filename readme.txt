=== SmartBroker ===
Contributors: phuvf
Tags: yachts, boats, brokerage, brokers
Donate link: http://www.smart-broker.co.uk
Requires at least: 3.3.0
Tested up to: 3.3.1
Stable tag: 1.1

Embeds yacht listings from the SmartBroker service into your WordPress site. Requires a SmartBroker subscription.

== Description ==

This plugin embeds yacht brokerage listings from the SmartBroker service 
([http://www.smart-broker.co.uk](http://www.smart-broker.co.uk/ "A web-based sales tool for yacht and boat brokers"))
into your WordPress site.

In order to use this plugin, you'll require a paid-for account with SmartBroker - please see our 
[pricing page](http://www.smart-broker.co.uk/?page_id=95 "Pricing information for SmartBroker") for more details. 

== Installation ==

* Upload `smartbroker.zip` to the `/wp-content/plugins/` directory
* Un-zip the file. This will create the smartbroker directory and all the necessry sub-directories
* Activate the plugin through the 'Plugins' menu in WordPress
* Set the address of your SmartBroker server in `Settings->SmartBroker`
* Create a page for your SmartBroker search - and add the shortcode `[sb_search_page]` to it
* Create a page for the SmartBroker boat listings - and add the shortcode `[sb_listing]` to it
* Update `page-id` values for *search page* and  *listing page* in `Settings->SmartBroker`

> If you want to use a demo server to test your installation, set the server address to `http://demo.smart-broker.co.uk`.
> This is the address of the server used for the *Live Demo* section of the SmartBroker site.
> You'll need an free authentication token to make this work - get in tuch for details.

Once you have followed the installation instructions, you can also add the following shortcodes anywhere on your site:

* `[sb_featured]` - a scrolling carousel feature highlighting some of your listings
* `[sb_search_box]` - a stand-alone listings search box (similar to that found on the left hand side of the search page)
* `[sb_search_box_small]` - a simple size-type search box
* `[sb_search_by_ref]` - a small search-by-reference-number box

**Options**

The following options can be set with [sb_search_box] and [sb_search_box_small]:

* size_low - the pre-set value of the lower size slider, in feet (default: 28, integers only)
* size_high - the pre-set value of the upper size slider, in feet (default: 45, integers only)

For example, the following shortcode will produced a small search box with a pre-set size range of 30-40ft:

*[sb_search_box_small size_low="30" size_high="40"]*

In addition, the following options can be set for [sb_search_box] only:

* price_low - the pre-set value of the lower price slider, in GBP (default: 30,000, integers only)
* price_high - the pre-set value of the upper price slider, in GBP (default: 150,000, integers only)

For example, the following shortcode will produced a search box with a pre-set size range of 40-50ft and a price range of 100,000 ֠200,000 GBP:

*[sb_search_box size_low="40" size_high="50" price_low="100000" price_high="200000"]*

The options available for [sb_search_box] can also be used on [sb_listing] (see installation instructions) to set default values if none are passed to it from elsewhere (e.g. from a get request).

== Frequently Asked Questions ==

= Huh, what, SmartBroker? - never heard of it! =

A brief overview of the service is available at [http://www.smart-broker.co.uk](http://www.smart-broker.co.uk/ "A web-based sales tool for yacht and boat brokers") 

== Changelog ==
= 1.1 =

* Changed xml requests to use https
* Added [sb_search_box_small] & [sb_search_by_ref] shortcodes
* Added optional settings for [sb_search_box] and [sb_search_box_small]

= 1.0 =
* Initial release

== Upgrade Notice ==
= 1.1 =
This upgrade secures the data feed from WordPress to SmartBroker. Users of v1.0 should upgrade as soon as possible.

= 1.0 =
Initial release