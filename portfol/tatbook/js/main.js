$(function() {
    $("#fullPage").click(function() {
        $("#rightWrapper").toggleClass("full-page");
        $("#header").toggleClass("full-page");
    });
})

$(function() {
    $("#listView li").click(function () {
        if ( $("#listView li").hasClass("list-item-active") ) {
            $("#listView li").removeClass("list-item-active");
        }
        $(this).addClass("list-item-active");
    });
    $('#left_menu > ul > li > a').on('click', function() {
        var ul = $(this).find('ul');
        if(!ul.length) return;
        console.log(ul.is(':visible'));
        if(ul.is(':visible')) {
            ul.hide();
        }else{
            ul.show();
        }
    })
});
function OpenMenu()
{

    if ($('#left_menu').css("marginLeft") == '0px') {
        //$('.content_bl .container').removeClass('padd_content');
        $('.btn_menu').removeClass('active');
        $('.left_menu_nav, #left_menu').removeClass('fix');
        $('.left_menu_nav > .btn_menu').removeClass('hidden');
        $('#left_menu').animate({
                marginLeft: "-300px"
            }, 100
        );
        $('#search_bl').animate({
                marginLeft: "-500px"
            }, 100
        );
    } else {
        $('.btn_menu').addClass('active');
        //$('.content_bl .container').addClass('padd_content');
        $('.left_menu_nav > .btn_menu').addClass('hidden');
        setTimeout(function(){
            $('.left_menu_nav, #left_menu').addClass('fix');
        }, 300);
        $('#left_menu').animate({
                marginLeft: "0px"
            }, 100
        );

    }
}
function OpenSearch()
{

    if ($('#search_bl').css("marginLeft") == '0px') {
        $('#search_bl').removeClass('active');
        $('#search_bl').animate({
                marginLeft: "-500px"
            }, 100
        );
    } else {
        $('#search_bl').addClass('active');
        $('#search_input').focus();
        $('#search_bl').animate({
                marginLeft: "0px"
            }, 100
        );

    }
}
function checkParams() {
    var search = $('#search_input').val();

    if(search.length != 0) {
        $('#submit').removeAttr('disabled').addClass('active_btn');
    } else {
        $('#submit').attr('disabled', 'disabled').removeClass('active_btn');
    }
}
jQuery(document).ready(function($) {
    $('.quont-minus').click(function () {
        var $input = $(this).parent().find('input');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $('.quont-plus').click(function () {
        var $input = $(this).parent().find('input');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
});
$(document).ready(function() {
//Sort random function
    function random(owlSelector){
        owlSelector.children().sort(function(){
            return Math.round(Math.random()) - 0.5;
        }).each(function(){
            $(this).appendTo(owlSelector);
        });
    }
    $(".carousel_scroll, #carousel_scroll").owlCarousel({
        itemsCustom: [
            [0, 1],
            [500, 2],
            [700, 3],
            [900, 4],
            [1200, 4],
            [1400, 5],
            [1600, 6]
        ],
        /*itemsDesktop : [1200,3], //3 items between 1000px and 901px
        itemsDesktopSmall : [900,2], // betweem 900px and 501px
        itemsTablet: [500,1], //1 items between 500 and 0
        itemsMobile : false, // itemsMobile disabled - inherit from itemsTablet option*/
        touchDrag: false,
        mouseDrag: false,
        loop:true,
        margin: 0,
//            nav:true,
        navigation: true,
        navigationText: [
            "<a class='btn_left'><img src='img/arrow_book_left.png'></a>",
            "<a class='btn_right'><img src='img/arrow_book_right.png'></a>"
        ],
        beforeInit : function(elem){
            //Parameter elem pointing to $("#owl-demo")
            random(elem);
        }

    });

});
$(".carousel").carousel({
    swipe: 30, // percent-per-second, default is 50. Pass false to disable swipe
    interval : false
});
(function($){
    $(window).load(function(){
        $.mCustomScrollbar.defaults.scrollButtons.enable=true; //enable scrolling buttons by default
        $.mCustomScrollbar.defaults.axis="yx"; //enable 2 axis scrollbars by default
        $("#carousel_scroll .owl-wrapper-outer, .carousel_scroll .owl-wrapper-outer").mCustomScrollbar({
            scrollInertia:400,
            scrollbarPosition: "outside",
            axis:"x", // horizontal scrollbar
            mouseWheel:{ enable: false },
        });
    });
})(jQuery);





