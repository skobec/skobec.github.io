// workspace switcher
$(function(){

	var $container = $('[data-container="menu"]'),
			switcherButton = $('[data-action="switcher"]', $container),
			legendButton = $('[data-action="legend"]'),
			$window = $('window'),
			$switcher = $('[data-container="switcher"]'),
			$switcherItem = $('[data-item="sidebar"]', $switcher),
			$switcherItem = $('[data-item="switcher"]', $switcher),
			$switcherLink = $('[data-item="switcher"]', $switcher),
			$card = $('[data-item="card"]'),
			$cardSidebar = $('[data-item="sidebar"]', $card),
			$filterPlan = $('.filter-plan'),
			$filterYear = $('.filter-year'),
			$filterPeriod = $('.filter-period'),
			$overlay = $('.overlay'),
			$screenWrapper = $('.js-screen-wrapper');

	switcherButton.on('click', function (e){
		$switcher.slideToggle('fast');
		e.preventDefault();
	});
	$switcherLink.on('click', function (e){
		$switcherItem.removeClass('is-active')
		$(this).addClass('is-active');
		e.preventDefault();
	});

	legendButton.on('click', function (e){
		$cardSidebar.toggleClass('is-collapsed');

	});

// card actions
	var $container = $('[data-container="card"]'),
			$item = $('[data-item="card"]', $container),
			actionButton = $('[data-action="clone"]', $item),
			expandButton = $('[data-action="expand"]', $item),
			$clone = $item.first();

	actionButton.on('click', function (e){
		$clone.clone().removeClass('cards-item_full').appendTo($('[data-container="card"]'));
		e.preventDefault();
	});

	//expandButton.on('click', function (e){
	//	$(this).parent().toggleClass('is-active')
	//		.parent().parent().toggleClass('is-expanded').removeClass('cards-item_full');
	//	e.preventDefault();
	//});

// card view options
//	var $container = $('[data-container="view"]'),
//			$item = $('[data-item="view"]', $container),
//			action = $('[data-action="view"]', $item);
//
//	action.on('click', function (e){
//		$(this).parent().addClass('is-active')
//			.siblings().removeClass('is-active');
//		e.preventDefault();
//	});

// card legend hover effect
	var $container = $('[data-container="legend"]'),
			$item = $('[data-item="legend"]', $container);

	$item.on({
		mouseenter: function (e){
			$(this).addClass('is-active')
				.siblings().addClass('is-inactive');
		},
		mouseleave: function (e){
			$(this).removeClass('is-active')
				.siblings().removeClass('is-inactive');
		}
	});

// chart
	$('#container').highcharts({
		chart: {
			type: 'area',
			backgroundColor: '#efefef'
		},

		credits: {
			enabled: false
		},

		exporting: {
			enabled: false
		},

		title: {
			style: {
				display: 'none'
			}
		},

		subtitle: {
			style: {
				display: 'none'
			}
		},

		xAxis: {
			categories: ['1750', '1800', '1850', '1900', '1950', '1999', '2050'],
			tickmarkPlacement: 'on',
			title: {
				enabled: false
			}
		},

		yAxis: {
			title: {
				text: ''
			}
		},

		tooltip: {
			pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.percentage:.1f}%</b> ({point.y:,.0f} millions)<br/>',
			// shared: true,
			borderWidth: 1,
			borderRadius: 0,
			shadow: false,
			backgroundColor: '#fff',
			borderColor: '#bbb'
			// useHTML: true,
			// formatter: function() {
			// 	return ("<div class='cards-chart-tooltip'>" + this.y + "</div>");
			// }
		},
		plotOptions: {
			area: {
				dataLabels:{enabled:false},
				stacking: 'percent',
				lineColor: '#ffffff',
				lineWidth: 1,
				marker: {
					lineWidth: 1,
					lineColor: '#ffffff'
				}
			}
		},
		series: [{
			name: 'Asia',
			data: [502, 635, 809, 947, 1402, 3634, 5268]
		}, {
			name: 'Africa',
			data: [106, 107, 111, 133, 221, 767, 1766]
		}, {
			name: 'Europe',
			data: [163, 203, 276, 408, 547, 729, 628]
		}, {
			name: 'America',
			data: [18, 31, 54, 156, 339, 818, 1201]
		}, {
			name: 'Oceania',
			data: [2, 2, 2, 6, 13, 30, 46]
		}]
	});

	// show widget
//	var
//		$addWidget = $('.js-icon-plus'),
//		$widget = $('.js-widget'),
//		$closeBtn = $('.js-widget-close'),
//		widgetClassVisible = 'widget_state_visible';
//
//		$('.cards-item-add').on('click', function(){
//			$widget.addClass('widget_active');
//			$('body').addClass('body-hidden');
//		});
//
//		$closeBtn.on('click', function(){
//			$widget.removeClass('widget_active');
//			$('body').removeClass('body-hidden');
//		});
//
//	$(document).mouseup(function (e) {
//	    var container = $widget;
//	    if (!container.is(e.target) // if the target of the click isn't the container...
//	        && container.has(e.target).length === 0) // ... nor a descendant of the container
//	    {
//	        container.removeClass('widget_active');
//			$('body').removeClass('body-hidden');
//	    }
//	});
//
//
//	// select tab
//	$('.widget__content-topic-item').on('click', function () {
//		var itemToShow = $(this).attr('data-item');
//		$('.widget__content-topic-item_is_active')
//			.removeClass('widget__content-topic-item_is_active');
//		$('.widget__content-question_is_active')
//			.removeClass('widget__content-question_is_active');
//		$(this)
//			.addClass('widget__content-topic-item_is_active');
//		$('.widget__content').find('[data-view="' + itemToShow + '"]')
//			.addClass('widget__content-question_is_active');
//	});
//


	//$filterPlan.on('click', function(){
	//	var state = $('.plan-main').hasClass('plan-main_state_active');
	//	$('.plan-main').toggleClass('plan-main_state_active', !state);
	//});
    //
	//$filterYear.on('click', function(){
	//	var state = $('.years').hasClass('years_state_active');
	//	$('.years').toggleClass('years_state_active', !state);
	//});
    //
	//$filterPeriod.on('click', function(){
	//	var state = $('.period').hasClass('period_state_active');
	//	$('.period').toggleClass('period_state_active', !state);
	//});

	$(document).mouseup(function (e)
	{

	    var container = $(".plan");
        var filter = $(".cards-filter-text");

	    if (!container.is(e.target) // if the target of the click isn't the container...
	        && container.has(e.target).length === 0 && !filter.is(e.target)) // ... nor a descendant of the container
	    {
	        //container.removeClass('period_state_active');
	        //container.removeClass('years_state_active');
	        //container.removeClass('plan-main_state_active');
	        container.removeClass('active');
	    }
	});

	var is_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

	if(!is_firefox){
		$('.widget__content-topic-item').css('font-weight','bold');
	}

	$(window).on('scroll', function (){
		$screenWrapper.addClass('education__screen-wrapper_state_active');
		$('.education__screen-disabled').show();
		if($(this).scrollTop()===0){
			$screenWrapper.removeClass('education__screen-wrapper_state_active');
			$('.education__screen-disabled').hide();
		}
		if($(this).scrollTop()>1000){
			$('.education__screen-disabled').hide();
		}
	});

	var $menuIcon = $('.js-icon-menu'),
		$screenMenu = $('.js-screen-menu'),
		$screenMenuCloseIcon = $('.js-screen-menu-close'),
		showClass = 'screen-menu_show',
		iconMenuActiveClass = 'icon-menu_blue',

		$iconHelp = $('.js-icon-help'),
		$iconDashboard = $('.js-icon-dashboard'),
		iconHelpClassVisible = 'icon-help_is_visible',
		iconDashboardClassVisible = 'icon-dashboard_is_visible',

		$searchMain = $('.intro__search'),
		$searchBtn = $('.js-search-button'),
		$logo = $('.js-logo'),
		$linksHeader = $('.js-logo-links'),

		logoActiveClass = 'intro__logo_state_menu',
		iconSearchActiveClass = 'icon-search_blue',
		searchActiveClass = 'intro__search_showed';


	$searchBtn.on('click', function(){

		$('.intro__search').addClass('intro__search_showed');
		$screenMenu.removeClass(showClass);
		$linksHeader.removeClass('intro__logo-links_showed')


		$menuIcon.removeClass(iconMenuActiveClass);
		$screenMenu.removeClass(showClass);

		var state = $(this).hasClass(iconSearchActiveClass);
		$(this).toggleClass(iconSearchActiveClass, !state);

		if(!state) {
			$searchMain.addClass(searchActiveClass);
			$('body').addClass('hidden-dashboard');
			$iconHelp.addClass(iconHelpClassVisible);
			$iconDashboard.addClass(iconDashboardClassVisible);

		} else {
			$searchMain.removeClass(searchActiveClass);
			$('body').removeClass('hidden-dashboard');
			$iconHelp.removeClass(iconHelpClassVisible);
			$iconDashboard.removeClass(iconDashboardClassVisible);
		}
	});

    $screenMenuCloseIcon.on('click', function () {
        $screenMenu.removeClass(showClass);
        $iconHelp.removeClass(iconHelpClassVisible);
        $iconDashboard.removeClass(iconDashboardClassVisible);
        $searchBtn.removeClass(iconSearchActiveClass)
        $menuIcon.removeClass(iconMenuActiveClass);
        $searchMain.removeClass(searchActiveClass);
        $voice.removeClass(voiceActiveClass);
        $logo.removeClass(logoActiveClass);
        $linksHeader.removeClass('intro__logo-links_showed')
        $('body').removeClass('hidden');
    });

	$menuIcon.on('click', function () {

			var stateMenu = $logo.hasClass(logoActiveClass);
			$logo.toggleClass(logoActiveClass, !stateMenu);

		$searchBtn.removeClass(iconSearchActiveClass);
		$searchMain.removeClass(searchActiveClass);

		var state = $(this).hasClass(iconMenuActiveClass);
		$(this).toggleClass(iconMenuActiveClass, !state);

		if(!state) {
			$screenMenu.addClass(showClass);
			setTimeout(function(){
				$linksHeader.addClass('intro__logo-links_showed')
			},600)
			$('body').addClass('hidden-dashboard');
			$iconHelp.addClass(iconHelpClassVisible);
			$iconDashboard.addClass(iconDashboardClassVisible);
		} else {
			$screenMenu.removeClass(showClass);
			$linksHeader.removeClass('intro__logo-links_showed');
			$('body').removeClass('hidden-dashboard');
			$iconHelp.removeClass(iconHelpClassVisible);
			$iconDashboard.removeClass(iconDashboardClassVisible);
		}
	});

	// animation for subItemMenu - right block at menu-screen
	var
		$navLink = $('.js-list-menu-link'),
		$detailItems = $('.js-list-menu-detail'),
		$linkWithDrop = $('.js-list-menu-link').filter('[data-droplist="true"]'),
		activeClass = 'list-menu__link_active',
		slideClass = 'list-menu__detail_to-slide';

	$('body').on('click', '.js-list-menu-link', function (e) {

		var
			clickLink = $(this).attr('data-action'),
			$linkToMain = $('.js-list-menu-link').filter('[data-action="main"]'),
			$detailToShow = $('.js-list-menu-detail').filter('[data-view="' + clickLink + '"]'),
			$target = $(e.target);

		$navLink.removeClass(activeClass);

		$(this).addClass(activeClass);
		if ($(this).hasClass('info__link')) {
			$('.info__link-tabs').removeClass('info__link-tabs_is_active');
			$(this).addClass('info__link-tabs_is_active');
		}

		$target.is($linkWithDrop) ?
			$detailToShow.addClass(slideClass) : ''

		if ($target.is($linkToMain)) {
			$screenMenu.removeClass(showClass);
			$('.intro__search').removeClass('intro__search_showed');
			$iconHelp.removeClass(iconHelpClassVisible);
			$iconDashboard.removeClass(iconDashboardClassVisible);
			$menuIcon.removeClass(iconMenuActiveClass);
			$logo.removeClass(logoActiveClass);
			$('body').removeClass('hidden-dashboard');
			$linksHeader.removeClass('intro__logo-links_showed')
		}

		if (!$screenMenu.hasClass(showClass)) {
			$navLink.removeClass(activeClass);
		}
	});

	var $cardsLink = $('.js-title-link'),
		$cardsPopup = $('.js-cards-popup'),
		$closeCardsPopup = $('.js-close-cards-popup'),

		cardsPopupActiveClass = 'cards-popup_state_active';

	$('#widgets_container').on('click','.js-title-link', function(){
		var state = $cardsPopup.hasClass(cardsPopupActiveClass);
		$cardsPopup.toggleClass(cardsPopupActiveClass, !state);
        $cardsPopup.offset({top:$(this).offset().top+25,left:$(this).offset().left});
	});

	$closeCardsPopup.on('click', function(){
		$cardsPopup.removeClass(cardsPopupActiveClass);
	});

	$(document).mouseup(function (e) {
	    var container = $('.js-title-link, .js-cards-popup');

	    if (!container.is(e.target) && container.has(e.target).length === 0) {
	        container.removeClass(cardsPopupActiveClass);
	    }
	});

    if ($.fn.jelect) {
        $('.js-jelect').jelect();
    }

// Темы

    $('.js-appearence-thumbnail').on('click', function(){
        $(this).removeClass('theme_active');
    });

    var $dashboard = $('.is-dashboard'),

        $brownTheme = $('.js-theme-brown'),
        bodyBrownClass = 'is-brown';

    $brownTheme.on('click', function() {

        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-green');
        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-green');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-green');
        });
        $dashboard.attr('class','is-dashboard');
        $dashboard.addClass(bodyBrownClass);
    });


    var $pinkTheme = $('.js-theme-pink'),
        bodyPinkClass = 'is-pink';

    $pinkTheme.on('click', function(){
        $dashboard.attr('class','is-dashboard');
        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-pink');

        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-green');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-pink');
        })
        $dashboard.addClass(bodyPinkClass);
    });

    var $shipTheme = $('.js-ship-theme'),
        bodyShipClass = 'is-ship';

    $shipTheme.on('click', function(){
        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');
        $dashboard.attr('class','is-dashboard');
        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-green');

        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-green');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-green');
        })
        $dashboard.addClass(bodyShipClass);
    });


    var $baseTheme = $('.js-theme-base');

    $baseTheme.on('click', function(){
        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass);


        $('body').attr('class','is-dashboard')


        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass);
            iconClass = $(this).attr('class','icon icon-' + newClass);
        });

    });

    var $ivoryTheme = $('.js-theme-ivory'),
        bodyIvoryClass = 'is-ivory';


    $ivoryTheme.on('click', function(){
        $dashboard.attr('class','is-dashboard');
        $dashboard.addClass(bodyIvoryClass);

        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-red');

        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-red');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-red');
        });
    });

    var $industryTheme = $('.js-theme-industry'),
        bodyIndustryClass = 'is-industry';


    $industryTheme.on('click', function(){
        $dashboard.attr('class','is-dashboard');
        $dashboard.addClass(bodyIndustryClass);

        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-green');

        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-red');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-green');
        });
    });

    var $engineTheme = $('.js-theme-engine'),
        bodyEngineClass = 'is-engine';


    $engineTheme.on('click', function(){
        $dashboard.attr('class','is-dashboard');
        $dashboard.addClass(bodyEngineClass);

        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-green');

        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-red');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-green');
        });
    });

    var $sunsetTheme = $('.js-theme-sunset'),
        bodySunsetClass = 'is-sunset';


    $sunsetTheme.on('click', function(){
        $dashboard.attr('class','is-dashboard');
        $dashboard.addClass(bodySunsetClass);

        $('.js-appearence-thumbnail').removeClass('theme_active');
        $(this).addClass('theme_active');

        var iconPlusClass = $('.cards-item-add .icon').attr('class').split(' ')[1];
        newClass = iconPlusClass.split('-')[1];
        $('.cards-item-add .icon').attr('class','icon icon-' + newClass + '-green');

        $('.cards-cover .icon').each(function(){
            var iconClass = $(this).attr('class').split(' ')[1],
                newClass = iconClass.split('-')[1];

            $(this).attr('class','icon ' + iconClass + '-red');
            iconClass = $(this).attr('class','icon icon-' + newClass + '-green');
        });
    });

    var $voiceSearchBtn = $('.js-voice-search'),
        $voice = $('.js-voice'),

        voiceActiveClass = 'intro__search-voice_showed';

    $voiceSearchBtn.on('click', function(){
        console.log('works');
        $voice.addClass(voiceActiveClass);
        $screenMenu.removeClass(showClass);
        $menuIcon.removeClass(iconMenuActiveClass);
        $screenMenuCloseIcon.removeClass(iconSearchActiveClass);
        $('.intro__search').removeClass('intro__search_showed');
        $(this).addClass(voiceIconActiveClass);
    });


});

