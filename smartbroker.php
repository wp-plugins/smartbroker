<?php
/*
Plugin Name: SmartBroker
Plugin URI: http://www.smart-broker.co.uk
Description: A plugin to insert SmartBroker data into a Wordpress site
Version: 3.0
Author: Nick Roberts
Author URI: http://www.smart-broker.co.uk
License: GPL2

Copyright 2012  Nick Roberts  (email: contact@smart-broker.co.uk)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

include_once('search_v2.php');

//config variables
$sb_config= get_option('sb_plugin_options');
if ($sb_config['currency_1'] == '') {
	$sb_config['currency_1'] = 'EUR';
	}
if ($sb_config['currency_2'] == '') {
	$sb_config['currency_2'] = 'GBP';
	}
if ($sb_config['tax_label'] == '') {
	$sb_config['tax_label'] = 'VAT';
	}
if ($sb_config['listing_default_tab'] == '') {
	$sb_config['listing_default_tab'] = 'sb_brokers_notes';
	}
$valid_theme_names = array('ui-lightness', 
							'ui-darkness',
							'smoothness',
							'start',
							'redmond',
							'sunny',
							'overcast',
							'le-frog',
							'flick',
							'pepper-grinder',
							'eggplant',
							'dark-hive',
							'cupertino',
							'south-street',
							'blitzer',
							'humanity',
							'hot-sneaks',
							'excite-bike',
							'vader',
							'dot-luv',
							'mint-choc',
							'black-tie',
							'swanky-purse');
if (!in_array($sb_config['theme'], $valid_theme_names)) {
	$sb_config['theme'] = 'ui-lightness';
	}
	
$sb_config['video_link'] = '<iframe class="video" width="200" height="115" src="http://www.youtube-nocookie.com/embed/%s?rel=0&wmode=opaque&modestbranding=1&showinfo=0&theme=light" frameborder="0" allowfullscreen></iframe>';

//actions we're going to use
add_action('admin_menu', 'my_plugin_menu');
add_action('admin_init', 'sb_plugin_admin_init');

//the shortcodes we use
add_shortcode('sb_listing', 'sb_listing_func' );
add_shortcode('sb_search_page', 'sb_search_page_func');
add_shortcode('sb_featured', 'sb_featured_func');
add_shortcode('sb_search_box_v2', 'sb_search_box_v2_func');
add_shortcode('sb_search_box_small', 'sb_search_box_small_func');
add_shortcode('sb_search_by_ref', 'sb_search_by_ref_func');
add_shortcode('sb_search_page_v2', 'sb_search_page_v2_func');
 
//code to hide listing page link
add_action('wp_head', 'hide_listing_page');
add_action('wp_head', 'add_smartbroker_custom_css');

function hide_listing_page() {
	global $sb_config;
	echo "<style>.page-item-".$sb_config['listing_page']." {display: none;}
	body.page-id-".$sb_config['listing_page']." .recent-posts {display: none;}
	body.page-id-".$sb_config['search_page']." .recent-posts {display: none;}</style>";
	}
	
function add_smartbroker_custom_css () {
	global $sb_config;
	echo "<style>\r\n/*Added by SmartBroker plugin*/\r\n";
	echo $sb_config['css'];
	echo "</style>";
	}

function sb_plugin_admin_init(){
	register_setting('sb_plugin_options', 'sb_plugin_options', '' );
	add_settings_section('sb_server_settings', 'SmartBroker server settings', 'sb_server_section_text', 'sb_plugin');
	add_settings_section('sb_page_settings', 'Page settings', 'sb_page_section_text', 'sb_plugin');
	add_settings_section('sb_theme_settings', 'Theme settings', 'sb_theme_section_text', 'sb_plugin');
	add_settings_section('sb_tax_settings', 'Tax settings', 'sb_tax_section_text', 'sb_plugin');
	add_settings_section('sb_currency_settings', 'Currency settings', 'sb_currency_section_text', 'sb_plugin');
	add_settings_section('sb_contact_settings', 'Contact settings', 'sb_contact_section_text', 'sb_plugin');
	add_settings_section('sb_css_settings', 'Custom CSS settings', 'sb_css_section_text', 'sb_plugin');
	
	add_settings_field('sb_server_address', 'SmartBroker server address', 'sb_server_address_string', 'sb_plugin', 'sb_server_settings');
	add_settings_field('sb_auth', 'Server authentication token', 'sb_auth_string', 'sb_plugin', 'sb_server_settings');
	
	add_settings_field('sb_search_page_v2', 'SmartBroker search page v2 ID', 'sb_search_page_v2_string', 'sb_plugin', 'sb_page_settings');
	add_settings_field('sb_listing_page', 'SmartBroker listing page ID', 'sb_listing_page_string', 'sb_plugin', 'sb_page_settings');
	add_settings_field('sb_listing_default_tab', 'Listing default tab', 'sb_listing_default_tab_string', 'sb_plugin', 'sb_page_settings');
	
	add_settings_field('sb_theme', 'Theme', 'sb_theme_string', 'sb_plugin', 'sb_theme_settings');
	
	add_settings_field('sb_currency_1', 'Primary currency', 'sb_currency_1_string', 'sb_plugin', 'sb_currency_settings');
	add_settings_field('sb_currency_2', 'Secondary currency', 'sb_currency_2_string', 'sb_plugin', 'sb_currency_settings');
	
	add_settings_field('sb_hide_tax_label', 'Hide tax label', 'sb_hide_tax_label_string', 'sb_plugin', 'sb_tax_settings');
	add_settings_field('sb_tax_label', 'Tax label', 'sb_tax_label_string', 'sb_plugin', 'sb_tax_settings');
	
	add_settings_field('sb_email', 'Email address', 'sb_email_string', 'sb_plugin', 'sb_contact_settings');
	add_settings_field('sb_phone', 'Phone number', 'sb_phone_string', 'sb_plugin', 'sb_contact_settings');
	add_settings_field('sb_disclaimer', 'Standard disclaimer', 'sb_disclaim_string', 'sb_plugin', 'sb_contact_settings');
	add_settings_field('sb_tracking', 'Enable SmartBroker tracking', 'sb_tracking_string', 'sb_plugin', 'sb_contact_settings');
	
	add_settings_field('sb_css', 'Custom CSS', 'sb_css_string', 'sb_plugin', 'sb_css_settings');
	
	}

