 $(document).ready(function(){
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
	
	$("a[rel^='prettyPhoto']").prettyPhoto();
	
	var euroRate = $('#ex_rate').html();
 //-------------------------------------------------------------------------------------------------------------------------------------------------------------
 //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	var showAdvanced = false;
 //-------------------------------------------------------------------------------------------------------------------------------------------------------------
 //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 //slider size code
	var sizeLow = $('#size_low_get').html();
	if (sizeLow == null) {sizeLow = $('#size_low').val();}
	if (sizeLow == '') {sizeLow = 28;}	
 	var sizeHigh = $('#size_high_get').html();
	if (sizeHigh == null) {sizeHigh = $('#size_high').val();}
	if (sizeHigh == '') {sizeHigh = 45;}

	$("#size_low").val(sizeLow);//set hidden field values
	$("#size_high").val(sizeHigh);
	sizeLowM = sigFigs(sizeLow * 0.3048,2);//set text
	sizeHighM= sigFigs(sizeHigh * 0.3048,2);
	$('#sizedesc').html("<span id='sb_size_low'>"+sigFigs(sizeLow,2) + "</span> - <span id='sb_size_high'>" + sigFigs(sizeHigh,2) + "</span> feet | " + sizeLowM + " - " + sizeHighM + " m");
	
	$('#slider_size').slider({
		animate: true,
		max: 120,
		min: 6,
		range: true,
		values: [sizeLow, sizeHigh],
		step: 1,
		orientation: 'horizontal',
		slide: function(event, ui) {
				sizeLow = ui.values[0];
				sizeHigh = ui.values[1];
				//set hidden field values
				$("#size_low").val(sizeLow);
				$("#size_high").val(sizeHigh);
				//set text
				sizeLowM = sigFigs(sizeLow * 0.3048,2);
				sizeHighM= sigFigs(sizeHigh * 0.3048,2);
				$('#sizedesc').html(sigFigs(sizeLow,2) + " - " + sigFigs(sizeHigh,2) + " feet | " + sizeLowM + " - " + sizeHighM + " m");
				}
		});
	


 //-----------------------------------------------------------------------------------------------------------------------------------------------------
 //-----------------------------------------------------------------------------------------------------------------------------------------------------
 //slider price code
	var priceLow = $('#price_low_get').html();
	if (priceLow == null) {priceLow = $('#price_low').val();}
	if (priceLow == '') {priceLow = 30000;}	
 	var priceHigh = $('#price_high_get').html();
	if (priceHigh == null) {priceHigh = $('#price_high').val();}
	if (priceHigh == '') {priceHigh = 150000;}
		
	$("#price_low").val(priceLow);//set hidden field values
	$("#price_high").val(priceHigh);
	priceLowEuro = sigFigs(priceLow * euroRate,2); //set text
	priceHighEuro = sigFigs(priceHigh * euroRate,2);
	$('#pricedesc').html("&euro;" + TS(priceLowEuro) + " - &euro;" + TS(priceHighEuro) + " | &pound;" + TS(priceLow) + " - &pound;" + TS(priceHigh));

	$('#slider_price').slider({
		animate: true,
		max: 13815, // ln 1,000,000 * 1000
		min: 5703, // ln 500 * 1000
		range: true,
		values: [Math.log(priceLow)*1000, Math.log(priceHigh)*1000],
		orientation: 'horizontal',
		slide: function(event, ui) {
					priceLow = sigFigs(Math.exp((ui.values[0]/1000)),2);
					priceHigh = sigFigs(Math.exp((ui.values[1]/1000)),2);
					//set hidden field values
					$("#price_low").val(priceLow);
					$("#price_high").val(priceHigh);
					//calculate euro values (for display only)
					priceLowEuro = sigFigs(priceLow * euroRate,2);
					priceHighEuro = sigFigs(priceHigh * euroRate,2);
					//set text
					$('#pricedesc').html("&euro;" + TS(priceLowEuro) + " - &euro;" + TS(priceHighEuro) + " | &pound;" + TS(priceLow) + " - &pound;" + TS(priceHigh));
			}
		});
	
	
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------
//advanced search hide/show
	var showAdvanced = true;
	if ($('#size_low_get').html() == null){showAdvanced = false;}

	if (!(showAdvanced)) {
		$('#advanced_search').hide();
		}
	else {
		$('#advanced_search_icon').addClass('ui-icon-circle-triangle-n');
		$('#advanced_search_icon').removeClass('ui-icon-circle-triangle-s')
		}
	$('#advanced_search_handle').click(function() {
		   if ($('#advanced_search').is(":hidden"))
               {
                    $('#advanced_search').slideDown("slow");
					$('#advanced_search_icon').addClass('ui-icon-circle-triangle-n');
					$('#advanced_search_icon').removeClass('ui-icon-circle-triangle-s');
               } else {
                    $('#advanced_search').slideUp("slow");
					$('#advanced_search_icon').addClass('ui-icon-circle-triangle-s');
					$('#advanced_search_icon').removeClass('ui-icon-circle-triangle-n');

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
	
	$('#boat_search').submit(function(){
		filter_results();
		return false;
		});
	
	//////////// if we have data passed into search page, use it to set search box
	function getURLParameter(name) {
    return decodeURI(
        (RegExp(name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
		);
		}
	
	function filter_results(){
		$('tr.sb_search_result').hide();
		//get search paramaters
		var size_low = parseFloat($('#size_low').val());
		var size_high = $('#size_high').val();
		var price_low = $('#price_low').val();
		var price_high = $('#price_high').val();
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
				
			}).fadeIn().size();
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