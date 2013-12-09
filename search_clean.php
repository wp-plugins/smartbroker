<?php
include_once "pagination.php";
function sb_search_page_clean_func($atts) {
	
	global $sb_config, $post;
	extract( shortcode_atts(array(
			'size_low' => '28',
			'size_high' => '45',
			'size_min' => '10',
			'size_max' => '100',
			'price_low' => '30000',
			'price_high' => '150000',
			'price_min' => '200',
			'price_max' =>'2000000',
			'results_per_page' => '10',
			'keyword_examples' => "e.g. roller furling, fridge",
			'layout' => '')
		, $atts ));
		
	$sb_config['layout'] = $layout;
	
	if (array_key_exists('sl', $_GET) AND ($_GET['sl'] != '')) {$data['sl'] = intval($_GET['sl']);} else {$data['sl'] = $size_low;}	
	if (array_key_exists('sh', $_GET) AND ($_GET['sh'] != '')) {$data['sh'] = intval($_GET['sh']);}	else {$data['sh'] = $size_high;}	
	if (array_key_exists('pl', $_GET) AND ($_GET['pl'] != '')) {$data['pl'] = intval($_GET['pl']);}	else {$data['pl'] = $price_low;}	
	if (array_key_exists('ph', $_GET) AND ($_GET['ph'] != '')) {$data['ph'] = intval($_GET['ph']);} else {$data['ph'] = $price_high;}	
	
	$data['ln'] = $results_per_page;
	
	$fields_xml = load_fields_xml();

	$a = '';
	
	//start search box
	$a .=  "<div class='sb_wrapper'>
			<form method='get' action='".site_url('/')."' id='boat_search_v2'>";
			
			
			
	//first section - boat type
	$a .= "<div class='smartbroker_section smartbroker_group'>
		<div class='smartbroker_col smartbroker_span_1_of_2'>
		<input type='hidden' name='page_id' value='".$post->ID."' />
		".__('Boat type:','smartbroker')."
		</div>
		
		<div class='smartbroker_col smartbroker_span_1_of_2'>
			".create_type_dropdown($fields_xml)."
		</div>
		
		</div>";
	
	//second section - size slider
	$a .= "
		<div class='smartbroker_section smartbroker_group'>
		
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				".__('Boat size:','smartbroker')." <span class='sizedesc'></span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<div class='slider_container' title='".__('Drag handles to set boat size','smartbroker')."'><div class='slider_size slider'></div></div>
			</div>
		
		</div>";
	
	//third section: price slider
	$a .= "
		<div class='smartbroker_section smartbroker_group'>
		
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				".__('Price:','smartbroker')." <span class='pricedesc'></span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<div class='slider_container' style='height: 100%;' title='".__('Drag handles to set boat price','smartbroker')."'><div class='slider_price slider'></div></div>
			</div>
		</div>";
		
	//forth: advanced search
	$a .= "
	<div id='sb_advanced_options'>
	<hr/>
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				".__('Keywords:','smartbroker')."<br/><em>".$keyword_examples."</em>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<input name='lk' value=\"".stripslashes($_GET['lk'])."\"></input>
			</div>
		</div>
			
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".__('Builder:','smartbroker')."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Available makes are listed'>&nbsp;</span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".create_builder_dropdown($fields_xml)."
			</div>
		</div>
		
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".__('Currently lying:','smartbroker')."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Boats are available in the countries listed.'>&nbsp;</span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".create_country_dropdown($fields_xml)."
			</div>	
		</div>
		
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2'>
			".__('Built after:','smartbroker')."
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>	
			".create_built_after_dropdown()."
			</div>
		</div>
	</div> <!-- End sb_advanced_options -->";
			
	
	//fifth: advanced and search button
	$a .= "
		<div class='smartbroker_section smartbroker_group'>
		
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				<a href='#'><small id='sb_show_advanced'>Show advanced search options &#9660;</small></a>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<input type='submit' value='".__('Search','smartbroker')."' /> 
			</div>
		</div>";
		
	
	
	
		//receiving inputs for slider values
		$a .=  "<input type='hidden' name='pl' class='price_low' value='".intval($data['pl'])."'/>\r\n";
		$a .=  "<input type='hidden' name='ph' class='price_high' value='".intval($data['ph'])."'/>\r\n";
		$a .=  "<input type='hidden' name='sl' class='size_low' value='".intval($data['sl'])."'/>\r\n";
		$a .=  "<input type='hidden' name='sh' class='size_high' value='".intval($data['sh'])."'/>\r\n";
		$a .=  "<input type='hidden' name='ln' value='".intval($data['ln'])."'/>\r\n";
		$a .=  "<div style='display: none;' id='size_min'>".intval($size_min)."</div>\r\n";
		$a .=  "<div style='display: none;' id='size_max'>".intval($size_max)."</div>\r\n";
		$a .=  "<div style='display: none;' id='price_min'>".intval($price_min)."</div>\r\n";
		$a .=  "<div style='display: none;' id='price_max'>".intval($price_max)."</div>\r\n\r\n";
	
	$a .= "</form>
	</div> <!-- end smartbroker_section smartbroker_group-->
	<hr/>";

	//$a .=  "<h2>Search results</h2>";
	//$a .=  "<table>";
	

	$xml = load_results_xml($data);
	$total_rows = $xml['count'];
	$returned_rows = count($xml);
	$requested_rows = $xml['requested_rows'];
	$start_row = $xml['start'];
	$end_row = $start_row + $returned_rows - 1;
	
	if ($total_rows > 0) {
		$results_string = sprintf(__('%s results found - showing results %s to %s.','smartbroker'),$total_rows, $start_row, $end_row);
		$a .=  "&nbsp;$results_string<table id='results'>";
		foreach ($xml->boat as $boat) {
			$a .=  search_clean_result_item($boat);
			}
		$a .=  pagination_links($total_rows,$start_row,$requested_rows);
		} else {
		//$a .=  "<p>&nbsp;No results found</p>";
		$a .=  blank_slate_row();
		}
	$a .=  "</div></div></div>";
	
		
	//data required by javascript
	$a .= "<div style='display: none;' id='sb_server_address'>".$sb_config['server_address']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_listing_page'>".$sb_config['listing_page']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1'>".$sb_config['currency_1']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1_symbol'>".get_symbol($sb_config['currency_1'])."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2'>".$sb_config['currency_2']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2_symbol'>".get_symbol($sb_config['currency_2'])."</div>\r\n";
	$a .= "<div class='eur_rate' style='display: none;'>".$sb_config['eur_rate']."</div>\r\n";
	$a .= "<div class='usd_rate' style='display: none;'>".$sb_config['usd_rate']."</div>\r\n";
	
	echo $a;
	}
	
