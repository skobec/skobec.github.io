$(document).ready(function(){


	//main menu
	$('.main_menu .btn_toggle').on('click touchstart', function() {
		$('.main_menu .submenu, .overlay').toggleClass('active');
		return false;
	})
	$('.overlay').on('click', function() {
		$('.main_menu .submenu, .overlay').toggleClass('active');
	})


	//photo slider
	$('.photo_slider').owlCarousel({
		items: 2,
		loop: true,
		dots: true,
		nav: false,
		margin: 0
	})


	//select menu
	$('.select_menu_checked a').on('click', function() {
		if ($(this).hasClass('active')) {} else {
			$(this).parent('.select_menu_checked').children('a').removeClass('active');
			$(this).addClass('active');
			return false;
		}
	})

	//datepicker
	$('.frm_cal input[type="text"]').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'dd.mm.yy',
		dayNames: [ "Воскресение", "Понедельник", "Вторник", "Среда", "Четверг", "Пятница", "Суббота" ],
		dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ] ,
		monthNames: [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ],
		monthNamesShort: [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ],
		firstDay: 1 
	});
});