function sb_server_section_text() {echo "";}
function sb_tax_section_text() {echo "";}
function sb_page_section_text() {echo "";}
function sb_currency_section_text() {echo "";}
function sb_theme_section_text() {echo "";}
function sb_contact_section_text() {echo "";}
function sb_css_section_text() {echo "";}
	
function sb_server_address_string() {
	global $sb_config;
	echo "<input id='server_address' name='sb_plugin_options[server_address]' size='40' type='text' value='".$sb_config['server_address']."' />
	<p>e.g. <i>http://mybrokerage.smart-broker.co.uk</i> - always include the <i>'http://'</i> part.</p>
	<p>Use <em>http://demo.smart-broker.co.uk</em> if you don't yet have a SmartBroker system and just wish to test your site with placeholder data.";
	}
	
function sb_auth_string() {
	global $sb_config;
	echo "<input id='sb_auth' name='sb_plugin_options[auth]' size='40' type='text' value='".$sb_config['auth']."' />
	<p>This is available by logging into your SmartBroker system and going to Admin Console->Configuration. The token is listed at the bottom of the Advanced Config box - note that you'll need to be a superuser to see this.</p><p>If you are using the <em>demo.smart-broker.co.uk</em> server, the authentication token is <em>vjrxhvmkq67wb14639v5</em></p>";
	}
	
function sb_search_page_string() {
	global $sb_config;
	echo "<input id='search_page' name='sb_plugin_options[search_page]' size='10' type='text' value='".$sb_config['search_page']."' />
	<p>This is the id number of the WordPress page that contains the shortcode [sb_search_page].</p>";
	}

function sb_search_page_v2_string() {
	global $sb_config;
	echo "<input id='search_page_v2' name='sb_plugin_options[search_page_v2]' size='10' type='text' value='".$sb_config['search_page_v2']."' />
	<p>This is the id number of the WordPress page that contains the shortcode [sb_search_page_v2].</p>";
	}

function sb_listing_page_string() {
	global $sb_config;
	echo "<input id='listing_page' name='sb_plugin_options[listing_page]' size='10' type='text' value='".$sb_config['listing_page']."' />
	<p>This is the id number of the WordPress page that contains the shortcode [sb_listing].</p>";
	}

function sb_listing_default_tab_string() {
	global $sb_config;
	echo "<input id='listing_default_tab' name='sb_plugin_options[listing_default_tab]' size='40' type='text' value='".$sb_config['listing_default_tab']."' />
	<p>The default tab to automatically open when viewing a listing. Choose from:<i>sb_brokers_notes, sb_specifications, sb_photos</i> or <i>sb_find_out_more</i>.</p>
	<p>Defaults to <i>#sb_brokers_notes</i>.</p>";
	}

function sb_currency_1_string() {
	global $sb_config;
	echo "<input id='currency_1' name='sb_plugin_options[currency_1]' size='10' type='text' value='".$sb_config['currency_1']."' />
	<p>The primary currency for the plug-in to run. Choose from GBP, EUR or USD. A currency coversion will be included for any listings not in this currency.<br/><br/>Will default to EUR if left blank.</p>";
	}
	
function sb_currency_2_string() {
	global $sb_config;
	echo "<input id='currency_2' name='sb_plugin_options[currency_2]' size='10' type='text' value='".$sb_config['currency_2']."' />
	<p>The secondary currency for the plug-in to run. Choose from GBP, EUR or USD, and don't use the same value as the primary currency. This is used on the search sliders, where two currencies are displayed while sliding.<br/><br/>Will default to GBP if left blank.</p>";
	}
	
function sb_tax_label_string() {
	global $sb_config;
	echo "<input id='tax_label' name='sb_plugin_options[tax_label]' size='10' type='text' value='".$sb_config['tax_label']."' />
	<p>The sales tax label to use in the plugin. Typcial values would by VAT (in the UK) or BTW (in the Netherlands).<br/><br/>
	This string is used in the phrases 'VAT paid' and 'VAT not paid'.<br/><br/>
	Will default to 'VAT' if left blank.</p>";
	}

