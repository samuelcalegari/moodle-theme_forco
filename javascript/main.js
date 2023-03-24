require(['jquery', 'jqueryui'], function ($, jqui) {
    console.log('init');
    $("#page").scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('#back-to-top').addClass('show');
        } else {
            $('#back-to-top').removeClass('show');
        }
    });

    // scroll body to 0px on click
    $('#back-to-top').click(function () {
        $('#page').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
});
