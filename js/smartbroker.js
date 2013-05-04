 jQuery.noConflict();

jQuery.fn.sb_slider = jQuery.fn.slider;

delete jQuery.fn.slider;
 
 jQuery(document).ready(function($){
 
	//hide honeypot
	$('#hpt').hide();
	
	var sb_server = $('#sb_server_address').html();
	var sb_listing_page = $('#sb_listing_page').html();
	
	$('#tabs').tabs();
	
	/*set page title*/
	var newtitle = $('#sb_boat_builder_and_model').html();
	if (newtitle) {
		var oldtitle = $('title').html();
		var endpart = oldtitle.split("|");
		$('title').html(newtitle+" |"+endpart[1]);
		}
	
	$('.button').addClass('ui-state-default ui-corner-all bold').hover(function() {
		$(this).addClass('ui-state-hover');
		}, function() {
		$(this).removeClass('ui-state-hover');
		});
	
	$("a[rel^='sb_prettyPhoto']").prettyPhoto();
	
	var euroRate = $('.eur_rate').first().html();
	var usdRate = $('.usd_rate').first().html();

 //-------------------------------------------------------------------------------------------------------------------------------------------------------------
 //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	var showAdvanced = false;
 //-------------------------------------------------------------------------------------------------------------------------------------------------------------
 //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 //slider size code
	var sizeLow = $('#size_low_get').html();
	if (sizeLow == null) {sizeLow = $('.size_low').val();}
	if ((sizeLow == '')||(sizeLow == undefined)) {sizeLow = 28;}	
 	var sizeHigh = $('#size_high_get').html();
	if (sizeHigh == null) {sizeHigh = $('.size_high').val();}
	if ((sizeHigh == '')||(sizeHigh == undefined)) {sizeHigh = 45;}
	
	$(".size_low").val(sizeLow);//set hidden field values
	$(".size_high").val(sizeHigh);
	sizeLowM = sigFigs(sizeLow * 0.3048,2);//set text
	sizeHighM= sigFigs(sizeHigh * 0.3048,2);
	$('.sizedesc').html("<span id='sb_size_low'>"+sigFigs(sizeLow,2) + "</span> - <span id='sb_size_high'>" + sigFigs(sizeHigh,2) + "</span> feet | " + sizeLowM + " - " + sizeHighM + " m");
	
	size_min = parseInt($('#size_min').html());
	size_max = parseInt($('#size_max').html());
	
	$('.slider_size').sb_slider({
		animate: true,
		max: size_max,
		min: size_min,
		range: true,
		values: [sizeLow, sizeHigh],
		step: 1,
		orientation: 'horizontal',
		slide: function(event, ui) {
				sizeLow = ui.values[0];
				sizeHigh = ui.values[1];
				//set hidden field values
				$(".size_low").val(sizeLow);
				$(".size_high").val(sizeHigh);
				$('.slider_size').sb_slider({values: [sizeLow, sizeHigh]});
				//set text
				sizeLowM = sigFigs(sizeLow * 0.3048,2);
				sizeHighM= sigFigs(sizeHigh * 0.3048,2);
				$('.sizedesc').html(sigFigs(sizeLow,2) + " - " + sigFigs(sizeHigh,2) + " feet | " + sizeLowM + " - " + sizeHighM + " m");
				}
		});
	


 //-----------------------------------------------------------------------------------------------------------------------------------------------------
 //-----------------------------------------------------------------------------------------------------------------------------------------------------
 //slider price code
	var priceLow = $('#price_low_get').html();
	if (priceLow == null) {priceLow = $('.price_low').val();}
	if ((priceLow == '')||(priceLow == undefined)) {priceLow = 30000;}	
 	var priceHigh = $('#price_high_get').html();
	if (priceHigh == null) {priceHigh = $('.price_high').val();}
	if ((priceHigh == '')||(priceHigh == undefined)) {priceHigh = 150000;}
	
	price_min = parseInt($('#price_min').html());
	price_max = parseInt($('#price_max').html());
	
	var currency_1 = $('#sb_currency_1').html();
	var currency_2 = $('#sb_currency_2').html();
	var currency_1_symbol = $('#sb_currency_1_symbol').html();
	var currency_2_symbol = $('#sb_currency_2_symbol').html();
	
	//work out rate from c1 -> c2
	if ((currency_1 == 'GBP') && (currency_2 == 'EUR')) {
		//convert GBP - EUR
		var rate = euroRate;
		var rateToGbp = 1;
		}
	else if ((currency_1 == 'EUR') && (currency_2 == 'GBP')) {
		//convert EUR - GBP
		var rate = 1 / euroRate;
		var rateToGbp = 1/euroRate;
		}
	else if ((currency_1 == 'GBP') && (currency_2 == 'USD')) {
		//convert GBP - USD
		var rate = usdRate;
		var rateToGbp = 1;
		}
	else if ((currency_1 == 'USD') && (currency_2 == 'GBP')) {
		//convert USD - GBP
		var rate = 1 / usdRate;
		var rateToGbp = 1/usdRate;
		}
	else if ((currency_1 == 'EUR') && (currency_2 == 'USD')) {
		//convert EUR - USD
		var rate = usdRate / euroRate;
		var rateToGbp = 1/euroRate;
		}
	else if ((currency_1 == 'USD') && (currency_2 == 'EUR')) {
		//convert USD - EUR
		var rate = euroRate / usdRate;
		var rateToGbp = 1/usdRate;
		}
	else {
		var rate = 1;
		var rateToGbp = 1;
		}

		
	var priceLow1 = priceLow;
	var priceHigh1 = priceHigh;
	var priceLow2 = sigFigs(priceLow1 * rate,2);;
	var priceHigh2 = sigFigs(priceHigh1 * rate,2);;
	
	
	$(".price_low").val(priceLow1);//set hidden field values
	$(".price_high").val(priceHigh1);
	$('.pricedesc').html(currency_1_symbol + TS(priceLow1) + " - " + currency_1_symbol + TS(priceHigh1) + " | " + currency_2_symbol + TS(priceLow2) + " - " + currency_2_symbol + TS(priceHigh2));

	$('.slider_price').sb_slider({
		animate: true,
		//max: 13815, // ln 1,000,000 * 1000
		max: Math.log(price_max) * 1000,
		//min: 5703, // ln 500 * 1000
		min: Math.log(price_min) * 1000,
		range: true,
		values: [Math.log(priceLow1)*1000, Math.log(priceHigh1)*1000],
		orientation: 'horizontal',
		slide: function(event, ui) {
					priceLow1 = sigFigs(Math.exp((ui.values[0]/1000)),2);
					priceHigh1 = sigFigs(Math.exp((ui.values[1]/1000)),2);
					//set hidden field values
					$(".price_low").val(priceLow1);
					$(".price_high").val(priceHigh1);
					$('.slider_price').sb_slider({values: [Math.log(priceLow1)*1000, Math.log(priceHigh1)*1000]});
					//calculate euro values (for display only)
					priceLow2 = sigFigs(priceLow1 * rate,2);
					priceHigh2 = sigFigs(priceHigh1 * rate,2);
					//set text
					$('.pricedesc').html(currency_1_symbol + TS(priceLow1) + " - " + currency_1_symbol + TS(priceHigh1) + " | " + currency_2_symbol + TS(priceLow2) + " - " + currency_2_symbol + TS(priceHigh2));
			}
		});
	
	
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//advanced search hide/show
	var showAdvanced = true;
	if ($('#size_low_get').html() == null){showAdvanced = false;}

	if (!(showAdvanced)) {
		$('.advanced_search').hide();
		}
	else {
		$('.advanced_search_icon').addClass('ui-icon-circle-triangle-n');
		$('.advanced_search_icon').removeClass('ui-icon-circle-triangle-s')
		}
	$('.advanced_search_handle').click(function() {
		   if ($('.advanced_search').is(":hidden"))
               {
                    $('.advanced_search').slideDown("slow");
					$('.advanced_search_icon').addClass('ui-icon-circle-triangle-n');
					$('.advanced_search_icon').removeClass('ui-icon-circle-triangle-s');
               } else {
                    $('.advanced_search').slideUp("slow");
					$('.advanced_search_icon').addClass('ui-icon-circle-triangle-s');
					$('.advanced_search_icon').removeClass('ui-icon-circle-triangle-n');

               }
		});
		
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//populate region box on country change, control disabled state
	function disable_region() {
		$("select#region").attr("disabled", true).html("&nbsp;");
		$('#country_warning').html("<small><i>&nbsp;(select country first)</i></small>");
		}
	
	function enable_region(region) {
		//$('#country_warning').html("<img src='"+installLocation+"/images/ajax-loader.gif' style='position: relative; top: 3px; left: 5px; margin-right: .5em;' alt='Please wait' />Loading");
		//$.getJSON(installLocation+"/includes/ajax/load_regions.php",{country: $("select#country").val(), v: Math.random()}, function(j){
		//	var optins = "<option value='any'>(show all)</option>";	
		//	for (var i = 0; i < j.length; i++) {
		//		optins += '<option value="' + j[i].optionDisplay + '"';
		//		if (j[i].optionDisplay == region) {
		//			optins += " selected='selected'";
		//			}
		//		optins += '>' + j[i].optionDisplay + '</option>';
				
		//		}
		//	$("select#region").removeAttr("disabled").html(optins);
		//	$('#country_warning').html("&nbsp;");
		//	});
		}

	if ($('#country_get').html() == null || $('#country_get').html() == 'any'){
		disable_region(); //there's no country, disable region box
		}
	else {
		enable_region($('#region_get').html());
		}	
	
	$("select#country").change(function(){
		if ($("select#country").val() == 'any') {
			disable_region();
			}
		else {
			enable_region($('#region_get').html());
			}
		});
	 
		
	//shade alternate lines on table
	$('#search_results_table tr:odd').addClass('odd');


	function sigFigs(n, sig) {
		var mult = Math.pow(10,
			sig - Math.floor(Math.log(n) / Math.LN10) - 1);
		return Math.round(Math.round(n * mult) / mult);
		}

	function TS(v){
		var val = v.toString();
		var result = "";
		var len = val.length;
		while (len > 3){
		result = ","+val.substr(len-3,3)+result;
		len -=3;
		}
		return val.substr(0,len)+result;
		}
	
	///////////////////////////////////////////////////////////
	//search results code
	///////////////////////////////////////////////////////////
	//config fields if get values exist
	var built_get = $('#built_get').html();
	$('select[name="built"] option').filter(function() {
		return ($(this).val() == built_get);
		}).attr('selected', 'selected');
	
	var type_get = $('#type_get').html();
	$('select[name="type"] option').filter(function() {
		return ($(this).val() == type_get);
		}).attr('selected', 'selected');
	
	var fuel_get = $('#fuel_get').html();
	$('select[name="fuel"] option').filter(function() {
		return ($(this).val() == fuel_get);
		}).attr('selected', 'selected');
	
	var country_get = $('#country_get').html();
	$('select[name="country"] option').filter(function() {
		return ($(this).val() == country_get);
		}).attr('selected', 'selected');

	//hide all results to start
	$('tr.sb_search_result').fadeOut();
	filter_results();
	function getCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
		return null;
		}
	if (getCookie("search_page")) {
		sb_go_to_page(getCookie("search_page"));
		}
	
	$('#boat_search').submit(function(){
		filter_results();
		document.cookie =  "search_page=0; path=/";
		return false;
		});
		
	$('#sb_search_box').submit(function(){
		document.cookie =  "search_page=0; path=/";
		});
	
	//////////// if we have data passed into search page, use it to set search box
	function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
		);
		}
	
	function filter_results(){
		//remove sb-visible call from all results to start
		$('tr.sb_search_result').removeClass('sb_visible');
		$('tr.sb_search_result').hide();
		
		//get search paramaters
		var size_low = parseFloat($('.size_low').val());
		var size_high = $('.size_high').val();
		var price_low = $('.price_low').val();
		price_low = price_low * rateToGbp; // convert to GBP prices
		var price_high = $('.price_high').val();
		price_high = price_high * rateToGbp; // convert to GBP prices
		var type = $('select[name="type"] option').filter(':selected').val();
		var country = $('#country option').filter(':selected').val();
		var built = $('select[name="built"] option').filter(':selected').val();
		var fuel = $('select[name="fuel"] option').filter(':selected').val();
		if (fuel) {fuel = fuel.toLowerCase()};
		
		//filter results to match parameters
		var num_results = $('tr.sb_search_result').filter(function(){
			var loa = parseFloat($(this).children().children('.result_loa').html());
			if (loa > size_low) {var size_low_test = true;}
			if (loa < size_high) {var size_high_test = true;}
			
			var price = parseFloat($(this).children().children('.result_price_gbp').html());
			if (price > price_low) {var price_low_test = true;}
			if (price < price_high) {var price_high_test = true;}
			
			var result_type = $(this).children().children('.result_type').html();
			if (type == 'any') {var type_test = true;}
			else if (type == result_type) {var type_test = true;}
			else if ((type == 'sail') && (parseFloat(result_type) < 6)) {var type_test = true;}
			else if ((type == 'power') && (parseFloat(result_type) > 5)) {var type_test = true;}
			else {var type_test = false};
			
			var result_country = $(this).children().children('.result_country').html();
			if (country == 'any') {var country_test = true;}
			else if (result_country == country) {var country_test = true;}
			else {var country_test = false};
			
			var result_year_built = $(this).children().children('.result_year').html();
			if (built == 'any') {var year_test = true;}
			else if (parseFloat(result_year_built) >= parseFloat(built)) {var year_test = true;}
			else {var year_test = false};
			
			var result_fuel = $(this).children().children('.result_fuel').html();
			result_fuel = result_fuel.toLowerCase(result_fuel);
			if (fuel == 'any') {var fuel_test = true;}
			else if (result_fuel == fuel) {var fuel_test = true;}
			else {var fuel_test = false};
			return (size_low_test && size_high_test && price_low_test && price_high_test && type_test && country_test && year_test && fuel_test);
				
			}).addClass('sb_visible');
			//by this point, all search results are tagged with class 'sb-visible'
			
			//pagenation code
			//how much items per page to show  
			var show_per_page = 10;  
			//getting the amount of elements inside content div  
			var number_of_items = $('.sb_visible').size();
			
			//calculate the number of pages we are going to have  
			var number_of_pages = Math.ceil(number_of_items/show_per_page);  
			//set the value of our hidden input fields  
			$('#sb_current_page').val(0);  
			$('#sb_show_per_page').val(show_per_page);  
		  
			//now when we got all we need for the navigation let's make it '  
		  
			/* 
			what are we going to have in the navigation? 
				- link to previous page 
				- links to specific pages 
				- link to next page 
			*/
			var num = '';
			//alert ("number_of_items = "+number_of_items);
			//alert ("show_per_page = "+show_per_page);
			if ((number_of_items > show_per_page) && (number_of_items != 0)) {
				//alert('setting num now');
				num = 'Showing boats 1 to '+ show_per_page +'.';
				}
			top_text = '<p>'+ number_of_items +' boats found. <span id="sb_from_to">'+num+'</span></p>';
			
			if (number_of_items == '0') {
				sb_page_id = $('[name=page_id]').val();
				top_text += "<p>Search for:</p>";
				top_text += "<table style='width: 100%;'><tr>\
		<td><h3 style='text-align: center;'>Sail (all types)</h3></td>\
		<td><h3 style='text-align: center;'>Power (all types)</h3></td>\
	</tr>\
	<tr>\
		<td style='vertical-align: middle; text-align: center;'><div id='sb_sail_icon'></div></td>\
		<td style='vertical-align: middle; text-align: center;'><div id='sb_power_icon'></div></td>\
	</tr>\
	<tr>\
		<td>\
		<form method='get' action='"+document.URL+"'>\
		<div>\
		<input type='hidden' name='type' value='sail' />\
		<input type='hidden' name='country' value='any' />\
		<input type='hidden' name='built' value='any' />\
		<input type='hidden' name='fuel' value='any' />\
		<input type='hidden' name='price_low' value='300' />\
		<input type='hidden' name='price_high' value='1000000' />\
		<input type='hidden' name='size_low' value='6' />\
		<input type='hidden' name='size_high' value='35' />\
		<input type='hidden' name='page_id' value='"+sb_page_id+"' />\
		</div>\
		<p style='text-align: center;'>\
		<button class='button' type='submit'>Under 35 ft / 11m</button></p>\
		</form>\
		<form method='get' action='"+document.URL+"'>\
		<div>\
		<input type='hidden' name='type' value='sail' />\
		<input type='hidden' name='country' value='any' />\
		<input type='hidden' name='built' value='any' />\
		<input type='hidden' name='fuel' value='any' />\
		<input type='hidden' name='price_low' value='300' />\
		<input type='hidden' name='price_high' value='1000000' />\
		<input type='hidden' name='size_low' value='34' />\
		<input type='hidden' name='size_high' value='120' />\
		<input type='hidden' name='page_id' value='"+sb_page_id+"' />\
		</div>\
		<p style='text-align: center;'>\
		<button class='button' type='submit'>Over 35 ft / 11m</button></p>\
		</form></td>\
		<td>\
		<form method='get' action='"+document.URL+"'>\
		<div>\
		<input type='hidden' name='type' value='power' />\
		<input type='hidden' name='country' value='any' />\
		<input type='hidden' name='built' value='any' />\
		<input type='hidden' name='fuel' value='any' />\
		<input type='hidden' name='price_low' value='300' />\
		<input type='hidden' name='price_high' value='1000000' />\
		<input type='hidden' name='size_low' value='6' />\
		<input type='hidden' name='size_high' value='35' />\
		<input type='hidden' name='page_id' value='"+sb_page_id+"' />\
		</div>\
		<p style='text-align: center;'>\
		<button class='button' type='submit'>Under 35 ft / 11m</button></p>\
		</form>\
		<form method='get' action='"+document.URL+"'>\
		<div>\
		<input type='hidden' name='type' value='power' />\
		<input type='hidden' name='country' value='any' />\
		<input type='hidden' name='built' value='any' />\
		<input type='hidden' name='fuel' value='any' />\
		<input type='hidden' name='price_low' value='300' />\
		<input type='hidden' name='price_high' value='1000000' />\
		<input type='hidden' name='size_low' value='34' />\
		<input type='hidden' name='size_high' value='120' />\
		<input type='hidden' name='page_id' value='"+sb_page_id+"' />\
		</div>\
		<p style='text-align: center;'>\
		<button class='button' type='submit'>Over 35 ft / 11m</button></p>\
		</form>		</td>\
	</tr>\
	</table>";
				}
			
			var page_tag = '';
			if (number_of_pages > 0) {
				page_tag = "Page 1 of "+ number_of_pages; }
			
			var navigation_html = '&nbsp;&nbsp;&nbsp;<a class="sb_first_link" href="javascript:sb_go_to_page(0);"><strong>&laquo;</strong></a> <a class="sb_previous_link" href="javascript:sb_previous();">&lt;</a>';  
			var current_link = 0;  
			while(number_of_pages > current_link){  
				navigation_html += ' <a class="sb_page_link" href="javascript:sb_go_to_page(' + current_link +')" longdesc="' + current_link +'">'+ (current_link + 1) +'</a>';  
				current_link++;  
			}  
			navigation_html += ' <a class="sb_next_link" href="javascript:sb_next();">&gt;</a> <a class="sb_last_link" href="javascript:sb_last();"><strong>&raquo;</strong></a>';  
			
			$('#sb_page_tag').html(page_tag);
			if (number_of_pages > 1) {
			$('#sb_page_navigation').html(navigation_html); } else {$('#sb_page_navigation').html(''); }
			$('#sb_top_text').html(top_text);
			
			//add styling to the new buttons!
			$('.button').addClass('ui-state-default ui-corner-all bold').hover(function() {
				$(this).addClass('ui-state-hover');
				}, function() {
				$(this).removeClass('ui-state-hover');
				});
		  
			//add active_page class to the first page link  
			$('#sb_page_navigation .sb_page_link:first').css('font-weight','bold').addClass('sb_active_page');  
		  
			//hide all the elements inside content div  
			//$('#content').children().css('display', 'none');  
		  
			//and show the first n (show_per_page) elements  
			$('.sb_visible').slice(0, show_per_page).show();    
			
			
			if (num_results == 0) {
				$('#sb_no_results').show();
				} else {
				$('#sb_no_results').hide();
				}
			
		}

	$('.featured-card').hover(
		function () {
			$(this).addClass("ui-state-hover");
			},
		function () {
			$(this).removeClass("ui-state-hover");
			}
		);
	
	$('.jcarrow').live("mouseenter",function() {
			$(this).removeClass("ui-state-default").addClass("ui-state-hover");
			}
		).live("mouseleave", function(){
			$(this).removeClass("ui-state-hover").addClass("ui-state-default");
		});;
	
	});
	
