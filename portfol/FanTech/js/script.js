$(document).ready(function() {

    $('#contact_form').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            first_name: {
                validators: {
                    stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Укажите имя'
                    }
                }
            },
            last_name: {
                validators: {
                    stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Укажите фамилию'
                    }
                }
            },
            doljnost: {
                validators: {
                    stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Укажите должность'
                    }
                }
            },
            company: {
                validators: {
                    stringLength: {
                        min: 2,
                    },
                    notEmpty: {
                        message: 'Укажите компанию'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your email address'
                    },
                    emailAddress: {
                        message: 'Please supply a valid email address'
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your phone number'
                    },
                    phone: {
                        country: 'US',
                        message: 'Please supply a vaild phone number with area code'
                    }
                }
            },
            address: {
                validators: {
                    stringLength: {
                        min: 8,
                    },
                    notEmpty: {
                        message: 'Please supply your street address'
                    }
                }
            },
            city: {
                validators: {
                    stringLength: {
                        min: 4,
                    },
                    notEmpty: {
                        message: 'Please supply your city'
                    }
                }
            },
            state: {
                validators: {
                    notEmpty: {
                        message: 'Please select your state'
                    }
                }
            },
            zip: {
                validators: {
                    notEmpty: {
                        message: 'Please supply your zip code'
                    },
                    zipCode: {
                        country: 'US',
                        message: 'Please supply a vaild zip code'
                    }
                }
            },
            comment: {
                validators: {
                    stringLength: {
                        min: 10,
                        max: 200,
                        message:'Please enter at least 10 characters and no more than 200'
                    },
                    notEmpty: {
                        message: 'Please supply a description of your project'
                    }
                }
            }
        }
    })
        .on('success.form.bv', function(e) {
            $('#success_message').slideDown({ opacity: "show" }, "slow") // Do something ...
            $('#contact_form').data('bootstrapValidator').resetForm();

            e.preventDefault();

            var $form = $(e.target);

            var bv = $form.data('bootstrapValidator');

        });



    $(function () {
        $('[data-toggle="popover"]').popover({
            placement : 'right'
        })
    });



    // кастомизация поля с загрузкой фото


    (function($) {

        var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',
            isIE = /msie/i.test( navigator.userAgent );

        $.fn.customFile = function() {

            return this.each(function() {

                var $file = $(this).addClass('custom-file-upload-hidden'), // the original file input
                    $wrap = $('<div class="file-upload-wrapper">'),
                    $input = $('<input type="text" placeholder="Прикрепите фото" class="file-upload-input form-control" />'),
                    $button = $('<button type="button" class="file-upload-button" data-toggle="popover" data-trigger="hover" title="Формат файлов:" data-content="1.JPG 2. PNG"><i class="load-ico"></i></button>'),
                    $label = $('<label class="file-upload-button" for="'+ $file[0].id +'">Select a File</label>');


                $file.css({
                    position: 'absolute',
                    left: '-9999px'
                });

                $wrap.insertAfter( $file )
                    .append( $file, $input, ( isIE ? $label : $button ) );


                $file.attr('tabIndex', -1);
                $button.attr('tabIndex', -1);

                $button.click(function () {
                    $file.focus().click(); // Open dialog
                });

                $file.change(function() {

                    var files = [], fileArr, filename;


                    if ( multipleSupport ) {
                        fileArr = $file[0].files;
                        for ( var i = 0, len = fileArr.length; i < len; i++ ) {
                            files.push( fileArr[i].name );
                        }
                        filename = files.join(', ');


                    } else {
                        filename = $file.val().split('\\').pop();
                    }

                    $input.val( filename )
                        .attr('title', filename)
                        .focus();

                });

                $input.on({
                    blur: function() { $file.trigger('blur'); },
                    keydown: function( e ) {
                        if ( e.which === 13 ) { // Enter
                            if ( !isIE ) { $file.trigger('click'); }
                        } else if ( e.which === 8 || e.which === 46 ) {

                            $file.replaceWith( $file = $file.clone( true ) );
                            $file.trigger('change');
                            $input.val('');
                        } else if ( e.which === 9 ){
                            return;
                        } else {
                            return false;
                        }
                    }
                });

            });

        };

        if ( !multipleSupport ) {
            $( document ).on('change', 'input.customfile', function() {

                var $this = $(this),

                    uniqId = 'customfile_'+ (new Date()).getTime(),
                    $wrap = $this.parent(),

                    $inputs = $wrap.siblings().find('.file-upload-input')
                        .filter(function(){ return !this.value }),

                    $file = $('<input type="file" id="'+ uniqId +'" name="'+ $this.attr('name') +'"/>');

                setTimeout(function() {
                    if ( $this.val() ) {

                        if ( !$inputs.length ) {
                            $wrap.after( $file );
                            $file.customFile();
                        }
                    } else {
                        $inputs.parent().remove();
                        $wrap.appendTo( $wrap.parent() );
                        $wrap.find('input').focus();
                    }
                }, 1);

            });
        }

    }(jQuery));

    $('input[type=file]').customFile();

});
// кастомизация поля с загрузкой фото

// добавление спикеров и удаление
var fr = new FileReader;
var img = new Image;

function addTodoItem() {
    var todoItem1 = $("#new-todo-item1").val();
    var todoItem2 = $("#new-todo-item2").val();
    var todoItem3 = $("#new-todo-item3").val();
    var todoItem4 = $("#new-todo-item4").val();
    var todoItem5 = $("#new-todo-item5").val();
    $("#todo-list").append("<li class='spiker_bl'>" +
        " <div class='img_bl'>" + "<img src='" + img.src + "'>" + "</div>" +
        " <div class='name_and_family'>" +
        "<p>"+ todoItem1 + "</p>" +
        "<p>"+ todoItem2 + "</p>" +
        "</div>" +
        "<p>"+ todoItem3 + "</p>" +
        "<p>"+ todoItem4 + "</p>" +
        " <div class='site_bl'>" +
        "<p>"+ todoItem5 + "</p>" +
        "</div>" +
        "<button class='btn todo-item-delete'>"+
        "Удалить</button></li>");

    $("#new-todo-item1").val("");
    $("#new-todo-item2").val("");
    $("#new-todo-item3").val("");
    $("#new-todo-item4").val("");
    $("#new-todo-item5").val("");
}

function deleteTodoItem(e, item) {
    e.preventDefault();
    $(item).parent().fadeOut('slow', function() {
        $(item).parent().remove();
    });
}


function completeTodoItem() {
    $(this).parent().toggleClass("strike");
}


$(function() {

    $("#add-todo-item").on('click', function(e){
        e.preventDefault();
        addTodoItem()
    });


    $("#todo-list").on('click', '.todo-item-delete', function(e){
        var item = this;
        deleteTodoItem(e, item)
    });

    $(document).on('click', ".todo-item-done", completeTodoItem)

});




// добавление спикеров и удаление



//Загрузка изображения
$('#file').change(function() {



    fr.onload = function() {
        img.src = fr.result;
    };

    fr.readAsDataURL(this.files[0]);

});

//Загрузка изображения
