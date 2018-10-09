jQuery(function ($) {

    $('.lvca-slider').each(function () {

        var slider_elem = $(this);

        var settings = slider_elem.data('settings');

        var animation = settings['animation'] || "slide";

        var direction = settings['direction'] || "horizontal";

        var slideshow_speed = parseInt(settings['slideshow_speed']) || 5000;

        var animation_speed = parseInt(settings['animation_speed']) || 600;

        var pause_on_action = settings['pause_on_action'] ? true : false;

        var pause_on_hover = settings['pause_on_hover'] ? true : false;

        var direction_nav = settings['direction_nav'] ? true : false;

        var control_nav = settings['control_nav'] ? true : false;

        var slideshow = settings['slideshow'] ? true : false;

        var slideshow = settings['slideshow'] ? true : false;

        var randomize = settings['randomize'] ? true : false;

        var loop = settings['loop'] ? true : false;


        var $slider = slider_elem.find('.lvca-flexslider');


        $slider.flexslider({
            selector: ".lvca-slides > .lvca-slide",
            animation: animation,
            direction: direction,
            slideshowSpeed: slideshow_speed,
            animationSpeed: animation_speed,
            namespace: "lvca-flex-",
            pauseOnAction: pause_on_action,
            pauseOnHover: pause_on_hover,
            controlNav: control_nav,
            directionNav: direction_nav,
            prevText: "Previous<span></span>",
            nextText: "Next<span></span>",
            smoothHeight: false,
            animationLoop: true,
            slideshow: slideshow,
            easing: "swing",
            randomize: randomize,
            animationLoop: loop,
            controlsContainer: "lvca-slider"
        });
    });

});