function sb_hide_tax_label_string() {
	global $sb_config;
	$a = '';
	if (array_key_exists('hide_tax_label', $sb_config) AND ($sb_config['hide_tax_label'] == 'on')) {
		$a = "checked='checked'";
		}
	echo "<input type='checkbox' id='hide_tax_label' name='sb_plugin_options[hide_tax_label]' $a />
	<p>Hide the tax label (e.g. 'VAT paid', 'VAT not paid') completely.</p>";
	}
	
function sb_email_string() {
	global $sb_config;
	echo "<input id='email' name='sb_plugin_options[email]' size='30' type='text' value='".$sb_config['email']."' />
	<p>The email address that <b>1:</b> Enquiries will be sent to <b>2:</b> Visitors will be asked to use to email your business</p>";
	}

function sb_phone_string() {
	global $sb_config;
	echo "<input id='phone' name='sb_plugin_options[phone]' size='30' type='text' value='".$sb_config['phone']."' />
	<p>The phone number for your business.</p>";
	}
	
function sb_disclaim_string() {
	global $sb_config;
	echo "<textarea id='sb_disclaim' name='sb_plugin_options[disclaim]' cols='100' rows='5'>$sb_config[disclaim]</textarea>
	<p>The standard disclaimer that is appending to every boat listing.</p>";
	}
	
function sb_tracking_string() {
	global $sb_config;
	$a = '';
	if (array_key_exists('sb_tracking', $sb_config) AND ($sb_config['sb_tracking'] == 'on')) {
		$a = "checked='checked'";
		}
	echo "<input type='checkbox' id='tracking' name='sb_plugin_options[sb_tracking]' $a />
	<p><b>Warning</b> - enabling tracking will send user details (names and email addresses) for both administrators and subscribers
	to SmartBroker servers.<br />Make sure you have permission from your users before enabling this option - 
	this may be an issue if you install SmartBroker onto an established site with existing registered users.<br />
	Users who register after the installation of SmartBroker should provide permission for tracking during sign-up
	- this should be included in your terms or on your registration form.</p>
	<p>No passwords or other security details are ever sent to SmartBroker.</p>
	<p>If user tracking is turned <b>on</b>, users will need to register/login to see boat photos &amp; videos (apart from the main illustration).</p>
	<p>If user tracking is turned <b>off</b>, all boat photos &amp; videos are available to all users.</p>
	<p>If you are using user-tracking, you may wish to consider something like the 
	<a href='http://wordpress.org/extend/plugins/simplemodal-login/'>SimpleModal Login plugin</a> to make the registration/login process neater for your guests.";
	}

function sb_theme_string() {
	global $sb_config;
	echo "<input id='theme' name='sb_plugin_options[theme]' size='10' type='text' value='".$sb_config['theme']."' /><br/>
	<p>For applicable theme names and examples, please go to <a href='http://jqueryui.com/themeroller/#themeGallery'>http://jqueryui.com/themeroller/#themeGallery</a></p>
	<p>Choose from: <i>ui-lightness, ui-darkness, smoothness, start, redmond, sunny, overcast, le-frog, flick, pepper-grinder,
	eggplant, dark-hive, cupertino, south-street, blitzer, humanity, hot-sneaks, excite-bike, vader, dot-luv, mint-choc, black-tie, swanky-purse</i>.</p>
	<p>Will default to <i>ui-lightness</i> if no valid theme found.</p>";
	}
	
function sb_css_string() {
	global $sb_config;
	echo "<textarea id='sb_css' name='sb_plugin_options[css]' cols='100' rows='5'>$sb_config[css]</textarea>
	<p>Add CSS to the <i>div.sb_wrapper</i> element to style only SmartBroker elements.</p>";
	}
	
function my_plugin_menu() {
	add_options_page('SmartBroker Options', 'SmartBroker', 'manage_options', 'smartbroker', 'sb_plugin_options');
	}

function sb_plugin_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	echo '<div class="wrap">';
	echo "<img src='".plugins_url('images/logo.png', __FILE__)."'
	alt='Smart Broker' style='padding: 10px;' />";
	echo "<h3>SmartBroker is a web-based sales tool for yacht brokers.</h3>
	<p>To use this SmartBroker plugin with real data from real boats, you'll need an account with us - sign up at
	<a href='http://www.smart-broker.co.uk'>www.smart-broker.co.uk</a>.</p>
	<p>Once you have an account, we can provide technical support on the whole SmartBroker system.</p>
	<h3>Installation</h3>
	<p>The very minimum you'll need to do to get the system running is:</p>
	<ol><li>Install the plugin (you've already done this if you're reading these words).</li>
	<li>Create a new page called 'Search for boats'&nbsp;(or similar), and add the shortcode [sb_search_page_v2] to it.</li>
	<li>Enter the page_id of this page in the 'SmartBroker search page ID v2'&nbsp;box below.</li>
	<li>Create a new page called 'Boat listing'&nbsp;(or similar), and add the shortcode [sb_listing] to it.</li>
	<li>Enter the page_id of this page in the 'SmartBroker listing page ID'&nbsp;box below.</li>
	<li>Fill in the 'Server address' and 'Server authentication token' fields below.</li>
	</ol>
	<h3>Shortcodes</h3>
	<p>In addition to the shortcodes [sb_search_page_v2] and [sb_listing] (see above), the following shortcodes can
	be used anywhere within your site:</p>
	<ol>
