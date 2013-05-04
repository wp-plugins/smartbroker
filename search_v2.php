<?
include_once "pagination.php";
function sb_search_page_v2_func($atts) {
	
	global $sb_config, $post;
	extract( shortcode_atts( array(
		'size_low' => '28',
		'size_high' => '45',
		'size_min' => '10',
		'size_max' => '100',
		'price_low' => '30000',
		'price_high' => '150000',
		'price_min' => '200',
		'price_max' =>'2000000',
		'results_per_page' => '10',
		'keyword_examples' => "e.g. roller furling, fridge"
		), $atts ) );
	
	if (array_key_exists('sl', $_GET) AND ($_GET['sl'] != '')) {$data['sl'] = intval($_GET['sl']);} else {$data['sl'] = $size_low;}	
	if (array_key_exists('sh', $_GET) AND ($_GET['sh'] != '')) {$data['sh'] = intval($_GET['sh']);}	else {$data['sh'] = $size_high;}	
	if (array_key_exists('pl', $_GET) AND ($_GET['pl'] != '')) {$data['pl'] = intval($_GET['pl']);}	else {$data['pl'] = $price_low;}	
	if (array_key_exists('ph', $_GET) AND ($_GET['ph'] != '')) {$data['ph'] = intval($_GET['ph']);} else {$data['ph'] = $price_high;}	
	
	$data['ln'] = $results_per_page;
	
	$fields_xml = load_fields_xml();
	
	//add sliders
	echo "<div class='sb_wrapper'>
		<div class='smartbroker_section smartbroker_group'>
		<div class='smartbroker_col smartbroker_span_2_of_5'>
		<div class='ui-widget ui-widget-header ui-corner-top header'><p>Search for boats</p></div>
		<div class='ui-widget ui-widget-content ui-corner-bottom content' style='display: block !important;'>
		<form method='get' action='/' id='boat_search_v2'><input type='hidden' name='page_id' value='".$post->ID."' />";
	echo "<p>Boat type: ".create_type_dropdown($fields_xml)."</p>";
	echo "<p>Boat size: <span class='sizedesc'></span></p>
		<div class='slider_container' title='Drag handles to set boat size'><div class='slider_size slider'></div></div><br/>
		<p>Price: <span class='pricedesc'></span></p>
		<div class='slider_container' title='Drag handles to set boat price'><div class='slider_price slider'></div></div>";;
	//receiving inputs for slider values
	echo "<input type='hidden' name='pl' class='price_low' value='".intval($data['pl'])."'/>\r\n";
	echo "<input type='hidden' name='ph' class='price_high' value='".intval($data['ph'])."'/>\r\n";
	echo "<input type='hidden' name='sl' class='size_low' value='".intval($data['sl'])."'/>\r\n";
	echo "<input type='hidden' name='sh' class='size_high' value='".intval($data['sh'])."'/>\r\n";
	echo "<input type='hidden' name='ln' value='".intval($data['ln'])."'/>\r\n";
	echo "<div style='display: none;' id='size_min'>".intval($size_min)."</div>\r\n";
	echo "<div style='display: none;' id='size_max'>".intval($size_max)."</div>\r\n";
	echo "<div style='display: none;' id='price_min'>".intval($price_min)."</div>\r\n";
	echo "<div style='display: none;' id='price_max'>".intval($price_max)."</div>\r\n\r\n";
	
	//currency input field (uses primmary currency from sb_config)
	
	echo "<p><br/>Keywords: <input name='lk' value=\"".stripslashes($_GET['lk'])."\"></input>
	<br/><em>".$keyword_examples."</em></p>";
	
	echo "<p>Builder: ".create_builder_dropdown($fields_xml)."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Available makes are listed'>&nbsp;</span></p>";
	echo "<p>Currently lying: ".create_country_dropdown($fields_xml)."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Boats are available in the countries listed.'>&nbsp;</span></p>";
	echo "<p>Built after: ".create_built_after_dropdown()."</p>";
	echo "<button type='submit' class='button'/><p>Search</p></button></form></div>
	<div class='ui-widget ui-widget-header ui-corner-top header' style='margin-top: .5em;'>
	<p>Find by reference number</p></div>
	<div class='ui-widget ui-widget-content ui-corner-bottom content' style='padding: 0em .5em; display: block !important;'>
		<form method='get' action='/' target='_parent'>
		<input type='hidden' name='page_id' value='".$post->ID."'/>
		<table><tr><td style='vertical-align: middle;'><p>Boat reference:</p></td>
		<td style='vertical-align: middle;'><p><input type='text' name='id' size='10'/></p></td>
		<td style='vertical-align: middle;'><button class='button' type='submit'><p>Go</p></button></td>
		</tr></table>
		</form>
	</div>";
	echo "</div><div class='smartbroker_col smartbroker_span_3_of_5'>
	<div class='ui-widget ui-widget-header ui-corner-top header'><p>Search results</p></div>
	<div class='ui-widget ui-widget-content ui-corner-bottom content' id='sb_search_results' style='display: block !important;'>
	<table>";
	

	$xml = load_results_xml($data);
	$total_rows = $xml['count'];
	$returned_rows = count($xml);
	$requested_rows = $xml['requested_rows'];
	$start_row = $xml['start'];
	$end_row = $start_row + $returned_rows - 1;
	
	if ($total_rows > 0) {
		echo "<p>&nbsp;$total_rows results found - showing results $start_row to $end_row.</p><table id='results'>";
		foreach ($xml->boat as $boat) {
			echo search_v2_result_item($boat);
			}
		echo "</table>";
		echo pagination_links($total_rows,$start_row,$requested_rows);
		} else {
		//echo "<p>&nbsp;No results found</p>";
		echo blank_slate_row();
		}
	echo "</table></div></div></div></div>";
	
		
	//data required by javascript
	$a = "<div style='display: none;' id='sb_server_address'>".$sb_config['server_address']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_listing_page'>".$sb_config['listing_page']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1'>".$sb_config['currency_1']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1_symbol'>".get_symbol($sb_config['currency_1'])."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2'>".$sb_config['currency_2']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2_symbol'>".get_symbol($sb_config['currency_2'])."</div>\r\n";
	$a .= "<div class='eur_rate' style='display:none;'>".$sb_config['eur_rate']."</div>\r\n";
	$a .= "<div class='usd_rate' style='display:none;'>".$sb_config['usd_rate']."</div>\r\n";
	echo $a;
	}
	
