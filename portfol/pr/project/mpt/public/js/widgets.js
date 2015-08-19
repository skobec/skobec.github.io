$(document).ready(function () {
    $('.cards').on('click', 'a[data-action="expand"]', function (e) {
        $(this).parent().toggleClass('is-active')
                .parent().parent().toggleClass('is-expanded');
        e.preventDefault();
    });
});