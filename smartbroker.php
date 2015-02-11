<?php
/*
Plugin Name: SmartBroker
Plugin URI: http://www.smart-broker.co.uk
Description: A plugin to insert SmartBroker data into a Wordpress site
Version: 6.1.4
Author: Nick Roberts
Author URI: http://www.smart-broker.co.uk
License: GPL2
Text Domain: smartbroker

Copyright 2013  Nick Roberts  (email: contact@smart-broker.co.uk)

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
include_once('listing.php');
include_once('search.php');
include_once('utility_functions.php');
include_once('activation.php');
include_once('config.php');
include_once('xml_helpers.php');
include_once("pagination.php");
include_once("dropdowns.php");
include_once("featured.php");
include_once("sb_widgets.php");
include_once("white_label_settings.php");
include_once("rewrite.php");

//Default config variables
$sb_config= get_option('sb_plugin_options');
if ($sb_config['currency_1'] == '') {
	$sb_config['currency_1'] = 'EUR';
	}
if	($sb_config['server_address'] == '') {
	$sb_config['server_address'] = $sb_white_label['default_server'];
	}

$sb_config['video_link'] = '<iframe class="video" width="200" height="115"
src="http://www.youtube-nocookie.com/embed/%s?rel=0&wmode=opaque&modestbranding=1&showinfo=0&theme=light"
frameborder="0" allowfullscreen></iframe>';

//activation hook
register_activation_hook(__FILE__, 'make_pages');

//actions we're going to use
add_action('admin_menu', 'sb_plugin_menu');
function sb_plugin_menu() {
	global $sb_white_label;
	add_options_page($sb_white_label['name'].' Options', $sb_white_label['name'], 'manage_options', 'smartbroker', 'sb_plugin_options');
	}
	
add_action('admin_init', 'sb_plugin_admin_init');
	
//the shortcodes we use
add_shortcode($sb_white_label['sc_prefix'].'listing', 'sb_listing_func' );
add_shortcode($sb_white_label['sc_prefix'].'search_page', 'sb_search_page_func');
add_shortcode($sb_white_label['sc_prefix'].'featured', 'sb_featured_func');
 
//code to hide listing page link (located in utility_functions.php)
add_action('wp_head', 'hide_listing_page');
add_action('wp_head', 'add_smartbroker_custom_css');

add_filter( 'rewrite_rules_array','sb_insert_rewrite_rules' );
add_filter( 'query_vars','sb_insert_query_vars' );
add_action( 'wp_loaded','sb_flush_rules' );

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
		wp_register_script('sb_clean', plugins_url('js/clean.js', __FILE__));
		wp_enqueue_script('sb_clean');
		wp_register_script('sb_blueimp_gallery', plugins_url('js/blueimp-gallery.js', __FILE__),'','',true);
		wp_enqueue_script('sb_blueimp_gallery');
		wp_register_script('sb_blueimp_gallery_runner', plugins_url('js/blueimp-gallery-runner.js', __FILE__),'','',true);
		wp_enqueue_script('sb_blueimp_gallery_runner');
		}

// Add CSS includes to head
add_action('wp_enqueue_scripts','sb_styles');
	function sb_styles() {
		global $sb_config;
		//wp_register_style('sb_jquery_ui_theme', "http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/$sb_config[theme]/jquery-ui.css");
		//wp_enqueue_style('sb_jquery_ui_theme');
		wp_register_style('sb_theme', plugins_url('css/smartbroker.css', __FILE__));
		wp_enqueue_style('sb_theme');
		wp_register_style('sb_prettyphoto_css', plugins_url('css/prettyPhoto.css', __FILE__));
		wp_enqueue_style('sb_prettyphoto_css');
		wp_register_style('sb_jcarousel_css', plugins_url('css/jcarousel.css', __FILE__));
		wp_enqueue_style('sb_jcarousel_css');
		wp_register_style('sb_responsive_css', plugins_url('css/responsive.css', __FILE__));
		wp_enqueue_style('sb_responsive_css');
		wp_register_style('sb_blueimp_css', plugins_url('css/blueimp-gallery.css', __FILE__));
		wp_enqueue_style('sb_blueimp_css');
		}

function sb_set_plugin_meta( $links, $file ) {

	if ( strpos( $file, 'smartbroker.php' ) !== false ) {
		$links = array_merge( $links, array( '<a href="/wp-admin/options-general.php?page=smartbroker">' . __( 'Settings' ) . '</a>' ) );
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'sb_set_plugin_meta', 10, 2 );

//register widgets
/**
 * Register Widget Area.
 *
 */
function sb_listing_under_photos_widget_init() {
	register_sidebar(array(
		'name' => 'Boat listing page under photos',
		'id' => 'sb_listing_under_photos',
		'before_widget' => '<div>',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="rounded">',
		'after_title' => '</h2>',
		));
	}
add_action( 'widgets_init', 'sb_listing_under_photos_widget_init' );
	
?>