$(document).ready(function(){

    //header navbar scroll
	$(window).scroll(function() {
		if ($(this).scrollTop() > 90){
			$('.header_navbar').addClass("sticky");
		}
		else{
			$('.header_navbar').removeClass("sticky");
		}
	});
    //end header navbar scroll

    //progressbar
	$(window).scroll(
		function() {
			var start = $(".progress_bar_bl").offset().top-1000;
			if ($(this).scrollTop() > start) {
				var currentNumber = $('.dial').val();
				var currentNumber1 = $('.dial1').val();
				var currentNumber2 = $('.dial2').val();
				var currentNumber3 = $('.dial3').val();
				$(".dial").knob();
				$({numberValue: currentNumber}).animate({numberValue: 90}, {
					duration: 2000,
					easing: "linear",
					step: function() {
						$(".dial").val(Math.ceil(this.numberValue)).trigger("change");
					}
				});
				$(".dial1").knob();
				$({numberValue: currentNumber1}).animate({numberValue: 75}, {
					duration: 2000,
					easing: "linear",
					step: function() {
						$(".dial1").val(Math.ceil(this.numberValue)).trigger("change");
					}
				});

				$(".dial2").knob();
				$({numberValue: currentNumber2}).animate({numberValue: 70}, {
					duration: 2000,
					easing: "linear",
					step: function() {
						$(".dial2").val(Math.ceil(this.numberValue)).trigger("change");
					}
				});
				$(".dial3").knob();
				$({numberValue: currentNumber3}).animate({numberValue: 85}, {
					duration: 2000,
					easing: "linear",
					step: function() {
						$(".dial3").val(Math.ceil(this.numberValue)).trigger("change");
					}
				});
			}
		}
	);
//end progressbar

//validation

var jVal = {
	'fullName' : function() {
	
		$('.touch_bl').append('<div id="nameInfo" class="info"></div>');
		
		var nameInfo = $('#nameInfo');
		var ele = $('#fullname');
		var pos = ele.offset();
		
		nameInfo.css({
			top: pos.top-35,
			left: pos.left+ele.width()-180
		});
		
		if(ele.val().length < 6) {
			jVal.errors = true;
				nameInfo.removeClass('correct').addClass('error').html('&larr; как минимум 6 символов!').show();
				ele.removeClass('normal').addClass('wrong');				
		} else {
				nameInfo.removeClass('error').addClass('correct').html('Ок').hide();
				ele.removeClass('wrong').addClass('normal');
		}
	},

	'email' : function() {
	
		$('.touch_bl').append('<div id="emailInfo" class="info"></div>');
	
		var emailInfo = $('#emailInfo');
		var ele = $('#email');
		var pos = ele.offset();
		
		emailInfo.css({
			top: pos.top-35,
			left: pos.left+ele.width()-250
		});
		
		var patt = /^.+@.+[.].{2,}$/i;
		
		if(!patt.test(ele.val())) {
			jVal.errors = true;
				emailInfo.removeClass('correct').addClass('error').html('&larr; Введите правильный email, хорошо?').show();
				ele.removeClass('normal').addClass('wrong');					
		} else {
				emailInfo.removeClass('error').addClass('correct').html('&radic;').hide();
				ele.removeClass('wrong').addClass('normal');
		}
	},
	
	'about' : function() {
	
		$('.touch_bl').append('<div id="aboutInfo" class="info"></div>');
	
		var aboutInfo = $('#aboutInfo');
		var ele = $('#about');
		var pos = ele.offset();
		
		aboutInfo.css({
			top: pos.top-35,
			left: pos.left+ele.width()-205
		});

		if(ele.val().length < 35) {
			jVal.errors = true;
				aboutInfo.removeClass('correct').addClass('error').html('&larr; Очень маленькое сообщение!').show();
				ele.removeClass('normal').addClass('wrong').css({'font-weight': 'normal'});		
		} else {
				aboutInfo.removeClass('error').addClass('correct').html('&radic;').hide();
				ele.removeClass('wrong').addClass('normal');
		}
	}

};

// ====================================================== //


$('#fullname').change(jVal.fullName);
$('#email').change(jVal.email);
$('#about').change(jVal.about);

//endValidation
});