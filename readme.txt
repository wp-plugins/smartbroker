=== SmartBroker ===
Contributors: phuvf
Tags: yachts, boats, brokerage, brokers
Donate link: http://www.smart-broker.co.uk
Requires at least: 3.3.0
Tested up to: 3.3.1
Stable tag: 1.0

Embeds yacht listings from the SmartBroker service into your WordPress site. Requires a SmartBroker subscription.

== Description ==

This plugin embeds yacht brokerage listings from the SmartBroker service 
([http://www.smart-broker.co.uk](http://www.smart-broker.co.uk/ "A web-based sales tool for yacht and boat brokers"))
into your WordPress site.

Once you have followed the installation instructions, you can also add the following shortcodes anywhere on your site:

* `[sb_featured]` - a scrolling carousel feature highlighting some of your listings
* `[sb_search_box]` - a stand-alone listings search box (similar to that found on the left hand side of the search page)




== Installation ==

* Upload `smartbroker.zip` to the `/wp-content/plugins/` directory
* Un-zip the file. This will create the smartbroker directory and all the necessry sub-directories
* Activate the plugin through the 'Plugins' menu in WordPress
* Set the address of your SmartBroker server in `Settings->SmartBroker`
* Create a page for your SmartBroker search - and add the shortcode `[sb_search_page]` to it
* Create a page for the SmartBroker boat listings - and add the shortcode `[sb_listing]` to it
* Update `page-id` values for *search page* and  *listing page* in `Settings->SmartBroker`

> If you want to use a demo server to test your installation, set the server address to `http://www.firefly-boats.com`.
> This is the address of the server used for the *Live Demo* section of the SmartBroker site.

== Frequently Asked Questions ==

= Huh, what, SmartBroker? - never heard of it! =

A brief overview of the service is available at [http://www.smart-broker.co.uk](http://www.smart-broker.co.uk/ "A web-based sales tool for yacht and boat brokers") 

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif). Note that the screenshot is taken from
the directory of the stable readme.txt, so in this case, `/tags/4.3/screenshot-1.png` (or jpg, jpeg, gif)

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
* Initial release