/**
 * Sticky notifier
 */

(function ($) {
    // Using it without an object
    $.sticky = function (note, options, callback) {
        return $.fn.sticky(note, options, callback);
    };

    $.fn.sticky = function (note, options, callback) {
        var settings =
        {
            'speed'      : 'fast',	 // animations: fast, slow, or integer
            'duplicates' : true,  // true or false
            'autoclose'  : 5000,  // integer or false,
            'position'   : 'top-right',// top-left, top-right, bottom-left, or bottom-right,
            'sticky'     : '', // sticky to special event (channel) or something else,
            'closeCallback' : function(){}
        };

        // Passing in the object instead of specifying a note
        if (!note) {
            note = this.html();
        }

        if (options) {
            $.extend(settings, options);
        }

        var position = settings.position;
        // Variables
        var display = true;
        var duplicate = 'no';

        // Somewhat of a unique ID
        var uniqID = Math.floor(Math.random() * 99999);

        // Handling duplicate notes and IDs
        $('.sticky-note').each(function () {
            if ($(this).html() == note && $(this).is(':visible')) {
                duplicate = 'yes';
                if (!settings['duplicates']) {
                    display = false;
                }
            }
            if ($(this).attr('id') == uniqID) {
                uniqID = Math.floor(Math.random() * 9999999);
            }
        });

        // Make sure the sticky queue exists
        if (!$('body').find('.sticky-queue').html()) {
            $('body').append('<div class="sticky-queue ' + position + '"></div>');
        }

        // Can it be displayed?
        if (display) {
            // Building and inserting sticky note
            $('.sticky-queue').prepend('<div class="sticky border-' + position + '" id="' + uniqID + '"></div>');
            $('#' + uniqID).append('<span class="sticky-close" rel="' + uniqID + '" title="Close"></span>');
            $('#' + uniqID).append('<div class="sticky-note" rel="' + uniqID + '">' + note + '</div>');
            $('#' + uniqID).append('<div class="sticky-hidden" rel="' + uniqID + '">' + settings.sticky + '</div>');

            // Smoother animation
            //var height = $('#' + uniqID).height();
            $('#' + uniqID).css('height', 'auto');

            $('#' + uniqID).slideDown(settings['speed']);
            display = true;
        }

        // Listeners
        $('.sticky').ready(function () {
            // If 'autoclose' is enabled, set a timer to close the sticky
            if (settings['autoclose']) {
                $('#' + uniqID).delay(settings['autoclose']).fadeOut(settings['speed']);
            }
        });
        // Closing a sticky
        $('.sticky-close').click(function () {
            $.stickyСlose($(this).attr('rel'), settings['speed'], settings['closeCallback'])
        });


        // Callback data
        var response =
        {
            'id'        : uniqID,
            'duplicate' : duplicate,
            'displayed' : display,
            'position'  : position
        }

        // Callback function?
        if (callback) {
            callback(response);
        }
        else {
            return (response);
        }
    }

    $.stickyСlose = function(id, speed, callback) {
        $('#' + id).dequeue().fadeOut(speed);
        if (callback) {
            callback();
        }
    }

    $.stickyUpdate = function(id, message) {
        $("#" + id + " > .sticky-note").html(message);
    }
})(jQuery);