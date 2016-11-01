$(document).ready(function() {

    $('#contact_form').bootstrapValidator({
        // To use feedback icons, ensure that you use Bootstrap v3.1.0 or later
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

            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

        });



    $(function () {
        $('[data-toggle="popover"]').popover({
            placement : 'right'
        })
    });



    // custom upload file

    (function($) {

        // Browser supports HTML5 multiple file?
        var multipleSupport = typeof $('<input/>')[0].multiple !== 'undefined',
            isIE = /msie/i.test( navigator.userAgent );

        $.fn.customFile = function() {

            return this.each(function() {

                var $file = $(this).addClass('custom-file-upload-hidden'), // the original file input
                    $wrap = $('<div class="file-upload-wrapper">'),
                    $input = $('<input type="text" class="file-upload-input form-control" />'),
                    // Button that will be used in non-IE browsers
                    $button = $('<button type="button" class="file-upload-button" data-toggle="popover" data-trigger="hover" title="Формат файлов:" data-content="1.JPG 2. PNG"><i class="load-ico"></i></button>'),
                    // Hack for IE
                    $label = $('<label class="file-upload-button" for="'+ $file[0].id +'">Select a File</label>');

                // Hide by shifting to the left so we
                // can still trigger events
                $file.css({
                    position: 'absolute',
                    left: '-9999px'
                });

                $wrap.insertAfter( $file )
                    .append( $file, $input, ( isIE ? $label : $button ) );

                // Prevent focus
                $file.attr('tabIndex', -1);
                $button.attr('tabIndex', -1);

                $button.click(function () {
                    $file.focus().click(); // Open dialog
                });

                $file.change(function() {

                    var files = [], fileArr, filename;

                    // If multiple is supported then extract
                    // all filenames from the file array
                    if ( multipleSupport ) {
                        fileArr = $file[0].files;
                        for ( var i = 0, len = fileArr.length; i < len; i++ ) {
                            files.push( fileArr[i].name );
                        }
                        filename = files.join(', ');

                        // If not supported then just take the value
                        // and remove the path to just show the filename
                    } else {
                        filename = $file.val().split('\\').pop();
                    }

                    $input.val( filename ) // Set the value
                        .attr('title', filename) // Show filename in title tootlip
                        .focus(); // Regain focus

                });

                $input.on({
                    blur: function() { $file.trigger('blur'); },
                    keydown: function( e ) {
                        if ( e.which === 13 ) { // Enter
                            if ( !isIE ) { $file.trigger('click'); }
                        } else if ( e.which === 8 || e.which === 46 ) { // Backspace & Del
                            // On some browsers the value is read-only
                            // with this trick we remove the old input and add
                            // a clean clone with all the original events attached
                            $file.replaceWith( $file = $file.clone( true ) );
                            $file.trigger('change');
                            $input.val('');
                        } else if ( e.which === 9 ){ // TAB
                            return;
                        } else { // All other keys
                            return false;
                        }
                    }
                });

            });

        };

        // Old browser fallback
        if ( !multipleSupport ) {
            $( document ).on('change', 'input.customfile', function() {

                var $this = $(this),
                    // Create a unique ID so we
                    // can attach the label to the input
                    uniqId = 'customfile_'+ (new Date()).getTime(),
                    $wrap = $this.parent(),

                    // Filter empty input
                    $inputs = $wrap.siblings().find('.file-upload-input')
                        .filter(function(){ return !this.value }),

                    $file = $('<input type="file" id="'+ uniqId +'" name="'+ $this.attr('name') +'"/>');

                // 1ms timeout so it runs after all other events
                // that modify the value have triggered
                setTimeout(function() {
                    // Add a new input
                    if ( $this.val() ) {
                        // Check for empty fields to prevent
                        // creating new inputs when changing files
                        if ( !$inputs.length ) {
                            $wrap.after( $file );
                            $file.customFile();
                        }
                        // Remove and reorganize inputs
                    } else {
                        $inputs.parent().remove();
                        // Move the input so it's always last on the list
                        $wrap.appendTo( $wrap.parent() );
                        $wrap.find('input').focus();
                    }
                }, 1);

            });
        }

    }(jQuery));

    $('input[type=file]').customFile();

});
// custom upload file