function load_fields_xml() {
	global $sb_config;
	$xml_file = $sb_config['server_address']."/system/wp_plugin/search_box_fields.php?auth=$sb_config[auth]";
	$xml = sb_load_xml($xml_file);
	$sb_config['eur_rate'] = $xml['EUR'];
	$sb_config['usd_rate'] = $xml['USD'];
	return $xml;
	}
	
function load_results_xml($data) {
	global $sb_config;
	foreach ($data as $k => $v) {
		if (intval($_GET[$k]) == 0) {
			$_GET[$k] = $v;
			}
		}
	$search_string = http_build_query($_GET);
	$xml_file = $sb_config['server_address']."/system/wp_plugin/search.php?auth=$sb_config[auth]&".$search_string;
	$xml =  sb_load_xml($xml_file);
	return $xml;
	}
	
function create_country_dropdown($xml) {
	$a =  "<select name='cn'><option value=''>Any country</option>";
	foreach ($xml->countries->country as $country) {
		$s = '';
		$code = strtolower($country->code);
		if ($_GET['cn'] == $code) {
			$s = "selected='selected'";
			}
		$a .= "<option value='".$code."' $s>".$country->name."</option>";
		}
	$a .= "</select>";
	return $a;
	}
function create_type_dropdown($xml) {
	$sail_s = '';
	$power_s = '';
	if ($_GET['tp'] == 's') {$sail_s = "selected='selected'";}
	if ($_GET['tp'] == 'p') {$power_s = "selected='selected'";}
	$a =  "<select name='tp'>";
	$a .= "<option value='a'>All boat types</option>";
	
	$sail_group = "<optgroup label='Sail'>";
	$sail_group .= "<option value='s' $sail_s>All sail boats</option>";
	
	$power_group = "<optgroup label='Power'>";
	$power_group .= "<option value='p' $power_s>All power boats</option>";
	
	foreach ($xml->boat_types->type as $type) {
		$s = '';
		$code = $type->id;
		if ($_GET['tp'] == $code) {
			$s = "selected='selected'";
			}
		if ($code < 6) {
			$sail_group .= "<option value='".$type->id."' title='".$type->notes."' $s>".$type->type."</option>";
			} else {
			$power_group .= "<option value='".$type->id."' title='".$type->notes."' $s>".$type->type."</option>";
			}
		}
	$sail_group .= "</optgroup>";
	$power_group .= "</optgroup>";
	$a .= $sail_group.$power_group."</select>";
	return $a;
	}
