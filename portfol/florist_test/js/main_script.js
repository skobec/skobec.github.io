var options = {
    trigger: 'hover',
    placement: function () {
        if ($(window).scrollTop() > 100) {
            return "bottom";
        } else {
            return "top";
        }
    }
};
$("[data-toggle=popover]").popover(options);
