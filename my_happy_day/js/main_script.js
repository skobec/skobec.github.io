$(document).ready(function(){
    var max = 10000;
    var rand = Math.floor( ( Math.random() * max ) + 1.00 );
    document.getElementById( 'rand' ).innerText = rand;
    if (top.location != location) {
        top.location.href = document.location.href ;
    };

$(function(){
    window.prettyPrint && prettyPrint();
    $('#dp1').datepicker({
        format: 'mm-dd-yyyy'
    });

    // disabling dates
    var nowTemp = new Date();
    var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    var checkin = $('#dpd1').datepicker({
        onRender: function(date) {
            return date.valueOf() < now.valueOf() ? 'disabled' : '';
        }
    }).on('changeDate', function(ev) {
        if (ev.date.valueOf() > checkout.date.valueOf()) {
            var newDate = new Date(ev.date)
            newDate.setDate(newDate.getDate() + 1);
            checkout.setValue(newDate);
        }
        checkin.hide();
        $('#dpd2')[0].focus();
    }).data('datepicker');
});

    $('#tags-input').selectivity({
        items: ['Музыкальный бэнд', 'Тамада', 'Фотограф', 'Певец'],
        multiple: true,
        tokenSeparators: [' '],
        value: ['Музыкальный бэнд', ]
    });
});