function create_builder_dropdown($xml) {
	$a =  "<select name='bd'><option value=''>Any builder</option>";
	foreach ($xml->builders->builder as $builder) {
		$s = '';
		$code = $builder;
		if ($_GET['bd'] == $builder) {
			$s = "selected='selected'";
			}
		$a .= "<option value='".$builder."' $s>".$builder."</option>";
		}
	$a .= "</select>";
	return $a;
	}

function create_built_after_dropdown() {
	$a =  "<select name='bt'><option value=''>Show all</option>";
	$n = date('Y', time()) - 1;
	while ($n >= 1970) {
		$s = '';
		if ($_GET['bt'] == $n) {
			$s = "selected='selected'";
			}
		$a .= "<option value='".$n."' $s>".$n."</option>";
		$n--;
		}
	$a .= "</select>";
	return $a;
	}
	
function search_v2_result_item($boat) {
		global $sb_config;
		
		$link = "/?page_id=".$sb_config['listing_page']."&boat_id=".$boat->boat_id.'#'.$sb_config['listing_default_tab'];
		$img_link = $sb_config['server_address']."/images/boats/".$boat->boat_id."/small/".str_replace("/","-",$boat->model)."-".$boat->photo_id.".jpg";
		$desc = $boat->builder." ".$boat->model;
		
		if ($sb_config['hide_tax_label'] == 'on') {
			$vat_message = '';}
			elseif ($boat->vat_paid == '1') {
			$vat_message = $sb_config['tax_label']." paid";}
			else {$vat_message = $sb_config['tax_label']." not paid";}
			
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
			$currency_conversion = '<br />'.$currency_conversion;
			}
		
		return "
		<tr clas=='sb_search_result'>
		<td>
			<a href='$link' style='display: block; width: 130px; height: 90px;'>
			<img src='$img_link' alt='$desc'
			title='$desc' style='padding: 5px; height: 80px; width: 120px;'>
			</a>
		</td>
		<td style='text-align: left; vertical-align: middle;'>
			<h3><a href='$link'>$desc</a></h3>
			<p>$length, built $boat->year, lying $boat->region $boat->country</p>
		</td>
		<td style='text-align: center; vertical-align: middle;'>
			<p>$curr_symbol $price$currency_conversion<br/>$vat_message</p>
		</td>
		<td style='text-align: center; vertical-align: middle;'>
		</td></tr>";
		}
	
	function blank_slate_row() {
		global $sb_config;
		global $post;
		return "<tr><td colspan='4'><p><em>No results match your search.</em><br /><br />Quick searches:</p>
			<p>Show all: <a href='?page_id=".$post->ID."&tp=s'>
			 Sail</a> | 
			<a href='?page_id=".$post->ID."&tp=p'>
			Power</a> | 
			<a href='?page_id=".$post->ID."&type=all&country=any&built=any&fuel=any&price_low=1&price_high=1000000&size_low=10&size_high=120&order=phl'>
			Either</a></p></td></tr>";
		}
		
