$(document).ready(function() {
    var min = 600,
        max = 10000;
    var el = document.getElementById("counter");

    function rand() {
        el.innerHTML = "<span class='add_number'>0</span><span class='add_number'>0</span>";
        var num = getRandNum(min, max).toString();
        for (var i = num.length - 1; i >= 0; i--) {
            var e = document.createElement("span");
            e.className = "cnumber";
            e.innerHTML = num[i];
            el.appendChild(e);
        }
    }
    rand();

    function getRandNum(min, max, check) {
        if (!check) return Math.round(Math.random() * (max - min) + min);
        else return Math.random() * (max - min) + min;
    }


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