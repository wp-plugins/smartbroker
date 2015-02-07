<?php
// flush_rules() if our rules are not yet included
function sb_flush_rules(){
	$rules = get_option( 'rewrite_rules' );

	if ( ! isset( $rules['boat/([0-9]+)/(\w+)'] ) ) {
		global $wp_rewrite;
	   	$wp_rewrite->flush_rules();
	}
	//print_r($rules);
}

// Adding a new rule
function sb_insert_rewrite_rules( $rules )
{
	global $sb_config;
	$newrules = array();
	$newrules['boat/([0-9]+)/(\w+)'] = 'index.php?page_id='.$sb_config['listing_page'].'&boat_id=$matches[1]&server_address=http%3A%2F%2F$matches[2].smart-broker.co.uk';
	return $newrules + $rules;
}

// Adding the id var so that WP recognizes it
function sb_insert_query_vars( $vars )
{
    array_push($vars, 'boat_id');
    array_push($vars, 'server_address');
    return $vars;
}
?>