function sb_search_box_v2_func($atts) {
	extract( shortcode_atts( array(
		'size_low' => '28',
		'size_high' => '45',
		'size_min' => '10',
		'size_max' => '100',
		'price_low' => '30000',
		'price_high' => '150000',
		'price_min' => '200',
		'price_max' =>'2000000',
		'keyword_examples' => "e.g. roller furling, fridge"
	), $atts ) );
	global $sb_config;
	$fields_xml = load_fields_xml();
	
	//add sliders
	$a .= "<div class='sb_wrapper' style='max-width: 400px;'>
		<div class='ui-widget ui-widget-header ui-corner-top header'><p>Search for boats</p></div>
		<div class='ui-widget ui-widget-content ui-corner-bottom content' style='display: block !important;'>
		<form method='get' action='/' id='boat_search_v2'><input type='hidden' name='page_id' value='".$post->ID."' />";
	$a .= "<p>Boat type: ".create_type_dropdown($fields_xml)."</p>";
	$a .= "<p>Boat size: <span class='sizedesc'></span></p>
		<div class='slider_container' title='Drag handles to set boat size'><div class='slider_size slider'></div></div><br/>
		<p>Price: <span class='pricedesc'></span></p>
		<div class='slider_container' title='Drag handles to set boat price'><div class='slider_price slider'></div></div>";;
	//receiving inputs for slider values
	$a .= "<div style='display: none;' id='sb_currency_1'>".$sb_config['currency_1']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_1_symbol'>".get_symbol($sb_config['currency_1'])."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2'>".$sb_config['currency_2']."</div>\r\n";
	$a .= "<div style='display: none;' id='sb_currency_2_symbol'>".get_symbol($sb_config['currency_2'])."</div>\r\n";
	$a .= "<input type='hidden' name='page_id' value='".$sb_config['search_page_v2']."'/>\r\n";
	$a .= "<input type='hidden' name='pl' class='price_low' value='".intval($price_low)."'/>\r\n";
	$a .= "<input type='hidden' name='ph' class='price_high' value='".intval($price_high)."'/>\r\n";
	$a .= "<input type='hidden' name='sl' class='size_low' value='".intval($size_low)."'/>\r\n";
	$a .= "<input type='hidden' name='sh' class='size_high' value='".intval($size_high)."'/>\r\n";
	$a .= "<input type='hidden' name='ln' value='".intval($data['ln'])."'/>\r\n";
	$a .= "<div style='display: none;' id='size_min'>".intval($size_min)."</div>\r\n";
	$a .= "<div style='display: none;' id='size_max'>".intval($size_max)."</div>\r\n";
	$a .= "<div style='display: none;' id='price_min'>".intval($price_min)."</div>\r\n";
	$a .= "<div style='display: none;' id='price_max'>".intval($price_max)."</div>\r\n";
	$a .= "<div class='eur_rate' style='display:none;'>".$sb_config['eur_rate']."</div>\r\n";
	$a .= "<div class='usd_rate' style='display:none;'>".$sb_config['usd_rate']."</div>\r\n";
	
	//currency input field (uses primmary currency from sb_config)
	
	$a .= "<p><br/>Keywords: <input name='lk' value=\"".stripslashes($_GET['lk'])."\"></input>
	<br/><em>".$keyword_examples."</em></p>";
	
	$a .= "<p>Builder: ".create_builder_dropdown($fields_xml)."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Available makes are listed'>&nbsp;</span></p>";
	$a .= "<p>Currently lying: ".create_country_dropdown($fields_xml)."&nbsp;<span class='ui-icon ui-icon-info search_icon' title='Boats are available in the countries listed.'>&nbsp;</span></p>";
	$a .= "<p>Built after: ".create_built_after_dropdown()."</p>";
	$a .= "<button type='submit' class='button'/><p>Search</p></button></form></div></div>";
	return $a;
	}
?>