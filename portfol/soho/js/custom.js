/**
 * Created by gleb on 09.08.16.
 */
jQuery( document ).ready(function() {
    jQuery('.owl-carousel').owlCarousel({
        autoplay:false,
        autoHeight: true,
        loop:true,
        margin:10,
        nav:false,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            1000:{
                items:1
            }
        }
    });

    jQuery('#formModal').on('show.bs.modal', function (event) {
        var button = jQuery(event.relatedTarget);
        var title = button.data('title');
        var text = button.find('.pic-link--object--img > div').html();
        var text2 = button.parent().find('.textPicBlock--item--text2').html();
        var pic = button.find('.pic-link--object--img').css('background-image');
        var pic2 = button.css('background-image');
        var langs = button.data('lang');
        var arrLang = langs.split(' ');
        if (pic === undefined) {
            pic = pic2;
        }
        if (text === undefined) {
            text = text2;
        }
        var modal = jQuery(this);
        modal.find('.modal-title').text(title);
        //modal.find('.modal-body input').val(title);
        modal.find('.modal-text').html(text);
        modal.find('.modal-content').css('background-image', pic);
        modal.find('.lang-box').html('');
        jQuery.each( arrLang, function(i, value ) {
            if (value.length > 0) {
                modal.find('.lang-box').append('<i class="ico-lang '+value+'"></i>');
            }
        });
    });

    jQuery('#formModalGreen').on('show.bs.modal', function (event) {
        var button = jQuery(event.relatedTarget);
        var pic = button.closest('.item').find('.slide-block--header--image').attr('src');
        var title = button.data('title');
        var modal = jQuery(this);
        modal.find('.modal-title').text(title);
        modal.find('.lang-box').html('');
        modal.find('.lang-box').append('<i class="ico-lang" style="background-image: url('+pic+')"></i>');
        //modal.find('.modal-body input').val(title);
        //modal.find('.lang-box').html('');
    });

    jQuery("#recipient-tel, #recipient-tel1").mask("+7(999) 999-99-99", {completed:function(){
        jQuery('.sendFormLesson').removeClass('disabled');
    }});

    // jQuery("#phoneInput").mask("+7(999) 999-99-99", {completed:function(){
        // jQuery('.form-send--button').prop('disabled', false);
    // }});

    jQuery('.lang_select_icon').on('click', function(e)   {
        //jQuery('.content--media--link').removeClass('active');
        e.preventDefault();
        //jQuery(this).addClass('active');

        var lang_title = jQuery(this).data('title');

        jQuery.each( jQuery('.content--media--link'), function(i, value ) {
            if (jQuery(value).data('title') == lang_title) {

                jQuery(value).addClass('active');
            }
            else {
                if (jQuery(value).hasClass('active')) {
                    jQuery(value).removeClass('active');
                }
            }

        });

        jQuery('#lang_select').val(lang_title);
    });
    var card = jQuery('.price--item .card');

    card.hover(
        function() {
            jQuery( this ).addClass('card-success');
        }, function() {
            jQuery( this ).removeClass('card-success');
        }
    );
    card.on('click', function(e){
        e.preventDefault();
        jQuery( this ).addClass('card-success');
    });

    jQuery('.ico_close').on('click', function(){
        jQuery(this).closest('.modal').modal('hide');
    });

    var stickyBlock = jQuery(".stickyJQ");
    var parentSticky = stickyBlock.closest('.row');

    stickyBlock.parent().css('height', parentSticky.height());
    stickyBlock.positionSticky();

    jQuery('.blog--item, .blog-sticky--post').on('click', function() {
        var link = jQuery(this).closest('div').find('a');
        window.location.href = link.attr('href');
    });
    // jQuery('.panel .textPicBlock--item--pic').on('click', function() {
        // var link = jQuery(this).closest('div').find('a');
    // });

    jQuery(".price--item--card").on('click', function() {
        var day = jQuery(this).find('.price--item--day').text();
        var time = jQuery(this).find('.price--item--time').text();
        var priceItem = jQuery(this).closest('.price--item');
        var title = priceItem.find('.price--item--title').text() + '- ' + priceItem.find('.price--item--small-title').text();
        var titleModal = title + ' ' + day + ' ' + time;
        jQuery(this).data('title', titleModal);
    });
});