<li>[sb_featured] - a scrolling carousel feature highlighting some of your listings</li>
<li>[sb_search_box_v2] - a stand-alone listings search box (similar to that found on the left hand side of the search page)</li>
<li>[sb_search_box_small] - a simple size-type search box</li>
<li>[sb_search_by_ref] - a small search-by-reference-number box</li>
</ol>
	
	<h3>Options</h3>
	<p><b>Sliders</b></p>
<p>The following options can be set for any page/insert that uses sliders:</p>

<ol><li>size_min - the lowest value available on the size slider, in feet (default: 10, integers only)</li>
<li>size_max - the highest value available on the size slider, in feet (default: 100, integers only)</li>
<li>size_low - the pre-set value of the lower size slider, in feet (default: 28, integers only)</li>
<li>size_high - the pre-set value of the upper size slider, in feet (default: 45, integers only)</li></ol>

<ol>
<li>price_min - the lowest value available on the price slider, in the primary currency (as set in settings page) (default: 200, integers only)</li>
<li>price_max - the highest value available on the price slider, in the primary currency (as set in settings page) (default: 2000000, integers only)</li>
<li>price_low - the pre-set value of the lower price slider, in GBP (default: 30,000, integers only)</li>
<li>price_high - the pre-set value of the upper price slider, in GBP (default: 150,000, integers only)</li>
</ol>
<p>For example, the following shortcode will produced a size slider with an available range of 20-70 feet, pre-set to the range 25-30 feet:</p>
<p><i>[sb_search_box_v2 size_min=\"20\" size_max=\"70\" size_low=\"15\" size_high=\"30\"]</i></p>
<p><b>Take care setting the _min and _max values. It's not possible to search outside these limits, so make sure they include all your listings.</b></p>
<p><b>Other Options</b></p>
<p>For [sb_search_page_v2] and [sb_search_box_v2], the example note for the keyword search box can be customised using:</p>
<ol><li>keyword_examples (default: \"e.g. roller furling, fridge\")</li></ol>
<p>Finally, you can set the number of results returned per page on [sb_seach_page_v2] using the shortcode:</p>
<ol><li>results_per_page (default: 10)</li></ol>
<p>A fully customised search page v2 will look like:</p>
<p><i>[sb_search_page_v2 size_min=\"10\" size_max=\"70\" size_low=\"15\" size_high=\"30\" price_min=\"100\" 
price_max=\"10000000\" price_low=\"30000\" price_high=\"80000\" keyword_examples=\"e.g. bow thruster\" results_per_page=\"20\"]</i></p>

	<p>Further support for this plugin is available in the <a href='http://www.smart-broker.co.uk/?page_id=300'>Installation Guide</a>.";
	echo '<form method="post" action="options.php"> ';
	settings_fields('sb_plugin_options');
	do_settings_sections('sb_plugin');
	echo "<input name='Submit' type='submit' value='Save Changes'/></form>";
	echo '</div>';
}

// Add JS includes to head
add_action('wp_enqueue_scripts','sb_scripts');

function sb_scripts() {
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
	wp_enqueue_script('jquery');
	wp_deregister_script ('jquery-ui');
	wp_register_script('jquery-ui','https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js');
	wp_enqueue_script('jquery-ui');
	wp_register_script('smartbroker_js', plugins_url('js/smartbroker.js', __FILE__));
	wp_enqueue_script('smartbroker_js');
	wp_register_script('sb_prettyphoto', plugins_url('js/prettyphoto.jquery.js', __FILE__));
	wp_enqueue_script('sb_prettyphoto');
	wp_register_script('sb_jcarousel', plugins_url('js/jcarousel.jquery.js', __FILE__));
	wp_enqueue_script('sb_jcarousel');
	}

// Add CSS includes to head
add_action('wp_enqueue_scripts','sb_styles');

function sb_styles() {
	global $sb_config;
	wp_register_style('sb_jquery_ui_theme', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/$sb_config[theme]/jquery-ui.css");
	wp_enqueue_style('sb_jquery_ui_theme');
	wp_register_style('sb_theme', plugins_url('css/smartbroker.css', __FILE__));
	wp_enqueue_style('sb_theme');
	wp_register_style('sb_prettyphoto_css', plugins_url('css/prettyPhoto.css', __FILE__));
	wp_enqueue_style('sb_prettyphoto_css');
	wp_register_style('sb_jcarousel_css', plugins_url('css/jcarousel.css', __FILE__));
	wp_enqueue_style('sb_jcarousel_css');
	wp_register_style('sb_responsive_css', plugins_url('css/responsive.css', __FILE__));
	wp_enqueue_style('sb_responsive_css');
	}

#########################################################
## utility functions
#########################################################

function sb_load_xml($xml_file) {
	libxml_use_internal_errors(true);
	echo "\n\r\n\r<!-- xml file: $xml_file -->\n\r\n\r";
	$data = FALSE;
	if (ini_get('allow_url_fopen')) {
		echo "\n\n<!-- SmartBroker: Loading XML via file_get_contents-->\n\n";
		$data = file_get_contents($xml_file);
		if ($data == FALSE) {
			echo "\n\n<!-- SmartBroker: Loading XML via file_get_contents FAIL -->\n\n";
			} else {
			echo "\n\n<!-- SmartBroker: Loading XML via file_get_contents SUCCESS: ".strlen($data)." bytes loaded-->\n\n";
			}
		}
	if (($data == FALSE) AND (function_exists("curl_exec"))) {
		echo "\n\n<!-- SmartBroker: Loading XML via cURL-->\n\n";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $xml_file);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data == FALSE) {
			echo "\n\n<!-- SmartBroker: Loading XML via cURL FAIL -->\n\n";
			} else {
			echo "\n\n<!-- SmartBroker: Loading XML via cURL SUCCESS: ".strlen($data)." bytes loaded-->\n\n";
			}
		} elseif ($data == FALSE) {
		echo "\n\n<!-- SmartBroker: No way of loading data! -->\n\n";
		}
	$sxe = simplexml_load_string($data);
	if (!$sxe) {
		echo "<p>Failed loading XML: check your server address &amp; authentication token in <i>Admin >> Settings >>SmartBroker</i></p>
		<p>Error report:</p><pre>";
		foreach(libxml_get_errors() as $error) {
			echo "\t", $error->message;
			}
		echo "</pre><p>Data dump: </p>";
		var_dump($data);
		} else {
		echo "\n\n<!-- SmartBroker: Data conversion to XML SUCCESS -->\n\n";
		}
	return $sxe;
	}
	
function sb_listing_func(){
	global $sb_config, $user_email, $user_identity, $current_user;
	$boat_id = '';
	if (array_key_exists('boat_id', $_GET)) {$boat_id = $_GET['boat_id'];}
	
	//configure tracking, if required
	$tracking_code = '';
	if (is_user_logged_in() AND array_key_exists('sb_tracking',$sb_config)) {
			$tracking_code = "&track=true&a1=".urlencode($user_email).
			"&a2=".urlencode($current_user->display_name);
		}
		
	$site_id = explode(".",$sb_config['server_address']);
	$site_id = $site_id[0];
	$site_id = explode("/",$site_id);
	$site_id = end($site_id);
	$xml_file = "https://www.smart-broker.co.uk/secure_feed.php?auth=$sb_config[auth]&ver=1.2&site_id=$site_id&action=boat&boat_id=$boat_id".$tracking_code;
	$xml = sb_load_xml($xml_file);

	
	$boats_returned = count($xml->children());
	if ($boats_returned != '1') {
		return "<p>I'm sorry, we can't find the boat you requested.</p>
		<p><a href='/?page_id=".$sb_config['search_page_v2']."'>Go to the search page</a></p>";
		exit;
		}
	
	//format currency
	$currency = $xml->boat->currency;
	$curr_symbol = get_symbol($currency);
	
	//format VAT message
	$vat_paid = $xml->boat->vat_included;
	if ($sb_config['hide_tax_label'] == 'on') {
		$vat_message = '';
		}
	elseif ($vat_paid == '1') {$vat_message = $sb_config['tax_label']." paid";}
	else {$vat_message = $sb_config['tax_label']." not paid";}
	
	//add currency conversion if not in primary currency
	$price = floatval($xml->boat->asking_price);
	$currency_conversion = currency_conversion($price, $currency, $xml['USD'], $xml['EUR']);
	
	//format provisional message
	$prov = $xml->boat->approved;
	$prov_message = '';
	if (!$prov) {
		$prov_message = "<p style='text-align: center;'><i>".lang('this_listing_contains_some_provisional_information_details may change')."</i></p>";
		}
		
	// compile cats
	$cats = '';
	$i = '';
	$j = '';
	foreach($xml->boat->specifications->categories->category as $category) {
		$e = "<hr/><h3>".$category->name."</h3><hr/><p>";
		foreach($category->item as $item) {
			$e .= $item->name;
			$note = $item->note;
			if ($note != '') {
				$e .= ": ".$note;
				}
			$e.= "<br/>";
			}
		$e = substr($e,0,-5)."</p>";
		if (strlen($i) > strlen($j)){$j .= $e;} else {$i .= $e;}
		}
	$cats = "<div class='smartbroker_section smartbroker_group'>
				<div class='smartbroker_col smartbroker_span_1_of_2'>$i</div>
				<div class='smartbroker_col smartbroker_span_1_of_2'>$j</div>
			</div>";
	
	//sort photos out
	function build_photo($link, $path, $desc, $cat) {
        global $sb_config;
        return "<a href='".$sb_config['server_address'].$link."' rel='sb_prettyPhoto[$cat]' title='".$desc."'>
        <img src='".$sb_config['server_address'].$path."' alt='' />
        </a>";
        }
	$m = '';
	if (is_user_logged_in() OR ($sb_config['sb_tracking'] != 'on')) {
		$m .= "<hr/><h3>Exterior</h3><hr/>";
		foreach($xml->boat->media->exterior->media as $media) {
			if ($media['type'] == 'photo') {
				$m .= build_photo($media->link,$media->path,$media->description, 'exterior');
				} elseif ($media['type'] == 'video') {
				$m .= sprintf($sb_config['video_link'],$media->video_id);
				}
			}
		$m .= "<hr/><h3>Interior</h3><hr/>";
		foreach($xml->boat->media->interior->media as $media) {
			if ($media['type'] == 'photo') {
				$m .= build_photo($media->link,$media->path,$media->description, 'interior');
				} elseif ($media['type'] == 'video') {
				$m .= sprintf($sb_config['video_link'],$media->video_id);
				}
			}
		$m .= "<hr/><h3>Technical</h3><hr/>";
		foreach($xml->boat->media->technical->media as $media) {
			if ($media['type'] == 'photo') {
				$m .= build_photo($media->link,$media->path,$media->description, 'technical');
				} elseif ($media['type'] == 'video') {
				$m .= sprintf($sb_config['video_link'],$media->video_id);
				}
			}
		} else {
		$num_photos = 0;
		if (isset($xml->boat->media->interior->media)) {
			$num_photos = $num_photos + count($xml->boat->media->interior->media->children());
			}
		if (isset($xml->boat->media->exterior->media)) {
			$num_photos = $num_photos + count($xml->boat->media->exterior->media->children());
			}
		if (isset($xml->boat->media->technical->media)) {
			$num_photos = $num_photos + count($xml->boat->media->technical->media->children());
			}
		$m = "<p>$num_photos more photos &amp; videos of this boat are available - please <a href='/wp-register.php' title='Register'>register</a> or 
		<a href='".wp_login_url(get_permalink()."&boat_id=".$_GET['boat_id'])."' title='Login'>log in</a> to see them.</p>";
		}
		
	//sort find out more tab
	if (is_user_logged_in() OR ($sb_config['sb_tracking'] != 'on')) {
		$find_out_more = "<p>Interested in this boat? Find out more or arrange a viewing by completeing this form.
		We'll get back to you with more information and take you thorough the options for viewing and buying this boat.</p>
		<p>Alternatively, don't forget that you can call us on <b>$sb_config[phone]</b> anytime for a chat.</p>
		<hr />
		
		<form action='".$sb_config['server_address']."/system/wp_plugin/wp_plugin_enquire.php' method='post'>
		<table><tr>
		<td><p>Your name:</p></td>
		<td><p><input type='text' name='name' value='$user_identity' size='14' /></p></td>
		</tr>
		
		<tr id='hpt'>
		<td><p>Please leave empty</p></td>
		<td><p><input type='text' name='email_address' /></p></td>
		</tr>
		
		<tr>
		<td><p>Your email address:</p></td>
		<td><p><input type='text' name='cwr' value='$user_email' size='19' /></p></td>
		</tr>
		
		
		<tr>
		<td><p>Phone number:</p></td>
		<td><p><input type='text' name='phone' size='15' /></p></td>
		</tr>
		
		<tr>
		<td><p>Preferred contact method:</p></td>
		<td><p><input type='radio' name='contact_method' value='phone' checked='checked' />&nbsp;Phone
			<br /><input type='radio' name='contact_method' value='email' />&nbsp;Email</p></td>
		</tr>
		
		<tr>
		<td style='vertical-align: top;'><p>Notes:</p></td>
		<td><p><textarea name='notes' rows='5' cols='30'></textarea></p></td>
		</tr>
		
		<tr>
		<td>
		<input type='hidden' name='boat_id' value='$_GET[boat_id]' />
		<input type='hidden' name='admin_email' value='".$sb_config['email']."' />
		<input type='hidden' name='path' value='http://".$_SERVER['SERVER_NAME']."/?page_id=".$sb_config['listing_page']."' />
		</td>
		<td><button type='submit' class='button'><p>Send enquiry</p></button></td>
		</tr>
		
		</table></form>";
		if (array_key_exists('enquiry_success',$_GET)) {
			if ($_GET['enquiry_success'] == 'true') {
				$m = "Thankyou for your enquiry. We will be in touch soon.";
				$s = 'ui-state-highlight';
				} elseif ($_GET['enquiry_success'] == 'false') {
				$m = "I'm afraid there has been a technical fault - your enquiry could not be sent to our system. Please call us on 123456 instead.";
				$s = 'ui-state-error';
				}
			$find_out_more .= "<div class='ui-widget $s ui-corner-all'>
			<p><span class='ui-icon ui-icon-info' style='float: left; margin-right: 5px;'></span>$m</p></div>";
			}
		} else {
		$find_out_more = "<p>To find out more about this yacht, please <a href='/wp-register.php' title='Register'>register</a> or 
		<a href='".wp_login_url(get_permalink()."&boat_id=".$_GET['boat_id'])."' title='Login'>log in</a>.<br/>
		Registration is free and takes seconds.</p>
		<p>Don't forget you can call us on $sb_config[phone] anytime for a chat.";
		}
	
	$admin_link = '';
	if (current_user_can('manage_options')) {
		$admin_link = " <a href='".$sb_config['server_address']."/system/wp_plugin/edit_boat.php
		?boat_id=".$_GET['boat_id']."&user_email=".urlencode($user_email)."'>
		[edit]</a>";
		}
		
	$brokers_notes = nl2br($xml->boat->brokers_notes);
	if ($brokers_notes == '') {
		$brokers_notes = "<em>(There are no broker's notes available for this boat)</em>";
		}
	//data required by javascript
	$a = "<div style='display: none;' id='server_address_address'>".$sb_config['server_address']."</div>";
	$a .= "<div style='display: none;' id='sb_listing_page'>".$sb_config['listing_page']."</div>";
	
	$a .= "
	<div class='sb_wrapper'>
	<h2><span id='sb_boat_builder_and_model'>".$xml->boat->builder." ".$xml->boat->model."</span>".$admin_link."</h2>
	<div class='smartbroker_section smartbroker_group'>
	<div class='smartbroker_col smartbroker_span_2_of_5'>
		<div id='sb_primary_image'>
			<img src='".$sb_config['server_address']."/images/boats/$boat_id/medium/".str_replace("/","-",$xml->boat->model)."-".$xml->boat->primary_photo.".jpg' title='42'  style='padding: 0px;'
			alt='".$xml->boats->model."' width='300px'/>		 
		</div>
	
		<div id='sb_key_facts_container'>
			$prov_message
			<div id='sb_key_facts_head' class='ui-corner-top ui-widget-header ui-widget'><p>Key facts</p></div>
			<div class='ui-widget-content ui-corner-bottom'>
				<table id='sb_key_facts_table'>	
					<tr id='ref'><td><p>Boat reference</p></td><td><p><i><b>$boat_id</b></i></p></td></tr>
					
					<tr id='status_row'><td><p>Status</p></td><td><p>".$xml->boat->status_text."</p></td></tr>
					
					<tr><td><p>Builder</p></td><td><p>".$xml->boat->builder."</p></td></tr>
					<tr><td><p>Boat model</p></td><td><p>".$xml->boat->model."</p></td></tr>
					<tr><td><p>Type</p></td><td><p>".$xml->boat->type_description."</p></td></tr>
					<tr><td><p>LOA</p></td><td><p>".round($xml->boat->length)." ft (".round(floatval($xml->boat->length)/3.28)." m)</p></td></tr>
					<tr><td><p>Built</p></td><td><p>".$xml->boat->year."</p></td></tr>
					<tr><td><p>Currently lying</p></td><td><p>".$xml->boat->region.", ".$xml->boat->country_name."</p></td></tr>
					<tr><td><p>Price</p></td>
					<td><p>".$curr_symbol.number_format($price)." ".$vat_message.'<br />'.$currency_conversion."</p></td></tr>
				</table>
			</div>
		</div>
	</div>
	<div class='smartbroker_col smartbroker_span_3_of_5'>
		
		<div id='sb_tabs_container'>
			<div id='tabs'>
					<ul id='tab_links'>
						<li><p><a href='#sb_broker_notes'>Notes</a></p></li>
						<li><p><a href='#sb_specifications'>Specifications</a></p></li>
						<li><p><a href='#sb_photos' id='photos_tab'>Photos &amp; videos</a></p></li>
						<li><p><a href='#sb_find_out_more' id='contact_tab'>Find out more</a></p></li>
										</ul>
					<div id='sb_broker_notes'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>Broker's notes</p></div>
						<div class='ui-widget-content ui-corner-bottom'>
							<p>".$brokers_notes."</p>
						</div>
					</div>
					<div id='sb_specifications'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>Specifications</p></div>
						<div class='ui-widget-content ui-corner-bottom'>$cats</div>
					</div>
					<div id='sb_photos'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>Photos &amp; videos</p></div>
						<div class='ui-widget-content ui-corner-bottom'>$m</div>
					</div>
					<div id='sb_find_out_more'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>Find out more</p></div>
						<div class='ui-widget-content ui-corner-bottom'>$find_out_more</div>
					</div>
					
									
			</div>
		</div>
	</div> <!--end of span_3_of_5 -->
	</div> <!-- end of col group -->";
	
	if ($sb_config['disclaim'] != '') {
		$a .= "<p class='ui-priority-secondary'>$sb_config[disclaim]</p>";
		}
	
	$a .= "</div> <!--end sb_wrapper -->";
	return $a;
	}

function sb_featured_func() {
	global $sb_config;
	$xml_file = $sb_config['server_address']."/system/wp_plugin/listings.php?auth=$sb_config[auth]&limit=10&rand=true";
	$xml = sb_load_xml($xml_file);
	$sb_config['eur_rate'] = $xml['EUR'];
	$sb_config['usd_rate'] = $xml['USD'];
	
	function add_featured ($boat) {
		global $sb_config;
		$img_link = $sb_config['server_address']."/images/boats/".$boat->boat_id."/small/".str_replace("/","-",$boat->model)."-".$boat->photo_id.".jpg";
		$desc = $boat->builder." ".$boat->model;
		$link = "/?page_id=".$sb_config['listing_page']."&boat_id=".$boat->boat_id.'#'.$sb_config['listing_default_tab'];
		$model = $boat->model;
			if ($sb_config['hide_tax_label'] == 'on') {
				$vat_message = '';
				} elseif ($boat->vat_paid == '1') {
				$vat_message = $sb_config['tax_label']." paid";
				} else {
				$vat_message = $sb_config['tax_label']." not paid";
				}
		//format currency
		$currency = $boat->currency;
		$curr_symbol = get_symbol($currency);
		$value = floatval($boat->price);
		$conversion = currency_conversion ($value, $currency, $sb_config['usd_rate'], $sb_config['eur_rate']);
		if ($conversion != '') {
			$conversion = "<br/>$conversion";
			}
		$price = number_format($value);
		return "<li>
		<div class='ui-widget ui-widget-header ui-corner-all featured-card' style='height: 250px; margin: 5px; padding: 10px;'>
			<a href='$link'
			style='position:relative; display: block; padding:0px;'>
				<img class='tooltip_img corner iradius5 ishadow5 inverse' src='$img_link'
				alt='$model' title='$model' height='85' width='127.5' style='padding: ;'/>
			</a>
			<p><a href='$link'>$desc</a></p>
			<p>$curr_symbol $price $conversion</p>
			<p>$vat_message</p>
		</div>
		</li>";
		}
	$feat_boats = '';
	foreach ($xml->boat as $boat) {
		$feat_boats .= add_featured($boat);
		}
	$a = "
	<div class='sb_wrapper'>
	<ul id='sb_carousel' class='jcarousel-skin-tango'>
	$feat_boats
	<li>
	<div class='ui-widget ui-widget-header ui-corner-all featured-card' style='height: 250px; margin: 5px; padding: 10px;'>
		<p style='padding-top: 3em;'>
			<a href='/?page_id=".$sb_config['search_page_v2']."'>Search for your perfect boat 
			<span class='sb_icon ui-icon ui-icon-circle-triangle-e'>&nbsp;</span></a>
		</p>
	</div>
	</li>
	</ul>
	</div>";
	return $a;
	}

function sb_search_box_small_func($atts) {
	extract(shortcode_atts( array(
		'size_low' => '28',
		'size_high' => '45'), $atts ) );
	global $sb_config;
	//print_r($sb_config);
	$a = "<div class='sb_wrapper' style='max-width: 400px;'>
	<div class='ui-widget ui-widget-header ui-corner-top header' style='margin-top: .5em;'><p>Quick search</p></div>
	<div class='ui-widget ui-widget-content ui-corner-bottom content' style='padding: .5em;'>
	<form action='/' method='get'>
	<input type='hidden' name='page_id' value='".$sb_config['search_page_v2']."'/>
	<p>Size: <input type='text' size='5' name='sl' value='".intval($size_low)."'/> - <input type='text' size='5' name='sh' value='".intval($size_high)."' /> ft</p>
	<p>Type: <input type='radio' name='type' value='s' /> Sail 
	<input type='radio' name='type' value='p' /> Power 
	<input type='radio' name='type' value='a' checked='checked' /> All</p>
	<div><button type='submit' class='button'><p>Search</p></button></div>
	</form>
	</div></div>";
	return $a;
	}
	
function sb_search_by_ref_func() {
	global $sb_config;
	$a = "<div class='sb_wrapper' style='max-width: 400px;'>
	<div class='ui-widget ui-widget-header ui-corner-top header' style='margin-top: .5em;'>
	<p>Find by reference number</p></div>
	<div class='ui-widget ui-widget-content ui-corner-bottom content' style='padding: 0em .5em;'>
		<form method='get' action='' target='_parent'>
		<input type='hidden' name='page_id' value='".$sb_config['listing_page']."'/>
		<table><tr><td style='vertical-align: middle;'><p>Boat reference:</p></td>
		<td style='vertical-align: middle;'><p><input type='text' name='boat_id' size='10' value='' /></p></td>
		<td style='vertical-align: middle;'><button class='button' type='submit'><p>Go</p></button></td>
		</tr></table>
		</form>
	</div>
	</div>";
	return $a;
	}

function get_symbol($curr) {
	if ($curr == 'EUR') {return "&euro;";}
	if ($curr == 'GBP') {return "&pound;";}
	if ($curr == 'USD') {return "$";};
	return '';
	}
	
function currency_conversion ($value, $currency, $usd_rate = 1.6, $eur_rate = 1.2) {
	global $sb_config;
	if ($currency != $sb_config['currency_1'])
		{
		$gbp_rate = 1;
		$usd_rate = floatval($usd_rate);
		$eur_rate = floatval($eur_rate);
		if (($currency == 'GBP') AND ($sb_config['currency_1'] == 'EUR')) {$rate = $eur_rate;}
		elseif (($currency == 'EUR') AND ($sb_config['currency_1'] == 'GBP')) {$rate = 1 / $eur_rate;}
		elseif (($currency == 'GBP') AND ($sb_config['currency_1'] == 'USD')) {$rate = $usd_rate;}
		elseif (($currency == 'USD') AND ($sb_config['currency_1'] == 'GBP')) {$rate = 1 / $usd_rate;}
		elseif (($currency == 'EUR') AND ($sb_config['currency_1'] == 'USD')) {$rate = $usd_rate / $eur_rate;}
		elseif (($currency == 'USD') AND ($sb_config['currency_1'] == 'EUR')) {$rate = $eur_rate / $usd_rate;}
		else {$rate = 1;}
		$new_price = $value * floatval($rate);
		return '(~ '.get_symbol($sb_config['currency_1']).number_format(round($new_price,-2)).')';
		}
	return '';
	}
	
?>