/* ###### Общая длина слайдов ###### */
function getslideswidth() {
    window.slideswidth = 0;
    $(".slide").each(function(){
        slideswidth += parseInt($(this).outerWidth(true));
    });
}

getslideswidth();

$(window).resize(function(){
    getslideswidth();
});

/* ###### Работа AJAX-формы ###### */
$('.form-list').ajaxForm(function() {
    // Успешная отправка формы
    $(".form-list").addClass("sent");
});

/* ###### Делаем активными пункты меню при скролле ###### */
function findactivelink() {
    var scrollleft = $("main").scrollLeft();
    $(".page-menu a").each(function(){
        if (scrollleft >= $(this.hash).position({of: ".slider-wrapper"}).left + scrollleft - 400) {
            $(this).parents("ul").find("a").removeClass("active");
            $(this).addClass("active");
        }
    });
}

/* ###### Работа ссылок меню ###### */
$(".page-menu a").click(function(event){
    event.preventDefault();
    var scrollleft = $("main").scrollLeft();
    $(this).parents("ul").find("a").removeClass("active");
    $(this).addClass("active");
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $("main").animate({scrollLeft: $(this.hash).position({of: ".slider-wrapper"}).left + scrollleft}, 1200);
    } else {
        $("main").animate({scrollLeft: $(this.hash).position({of: ".slider-wrapper"}).left + scrollleft}, 1200, "easeOutCubic");
    }

    closeburger();
});

/* ###### Работа ссылки scroll ###### */
$("a.scroll").click(function(event){
    event.preventDefault();
    var scrollleft = $("main").scrollLeft();
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        $("main").animate({scrollLeft: $(this.hash).position({of: ".slider-wrapper"}).left + scrollleft}, 1200);
    } else {
        $("main").animate({scrollLeft: $(this.hash).position({of: ".slider-wrapper"}).left + scrollleft}, 1200, "easeOutCubic");
    }

    $(".page-menu a").removeClass("active");
    $(".page-menu a[href='"+this.hash+"']").addClass("active");
});

/* ###### Изменение цвета при скролле ###### */
function colorChange() {
  var scrollleft = $("main").scrollLeft();
  var currColor = $("body").css("background-color");
  var newColor;
  $(".slide").each(function(){
    if (($(this).position("main").left+scrollleft-200)<scrollleft && $(this).data("color")) {
      newColor =  $(this).data("color");
    }
  });
  if (newColor != currColor) {
    $("body").css("background-color", newColor);
  }
}
$(document).ready(function(){
  colorChange();
});

/* ###### Функции при скролле ###### */
$("main").scroll(function(){
    var scrollleft = $(this).scrollLeft();

    /* ###### Изменение цвета при скролле ###### */
    colorChange();

    /* ###### Прогрессбар ###### */
    scrollPercentage = scrollleft / (slideswidth - $(this).width());
    $(".progess-bar").css("transform", "scale3d("+scrollPercentage+",1,1)");

    /* ###### Скрываем блок .scroll ###### */
    if (scrollleft > 100) {$(".scroll").addClass("hidden")}
    else {$(".scroll").removeClass("hidden")}

    /* ###### Делаем активными проекты при скролле ###### */
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {}
    else {
    $(".project-item").each(function(){
        if (($(this).position({of: ".slider-wrapper"}).left+scrollleft+($(this).outerWidth(true)-$(this).outerWidth()) < scrollleft + $(window).width()*0.75) && ($(this).position({of: ".slider-wrapper"}).left + ($(this).outerWidth(true)-$(this).outerWidth()) + scrollleft > scrollleft)) {
            $(this).find(".project-intro").addClass("project-intro--active");
        } else {
            $(this).find(".project-intro").removeClass("project-intro--active");
        }
    });
        }
});

/* ###### Горизонтальный скролл ###### */
function scrollinit() {window.scrollf = setTimeout(scrollanchor,700);}
function scrollstop() {clearTimeout(scrollf)}

scrollinit();
scrollstop();

