jQuery(function ($) {

    if ($().isotope === undefined) {
        return;
    }

    var custom_css = '';
    $('.lvca-block-grid').each(function () {

        var currentBlockObj = lvcaGrids.getBlockObjById($(this).data('block-uid'));

        var layoutMode = currentBlockObj.settings['layout_mode'];

        // layout Isotope after all images have loaded
        var $blockElemInner = $(this).find('.lvca-block-inner');

        $blockElemInner.isotope({
            itemSelector: '.lvca-block-column',
            layoutMode: layoutMode,
            transitionDuration: '0.8s',
        });

        $blockElemInner.imagesLoaded(function () {
            $blockElemInner.isotope('layout');
        });

        /* ----------- Reorganize Filters when device width changes -------------- */

        /* https://stackoverflow.com/questions/24460808/efficient-way-of-using-window-resize-or-other-method-to-fire-jquery-functions */
        var lvcaResizeTimeout;

        $(window).resize(function () {

            if (!!lvcaResizeTimeout) {
                clearTimeout(lvcaResizeTimeout);
            }

            lvcaResizeTimeout = setTimeout(function () {

                currentBlockObj.organizeFilters();

            }, 200);
        });

        /* -------------- Taxonomy Filter --------------- */

        $(this).find('.lvca-taxonomy-filter .lvca-filter-item a, .lvca-block-filter .lvca-block-filter-item a').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleFilterAction($(this));

            return false;
        });

        /* ------------------- Pagination ---------------------- */

        $(this).find('.lvca-pagination a.lvca-page-nav').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handlePageNavigation($(this));

        });

        /*---------------- Load More Button --------------------- */

        $(this).find('.lvca-pagination a.lvca-load-more').on('click', function (e) {

            e.preventDefault();

            currentBlockObj.handleLoadMore($(this));

        });

        /* ---------------------- Init Lightbox ---------------------- */

        currentBlockObj.initLightbox($(this));
        
        /* --------- Custom CSS Generation --------------- */

        var $grid_elem = $(this);

        var id_selector = '#' + $grid_elem.attr('id');

        var prefs = $grid_elem.data('prefs');

        var desktop_gutter = (typeof prefs['gutter'] !== 'undefined') ? prefs['gutter'] : 10;

        var tablet_gutter = (typeof prefs['tablet_gutter'] !== 'undefined') ? prefs['tablet_gutter'] : 10;

        var tablet_width = prefs['tablet_width'] || 800;

        var mobile_gutter = (typeof prefs['mobile_gutter'] !== 'undefined') ? prefs['mobile_gutter'] : 10;

        var mobile_width = prefs['mobile_width'] || 480;

        custom_css += id_selector + '.lvca-block .lvca-block-inner { margin-left: -' + desktop_gutter + 'px; margin-right: -' + desktop_gutter + 'px; }';

        custom_css += id_selector + '.lvca-block .lvca-block-inner .lvca-block-column { padding:' + desktop_gutter + 'px; }';

        custom_css += ' @media only screen and (max-width: ' + tablet_width + 'px) { ' + id_selector + '.lvca-block .lvca-block-inner { margin-left: -' + tablet_gutter + 'px; margin-right: -' + tablet_gutter + 'px; } ' + id_selector + '.lvca-block .lvca-block-inner .lvca-block-column { padding:' + tablet_gutter + 'px; } } ';

        custom_css += ' @media only screen and (max-width: ' + mobile_width + 'px) { ' + id_selector + '.lvca-block .lvca-block-inner { margin-left: -' + mobile_gutter + 'px; margin-right: -' + mobile_gutter + 'px; } ' + id_selector + '.lvca-block .lvca-block-inner .lvca-block-column { padding:' + mobile_gutter + 'px; } } ';
    });

    if (custom_css !== '') {
        custom_css = '<style type="text/css">' + custom_css + '</style>';
        $('head').append(custom_css);
    }
    
});