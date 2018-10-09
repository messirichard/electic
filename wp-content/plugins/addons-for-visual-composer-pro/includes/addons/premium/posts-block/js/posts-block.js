jQuery(function ($) {

    $('.lvca-block-posts').each(function () {

        if ($(this).find('.lvca-module').length === 0) {
            return; // no items to display or load and hence don't continue
        }

        var currentBlockObj = lvcaBlocks.getBlockObjById($(this).data('block-uid'));

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


    });

});