$(window).bind('DOMMouseScroll mousewheel', function(e){
    $("main").stop();
    scrollstop();
    if ($('.case-popup:hover').length === 0) {// Проверяем, неактивен ли попап. Если неактивен, то позволяем скролл.

    window.scrollleft = $("main").scrollLeft();
    window.firstscrollleft = $("main").scrollLeft();

    if(parseInt(e.originalEvent.wheelDelta || -e.originalEvent.detail) > 0) {window.newscroll = scrollleft-300; findactivelink();}
    else {window.newscroll = scrollleft+300; findactivelink();}
    if (newscroll < 0) {newscroll = 0; findactivelink();}
    if (newscroll > $("main")[0].scrollWidth-$("main")[0].offsetWidth) {newscroll = $("main")[0].scrollWidth-$("main")[0].offsetWidth+1}

    if (scrollleft > $("main")[0].scrollWidth-$("main")[0].offsetWidth) {
        newscroll = 0;
        $(".page-menu a").removeClass("active");
        $(".page-menu a[href='#about']").addClass("active");
    }

    $("main").animate({scrollLeft: newscroll}, 600, "easeOutCubic");
    scrollinit();
    }
});

/* ###### Якоря скролла ###### */
function scrollanchor() {
    $("main").stop();

    var scrollleft = $("main").scrollLeft();
    if (newscroll>firstscrollleft) {
    // right
        $("[data-anchor]").each(function(){
            if ($(this).data("anchor") === "case") {
                if (scrollleft < $(this).position({of: ".slider-wrapper"}).left + scrollleft + ($(this).outerWidth(true) - $(this).outerWidth()) - ($(window).width()-$(this).outerWidth())/2) {
                    window.newanchorindex = $(this).index("[data-anchor]");
                    return false;
                }
            } else {
                if (scrollleft < $(this).position({of: ".slider-wrapper"}).left + scrollleft) {
                    window.newanchorindex = $(this).index("[data-anchor]");
                    return false;
                }
            }
        });
    }
    else {
    // left
        $("[data-anchor]").each(function(){
            if (scrollleft > $(this).position({of: ".slider-wrapper"}).left + scrollleft) {
                window.newanchorindex = $(this).index("[data-anchor]");
            } else {return false;}
        });
    }

    var targetel = $("[data-anchor]:eq("+newanchorindex+")");

    if (targetel.data("anchor") === "case") {

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            $("main").stop().animate({scrollLeft: targetel.position({of: ".slider-wrapper"}).left + scrollleft + (targetel.outerWidth(true) - targetel.outerWidth()) - ($(window).width()-targetel.outerWidth())/2 }, 1200);
        } else {
            $("main").stop().animate({scrollLeft: targetel.position({of: ".slider-wrapper"}).left + scrollleft + (targetel.outerWidth(true) - targetel.outerWidth()) - ($(window).width()-targetel.outerWidth())/2 }, 1200, "easeOutCubic");
            setTimeout(findactivelink,300);
        }

    } else {
        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
            $("main").stop().animate({scrollLeft: targetel.position({of: ".slider-wrapper"}).left + scrollleft}, 1200);
        } else {
            $("main").stop().animate({scrollLeft: targetel.position({of: ".slider-wrapper"}).left + scrollleft}, 1200, "easeOutCubic");
            setTimeout(findactivelink,300);
        }
    }
}

/* ###### Надпись message в textarea ###### */
$("textarea").focus(function(){
    $(this).next("span.hide").addClass("hidden");
});

$("textarea").blur(function(){
    if ($(this).val() != "") {
        $(this).next("span.hide").addClass("hidden");
    } else {$(this).next("span.hide").removeClass("hidden"); }
});

/* ###### Переход на первый слайд при клике на лого ###### */
$(".header-logo").click(function(){
    $(".page-menu a[href='#about']")[0].click();
});

/* ###### Скролл перетаскиванием ###### */
/*
var x,y,top,left,down;

$(window).mousedown(function(e){
    down = true;
    x = e.pageX;
    y = e.pageY;
    top = $("main").scrollTop();
    left = $("main").scrollLeft();
    window.firstscrollleft = $("main").scrollLeft();
});

$("body").mousemove(function(e){
    if(down){
        var newX = e.pageX;
        var newY = e.pageY;

        $("main").scrollTop(top - newY + y);
        $("main").scrollLeft(left - newX + x);
        window.newscroll = $("main").scrollLeft();
        findactivelink();
    }
});

$("body").mouseup(function(e){down = false;});*/

/* ###### Открытие попапа ###### */
window.casesamount = $("[href='#case']").length;

// Scrollbar
var checkScrollBars = function(){
    var b = $('.case-popup .container');
    var normalw = 0;
    var scrollw = 0;
    normalw = window.innerWidth;
    scrollw = normalw - b.outerWidth();
    $('.case-popup--nav li:last-child').css({marginRight:scrollw+'px'});
}

