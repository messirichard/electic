jQuery(function ($) {


    var custom_css = '';

    $('.lvca-button').each(function () {
        
        var button_elem = $(this);
        
        var id_selector = '#' + button_elem.attr('id');

        var button_color = (typeof button_elem.data('color') !== 'undefined') ? button_elem.data('color') : '';

        var button_hover_color = (typeof button_elem.data('hover_color') !== 'undefined') ? button_elem.data('hover_color') : '';

        if (button_color !== '') {
            custom_css += id_selector + '.lvca-button { background-color: ' + button_color + ' !important; }';
        }

        if (button_hover_color !== '') {
            custom_css += id_selector + '.lvca-button:hover { background-color: ' + button_hover_color + ' !important; }';
        }
    });
    if (custom_css !== '') {
        custom_css = '<style type="text/css">' + custom_css + '</style>';
        $('head').append(custom_css);
    }

});
