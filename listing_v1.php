<?php

function sb_listing_v1_func() {
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
		return "<p>".__("I'm sorry, we can't find the boat you requested.",'smartbroker')."</p>
		<p><a href='/?page_id=".$sb_config['search_page_v2']."'>".__('Go to the search page','smartbroker')."</a></p>";
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
	elseif ($vat_paid == '1') {$vat_message = $sb_config['tax_label']." ".__('paid','smartbroker');}
	else {$vat_message = $sb_config['tax_label']." ".__('not paid','smartbroker');}
	
	//add currency conversion if not in primary currency
	$price = floatval($xml->boat->asking_price);
	$currency_conversion = currency_conversion($price, $currency, $xml['USD'], $xml['EUR']);
	
	//format provisional message
	$prov = $xml->boat->approved;
	$prov_message = '';
	if (!$prov) {
		$prov_message = "<p style='text-align: center;'><i>".__('This listing contains some provisional information: details may change','smartbroker')."</i></p>";
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
		$photos_pt1 = sprintf(__('%s more photos &amp; videos of this boat are available.','smartbroker'), $num_photos);
		$llnk = wp_login_url(get_permalink()."&boat_id=".$_GET['boat_id']);
		$photos_pt2 = sprintf(__("Please <a href='/wp-register.php' title='Register'>register</a> or <a href='%s' title='Login'>log in</a> to see them.",'smartbroker'), $llnk);
		$m = "<p>$photos_pt1 $photos_pt_2</p>";
		}
		
	//sort find out more tab
	if (is_user_logged_in() OR ($sb_config['sb_tracking'] != 'on')) {
		$find_out_more = "<p>".__("Interested in this boat? Find out more or arrange a viewing by completing this form.
		We'll get back to you with more information and take you thorough the options for viewing and buying this boat.",'smartbroker')."</p>
		<p>".
		sprintf(__("Alternatively, don't forget that you can call us on <b>%s</b> anytime for a chat.",'smartbroker'),$sb_config[phone])."</p>
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
		<td><p>".__('Your email address:','smartbroker')."</p></td>
		<td><p><input type='text' name='cwr' value='$user_email' size='19' /></p></td>
		</tr>
		
		
		<tr>
		<td><p>".__('Phone number:','smartbroker')."</p></td>
		<td><p><input type='text' name='phone' size='15' /></p></td>
		</tr>
		
		<tr>
		<td><p>".__('Preferred contact method:','smartbroker')."</p></td>
		<td><p><input type='radio' name='contact_method' value='phone' checked='checked' />&nbsp;".__('Phone','smartbroker')."
			<br /><input type='radio' name='contact_method' value='email' />&nbsp;".__('Email','smartbroker')."</p></td>
		</tr>
		
		<tr>
		<td style='vertical-align: top;'><p>".__('Notes:','smartbroker')."</p></td>
		<td><p><textarea name='notes' rows='5' cols='30'></textarea></p></td>
		</tr>
		
		<tr>
		<td>
		<input type='hidden' name='boat_id' value='$_GET[boat_id]' />
		<input type='hidden' name='admin_email' value='".$sb_config['email']."' />
		<input type='hidden' name='path' value='http://".$_SERVER['SERVER_NAME']."/?page_id=".$sb_config['listing_page']."' />
		</td>
		<td><button type='submit' class='button'><p>".__('Send enquiry','smartbroker')."</p></button></td>
		</tr>
		
		</table></form>";
		if (array_key_exists('enquiry_success',$_GET)) {
			if ($_GET['enquiry_success'] == 'true') {
				$m = __("Thankyou for your enquiry. We will be in touch soon.",'smartbroker');
				$s = 'ui-state-highlight';
				} elseif ($_GET['enquiry_success'] == 'false') {
				$m = sprintf(__("I'm afraid there has been a technical fault - your enquiry could not be sent to our system. Please call us on %s instead.",'smartbroker'),$sb_config[phone]);
				$s = 'ui-state-error';
				}
			$find_out_more .= "<div class='ui-widget $s ui-corner-all'>
			<p><span class='ui-icon ui-icon-info' style='float: left; margin-right: 5px;'></span>$m</p></div>";
			}
		} else {
		$find_out_more = "<p>";
		$llnk = wp_login_url(get_permalink()."&boat_id=".$_GET['boat_id']);
		$find_out_more .= sprintf(__("To find out more about this yacht, please <a href='/wp-register.php' title='Register'>register</a> or 
		<a href='%s' title='Login'>log in</a>.<br/>Registration is free and takes seconds.",'smartbroker'), $llnk);
		$find_out_more .= "</p><p>";
		$find_out_more .= sprintf(__("Don't forget you can call us on %s anytime for a chat.",'smartbroker'),$sb_config[phone]);
		}
	
	$admin_link = '';
	if (current_user_can('manage_options')) {
		$admin_link = " <a href='".$sb_config['server_address']."/system/wp_plugin/edit_boat.php
		?boat_id=".$_GET['boat_id']."&user_email=".urlencode($user_email)."'>
		[edit]</a>";
		}
		
	$brokers_notes = nl2br($xml->boat->brokers_notes);
	if ($brokers_notes == '') {
		$brokers_notes = "<em>".__("(There are no broker's notes available for this boat)",'smartbroker')."</em>";
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
					<tr id='ref'><td><p>".__("Boat reference",'smartbroker')."</p></td><td><p><i><b>$boat_id</b></i></p></td></tr>
					
					<tr id='status_row'><td><p>".__('Status','smartbroker')."</p></td><td><p>".$xml->boat->status_text."</p></td></tr>
					
					<tr><td><p>".__('Builder','smartbroker')."</p></td><td><p>".$xml->boat->builder."</p></td></tr>
					<tr><td><p>".__('Boat model','smartbroker')."</p></td><td><p>".$xml->boat->model."</p></td></tr>
					<tr><td><p>".__('Type','smartbroker')."</p></td><td><p>".$xml->boat->type_description."</p></td></tr>
					<tr><td><p>".__('LOA','smartbroker')."</p></td><td><p>".round($xml->boat->length)." ft (".round(floatval($xml->boat->length)/3.28)." m)</p></td></tr>
					<tr><td><p>".__('Built','smartbroker')."</p></td><td><p>".$xml->boat->year."</p></td></tr>
					<tr><td><p>".__('Currently lying','smartbroker')."</p></td><td><p>".$xml->boat->region.", ".$xml->boat->country_name."</p></td></tr>
					<tr><td><p>".__('Price','smartbroker')."</p></td>
					<td><p>".$curr_symbol.number_format($price)." ".$vat_message.'<br />'.$currency_conversion."</p></td></tr>
				</table>
			</div>
		</div>
	</div>
	<div class='smartbroker_col smartbroker_span_3_of_5'>
		
		<div id='sb_tabs_container'>
			<div id='tabs'>
					<ul id='tab_links'>
						<li><p><a href='#sb_broker_notes'>".__('Notes','smartbroker')."</a></p></li>
						<li><p><a href='#sb_specifications'>".__('Specifications','smartbroker')."</a></p></li>
						<li><p><a href='#sb_photos' id='photos_tab'>".__('Photos &amp; videos','smartbroker')."</a></p></li>
						<li><p><a href='#sb_find_out_more' id='contact_tab'>".__('Find out more','smartbroker')."</a></p></li>
										</ul>
					<div id='sb_broker_notes'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>".__("Broker's notes",'smartbroker')."</p></div>
						<div class='ui-widget-content ui-corner-bottom'>
							<p>".$brokers_notes."</p>
						</div>
					</div>
					<div id='sb_specifications'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>".__('Specifications','smartbroker')."</p></div>
						<div class='ui-widget-content ui-corner-bottom'>$cats</div>
					</div>
					<div id='sb_photos'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>".__('Photos &amp; videos','smartbroker')."</p></div>
						<div class='ui-widget-content ui-corner-bottom'>$m</div>
					</div>
					<div id='sb_find_out_more'>
						<div class='smartbroker_tab_header ui-widget-header ui-corner-top'><p>".__('Find out more','smartbroker')."</p></div>
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
?>