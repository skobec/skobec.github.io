$('.load_photo_bl__add .btn').hover(function() {
    $('.load_photo_bl__btn_load_img_bl__examples_bl').toggle();
});
$('#div-1').hide();
$('input[type="radio"]').click(function(){
    $('#' + $(this).attr('rel')).toggle( this.checked );
});
$(document).ready(function() {
    $('.number_post')
        .bootstrapValidator({
            message: 'Не верно',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                number: {
                    message: 'сума не является действительной',
                    validators: {
                        notEmpty: {
                            message: 'Введите сумму цифрами'
                        },
                        digits: {
                            message: 'Введите сумму цифрами'
                        }
                    }
                }
            }
        })
        .on('error.field.bv', function(e, data) {
            console.log(data.field, data.element, '-->error');
        })
        .on('success.field.bv', function(e, data) {
            console.log(data.field, data.element, '-->success');
        });
});
$(function () {
    $('[data-toggle="popover"]').popover()
});
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})
//var f = document.forms.Form;
//f.onchange = function() {
//    var n = f.querySelectorAll('[type="checkbox"]'),
//        l = f.querySelectorAll('[type="checkbox"]:checked');
//    for(var j=0; j<n.length; j++)
//        if (l.length >= 2) { // если отметить три и более галочки
//            n[j].disabled = true; // все чекбоксы становятся disabled
//            for(var i=0; i<l.length; i++)
//                l[i].disabled = false; // но disabled убирается с помеченных галочками чекбоксов
//        } else {
//            n[j].disabled = false; // если выделить менее трёх галочек, то disabled снимается со всех чекбоксов
//        }
//}
