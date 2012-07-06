$(document).ready(function () {
    /*$(document).keypress(function (e) {
        if (e.keyCode === $.ui.keyCode.ENTER){
            $('.ui-button').click();
        }
    });*/

    $('body').css("background-color", "#666666");
    $('.ui-icon-required').remove();
    //$('#loginButton').removeClass("ui-corner-all");

    if ($('.ui-messageStack').size() <= 0){
        $('.loginBox').position({
            my : 'center center',
            at : 'center center',
            of : $(window)
        });
    }

    $('#loginButton').click(function (e) {
        e.preventDefault();
        var $inputs = $('input[name=email_address], input[name=password]');
        var $button = $(this);
        $.ajax({
            url        : $('form[name=login]').attr('action'),
            type       : 'POST',
            dataType   : 'json',
            data       : $inputs.serialize(),
            cache      : false,
            beforeSend : function () {
                showAjaxLoader($button, 'small');
            },
            success    : function (data) {
                if (data.loggedIn == true){
                    document.location = data.redirectUrl;
                }
                else {
                    if ($('.messageStack_pageStack').size() > 0){
                        $('.messageStack_pageStack').replaceWith(data.pageStack);
                    }
                    else {
                        $(data.pageStack).insertBefore($('form[name=login]'));
                    }
                    removeAjaxLoader($button);
                }
            }
        });

        return false;
    });
});