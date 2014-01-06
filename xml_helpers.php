<?php
function sb_load_xml($xml_file) {
	global $sb_config;
	echo "\n<!-- xml file: $xml_file -->\n";
	$data = FALSE;
	$error = '';
	$error_msg = '';
	if (ini_get('allow_url_fopen')) { 
		echo "<!-- SmartBroker: Loading XML via file_get_contents-->\n";
		
		$data = @file_get_contents($xml_file);
		if ($data === FALSE) {
			echo "<!-- SmartBroker: Loading XML via file_get_contents fail -->\n";
			$error .= "file_get_contents failure for '$xml_file'.\r\n";
			} else {
			echo "<!-- SmartBroker: Loading XML via file_get_contents success: ".strlen($data)." bytes loaded-->\n";
			}
		}
	if (($data === FALSE) AND (function_exists("curl_exec"))) {
		echo "\n<!-- SmartBroker: Loading XML via cURL-->\n";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $xml_file);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data === FALSE) {
			echo "<!-- SmartBroker: Loading XML via cURL fail -->\n";
			$error .= "cURL failure for '$xml_file'.\r\n";
			} else {
			echo "<!-- SmartBroker: Loading XML via cURL success: ".strlen($data)." bytes loaded-->\n";
			}
		} elseif ($data === FALSE) {
		echo "<!-- SmartBroker: No way of loading data! -->\n";
		$error .= "No cURL option available either, exiting.\r\n";
		}
	
	libxml_use_internal_errors(true);
	$sxe = @simplexml_load_string($data);
	
	if ($sxe === FALSE) {
		$error .= "simple_xml_load_string error for data from for '$xml_file'. Errors follow:\r\n";
		 foreach (libxml_get_errors() as $e) {
			$error .= display_xml_error($e, $xml);
			}
		if ($data == 'Error: ID not valid') {
			$error_msg =  "<h3>Well, this is embarrassing...</h3>
			<p>I'm afraid we can't find the listing you're looking for. Maybe <a href='?page_id=".$sb_config['search_page']."' >search again</a>?</p>";
			}
		
		
		} else {
		echo "<!-- SmartBroker: Data conversion to XML success -->\r\n";
		return $sxe;
		}
	
	//if we get here we've got errors
	wp_mail('contact@smart-broker.co.uk','XML load error',"<pre>\r\n".$error."\n\nRequest URI: http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']."\r\n</pre>");
	if ($error_msg != '' ) {
		echo $error_msg;
		} else {
		echo "<h3>Well, this is embarrassing...</h3>
		<p>It appears that we're having a small technical problem at the moment. This issue has been reported to our support staff, so please check back here soon.</p>";
		}
	return FALSE;
	}
	
function load_fields_xml() {
	global $sb_config;
	$xml_file = $sb_config['server_address']."/system/wp_plugin/search_box_fields.php";
	$xml = sb_load_xml($xml_file);
	if ($xml !== FALSE) {
		foreach($xml->currencies->currency as $c) {
			$sb_config['currencies'][strval($c->currency)] = array('rate' => floatval($c->rate), 'symbol' => strval($c->symbol), 'name' => strval($c->name), 'suffix'=>strval($c->suffix)); 
			}
		$sb_config['price_min'] = $xml->limits->price_min;
		$sb_config['price_max'] = $xml->limits->price_max;
		$sb_config['size_min'] = $xml->limits->length_min;
		$sb_config['size_max'] = $xml->limits->length_max;
		$sb_config['year_max'] = $xml->limits->year_max;
		$sb_config['year_min'] = $xml->limits->year_min;
		foreach ($xml->units->unit as $u) {
			$sb_config['units'][strval($u->name)] = strval($u->dimension);
			}
		
		//convert price min and price max values into currency 1 (from EUR)
		$sb_config['price_min'] = $sb_config['price_min'] * $sb_config['currencies'][$sb_config['currency_1']]['rate'];
		$sb_config['price_max'] = $sb_config['price_max'] * $sb_config['currencies'][$sb_config['currency_1']]['rate'];
		
		//round to nearest 1000
		$sb_config['price_min'] = floor($sb_config['price_min'] / 1000) * 1000;
		$sb_config['price_max'] = ceil($sb_config['price_max'] / 1000) * 1000;
		
		return $xml;
		}
	return FALSE;
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
	
function display_xml_error($error, $xml) {
    $return  = $xml[$error->line - 1] . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line" .
               "\n  Column: $error->column";

    if ($error->file) {
        $return .= "\n  File: $error->file";
    }

    return "$return\n\n--------------------------------------------\n\n";
	}
?>