$("[href='#case']").click(function(event){
    event.preventDefault;
    event.stopPropagation();
    $(".progess-bar, footer, .page-menu, header").addClass("invisible");

    window.activecase = $(this).index()+1;

    var caseheading = $(this).find(".project-intro").text();
    var casetext = $(this).find(".popup-info--text").text();
    var casewhatwedid = $(this).find(".popup-info--whatwedid").text();
    var caseimages = $(this).find(".popup-info--images").html();

    $(".case-popup--heading").text(caseheading);
    $(".case-popup--text").text(casetext);
    $(".case-popup--whatwedid b").text(casewhatwedid);
    $(".case-popup--images").html(caseimages);

    $(".case-popup").show();
    setTimeout(function(){$(".case-popup").addClass("active");
         checkScrollBars();
    }, 10);
});

/* ###### Закрытие попапа ###### */
$(".cross").click(function(){
    $(".progess-bar, footer, .page-menu, header").removeClass("invisible");
    $(".case-popup").removeClass("active");
    setTimeout(function(){$(".case-popup").hide();}, 251);
});

/* ###### Смена кейса ###### */
window.nextcase;

$(".case-popup--nav li:first-child").click(function(){
    nextcase = activecase-1;
    if (nextcase < 1) {nextcase = casesamount}
    activecase = nextcase;
    changecase();
});

$(".case-popup--nav li:last-child").click(function(){
    nextcase = activecase+1;
    if (nextcase > casesamount) {nextcase = 1}
    activecase = nextcase;
    changecase();
});

function changecase() {
    $(".case-popup .container").addClass("hidden");
    setTimeout(function(){
        var caseheading = $("[href='#case']:nth-of-type("+nextcase+")").find(".project-intro").text();
        var casetext = $("[href='#case']:nth-of-type("+nextcase+")").find(".popup-info--text").text();
        var casewhatwedid = $("[href='#case']:nth-of-type("+nextcase+")").find(".popup-info--whatwedid").text();
        var caseimages = $("[href='#case']:nth-of-type("+nextcase+")").find(".popup-info--images").html();

        $(".case-popup--scroll").scrollTop(0);

        $(".case-popup--heading").text(caseheading);
        $(".case-popup--text").text(casetext);
        $(".case-popup--whatwedid b").text(casewhatwedid);
        $(".case-popup--images").html(caseimages);
    },300);
    setTimeout(function(){
        $(".case-popup .container").removeClass("hidden");
    },350);
}

/* ###### Если мобильные, добавляем поддержку тача ###### */
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    $("body").addClass("mobile");

    $(".project-intro").addClass("project-intro--active");

    //window.firstscrollleft = 0;
    //window.enablemscroll = true;

    $("main").on("scrollstop",function(){
        /*if (enablemscroll && $(this).scrollLeft()>0) {
        window.newscroll = $(this).scrollLeft();
        scrollanchor();
        window.firstscrollleft = $(this).scrollLeft();
            enablemscroll = false;
        } else {
            enablemscroll = false;
        }*/
        findactivelink();
    });
}

/* ###### Работа бургер-меню на мобильных ###### */
window.menuopened = false;

function closeburger() {
    $(".page-menu").removeClass("active");
    setTimeout(function(){$(".page-menu").css("display", "none");}, 251);
    $(".burger-menu-button").removeClass("active");
    $(".progess-bar, footer").removeClass("invisible");
    menuopened = false;
}

function openburger() {
    $(".page-menu").css("display", "flex");
    setTimeout(function(){$(".page-menu").addClass("active");}, 20);
    $(".burger-menu-button").addClass("active");
    $(".progess-bar, footer").addClass("invisible");
    menuopened = true;
}

$(".burger-menu-button").click(function(){
    if (!menuopened) {openburger();} else {closeburger();}
});

/* ###### Переключаем кейсы свайпами ###### */
$(".case-popup").on("swipeleft", function(){
   nextcase = activecase+1;
    if (nextcase > casesamount) {nextcase = 1}
    activecase = nextcase;
    changecase();
});

$(".case-popup").on("swiperight", function(){
   nextcase = activecase-1;
    if (nextcase < 1) {nextcase = casesamount}
    activecase = nextcase;
    changecase();
});
