


$(document).ready(function () {
// dropdown выпадающее меню start

    function hideallDropdowns() {
        $(".dropped .drop-menu-main-sub").hide();
        $(".dropped").removeClass('dropped');
        $(".dropped .drop-menu-main-sub .title").unbind("click");
    }

    function showDropdown(el) {
        var el_li = $(el).parent().addClass('dropped');
        el_li
            .find('.title')
            .click(function () {
                hideallDropdowns();
            })
            .html($(el).html());

        el_li.find('.drop-menu-main-sub').show();
    }

    $(".drop-down").click(function(){
        showDropdown(this);
    });
    // $( ".drop-down" ).toggle(
    //     function() {
    //         showDropdown(this);
    //     }, function() {
    //         hideallDropdowns();
    //
    //     }
    // );
    $(document).mouseup(function () {
        hideallDropdowns();
    });
    // dropdown выпадающее меню end





    // custom select
    $('select').each(function(){
        var $this = $(this), numberOfOptions = $(this).children('option').length;

        $this.addClass('select-hidden');
        $this.wrap('<div class="select"></div>');
        $this.after('<div class="select-styled"></div>');

        var $styledSelect = $this.next('div.select-styled');
        $styledSelect.text($this.children('option').eq(0).text());

        var $list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter($styledSelect);

        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list);
        }

        var $listItems = $list.children('li');

        $styledSelect.click(function(e) {
            e.stopPropagation();
            $('div.select-styled.active').not(this).each(function(){
                $(this).removeClass('active').next('ul.select-options').hide();
            });
            $(this).toggleClass('active').next('ul.select-options').toggle();
        });

        $listItems.click(function(e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active');
            $this.val($(this).attr('rel'));
            $list.hide();
            //console.log($this.val());
        });

        $(document).click(function() {
            $styledSelect.removeClass('active');
            $list.hide();
        });

    });
    // custom select

    // скрипт якоря
    // $(function() {
    //     $('a[href*=#]:not([href=#])').click(function() {
    //         if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
    //             var target = $(this.hash);
    //             target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
    //             if (target.length) {
    //                 $('html,body').animate({
    //                     scrollTop: target.offset().top
    //                 }, 1000);
    //                 return false;
    //             }
    //         }
    //     });
    // });
    // скрипт якоря

    //
    //
    // // shim layer with setTimeout fallback
    // window.requestAnimFrame = (function(){
    //     return  window.requestAnimationFrame       ||
    //         window.webkitRequestAnimationFrame ||
    //         window.mozRequestAnimationFrame    ||
    //         window.oRequestAnimationFrame      ||
    //         window.msRequestAnimationFrame     ||
    //         function( callback ){
    //             window.setTimeout(callback, 1000 / 60);
    //         };
    // })();
    //
    // (function(win, d) {
    //
    //     var $ = d.querySelector.bind(d);
    //
    //     var bubble1 = $('#bubble-1');
    //     var bubble2 = $('#bubble-2');
    //     var bubble3 = $('#bubble-3');
    //     var bubble4 = $('#bubble-4');
    //     var bubble5 = $('#bubble-5');
    //     var bubble6 = $('#bubble-6');
    //     var bubble7 = $('#bubble-7');
    //     var bubble8 = $('#bubble-8');
    //     var bubble9 = $('#bubble-9');
    //
    //     var mainBG = $('section#content');
    //
    //     var ticking = false;
    //     var lastScrollY = 0;
    //
    //     function onResize () {
    //         updateElements(win.scrollY);
    //     }
    //
    //     function onScroll (evt) {
    //
    //         if(!ticking) {
    //             ticking = true;
    //             requestAnimFrame(updateElements);
    //             lastScrollY = win.scrollY;
    //         }
    //     }
    //
    //     function updateElements () {
    //
    //         var relativeY = lastScrollY / 3000;
    //
    //         mainBG.style.backgroundPosition = 'center ' + pos(0, 0, relativeY, 0) + 'px';
    //
    //         bubble1.style.top = pos(254, -1400, relativeY, 0) + 'px';
    //         bubble1.style.left = 15 + '%';
    //
    //         bubble2.style.top = pos(954, -2400, relativeY, 0) + 'px';
    //         bubble2.style.left = 35 + '%';
    //
    //         bubble3.style.top = pos(1054, -900, relativeY, 0) + 'px';
    //         bubble3.style.left = 75 + '%';
    //
    //         bubble4.style.top = pos(1400, -3900, relativeY, 0) + 'px';
    //         bubble4.style.left = 5 + '%';
    //
    //         bubble5.style.top = pos(1730, -2900, relativeY, 0) + 'px';
    //         bubble5.style.left = 40 + '%';
    //
    //         bubble6.style.top = pos(2860, -4900, relativeY, 0) + 'px';
    //         bubble6.style.left = 90 + '%';
    //
    //         bubble7.style.top = pos(2550, -1900, relativeY, 0) + 'px';
    //         bubble7.style.left = 65 + '%';
    //
    //         bubble8.style.top = pos(2300, -700, relativeY, 0) + 'px';
    //         bubble8.style.left = 20 + '%';
    //
    //         bubble9.style.top = pos(3700, -6000, relativeY, 0) + 'px';
    //         bubble9.style.left = 85 + '%';
    //
    //         ticking = false;
    //     }
    //
    //     function pos(base, range, relY, offset) {
    //         return base + limit(0, 1, relY - offset) * range;
    //     }
    //
    //     function prefix(obj, prop, value) {
    //         var prefs = ['webkit', 'moz', 'o', 'ms'];
    //         for (var pref in prefs) {
    //             obj[prefs[pref] + prop] = value;
    //         }
    //     }
    //
    //     function limit(min, max, value) {
    //         return Math.max(min, Math.min(max, value));
    //     }
    //
    //     (function() {
    //
    //         updateElements(win.scrollY);
    //
    //         bubble1.classList.add('force-show');
    //         bubble2.classList.add('force-show');
    //         bubble3.classList.add('force-show');
    //         bubble4.classList.add('force-show');
    //         bubble5.classList.add('force-show');
    //         bubble6.classList.add('force-show');
    //         bubble7.classList.add('force-show');
    //         bubble8.classList.add('force-show');
    //         bubble9.classList.add('force-show');
    //     })();
    //
    //     win.addEventListener('resize', onResize, false);
    //     win.addEventListener('scroll', onScroll, false);
    //
    // })(window, document);




    // !!!!!!!!!!
    // redrawDotNav();
    //
    // /* Scroll event handler */
    // $(window).bind('scroll',function(e){
    //     parallaxScroll();
    //     redrawDotNav();
    // });
    //
    // /* Next/prev and primary nav btn click handlers */
    // $('a.manned-flight').click(function(){
    //     $('html, body').animate({
    //         scrollTop:0
    //     }, 1000, function() {
    //         parallaxScroll(); // Callback is required for iOS
    //     });
    //     return false;
    // });
    // $('a.frameless-parachute').click(function(){
    //     $('html, body').animate({
    //         scrollTop:$('#frameless-parachute').offset().top
    //     }, 1000, function() {
    //         parallaxScroll(); // Callback is required for iOS
    //     });
    //     return false;
    // });
    // $('a.english-channel').click(function(){
    //     $('html, body').animate({
    //         scrollTop:$('#english-channel').offset().top
    //     }, 1000, function() {
    //         parallaxScroll(); // Callback is required for iOS
    //     });
    //     return false;
    // });
    // $('a.about').click(function(){
    //     $('html, body').animate({
    //         scrollTop:$('#about').offset().top
    //     }, 1000, function() {
    //         parallaxScroll(); // Callback is required for iOS
    //     });
    //     return false;
    // });
    //
    // /* Show/hide dot lav labels on hover */
    // $('nav#primary a').hover(
    //     function () {
    //         $(this).prev('h1').show();
    //     },
    //     function () {
    //         $(this).prev('h1').hide();
    //     }
    // );
    //
    //
    // /* Scroll the background layers */
    // function parallaxScroll(){
    //     var scrolled = $(window).scrollTop();
    //     $('#parallax-bg1').css('top',(0-(scrolled*.25))+'px');
    //     $('#parallax-bg2').css('top',(0-(scrolled*.5))+'px');
    //     $('#parallax-bg3').css('top',(0-(scrolled*.75))+'px');
    // }
    //
    // /* Set navigation dots to an active state as the user scrolls */
    // function redrawDotNav(){
    //     var section1Top =  0;
    //     // The top of each section is offset by half the distance to the previous section.
    //     var section2Top =  $('#frameless-parachute').offset().top - (($('#english-channel').offset().top - $('#frameless-parachute').offset().top) / 2);
    //     var section3Top =  $('#english-channel').offset().top - (($('#about').offset().top - $('#english-channel').offset().top) / 2);
    //     var section4Top =  $('#about').offset().top - (($(document).height() - $('#about').offset().top) / 2);;
    //     $('nav#primary a').removeClass('active');
    //     if($(document).scrollTop() >= section1Top && $(document).scrollTop() < section2Top){
    //         $('nav#primary a.manned-flight').addClass('active');
    //     } else if ($(document).scrollTop() >= section2Top && $(document).scrollTop() < section3Top){
    //         $('nav#primary a.frameless-parachute').addClass('active');
    //     } else if ($(document).scrollTop() >= section3Top && $(document).scrollTop() < section4Top){
    //         $('nav#primary a.english-channel').addClass('active');
    //     } else if ($(document).scrollTop() >= section4Top){
    //         $('nav#primary a.about').addClass('active');
    //     }
    //
    // }

