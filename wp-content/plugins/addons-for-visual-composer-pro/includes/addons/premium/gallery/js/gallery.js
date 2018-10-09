jQuery(function ($) {

    if ($().isotope === undefined) {
        return;
    }

    var custom_css = '';

    $('.lvca-gallery-wrap').each(function () {

        var container = $(this).find('.lvca-gallery:first');
        if (container.length === 0) {
            return; // no items to filter or load and hence don't continue
        }

        // layout Isotope after all images have loaded
        var htmlContent = $(this).find('.js-isotope:first');

        var isotopeOptions = htmlContent.data('isotope-options');

        htmlContent.isotope({
            // options
            itemSelector: isotopeOptions['itemSelector'],
            layoutMode: isotopeOptions['layoutMode'],
            transitionDuration: '0.8s',
            masonry: {
                columnWidth: '.lvca-grid-sizer'
            }
        });

        htmlContent.imagesLoaded(function () {
            htmlContent.isotope('layout');
        });

        // Relayout on inline full screen video and back
        $(document).on('webkitfullscreenchange mozfullscreenchange fullscreenchange',function(e){
            htmlContent.isotope('layout');
        });

        /* -------------- Taxonomy Filter --------------- */

        $(this).find('.lvca-taxonomy-filter .lvca-filter-item a').on('click', function (e) {
            e.preventDefault();

            var selector = $(this).attr('data-value');
            container.isotope({filter: selector});
            $(this).closest('.lvca-taxonomy-filter').children().removeClass('lvca-active');
            $(this).closest('.lvca-filter-item').addClass('lvca-active');
            return false;
        });

        /* ------------------- Pagination ---------------------- */

        $(this).find('.lvca-pagination a.lvca-page-nav').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                $parent = $this.closest('.lvca-gallery-wrap'),
                settings = $parent.data('settings'),
                items = $parent.data('items'),
                maxpages = $parent.data('maxpages'),
                current = $parent.data('current'),
                paged = $this.data('page');

            // Do not continue if already processing or if the page is currently being shown
            if ($this.is('.lvca-current-page') || $parent.is('.lvca-processing'))
                return;

            if (paged == 'prev') {
                if (current <= 1)
                    return;
                paged = current - 1;
            }
            else if (paged == 'next') {
                if (current >= maxpages)
                    return;
                paged = current + 1;
            }

            $parent.addClass('lvca-processing');

            var data = {
                'action': 'lvca_load_gallery_items',
                'settings': settings,
                'items': items,
                'paged': paged
            };
            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            $.post(lvca_ajax_object.ajax_url, data, function (response) {

                var $grid = $parent.find('.lvca-gallery:first');

                var $existing_items = $grid.children('.lvca-gallery-item');

                $grid.isotope('remove', $existing_items);

                var $response = $('<div></div>').html(response);

                $response.imagesLoaded(function () {

                    var $new_items = $response.children('.lvca-gallery-item');

                    $grid.isotope('insert', $new_items);
                });

                // Set attributes of DOM elements based on page loaded

                $parent.data('current', paged);

                $this.siblings('.lvca-current-page').removeClass('lvca-current-page');

                $parent.find('.lvca-page-nav[data-page="' + parseInt(paged) + '"]').addClass('lvca-current-page');

                $parent.find('.lvca-page-nav[data-page="next"]').removeClass('lvca-disabled');
                $parent.find('.lvca-page-nav[data-page="prev"]').removeClass('lvca-disabled');

                if (paged <= 1)
                    $parent.find('.lvca-page-nav[data-page="prev"]').addClass('lvca-disabled');
                else if (paged >= maxpages)
                    $parent.find('.lvca-page-nav[data-page="next"]').addClass('lvca-disabled');

                $parent.removeClass('lvca-processing');
            });

        });


        /*---------------- Load More Button --------------------- */

        $(this).find('.lvca-pagination a.lvca-load-more').on('click', function (e) {
            e.preventDefault();

            var $this = $(this),
                $parent = $this.closest('.lvca-gallery-wrap'),
                paged = $this.attr('data-page'),
                settings = $parent.data('settings'),
                items = $parent.data('items'),
                maxpages = $parent.data('maxpages'),
                total = $parent.data('total'),
                current = $parent.data('current');

            if (current >= maxpages || $parent.is('.lvca-processing'))
                return;

            $parent.addClass('lvca-processing');

            paged = ++current;

            var data = {
                'action': 'lvca_load_gallery_items',
                'settings': settings,
                'items': items,
                'paged': paged
            };

            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            $.post(lvca_ajax_object.ajax_url, data, function (response) {

                var $grid = $parent.find('.lvca-gallery:first');

                var $response = $('<div></div>').html(response);

                $response.imagesLoaded(function () {

                    var $new_items = $response.children('.lvca-gallery-item');

                    $grid.isotope('insert', $new_items);

                });

                $parent.data('current', current);

                // Set remaining posts to be loaded and hide the button if we just loaded the last page
                if (settings['show_remaining']) {
                    if (current == maxpages) {
                        $this.find('span').text(0);
                    }
                    else {
                        var remaining = total - (current * settings['items_per_page']);
                        $this.find('span').text(remaining);
                    }
                }

                if (current == maxpages)
                    $this.addClass('lvca-disabled');

                $parent.removeClass('lvca-processing');
            });

        });

        /* ----------------- Lightbox Support ------------------ */

        $(this).fancybox({
            selector: 'a.lvca-lightbox-item, a.lvca-video-lightbox', // the selector for gallery item
            loop: true,
            caption: function (instance, item) {

                var caption = $(this).attr('title') || '';

                var description = $(this).data('description') || '';

                if (description !== '') {
                    caption += '<div class="lvca-fancybox-description">' + description + '</div>';
                }

                return caption;
            }
        });


        /* --------------- Custom CSS ------------------ */

        var settings = $(this).data('settings');

        var element_id = $(this).children('.lvca-gallery').eq(0).attr('id');
        var id_selector = '#' + element_id;

        custom_css += id_selector + '.lvca-gallery { margin-left: -' + settings['gutter'] + 'px; margin-right: -' + settings['gutter'] + 'px; }';

        custom_css += '@media screen and (max-width: ' + settings['tablet_width'] + 'px) {';

        custom_css += id_selector + '.lvca-gallery { margin-left: -' + settings['tablet_gutter'] + 'px; margin-right: -' + settings['tablet_gutter'] + 'px; }';

        custom_css += '}';

        custom_css += '@media screen and (max-width: ' + settings['mobile_width'] + 'px) {';

        custom_css += id_selector + '.lvca-gallery { margin-left: -' + settings['mobile_gutter'] + 'px; margin-right: -' + settings['mobile_gutter'] + 'px; }';

        custom_css += '}';


        custom_css += id_selector + '.lvca-gallery .lvca-gallery-item { padding: ' + settings['gutter'] + 'px; }';

        custom_css += '@media screen and (max-width: ' + settings['tablet_width'] + 'px) {';

        custom_css += id_selector + '.lvca-gallery .lvca-gallery-item { padding: ' + settings['tablet_gutter'] + 'px; }';

        custom_css += '}';

        custom_css += '@media screen and (max-width: ' + settings['mobile_width'] + 'px) {';

        custom_css += id_selector + '.lvca-gallery .lvca-gallery-item { padding: ' + settings['mobile_gutter'] + 'px; }';

        custom_css += '}';


    });

    if (custom_css !== '') {
        var inline_css = '<style type="text/css">' + custom_css + '</style>';
        $('head').append(inline_css);
    }


});