function search_clean_result_item($boat) {
		global $sb_config;
		
		$desc = $boat->builder." ".$boat->model;
		
		if ($sb_config['hide_tax_label'] == 'on') {
			$vat_message = '';}
			elseif ($boat->vat_paid == '1') {
			$vat_message = $sb_config['tax_label']." ".__('paid','smartbroker');}
			else {$vat_message = $sb_config['tax_label']." ".__('not paid','smartbroker');}
			
		$status_message = '';
		$featured = false;
		if ($boat->status != ''){	
			$status_message = $boat->status;
			}
		if ($status_message == 'Available') {
			$status_message = '';
			} elseif ($status_message == 'Featured') {
			$featured = true;
			$status_message = '';
			} else {
			$status_message = "<br/>$status_message";
			}
		
		$link = site_url("/?page_id=".$sb_config['listing_page']."&boat_id=".$boat->boat_id.'#'.$sb_config['listing_default_tab']);
		
		$length = round($boat->LOA)."ft (".round($boat->LOA/3.28)."m)";
		
		//format currency
		$currency = $boat->currency;
		if ($currency == "GBP") {
			$curr_symbol = "&pound;";
			$rate = 1;
			} elseif ($currency == "EUR") {
			$curr_symbol = "&euro;";
			$rate = $sb_config['eur_rate'];
			} elseif ($currency == "USD") {
			$curr_symbol = "$";
			$rate = $sb_config['usd_rate'];
			}
		$price = number_format(floatval($boat->price));
		$currency_conversion = currency_conversion(floatval($boat->price), $currency, $sb_config['usd_rate'], $sb_config['eur_rate']);
		if ($currency_conversion != '') {
			$currency_conversion = "<small>$currency_conversion</small>";
			}
		
		if ($featured) {
			$img_link = $sb_config['server_address']."/images/boats/".$boat->boat_id.'/medium/'.str_replace("/","-",$boat->model)."-".$boat->photo_id.".jpg";
				$a = "
			<div class='sb_listing_item sb_clearfix' style='clear: both; margin-bottom: .2em;'>
				
				<a href='$link' style='display: block; float: left; margin-right: 1em;'><img src='$img_link' alt='$desc' title='$desc'></a>
				<div style='padding-left: .5em; display: block;'>
					<h3 style='clear: right;'><a href='$link'>$desc</a></h3>
					
					<p>$length, ".__('built','smartbroker')." $boat->year, ".__('lying','smartbroker')." $boat->region $boat->country<br/>
						$curr_symbol$price $currency_conversion $vat_message</p>
					
					<input type='button' value='View boat details' onclick='window.location=\"$link\"' style='margin-bottom: .5em;'/>
				</div>
					
			</div>";
			} else {
			$img_link = $sb_config['server_address']."/images/boats/".$boat->boat_id.'/small/'.str_replace("/","-",$boat->model)."-".$boat->photo_id.".jpg";
			$a = "
			<div class='sb_listing_item sb_clearfix' style='clear: both;'>
				
				<a href='$link' style='display: block; float: left; margin-right: .5em; margin-top: .5em;'><img src='$img_link' alt='$desc' title='$desc'></a>
				<div style='display: inline-block;'>
				<h3 style='clear: right; margin-top: .5em;'><a href='$link'>$desc</a>$status_message</h3>
				
				<p>$length, ".__('built','smartbroker')." $boat->year, ".__('lying','smartbroker')." $boat->region $boat->country $curr_symbol$price $currency_conversion $vat_message</p>
				</div>	
			</div>";
			}
		return $a;
		}
		