// parralax script
    function parallaxIt() {

        // create variables
        var $fwindow = $(window);
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // on window scroll event
        $fwindow.on('scroll resize', function() {
            scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        });

        // for each of content parallax element
        $('[data-type="content"]').each(function (index, e) {
            var $contentObj = $(this);
            var fgOffset = parseInt($contentObj.offset().top);
            var yPos;
            var speed = ($contentObj.data('speed') || 1 );

            $fwindow.on('scroll resize', function (){
                yPos = fgOffset - scrollTop / speed;

                $contentObj.css('top', yPos);
            });
        });

        // for each of background parallax element
        $('[data-type="background"]').each(function(){
            var $backgroundObj = $(this);
            var bgOffset = parseInt($backgroundObj.offset().top);
            var yPos;
            var coords;
            var speed = ($backgroundObj.data('speed') || 0 );

            $fwindow.on('scroll resize', function() {
                yPos = - ((scrollTop - bgOffset) / speed);
                coords = + yPos + 'px';

                $backgroundObj.css({ top: coords });
            });
        });

        // triggers winodw scroll for refresh
        $fwindow.trigger('scroll');
    };

    parallaxIt();
    // parralax script


    // Select all links with hashes
    $('a[href*="#"].anchor-link')
    // Remove links that don't actually link to anything
        .not('[href="#"]')
        .not('[href="#0"]')
        .click(function(event) {
            // On-page links
            if (
                location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '')
                &&
                location.hostname == this.hostname
            ) {
                // Figure out element to scroll to
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                // Does a scroll target exist?
                if (target.length) {
                    // Only prevent default if animation is actually gonna happen
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 1000, function() {
                        // Callback after animation
                        // Must change focus!
                        var $target = $(target);
                        $target.focus();
                        if ($target.is(":focus")) { // Checking if the target was focused
                            return false;
                        } else {
                            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
                            $target.focus(); // Set focus again
                        };
                    });
                }
            }
        });
    $('.button-show-link').click(function () {
        $(this).toggleClass('open-bl');
        $('.block-show').slideToggle(500);
    })
    $('.button-show-link-brok1').click(function () {
        $('.button-show-link-brok1').toggleClass('open-bl');
        $('.block-show-brok1').slideToggle(500);
    })
    $('.button-show-link-brok2').click(function () {
        $('.button-show-link-brok2').toggleClass('open-bl');
        $('.block-show-brok2').slideToggle(500);
    })
});

