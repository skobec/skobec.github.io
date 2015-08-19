$(function () {

	'use strict';

	var intro = new Swiper('#intro', {
		slidesPerView: 5,
		loop: true,
		effect: 'fade',
		speed: 1000,
		autoplay: 10000,
		simulateTouch: false,
		preloadImages: false
	});

	var infoslider = new Swiper('#infoslider', {
		prevButton: '.js-swiper-prev',
		nextButton: '.js-swiper-next',
		slidesPerView: 1,
		loop: true,
		simulateTouch: false
	});

	// animation for menu-screen
	var
		$menuIcon = $('.js-icon-menu'),
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

	$menuIcon.on('click', function () {

			var stateMenu = $logo.hasClass(logoActiveClass);
			$logo.toggleClass(logoActiveClass, !stateMenu);

		$searchBtn.removeClass(iconSearchActiveClass);
		$searchMain.removeClass(searchActiveClass);

		var state = $(this).hasClass(iconMenuActiveClass);
		$(this).toggleClass(iconMenuActiveClass, !state);

		if(!state) {
			$('body').addClass('hidden');
			$screenMenu.addClass(showClass);
			setTimeout(function(){
				$linksHeader.addClass('intro__logo-links_showed')
			},1000);
			$iconHelp.addClass(iconHelpClassVisible);
			$iconDashboard.addClass(iconDashboardClassVisible);
		} else {
			$screenMenu.removeClass(showClass);
			$linksHeader.removeClass('intro__logo-links_showed');
			$iconHelp.removeClass(iconHelpClassVisible);
			$iconDashboard.removeClass(iconDashboardClassVisible);
			$('body').removeClass('hidden');
		}
	});

    $screenMenuCloseIcon.on('click', function () {
        $screenMenu.removeClass(showClass);
        $iconHelp.removeClass(iconHelpClassVisible);
        $iconDashboard.removeClass(iconDashboardClassVisible);
        $searchBtn.removeClass(iconSearchActiveClass);
        $menuIcon.removeClass(iconMenuActiveClass);
        $searchMain.removeClass(searchActiveClass);
        $voice.removeClass(voiceActiveClass);
        $logo.removeClass(logoActiveClass);
        $linksHeader.removeClass('intro__logo-links_showed');
        $('body').removeClass('hidden');
    });

	// animation for subItemMenu - right block at menu-screen
	var
		$navMenu = $('.js-list-menu'),
		$navLink = $('.js-list-menu-link'),
		$detailItems = $('.js-list-menu-detail'),
		$linkWithDrop = $('.js-list-menu-link').filter('[data-droplist="true"]'),
		activeClass = 'list-menu__link_active',
		notActiveClass = 'list-menu__link_not_active',
		slideClass = 'list-menu__detail_to-slide';

	$('body').on('click', '.js-list-menu-link', function (e) {

		var
			clickLink = $(this).attr('data-action'),
			$linkToMain = $('.js-list-menu-link').filter('[data-action="main"]'),
			$detailToShow = $('.js-list-menu-detail').filter('[data-view="' + clickLink + '"]'),
			$target = $(e.target);

		$navLink.removeClass(activeClass);

		$navLink.addClass(notActiveClass);
		$(this).addClass(activeClass);

		if ($(this).hasClass('info__link')) {
			$('.info__link-tabs').removeClass('info__link-tabs_is_active');
			$(this).addClass('info__link-tabs_is_active');
		}

		$target.is($linkWithDrop) ?
			$detailToShow.addClass(slideClass) : '';

		if ($target.is($linkToMain)) {
			$screenMenu.removeClass(showClass);
			$('.intro__search').removeClass('intro__search_showed');
			$iconHelp.removeClass(iconHelpClassVisible);
			$iconDashboard.removeClass(iconDashboardClassVisible);
			$menuIcon.removeClass(iconMenuActiveClass);
			$logo.removeClass(logoActiveClass);
			$('body').removeClass('hidden');
			$linksHeader.removeClass('intro__logo-links_showed')
		}

		if (!$screenMenu.hasClass(showClass)) {
			$navLink.removeClass(activeClass);
		}
	});



	$(document).mouseup(function (e)
		{
		    var container = $navMenu;

		    if (!container.is(e.target)
		        && container.has(e.target).length === 0) {
		        $detailItems.removeClass(slideClass);
		    	$navLink.removeClass(activeClass);
				$navLink.removeClass(notActiveClass);
		    }


	});


	$searchBtn.on('click', function(){
		$('.intro__search').addClass('intro__search_showed');
		$screenMenu.removeClass(showClass);
		$linksHeader.removeClass('intro__logo-links_showed');


		$menuIcon.removeClass(iconMenuActiveClass);
		$screenMenu.removeClass(showClass);

		var state = $(this).hasClass(iconSearchActiveClass);
		$(this).toggleClass(iconSearchActiveClass, !state);

		if(!state) {
			$searchMain.addClass(searchActiveClass);
			$('body').addClass('hidden');
			$iconHelp.addClass(iconHelpClassVisible);
			$iconDashboard.addClass(iconDashboardClassVisible);

		} else {
			$searchMain.removeClass(searchActiveClass);
			$('body').removeClass('hidden');
			$iconHelp.removeClass(iconHelpClassVisible);
			$iconDashboard.removeClass(iconDashboardClassVisible);
		}
	});


	var $budget = $('#budget');
	var $budgetFilter = $budget.find('.js-budget-filter');
	var $budgetPie = $budget.find('.js-budget-pie');
	var $budgetPieHint = $budget.find('.js-budget-pie-hint');
	var $budgetPiePart = $budget.find('.js-budget-pie-part');
	var $budgetMapiePart = $budget.find('.js-budget-mapie-part');
	var budgetFilterActiveClass = 'budget-filter__item_state_active';
	var budgetPieHintActiveClass = 'budget-pie__hint_state_active';
	var budgetPieHintDisabledClass = 'budget-pie__hint_state_disabled';
	var budgetPiePartActiveClass = 'budget-pie__part_state_active';
	var budgetPiePartDisabledClass = 'budget-pie__part_state_disabled';

	$budgetFilter.on('click', function () {
		$budgetFilter.removeClass(budgetFilterActiveClass);
		$(this).addClass(budgetFilterActiveClass);
	});


	$budgetPieHint.on({
		mouseenter: function () {
			var $this = $(this);
			var id = $this.data('id');
			var $currentBudgetPiePart = $budgetPiePart.filter('[data-id="' + id + '"]');

			$budgetPieHint.not($this).addClass(budgetPieHintDisabledClass);
			$budgetPiePart.not($currentBudgetPiePart).addClass(budgetPiePartDisabledClass);
			$this.removeClass(budgetPieHintDisabledClass).addClass(budgetPieHintActiveClass);
			$currentBudgetPiePart.addClass(budgetPiePartActiveClass);
		},
		mouseleave: function (event) {
			$budgetPieHint.removeClass(budgetPieHintActiveClass);
			$budgetPiePart.removeClass(budgetPiePartActiveClass);
		}
	});

	$budget.on('mousemove', function (event) {
		var $target = $(event.target);

		if (
			!$target.is($budgetPieHint) &&
			!$target.closest($budgetPieHint).length &&
			!$target.is($budgetMapiePart) &&
			!$target.closest($budgetMapiePart).length
		) {
			$budgetPieHint.removeClass(budgetPieHintActiveClass + ' ' + budgetPieHintDisabledClass);
			$budgetPiePart.removeClass(budgetPiePartActiveClass + ' ' + budgetPiePartDisabledClass);
		}
	});

	$budgetMapiePart.on('mouseenter mouseleave', function (event) {
		$budgetPieHint.filter('[data-id="' + this.dataset.id + '"]').trigger(event.type)
	});



	var
		$thumbnail = $('.js-appearence-thumbnail'),
		$text = $('.js-appearence-text'),
		textActiveClass = 'appearence__text_is_active',
		thumbnailActiveClass = 'appearence__thumbnail_is_active';

	$thumbnail.on('click', function () {
		$thumbnail.removeClass(thumbnailActiveClass);
		$text.removeClass(textActiveClass);
		$(this).addClass(thumbnailActiveClass);
		$(this).next().addClass(textActiveClass);
	});

	$( '.jelect' ).jelect();

	//show tabs by click on "Нужная инфо по бюджету за 3 клика"
	var
		$infoTabs = $('.js-info-tabs'),
		$infoTabsInner = $('.js-info-tabs-inner'),
		$infoLink = $('.js-info-link'),
		infoTabsActiveClass = 'info__tabs_is_active',
		infoLinkActiveClass = 'info__link_is_active',
		tabInnerActiveClass = 'info__tabs-inner_is_active';

	$infoLink.on('click', function () {
		var
			attrInfoLink = $(this).attr('data-action'),
			$tabToShow = $('.info__tabs').find('[data-view="' + attrInfoLink + '"]');

		$infoTabs.addClass(infoTabsActiveClass);
		$infoLink.removeClass(infoLinkActiveClass);
		$(this).addClass(infoLinkActiveClass);
		$infoTabsInner.removeClass(tabInnerActiveClass);
		$tabToShow.addClass(tabInnerActiveClass);
		!$(this).attr('data-droplist') ?
			$infoTabs.removeClass(infoTabsActiveClass) : ''
	});

    var $voiceSearchBtn = $('.js-voice-search'),
        $voice = $('.js-voice'),

        voiceActiveClass = 'intro__search-voice_showed';

    $voiceSearchBtn.on('click', function(){
        console.log('works');
        $voice.addClass(voiceActiveClass);
        $screenMenu.removeClass(showClass);
        $menuIcon.removeClass(iconMenuActiveClass);
        $searchBtn.removeClass(iconSearchActiveClass);
        $('.intro__search').removeClass('intro__search_showed');
        //$(this).addClass(voiceIconActiveClass);
    });


});
