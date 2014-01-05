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
	var rate = $('#sb_curr_2_rate').html();
		
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

	
	});
	