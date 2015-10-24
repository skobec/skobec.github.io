$.fn.overlay=function() {
    var el=$(this);
    $('body').prepend('<div id="fancy_overlay"></div>');
    $('#fancy_overlay').click(function()
    {
        $('#fancy_overlay').remove();
        $('#fancy_overlay_opacity').remove();
        $("div.header_links a").removeClass('active');
        $('.popup').css({left: -10000, top: -10000});
    });
    $('#fancy_overlay').show('slow');
    return this;
}

$.fn.overlay_opacity=function() {
    var el=$(this);
    $('body').prepend('<div id="fancy_overlay_opacity"></div>');
    $('#fancy_overlay_opacity').click(function()
    {
        $('#fancy_overlay').remove();
        $('#fancy_overlay_opacity').remove();
        $("div.header_links a").removeClass('active');
        $('.popup').css({left: -10000, top: -10000});
    });
    $('#fancy_overlay_opacity').show('slow');
    return this;
}

function Lib(){
    var self = this,
        $info_container = $(".info_message"),
        $info_text_container = $info_container.find(".text_height"),
        $character = $info_container.find(".character").find("img"),
        default_character = "img/girl_1.png",
        default_text;

    this.dots_obj = {};

    this.init = function(){
        self.init_carousel();
        self.prepare_dots();
        default_text = $info_text_container.html();

        $('.open_popup').click(function(){
            $(".popup").each(function(){
                $(this).css({left: -10000, top: -10000});
            });
            var popup_name = $(this).attr("rel"),
                $popup = $('#popup_' + popup_name);
            if ($popup.length) {
                $popup.overlay().centering(1, 1);
                   var si = setInterval(function(){
                        var $s = $popup.find('.scroll-pane');
                        if ($popup.length) {
                            $s.jScrollPane({
                                verticalDragMinHeight: 78,
                                verticalDragMaxHeight: 78,
                                horizontalDragMinWidth: 78,
                                horizontalDragMaxWidth: 78
                            });
                            clearInterval(si);
                        }
                    }, 10);
                }

            return false;
        });

        $('.popup_close').click(function(){
            $('#fancy_overlay').remove();
            $('.popup').css({left: -10000, top: -10000});
        });
    };


    this.set_obj = function(dots_obj) {
        self.dots_obj = dots_obj;
    };

    this.prepare_dots = function(){
        for (var i in self.dots_obj){
            if (self.dots_obj.hasOwnProperty(i) && self.check_data(self.dots_obj[i])) {
                $("#" + self.dots_obj[i].id).css({
                    left: self.dots_obj[i].coordinates[0],
                    top: self.dots_obj[i].coordinates[1]
                }).bind("click", function () {
                    if ($(this).hasClass("image_opened")){
                        self.close_all_dots(true);
                        return;
                    }

                    for (var ii in self.dots_obj){
                        if (self.dots_obj[ii].id == $(this).attr("id")){
                            var data = self.dots_obj[ii],
                                $dot = $(this);

                            if (data.popup !== undefined) {
                                var $popup = $('#popup_window');
                                $popup.on('show.bs.modal', function (e) {

                                });
                                $popup.modal('show');

                                $popup.find('.popup_close').click(function () {
                                    $popup.modal('hide');
                                });
                                self.init_carousel();
                            }

                            if (data.message.text !== undefined) {
                                $info_text_container.html(data.message.text + '<img id="character" alt="" />');
                            }

                            if (data.message.character !== undefined) {
                                $character.parent().removeClass("character_hide");
                                $character.parent().parent().removeClass("without_character");
                                $character.attr("src", data.message.character);
                            }
                            else{
                                $character.parent().addClass("character_hide");
                                $character.parent().parent().addClass("without_character");
                                $character.attr("src", default_character);
                            }

                            if (data.left_image !== undefined) {
                                $("#img_left").attr("src", data.left_image).show();
                            }
                            else{
                                $("#img_left").hide();
                            }
                            if (data.right_image !== undefined) {
                                $("#img_right").attr("src", data.right_image).show();
                            }
                            else{
                                $("#img_right").hide();
                            }
                            if (data.footer_image !== undefined) {
                                $("#img_footer").attr("src", data.footer_image).show();
                            }
                            else{
                                $("#img_footer").hide();
                            }
                            self.close_all_dots(false);
                            self.show_dot_image($dot, data);
                        }
                    }
                });

            }
        }
    };

    /**
     * ���������� �������� �����.
     * @param $dot
     * @param data - ������ � ����������� �����.
     */
    this.show_dot_image = function($dot, data){
        var $img = $("#"+data.click_image_target);

            if ($dot.hasClass("image_opened")){
                if (data.click_image_target !== undefined && $img.length) {
                    $img.hide();
                }
                $dot.removeClass("image_opened");
                $dot.find("a").removeClass("ico_close2");
            }
            else{
                if (data.click_image_target !== undefined && $img.length) {
                    $img.show();
                }
                $dot.addClass("image_opened");
                $dot.find("a").addClass("ico_close2");
            }

    };

    /**
     * ��������� ��� �������� �����.
     */
    this.close_all_dots = function(close_image){
        $(".image_opened").each(function(){
            $(this).removeClass("image_opened");
            $(this).find("a").removeClass("ico_close2");
            for (var i in self.dots_obj){
                if (self.dots_obj[i].id == $(this).attr("id")){
                    var $t = $("#"+self.dots_obj[i].click_image_target);
                    if (self.dots_obj[i].click_image_target !== undefined && $t.length) {
                        $t.hide();
                    }

                    if (close_image) {
                        if (self.dots_obj[i].left_image !== undefined) $("#img_left").hide();
                        if (self.dots_obj[i].right_image !== undefined) $("#img_right").hide();
                        if (self.dots_obj[i].footer_image !== undefined) $("#img_footer").hide();
                        $info_text_container.html(default_text);

                        $character.parent().removeClass("character_hide");
                        $character.parent().parent().removeClass("without_character");
                        $("#character").attr("src", default_character);
                    }
                }
            }
        });
    };






    this.init_carousel = function(){
        if ($.fn.owlCarousel) {

            var $c = $("#popup_carousel"),
                $n = $('.popup_next'),
                $p = $('.popup_prev');
            $c.owlCarousel({
                navigation: false,
                rewindSpeed: 400,
                pagination:false,
                singleItem: true
            });

            $n.click(function(){
                $c.trigger('owl.next');
            });
            $p.click(function(){
                $c.trigger('owl.prev');
            });
        }
    };


    /**
     * ��������� ������� ���� ����������� ������� ��� �����
     * @param dot_obj
     */
    this.check_data = function(dot_obj){
        var $obj = $("#" + dot_obj.id);

        if (! $obj.length) return false;

        return true;
    };




}