function sb_search_box_clean_func($atts) {
	global $sb_config, $post;
	extract( shortcode_atts(array(
			'size_low' => '28',
			'size_high' => '45',
			'size_min' => '10',
			'size_max' => '100',
			'price_low' => '30000',
			'price_high' => '150000',
			'price_min' => '200',
			'price_max' =>'2000000',
			'results_per_page' => '10',
			'keyword_examples' => "e.g. roller furling, fridge",
			'layout' => '')
		, $atts ));
		
	$sb_config['layout'] = $layout;
	
	if (array_key_exists('sl', $_GET) AND ($_GET['sl'] != '')) {$data['sl'] = intval($_GET['sl']);} else {$data['sl'] = $size_low;}	
	if (array_key_exists('sh', $_GET) AND ($_GET['sh'] != '')) {$data['sh'] = intval($_GET['sh']);}	else {$data['sh'] = $size_high;}	
	if (array_key_exists('pl', $_GET) AND ($_GET['pl'] != '')) {$data['pl'] = intval($_GET['pl']);}	else {$data['pl'] = $price_low;}	
	if (array_key_exists('ph', $_GET) AND ($_GET['ph'] != '')) {$data['ph'] = intval($_GET['ph']);} else {$data['ph'] = $price_high;}	
	
	$data['ln'] = $results_per_page;
	
	$fields_xml = load_fields_xml();

	$a = '';
	
	//start search box
	$a .=  "<div class='sb_wrapper'>
			<form method='get' action='".site_url('/')."' id='boat_search_v2'>";
			
			
			
	//first section - boat type
	$a .= "<div class='smartbroker_section smartbroker_group'>
		<div class='smartbroker_col smartbroker_span_1_of_2'>
		<input type='hidden' name='page_id' value='".$sb_config['search_page_v2']."' />
		".__('Boat type:','smartbroker')."
		</div>
		
		<div class='smartbroker_col smartbroker_span_1_of_2'>
			".create_type_dropdown($fields_xml)."
		</div>
		
		</div>";
	
	//second section - size slider
	$a .= "
		<div class='smartbroker_section smartbroker_group'>
		
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				".__('Boat size:','smartbroker')." <span class='sizedesc'></span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<div class='slider_container' title='".__('Drag handles to set boat size','smartbroker')."'><div class='slider_size slider'></div></div>
			</div>
		
		</div>";
	
	//third section: price slider
	$a .= "
		<div class='smartbroker_section smartbroker_group'>
		
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				".__('Price:','smartbroker')." <span class='pricedesc'></span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<div class='slider_container' style='height: 100%;' title='".__('Drag handles to set boat price','smartbroker')."'><div class='slider_price slider'></div></div>
			</div>
		</div>";
		
	//forth: advanced search
	$a .= "
	<div id='sb_advanced_options'>
	<hr/>
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				".__('Keywords:','smartbroker')."<br/><em>".$keyword_examples."</em>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<input name='lk' value=\"".stripslashes($_GET['lk'])."\"></input>
			</div>
		</div>
			
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".__('Builder:','smartbroker')."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Available makes are listed'>&nbsp;</span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".create_builder_dropdown($fields_xml)."
			</div>
		</div>
		
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".__('Currently lying:','smartbroker')."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Boats are available in the countries listed.'>&nbsp;</span>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				".create_country_dropdown($fields_xml)."
			</div>	
		</div>
		
		<div class='smartbroker_section smartbroker_group'>
			<div class='smartbroker_col smartbroker_span_1_of_2'>
			".__('Built after:','smartbroker')."
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>	
			".create_built_after_dropdown()."
			</div>
		</div>
	</div> <!-- End sb_advanced_options -->";
			
	
	//fifth: advanced and search button
	$a .= "
		<div class='smartbroker_section smartbroker_group'>
		
			<div class='smartbroker_col smartbroker_span_1_of_2' style='margin-top: 0;'>
				<a href='#'><small id='sb_show_advanced'>Show advanced search options &#9660;</small></a>
			</div>
			
			<div class='smartbroker_col smartbroker_span_1_of_2'>
				<input type='submit' value='".__('Search','smartbroker')."' /> 
			</div>
		</div>";
		
	
	
	
	//receiving inputs for slider values
	$a .=  "<input type='hidden' name='pl' class='price_low' value='".intval($data['pl'])."'/>\r\n";
	$a .=  "<input type='hidden' name='ph' class='price_high' value='".intval($data['ph'])."'/>\r\n";
	$a .=  "<input type='hidden' name='sl' class='size_low' value='".intval($data['sl'])."'/>\r\n";
	$a .=  "<input type='hidden' name='sh' class='size_high' value='".intval($data['sh'])."'/>\r\n";
	$a .=  "<input type='hidden' name='ln' value='".intval($data['ln'])."'/>\r\n";
	$a .=  "<div style='display: none;' id='size_min'>".intval($size_min)."</div>\r\n";
	$a .=  "<div style='display: none;' id='size_max'>".intval($size_max)."</div>\r\n";
	$a .=  "<div style='display: none;' id='price_min'>".intval($price_min)."</div>\r\n";
	$a .=  "<div style='display: none;' id='price_max'>".intval($price_max)."</div>\r\n\r\n";
		
	//data required by javascript
	$a .= "<div style='display: none;' id='sb_server_address'>".$sb_config['server_address']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_search_page'>".$sb_config['search_page_v2']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1'>".$sb_config['currency_1']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1_symbol'>".get_symbol($sb_config['currency_1'])."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2'>".$sb_config['currency_2']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2_symbol'>".get_symbol($sb_config['currency_2'])."</div>\r\n";
	$a .= "<div class='eur_rate' style='display: none;'>".$sb_config['eur_rate']."</div>\r\n";
	$a .= "<div class='usd_rate' style='display: none;'>".$sb_config['usd_rate']."</div>\r\n";
	
	$a .= "</form>
	</div> <!-- end smartbroker_section smartbroker_group-->
	<hr/>";
	return $a;
	}
?>