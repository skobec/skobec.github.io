jQuery(document).ready(function() {
	jQuery(".sq1").slider({
		min: 1000,
		max: 1000000,
		step:1000,
		value: 0,
		range: true,
		slide: function( event, ui ) {
			jQuery(".sq1").prev("p").children("span").children("strong").html(ui.values[1]);
			jQuery(".calc .row p span strong").html(ui.values[1]);
			if (jQuery(".sq2").slider("values", 1)<4){
				percent=0.04;
			}
			else {
				percent=0.05
			}
			jQuery(".rbg span strong").html( Math.ceil(ui.values[1] + (ui.values[1] * percent * jQuery(".sq2").slider("values", 1))) );
		}
	});
	jQuery(".sq2").slider({
		min: 1,
		max: 36,
		value: 0,
		range: true,
		slide: function( event, ui ) {
			jQuery(".sq2").prev("p").children("span").children("strong").html(ui.values[1]);
			if (ui.values[1]<4){
				percent=0.04;
				jQuery('.tarif_link').text('Рублевый');
			}
			else {
				percent=0.05
				jQuery('.tarif_link').text('Рублевый +');
			}
			jQuery(".rbg span strong").html( Math.ceil(jQuery(".sq1").slider("values", 1) + (jQuery(".sq1").slider("values", 1) * percent * ui.values[1])) );
		}
	});
});
//якоря навигации
$(document).ready(function(){
	$('a[href*=#]').bind("click", function(e){
		var anchor = $(this);
		$('html, body').stop().animate({
			scrollTop: $(anchor.attr('href')).offset().top
		}, 1000);
		e.preventDefault();
	});
	return false;
});