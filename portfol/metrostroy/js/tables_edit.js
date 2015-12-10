function addRowToTable(table, cell1, cell2, cell3, cell4) {
    var row;
    row = "<tr><td><span>" + cell1 + "</span></td><td><span style='text-align: center'>" + cell2 +" "+ cell3 +"</span></td><td><span>" + cell4 + "</span></td><td><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}
function addRowToTable2(table, cell1, cell2, cell3, cell4) {
    var row;
    row = "<tr><td><span style='text-align: left'>" + cell1 + "</span></td><td><span style='text-align: center'>" + cell2 +" "+ cell3 +"</span></td><td><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}
function addRowToTable3(table, cell1, cell2, cell3) {
    var row;
    row = "<tr><td style='width: 245px'><span style='text-align: center'>" + cell1 + "</span></td><td style='width: 70px'><span style='text-align: left'>" + cell2 +" "+ cell3 +"</span></td><td style='width:1px'><span>" + ('<a href="#" class="btn_edit"></a><a href="#" class="btn_del_zap"></a>') + "</span></td></tr>";
    table.append(row);
}
$(document).ready(function() {

    //$("#table_add_plans .btn_del_zap").on("click",function() {
    //    var tr = $(this).closest('tr');
    //    tr.css("background-color","#FF3700");
    //
    //    tr.fadeOut(400, function(){
    //        tr.remove();
    //    });
    //    return false;
    //});
    $(document).on('click', 'a.btn_del_zap', function () {
        $(this).closest('tr').fadeOut(300, function(){ });
        return false;
    });
    var addClass = function(el, className) {
            if (el.classList) {
                el.classList.add(className);
            } else {
                el.className += ' ' + className;
            }
        },
        hasClass = function(el, className) {
            return el.classList ?
                el.classList.contains(className) :
                new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
        },
        removeClass = function(el, className) {
            if (el.classList) {
                el.classList.remove(className);
            } else {
                el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
            }
        },
        updateSelectPlaceholderClass = function(el) {
            var opt = el.options[el.selectedIndex];
            if(hasClass(opt, "placeholder")) {
                addClass(el, "placeholder");
            } else {
                removeClass(el, "placeholder");
            }
        },
        selectList = document.querySelectorAll("select");
//Simulate placeholder text for Select box
    for(var i = 0; i < selectList.length; i++) {
        var el = selectList[i];
        updateSelectPlaceholderClass(el);
        el.addEventListener("change", function() {
            updateSelectPlaceholderClass(this);
        });
    }

    //ссылки на таблицу
    $('tr[data-href]').on("click", function(e) {
        //console.log(e.target.tagName);
        //return false;
        if (e.target.tagName !== "SPAN") {
            document.location = $(this).data('href');
        }
    });
    $('.bl_diagramm .key_ico, .edit_bl_diagr .key_ico, .edit_bl_diagr .clear_btn,.edit_bl_diagr .close').click(function(){
        $(this).closest('.slide_bl_diagr').find('.edit_bl_diagr').fadeToggle('300');
    })


    //маркер ввода даты в поле с датой(календари)



    var input = jQuery('.it');
    jQuery(input).bind('keyup change', function()
    {
        var error = false;

        var value = $(this).val().split('.');
        if (value.length != 3 || !(value[0] && value[1] && value[2].length == 4))
        {
            error = 'Invalid value';
        }
        else
        {
            var date = new Date(value[2] + '-' + value[1] + '-' + value[0]);

            if (isNaN(date.getTime()))
                error = 'Invalid date';
            else if (parseInt(value[0]) != date.getDate())
                error = 'Unexpected day of month';
            else if (parseInt(value[1]) != date.getMonth() + 1)
                error = 'Unexpected month';
            else
            {
                var rValueYear = value[2].toString().split('').reverse().join('');
                var rDateYear = date.getFullYear().toString().split('').reverse().join('');
                if (rValueYear.length > rDateYear.length || rDateYear.indexOf(rValueYear) !== 0)
                    error = 'Ambiguous year';
            }
        }

        if (error)
        {
            $('#error-' + (this.id || this.name)).text(error);
            //return false;
        }
        else
        {
            $('#error-' + (this.id || this.name)).text('Okay');
            return true;
        }
    });


    /**
     * @see http://github.com/NV/placeholder.js
     */
    jQuery.fn.textPlaceholder = function () {

        return this.each(function(){

            var that = this;

            if (that.placeholder && 'placeholder' in document.createElement(that.tagName)) return;

            var placeholder = that.getAttribute('placeholder');
            var input = jQuery(that);

            if (that.value === '' || that.value == placeholder) {
                input.addClass('text-placeholder');
                that.value = placeholder;
            }

            input.focus(function(){
                if (input.hasClass('text-placeholder')) {
                    this.value = '';
                    input.removeClass('text-placeholder')
                }
            });

            input.blur(function(){
                if (this.value === '') {
                    input.addClass('text-placeholder');
                    this.value = placeholder;
                } else {
                    input.removeClass('text-placeholder');
                }
            });

            that.form && jQuery(that.form).submit(function(){
                if (input.hasClass('text-placeholder')) {
                    that.value = '';
                }
            });

        });

    };

    /*
     Masked Input plugin for jQuery
     Copyright (c) 2007-2013 Josh Bush (digitalbush.com)
     Licensed under the MIT license (http://digitalbush.com/projects/masked-input-plugin/#license)
     Version: 1.3.1
     */
    (function($) {
        function getPasteEvent() {
            var el = document.createElement('input'),
                name = 'onpaste';
            el.setAttribute(name, '');
            return (typeof el[name] === 'function')?'paste':'input';
        }

        var pasteEventName = getPasteEvent() + ".mask",
            ua = navigator.userAgent,
            iPhone = /iphone/i.test(ua),
            android=/android/i.test(ua),
            caretTimeoutId;

        $.mask = {
            //Predefined character definitions
            definitions: {
                '9': "[0-9]",
                'a': "[A-Za-z]",
                '*': "[A-Za-z0-9]"
            },
            dataName: "rawMaskFn",
            placeholder: '_',
        };

        $.fn.extend({
            //Helper Function for Caret positioning
            caret: function(begin, end) {
                var range;

                if (this.length === 0 || this.is(":hidden")) {
                    return;
                }

                if (typeof begin == 'number') {
                    end = (typeof end === 'number') ? end : begin;
                    return this.each(function() {
                        if (this.setSelectionRange) {
                            this.setSelectionRange(begin, end);
                        } else if (this.createTextRange) {
                            range = this.createTextRange();
                            range.collapse(true);
                            range.moveEnd('character', end);
                            range.moveStart('character', begin);
                            range.select();
                        }
                    });
                } else {
                    if (this[0].setSelectionRange) {
                        begin = this[0].selectionStart;
                        end = this[0].selectionEnd;
                    } else if (document.selection && document.selection.createRange) {
                        range = document.selection.createRange();
                        begin = 0 - range.duplicate().moveStart('character', -100000);
                        end = begin + range.text.length;
                    }
                    return { begin: begin, end: end };
                }
            },
            unmask: function() {
                return this.trigger("unmask");
            },
            mask: function(mask, settings) {
                var input,
                    defs,
                    tests,
                    partialPosition,
                    firstNonMaskPos,
                    len;

                if (!mask && this.length > 0) {
                    input = $(this[0]);
                    return input.data($.mask.dataName)();
                }
                settings = $.extend({
                    placeholder: $.mask.placeholder, // Load default placeholder
                    completed: null
                }, settings);


                defs = $.mask.definitions;
                tests = [];
                partialPosition = len = mask.length;
                firstNonMaskPos = null;

                $.each(mask.split(""), function(i, c) {
                    if (c == '?') {
                        len--;
                        partialPosition = i;
                    } else if (defs[c]) {
                        tests.push(new RegExp(defs[c]));
                        if (firstNonMaskPos === null) {
                            firstNonMaskPos = tests.length - 1;
                        }
                    } else {
                        tests.push(null);
                    }
                });

                return this.trigger("unmask").each(function() {
                    var input = $(this),
                        buffer = $.map(
                            mask.split(""),
                            function(c, i) {
                                if (c != '?') {
                                    return defs[c] ? settings.placeholder : c;
                                }
                            }),
                        focusText = input.val();

                    function seekNext(pos) {
                        while (++pos < len && !tests[pos]);
                        return pos;
                    }

                    function seekPrev(pos) {
                        while (--pos >= 0 && !tests[pos]);
                        return pos;
                    }

                    function shiftL(begin,end) {
                        var i,
                            j;

                        if (begin<0) {
                            return;
                        }

                        for (i = begin, j = seekNext(end); i < len; i++) {
                            if (tests[i]) {
                                if (j < len && tests[i].test(buffer[j])) {
                                    buffer[i] = buffer[j];
                                    buffer[j] = settings.placeholder;
                                } else {
                                    break;
                                }

                                j = seekNext(j);
                            }
                        }
                        writeBuffer();
                        input.caret(Math.max(firstNonMaskPos, begin));
                    }

                    function shiftR(pos) {
                        var i,
                            c,
                            j,
                            t;

                        for (i = pos, c = settings.placeholder; i < len; i++) {
                            if (tests[i]) {
                                j = seekNext(i);
                                t = buffer[i];
                                buffer[i] = c;
                                if (j < len && tests[j].test(t)) {
                                    c = t;
                                } else {
                                    break;
                                }
                            }
                        }
                    }

                    function keydownEvent(e) {
                        var k = e.which,
                            pos,
                            begin,
                            end;

                        //backspace, delete, and escape get special treatment
                        if (k === 8 || k === 46 || (iPhone && k === 127)) {
                            pos = input.caret();
                            begin = pos.begin;
                            end = pos.end;

                            if (end - begin === 0) {
                                begin=k!==46?seekPrev(begin):(end=seekNext(begin-1));
                                end=k===46?seekNext(end):end;
                            }
                            clearBuffer(begin, end);
                            shiftL(begin, end - 1);

                            e.preventDefault();
                        } else if (k == 27) {//escape
                            input.val(focusText);
                            input.caret(0, checkVal());
                            e.preventDefault();
                        }
                    }

                    function keypressEvent(e) {
                        var k = e.which,
                            pos = input.caret(),
                            p,
                            c,
                            next;

                        if (e.ctrlKey || e.altKey || e.metaKey || k < 32) {//Ignore
                            return;
                        } else if (k) {
                            if (pos.end - pos.begin !== 0){
                                clearBuffer(pos.begin, pos.end);
                                shiftL(pos.begin, pos.end-1);
                            }

                            p = seekNext(pos.begin - 1);
                            if (p < len) {
                                c = String.fromCharCode(k);
                                if (tests[p].test(c)) {
                                    shiftR(p);

                                    buffer[p] = c;
                                    writeBuffer();
                                    next = seekNext(p);

                                    if(android){
                                        setTimeout($.proxy($.fn.caret,input,next),0);
                                    }else{
                                        input.caret(next);
                                    }

                                    if (settings.completed && next >= len) {
                                        settings.completed.call(input);
                                    }
                                }
                            }
                            e.preventDefault();
                        }
                    }

                    function clearBuffer(start, end) {
                        var i;
                        for (i = start; i < end && i < len; i++) {
                            if (tests[i]) {
                                buffer[i] = settings.placeholder;
                            }
                        }
                    }

                    function writeBuffer() { input.val(buffer.join('')); }

                    function checkVal(allow) {
                        //try to place characters where they belong
                        var test = input.val(),
                            lastMatch = -1,
                            i,
                            c;

                        for (i = 0, pos = 0; i < len; i++) {
                            if (tests[i]) {
                                buffer[i] = settings.placeholder;
                                while (pos++ < test.length) {
                                    c = test.charAt(pos - 1);
                                    if (tests[i].test(c)) {
                                        buffer[i] = c;
                                        lastMatch = i;
                                        break;
                                    }
                                }
                                if (pos > test.length) {
                                    break;
                                }
                            } else if (buffer[i] === test.charAt(pos) && i !== partialPosition) {
                                pos++;
                                lastMatch = i;
                            }
                        }
                        if (allow) {
                            writeBuffer();
                        } else if (lastMatch + 1 < partialPosition) {
                            input.val("");
                            clearBuffer(0, len);
                        } else {
                            writeBuffer();
                            input.val(input.val().substring(0, lastMatch + 1));
                        }
                        return (partialPosition ? i : firstNonMaskPos);
                    }

                    input.data($.mask.dataName,function(){
                        return $.map(buffer, function(c, i) {
                            return tests[i]&&c!=settings.placeholder ? c : null;
                        }).join('');
                    });

                    if (!input.attr("readonly"))
                        input
                            .one("unmask", function() {
                                input
                                    .unbind(".mask")
                                    .removeData($.mask.dataName);
                            })
                            .bind("focus.mask", function() {
                                clearTimeout(caretTimeoutId);
                                var pos,
                                    moveCaret;

                                focusText = input.val();
                                pos = checkVal();

                                caretTimeoutId = setTimeout(function(){
                                    writeBuffer();
                                    if (pos == mask.length) {
                                        input.caret(0, pos);
                                    } else {
                                        input.caret(pos);
                                    }
                                }, 10);
                            })
                            .bind("blur.mask", function() {
                                checkVal();
                                if (input.val() != focusText)
                                    input.change();
                            })
                            .bind("keydown.mask", keydownEvent)
                            .bind("keypress.mask", keypressEvent)
                            .bind(pasteEventName, function() {
                                setTimeout(function() {
                                    var pos=checkVal(true);
                                    input.caret(pos);
                                    if (settings.completed && pos == input.val().length)
                                        settings.completed.call(input);
                                }, 0);
                            });
                    checkVal(); //Perform initial check for existing values
                });
            }
        });


    })(jQuery);


    $(".it").mask("99/99/99");
    $(".it").textPlaceholder();
//маркер ввода даты в поле с календарем

});


