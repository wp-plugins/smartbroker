<?php

function sb_listing_clean_func(){
	global $sb_config, $user_email, $user_identity, $current_user;
	$boat_id = '';
	if (array_key_exists('boat_id', $_GET)) {$boat_id = $_GET['boat_id'];}
	
	$sb_config['video_link'] = '<div class="sb_clean_thumb" style="display: none;">
		<a href="http://www.youtube.com/watch?v=%1$s?rel=0&wmode=opaque&modestbranding=1&showinfo=0&theme=light" rel="sb_prettyPhoto[all]" title="Video: %2$s" style="text-decoration: none;">
			<div class="sb_clean_vt_holder">
				<span>&#9654;</span>
			</div>
		</a>
	</div>';
	
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
	$k = '';
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
		if ((strlen($i) > strlen($j)) AND (strlen($j) >= strlen($k))){$k .= $e;}
		elseif ((strlen($i) > strlen($k)) AND (strlen($k) >= strlen($j))) {$j .= $e;}
		else {$i .= $e;}
		}
	$cats = "<div class='smartbroker_section smartbroker_group'>
				<div class='smartbroker_col smartbroker_span_1_of_3'>$i</div>
				<div class='smartbroker_col smartbroker_span_1_of_3'>$j</div>
				<div class='smartbroker_col smartbroker_span_1_of_3'>$k</div>
			</div>";
	
	//sort photos out
	function build_photo($link, $path, $desc, $boat_id, $xml) {
        global $sb_config;
		$primary_path = "/images/boats/$boat_id/large/".str_replace("/","-",$xml->boat->model)."-".$xml->boat->primary_photo.".jpg";
		if ($link != $primary_path) {
			return "<div class='sb_clean_thumb'>
				<a href='".$sb_config['server_address'].$link."' rel='sb_prettyPhoto[all]' title='".$desc."'>
					<img src='".$sb_config['server_address'].$path."' alt='' />
				</a>
			</div>";
			}
		return '';
        }
	$m = "<div id='sb_clean_thumb_window'>";

	foreach($xml->boat->media->exterior->media as $media) {
		if ($media['type'] == 'photo') {
			$m .= build_photo($media->link,$media->path,$media->description, $boat_id, $xml);
			} elseif ($media['type'] == 'video') {
			$m .= sprintf($sb_config['video_link'],$media->video_id,$media->description);
			}
		}
	
	foreach($xml->boat->media->interior->media as $media) {
		if ($media['type'] == 'photo') {
			$m .= build_photo($media->link,$media->path,$media->description, $boat_id, $xml);
			}
		}
	
	foreach($xml->boat->media->technical->media as $media) {
		if ($media['type'] == 'photo') {
			$m .= build_photo($media->link,$media->path,$media->description, $boat_id, $xml);
			}
		}
	
	$m .= "</div>";
	
	//count photos and videos	
	$photos = $xml->xpath('//*[@type="photo"]');
	$num_photos = count($photos);
	$videos = $xml->xpath('//*[@type="video"]');
	$num_videos = count($videos);
		
	if (($num_photos > 8) OR ($num_videos > 0)) {
		$m .= "<a id='sb_view_all_link' href='#' style='text-align: right; display: block;'>+ ";
		if (($num_photos - 9) == 1) {
			$mes .= " 1 more photo";
			}
		if (($num_photos - 9) > 1) {
			$mes .= $num_photos - 9 ." more photos";
			}
		if ($num_videos == 1) {
			if ($mes != '') {
				$mes .= ' and';
				}
			$mes .= " 1 video";
			}
		if ($num_videos > 1)  {
			if ($mes != '') {
				$mes .= ' and';
				}
			$mes .= " $num_videos videos";
			}
		$m .= $mes."</a>";
		}
	
	
	//sort find out more tab
	if (is_user_logged_in() OR ($sb_config['sb_tracking'] != 'on')) {
		$find_out_more = "
		<p>".__("Interested in this boat? Find out more or arrange a viewing by completing this form.
		We'll get back to you with more information and take you thorough the options for viewing and buying this boat.",'smartbroker')."</p>
		<p>".
		sprintf(__("Alternatively, don't forget that you can call us on <b>%s</b> anytime for a chat.",'smartbroker'),$sb_config[phone])."</p>
		<hr />
		
		<form action='".$sb_config['server_address']."/system/wp_plugin/wp_plugin_enquire.php' method='post'>
		<p>Your name:<br/>
		<input type='text' name='name' value='$user_identity' size='14' /></p>
		
		<div id='hpt'>
		<p>Please leave empty</p><br/>
		<input type='text' name='email_address' /></p>
		</div>
		
		<p>".__('Your email address:','smartbroker')."<br />
		<input type='text' name='cwr' value='$user_email' size='19' /></p>

		<p>".__('Phone number:','smartbroker')."<br />
		<input type='text' name='phone' size='15' /></p>
		
		<p>".__('Preferred contact method:','smartbroker')."<br/>
		<input type='radio' name='contact_method' value='phone' checked='checked' />&nbsp;".__('Phone','smartbroker')."<br />
		<input type='radio' name='contact_method' value='email' />&nbsp;".__('Email','smartbroker')."</p>
	
		<p>".__('Notes:','smartbroker')."<br />
		<textarea name='notes' rows='5' cols='30'></textarea></p>
	
		<input type='hidden' name='boat_id' value='$_GET[boat_id]' />
		<input type='hidden' name='admin_email' value='".$sb_config['email']."' />
		<input type='hidden' name='path' value='http://".$_SERVER['SERVER_NAME']."/?page_id=".$sb_config['listing_page']."' />
		
		<button type='submit'>".__('Send enquiry','smartbroker')."</button><br/><br/>
		
		</form>";
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
	
	//images
	$image_link = $sb_config['server_address']."/images/boats/$boat_id/large/".str_replace("/","-",$xml->boat->model)."-".$xml->boat->primary_photo.".jpg";
	$image = "<div id='sb_primary_image'>
			<a href='$image_link' rel='sb_prettyPhoto[all]' title=''>
			<img src='$image_link' title='' alt='".$xml->boats->model."'/>
			</a>	 
		</div>
		<div class='sb_no_print'>$m</div>";
	
	//key facts
	$kf = "<div class='sb_wide_only'><h1 style='clear: left;'><span id='sb_boat_builder_and_model'>".$xml->boat->builder." ".$xml->boat->model."</span>".$admin_link."</h1></div>
	<h2 style='clear: left;'>".$curr_symbol.number_format($price)." ".$vat_message."
	<br /><small>".$currency_conversion."</small></h2>
	<p>".__('Built','smartbroker').': '.$xml->boat->year.'<br/>'.__('Currently lying','smartbroker').': '.$xml->boat->region.", ".$xml->boat->country_name.'</p>';
	if ($prov_message != '') {
		$kf .= "<p><em>$prov_message</em></p>";
		}
	
	//data required by javascript
	$a = "<div style='display: none;' id='server_address_address'>".$sb_config['server_address']."</div>";
	$a .= "<div style='display: none;' id='sb_listing_page'>".$sb_config['listing_page']."</div>";
	
	
	//main layout
	$a .= "
	<div class='sb_wrapper sb_theme_clean'>
	<div class='sb_narrow_only'><h1 style='clear: left;'><span id='sb_boat_builder_and_model'>".$xml->boat->builder." ".$xml->boat->model."</span><small>".$admin_link."</small></h1></div>
	<div>
		<div id='sb_clean_images'>
			$image
		</div>
		$kf
		<div class='sb_no_print'>
			<button onclick=\"location.href='#sb_clean_find_out_more';\" \>
				".__('Contact us about this boat','smartbroker')."
			</button>
			<br/><br/>
		</div>
		<hr/>
		<p><br/>$brokers_notes</p>
	</div>
	$cats
	<hr/><div class='sb_no_print'><a name='sb_clean_find_out_more'></a><h3>".__('Find out more','smartbroker')."</h3>$find_out_more</div>";
	
	if ($sb_config['disclaim'] != '') {
		$a .= "<hr /><p><small>$sb_config[disclaim]</small></p>";
		}
	
	$a .= "</div> <!--end sb_wrapper -->";
	return $a;
	}
?>