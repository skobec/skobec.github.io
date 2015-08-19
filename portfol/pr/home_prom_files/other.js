/**
 * Created by mart on 07.07.15.
 * Updated by Maxim Tugaev on 2015-07-23
 */
$(document).ready(function () {
    var search_result;


//    $('.intro__form-submit').on({
//        click: function () {
//            if ($('#search_field').val().length > 0) {
//                $('.intro__search-result').removeClass('hidden_block');
//            }
//            return false;
//        }
//    });
//    $('#search_field').on('change', function () {
//        if ($(this).val().length == 0) {
//            $('.intro__search-result').addClass('hidden_block');
//        }
//        return false;
//    })

    $('#search_field').autocomplete({
        serviceUrl: '/search',
        minChars: 3,
        transformResult: function (response) {
            search_result = $.map($.parseJSON(response).suggestions, function (element) {
                return {value: element.title, id: element.id};
            });
            return {
                suggestions: search_result
            };
        },
        onSelect: function (suggestion) {
            window.location.href = '/desktop/personal/id/' + suggestion.id;
        }
    });

    $('#search-button').on('click', function (e) {
        e.preventDefault();
        total = search_result.length;
        total_title = 'совпадений';
        switch (total % 10) {
            case 1:
                total_title = (total % 100 === 11) ? 'совпадений' : 'совпадение';
                break;
            case 2:
                total_title = (total % 100 === 12) ? 'совпадений' : 'совпадения';
                break;
            case 3:
                total_title = (total % 100 === 13) ? 'совпадений' : 'совпадения';
                break;
            case 4:
                total_title = (total % 100 === 14) ? 'совпадений' : 'совпадения';
                break;

        }
        var container = $('.intro__search-result');
        container.html('').show();
        var search_title = $('<span>', {class: 'intro__search-result-title'});
        search_title.text('Найдено ' + total + ' ' + total_title);
        search_title.appendTo(container);
        var result_list = $('<ul>')

        $(search_result).each(function (key, item) {

            var row = $('<li>').appendTo(result_list);
            var link = $('<a>', {'href': '/desktop/personal/id/'+item.id, 'class': 'intro__search-result-link'}).text(item.value).appendTo(row);
        });
        result_list.appendTo(container);

    });
});