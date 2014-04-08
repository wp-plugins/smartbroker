<?php
if (is_user_logged_in() OR ($sb_config['sb_tracking'] != 'on')) {
	$find_out_more = "
	<p>".nl2br($xml->config->find_out_more_form_intro)."</p>
	<hr />
	
	<form action='".$sb_config['server_address']."/system/wp_plugin/wp_plugin_enquire.php' method='post'>
	<p>Your name:<br/>
	<input type='text' name='name' value='$user_identity' size='14' /></p>
	
	<div id='hpt'>
	<p>Please leave empty</p><br/>
	<input type='email' name='email_address' /></p>
	</div>
	
	<p>".__('Your email address:','smartbroker')."<br />
	<input type='email' name='cwr' value='$user_email' size='19' /></p>

	<p>".__('Phone number:','smartbroker')."<br />
	<input type='tel' name='phone' size='15' /></p>
	
	<p>".__('Preferred contact method:','smartbroker')."<br/>
	<input type='radio' name='contact_method' value='phone' checked='checked' />&nbsp;".__('Phone','smartbroker')."<br />
	<input type='radio' name='contact_method' value='email' />&nbsp;".__('Email','smartbroker')."</p>

	<p>".__('Notes:','smartbroker')."<br />
	<textarea name='notes' rows='5' cols='30'></textarea></p>

	<input type='hidden' name='boat_id' value='$_GET[boat_id]' />
	<input type='hidden' name='admin_email' value='".$xml->config->email."' />
	<input type='hidden' name='path' value='http://".$_SERVER['SERVER_NAME']."/?page_id=".$sb_config['listing_page']."' />
	
	<button type='submit'>".__('Send enquiry','smartbroker')."</button><br/><br/>
	
	</form>";
	$_GET = stripslashes_deep($_GET);
	if (array_key_exists('msg',$_GET)) {
		$m = $_GET['msg'];
		if (array_key_exists('state',$_GET)) {
			$s = $_GET['state'];
			} else {
			$s = '';
			}
		$find_out_more .= "<div id='sb_response_msg'>
		<p><strong>$m</strong></p></div>";
		}
	} else {
	$find_out_more = "<p>";
	$llnk = wp_login_url(get_permalink()."&boat_id=".$_GET['boat_id']);
	$find_out_more .= sprintf(__("To find out more about this yacht, please <a href='/wp-register.php' title='Register'>register</a> or 
	<a href='%s' title='Login'>log in</a>.<br/>Registration is free and takes seconds.",'smartbroker'), $llnk);
	$find_out_more .= "</p><p>";
	$find_out_more .= sprintf(__("Don't forget you can call us on %s anytime for a chat.",'smartbroker'),$xml->config->phone);
	}
	
?>