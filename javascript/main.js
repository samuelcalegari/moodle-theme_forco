require(['jquery', 'jqueryui'], function ($, jqui) {

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').addClass('show');
        } else {
            $('#back-to-top').removeClass('show');
        }
    });

    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });

    setTimeout( function() {
        require(['core/str'], function (str) {
                var activebar = str.get_string('activatebar', 'theme_forco');
                $.when(activebar).done(function (localizedEditString) {
                    $('#uci_link').html(localizedEditString);
                });
            });
        },100);

    $("div[id^='accordion']").on('shown.bs.collapse', function () {

        var panel = $(this).find('.show');

        $('html, body').animate({
            scrollTop: panel.offset().top - 100
        }, 500);

    });
});