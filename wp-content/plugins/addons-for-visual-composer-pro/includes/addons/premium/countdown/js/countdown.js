jQuery(function ($) {

    $('.lvca-countdown').each(function () {

        var end_date = $(this).data('end-date');
        if (end_date) {
            $(this).countdown(end_date, function (event) {
                $(this).html(
                    event.strftime('<ul><li><span>%D</span>Days</li><li><span>%H</span>Hour</li><li><span>%M</span>Min</li><li><span>%S</span>Sec</li></ul>')
                );
            });
        }

    });

});