//now outside of document(ready) statement
function sb_go_to_page(page_num){  
	//get the number of items shown per page  
	var show_per_page = parseInt(jQuery('#sb_show_per_page').val());  
  
	//get the element number where to start the slice from  
	start_from = page_num * show_per_page;  
  
	//get the element number where to end the slice  
	end_on = start_from + show_per_page;  
  
	//hide all children elements of content div, get specific items and show them  
	//$('.sb_visible').css('display', 'none').slice(start_from, end_on).css('display', 'block');
	jQuery('.sb_visible').hide('slow').slice(start_from, end_on).show('slow');
  
	/*get the page link that has longdesc attribute of the current page and add active_page class to it 
	and remove that class from previously active page link*/  
	jQuery('.sb_page_link').css('font-weight','normal').removeClass('sb_active_page');
	jQuery('.sb_page_link[longdesc=' + page_num +']').css('font-weight','bold').addClass('sb_active_page');  
  
	//update the current page input field  
	jQuery('#sb_current_page').val(page_num); 

	//update from-to value
	end_value = end_on;
	if (end_value > jQuery('.sb_visible').size()) {
		end_value = jQuery('.sb_visible').size();
		}
	
	if (end_value > 0) {
		jQuery('#sb_from_to').html("Showing boats "+(start_from + 1) + " - " + end_value);
		}
	
	if (jQuery(".sb_search_results_wrapper").length != 0) { 
		jQuery("html, body").animate({ scrollTop: jQuery(".sb_search_results_wrapper").offset().top }, "slow");
		}
	document.cookie =  "search_page=" + page_num +"; path=/";
	}
	
function sb_previous(){ 
    new_page = parseInt(jQuery('#sb_current_page').val()) - 1;  
    //if there is an item before the current active link run the function  
    if(jQuery('.sb_active_page').prev('.sb_page_link').length==true){  
        sb_go_to_page(new_page);  
		}  
	}  
  
function sb_next(){  
    new_page = parseInt(jQuery('#sb_current_page').val()) + 1;  
    //if there is an item after the current active link run the function  
    if(jQuery('.sb_active_page').next('.sb_page_link').length==true){  
        sb_go_to_page(new_page);  
		}
	}