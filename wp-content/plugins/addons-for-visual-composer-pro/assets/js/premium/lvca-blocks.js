if (typeof (jQuery) != 'undefined') {

    var lvcaBlockObjColl;

    var lvcaBlockCache;

    var LVCA_Block;

    var lvcaBlocks;

    var lvcaGrids;

    jQuery.noConflict(); // Reverts '$' variable back to other JS libraries

    (function ($) {
        "use strict";

        lvcaBlockObjColl = [];

        lvcaBlockCache = {

            data: {},

            remove: function (blockSignature) {
                delete this.data[blockSignature];
            },

            exist: function (blockSignature) {
                return this.data.hasOwnProperty(blockSignature) && this.data[blockSignature] !== null;
            },

            get: function (blockSignature) {
                return this.data[blockSignature];
            },

            set: function (blockSignature, cachedData) {
                this.remove(blockSignature);
                this.data[blockSignature] = cachedData;
            }
        };

        //LVCA_Block class - each ajax block uses a object of this class for requests
        LVCA_Block = function (blockId) {

            var self = this;

            var $blockElem = $('#' + blockId).eq(0);

            self.blockId = blockId;
            self.query = $blockElem.data('query');
            self.settings = $blockElem.data('settings');
            self.blockType = self.settings['block_type'];
            self.currentPage = $blockElem.data('current');
            self.filterTaxonomy = $blockElem.data('filter-taxonomy');
            self.filterTerm = $blockElem.data('filter-term');

            var $filterItemColl = $blockElem.find('ul.lvca-block-filter-list > li.lvca-block-filter-item');

            self.filterWidths = [];
            // Preserve the filter widths for future use. Initially all filters are in the main list and visible until JS takes over
            $filterItemColl.each(function () {

                self.filterWidths.push($(this).width())

            });

            self._processNumberedPagination();

            self.organizeFilters();

        };

        LVCA_Block.prototype = {

            blockId: '',
            blockType: '',
            query: '',
            settings: '',
            currentPage: 1,
            filterTerm: '', //current chosen filter term
            filterTaxonomy: '',
            userAction: 'next_prev', // load more or next prev action
            filterWidths: [],
            is_ajax_running: false,

            doAjaxBlockRequest: function (userAction) {

                var self = this;

                // first look in the cache
                var myCacheSignature = self._getObjectSignature();

                if (lvcaBlockCache.exist(myCacheSignature)) {

                    self._doAjaxBlockLoadingStart(true, userAction);

                    var cachedResponseObj = lvcaBlockCache.get(myCacheSignature);

                    self._doAjaxBlockProcessResponse(true, cachedResponseObj, userAction);

                    self._doAjaxBlockLoadingEnd(true, userAction);

                    return 'cache_hit';
                }

                self._doAjaxBlockLoadingStart(false, userAction);

                var requestData = {
                    'action': 'lvca_load_posts_block',
                    'blockId': self.blockId,
                    'query': self.query,
                    'settings': self.settings,
                    'blockType': self.blockType,
                    'currentPage': self.currentPage,
                    'filterTerm': self.filterTerm,
                    'filterTaxonomy': self.filterTaxonomy,
                    'userAction': userAction
                };

                // We can also pass the url value separately from ajaxurl for front end AJAX implementations
                $.post(lvca_ajax_object.ajax_url, requestData, function (response) {

                    lvcaBlockCache.set(myCacheSignature, response); // store for future retrieval

                    self._doAjaxBlockProcessResponse(false, response, userAction);

                    self._doAjaxBlockLoadingEnd(false, userAction);

                });
            },

            organizeFilters: function () {

                var self = this;

                var $blockElem = jQuery('#' + self.blockId);

                var $blockHeaderElem = $blockElem.find('.lvca-block-header').eq(0);

                var availableWidth = self._getAvailableWidth($blockHeaderElem);

                self._resizeFilters($blockHeaderElem, availableWidth);

            },

            handleFilterAction: function ($target) {

                var userAction = 'filter';

                var $blockElem = $target.closest('.lvca-block');

                $blockElem.find('.lvca-block-filter-item, .lvca-filter-item').removeClass('lvca-active');

                $target.parent().addClass('lvca-active');

                if (this.is_ajax_running === true)
                    return;

                var filterTerm = $target.attr('data-term-id');

                var filterTaxonomy = $target.attr('data-taxonomy');

                $blockElem.data('filter-term', filterTerm);

                $blockElem.data('filter-taxonomy', filterTaxonomy);

                this.filterTerm = filterTerm;

                this.filterTaxonomy = filterTaxonomy;

                this.currentPage = 1; // reset the current page

                this.doAjaxBlockRequest(userAction);

            },

            handlePageNavigation: function ($target) {

                var userAction = 'next';

                var $blockElem = $target.closest('.lvca-block');

                var paged = $target.data('page');

                // Do not continue if already processing or if the page is currently being shown
                if ($target.is('.lvca-current-page') || $blockElem.is('.lvca-processing'))
                    return;

                if (this.is_ajax_running === true)
                    return;

                if (paged == 'prev') {

                    if (this.currentPage == 1)
                        return;

                    this.currentPage--;

                    userAction = 'prev';
                }
                else if (paged == 'next') {

                    if (this.currentPage >= this.maxpages)
                        return;

                    this.currentPage++;

                    userAction = 'next';
                }
                else {

                    this.currentPage = paged;

                    userAction = 'load_page';
                }

                this.doAjaxBlockRequest(userAction);
            },

            handleLoadMore: function ($target) {

                var $blockElem = $target.closest('.lvca-block');

                // Do not continue if already processing or if the page is currently being shown
                if (this.currentPage >= this.maxpages || $blockElem.is('.lvca-processing'))
                    return;

                if (this.is_ajax_running === true)
                    return;

                var userAction = 'load_more';

                this.currentPage++;

                this.doAjaxBlockRequest(userAction);

            },
            
            initLightbox: function ($blockElem) {

                if ($().fancybox === undefined) {
                    return;
                }

                /* ----------------- Lightbox Support ------------------ */

                $blockElem.fancybox({
                    selector: 'a.lvca-lightbox-item', // the selector for portfolio item
                    loop: true,
                    caption: function (instance, item) {

                        var title = $(this).attr('title') || '';

                        var link = $(this).data('post-link') || '';

                        var caption = '<a href="' + link + '" title="' + title + '">' + title + '</a>';

                        var excerpt = $(this).data('post-excerpt') || '';

                        if (excerpt !== '') {

                            var txt = document.createElement("textarea");

                            txt.innerHTML = excerpt;

                            excerpt = txt.value;

                            caption += '<div class="lvca-fancybox-description">' + excerpt + '</div>';
                        }

                        return caption;
                    }
                });

            },

            _doAjaxBlockLoadingStart: function (cacheHit, userAction) {

                var self = this;

                self.is_ajax_running = true;

                var $blockElem = jQuery('#' + self.blockId);

                $blockElem.addClass('lvca-processing');

                var $blockElementInner = $('#' + self.blockId).find('.lvca-block-inner');

                //$blockElementInner.css('opacity', 0);

                if (userAction == 'next' || userAction == 'prev' || userAction == 'filter' || userAction == 'load_page') {

                    if (cacheHit == false)
                        $blockElem.append('<div class="lvca-loader-gif"></div>');

                    $blockElementInner.addClass('animated fadeOut_to_1');
                }
            },

            _doAjaxBlockLoadingEnd: function (cacheHit, userAction) {

                var self = this;

                self.is_ajax_running = false;

                var $blockElem = jQuery('#' + self.blockId);

                $blockElem.removeClass('lvca-processing');

                $('.lvca-loader-gif').remove();

                var $blockElementInner = $blockElem.find('.lvca-block-inner');

                $blockElementInner.removeClass('animated fadeOut_to_1');

                switch (userAction) {
                    case 'next':
                    case 'load_page':
                        $blockElementInner.addClass("animated fadeInRightSmall").on('animationend webkitAnimationEnd oAnimationEnd', function () {
                            $blockElementInner.removeClass('animated fadeInRightSmall');
                        });

                        break;
                    case 'prev':
                        $blockElementInner.addClass("animated fadeInLeftSmall").on('animationend webkitAnimationEnd oAnimationEnd', function () {
                            $blockElementInner.removeClass('animated fadeInLeftSmall');
                        });
                        break;
                    case 'filter':
                        $blockElementInner.addClass("animated fadeIn").on('animationend webkitAnimationEnd oAnimationEnd', function () {
                            $blockElementInner.removeClass('animated fadeIn');
                        });
                        break;

                }

                //$blockElementInner.css('opacity', 1);

                self._ensureBlockObjectsAreVisible($blockElem, userAction);

            },

            _doAjaxBlockProcessResponse: function (cacheHit, response, userAction) {

                var self = this;

                //read the server response
                var responseObj = $.parseJSON(response); //get the data object

                if (this.blockId !== responseObj.blockId)
                    return; // not mine

                var $blockElem = $('#' + this.blockId); // we know the response is for this block

                if ('load_more' === userAction) {

                    $(responseObj.data).appendTo($blockElem.find('.lvca-block-inner'));
                } else {
                    $blockElem.find('.lvca-block-inner').html(responseObj.data); //in place
                }

                $blockElem.attr('data-current', responseObj.paged);

                $blockElem.attr('data-maxpages', responseObj.maxpages);


                $blockElem.find('.lvca-pagination .lvca-page-nav.lvca-current-page').removeClass('lvca-current-page');

                $blockElem.find('.lvca-page-nav[data-page="' + parseInt(responseObj.paged) + '"]').addClass('lvca-current-page');

                $blockElem.find('.lvca-page-nav[data-page="next"]').removeClass('lvca-disabled');
                $blockElem.find('.lvca-page-nav[data-page="prev"]').removeClass('lvca-disabled');

                //hide or show prev
                if (true === responseObj.hidePrev) {
                    $blockElem.find('.lvca-page-nav[data-page="prev"]').addClass('lvca-disabled');
                }

                //hide or show next
                if (true === responseObj.hideNext) {
                    $blockElem.find('.lvca-page-nav[data-page="next"]').addClass('lvca-disabled');
                }

                var maxpages = parseInt(responseObj.maxpages);

                // If the query is being filtered by a specific taxonomy term - the All option is not chosen
                if (responseObj.filterTerm.length) {

                    if (maxpages == 1) {
                        // Hide everything if no pagination is required
                        $blockElem.find('.lvca-page-nav').hide();
                    }
                    else {

                        // hide all pages which are irrelevant in filtered results
                        $blockElem.find('.lvca-page-nav').each(function () {

                            var page = $(this).attr('data-page'); // can return next and prev too

                            if (page.match('prev|next')) {
                                $(this).show(); // could have been hidden with earlier filter if maxpages == 1
                            }
                            else if (parseInt(page) > maxpages) {
                                $(this).hide();
                            }
                            else {
                                $(this).show(); // display the same if hidden due to previous filter
                            }
                        });
                    }
                }
                else {
                    // display all navigation if it was hidden before during filtering
                    $blockElem.find('.lvca-page-nav').show();
                }

                // Reorganize the pagination if there are too many pages to display navigation for
                this._processNumberedPagination();

                var remaining_posts = parseInt(responseObj.remaining);

                // Set remaining posts to be loaded and hide the button if we just loaded the last page
                if (self.settings['show_remaining'] && remaining_posts !== 0) {
                    $blockElem.find('.lvca-pagination a.lvca-load-more span').text(remaining_posts);
                }

                if (remaining_posts === 0) {
                    $blockElem.find('.lvca-pagination a.lvca-load-more').addClass('lvca-disabled');
                }
                else {
                    $blockElem.find('.lvca-pagination a.lvca-load-more').removeClass('lvca-disabled');
                }

            },

            _getObjectSignature: function () {

                var self = this;

                /*
                var objectSignature = JSON.parse(JSON.stringify(self));

                objectSignature.query = '';
                objectSignature.settings = '';
                objectSignature.userAction = '';
                */

                // create a block signature object without heavy footprint of settings and query fields
                var signatureObject = {

                    blockId: self.blockId,
                    blockType: self.blockType,
                    query: '',
                    settings: '',
                    currentPage: self.currentPage,
                    filterTerm: self.filterTerm,
                    filterTaxonomy: self.filterTaxonomy

                };

                var objectSignature = JSON.stringify(signatureObject);

                return objectSignature;
            },

            // Manage page number display so that it does not get too long with too many page numbers displayed
            _processNumberedPagination: function () {

                var self = this;

                var $blockElem = jQuery('#' + self.blockId);

                var maxpages = parseInt($blockElem.attr('data-maxpages'));

                var currentPage = parseInt($blockElem.attr('data-current'));

                // Remove all existing dotted navigation elements
                $blockElem.find('.lvca-page-nav.lvca-dotted').remove();

                // proceed only if there are too many pages to display navigation for
                if (maxpages > 5) {

                    var beenHiding = false;

                    $blockElem.find('.lvca-page-nav.lvca-numbered').each(function () {

                        var page = $(this).attr('data-page'); // can return next and prev too

                        var pageNum = parseInt(page);

                        // Deal with only those pages between 1 and maxpages
                        if (pageNum > 1 && pageNum <= maxpages) {

                            var $navElement = $(this);

                            if (pageNum == currentPage || (pageNum == currentPage - 1) || (pageNum == currentPage + 1) || (pageNum == currentPage + 2)) {

                                if (beenHiding)
                                    $('<a class="lvca-page-nav lvca-dotted" href="#" data-page="">...</a>').insertBefore($navElement);

                                $navElement.show();

                                beenHiding = false;
                            }
                            else if (pageNum == maxpages) {

                                if (beenHiding)
                                    $('<a class="lvca-page-nav lvca-dotted" href="#" data-page="">...</a>').insertBefore($navElement);

                                beenHiding = false; // redundant for now
                            }
                            else {

                                $navElement.hide();

                                beenHiding = true;
                            }
                        }
                    });
                }
            },

            _getAvailableWidth: function ($blockHeaderElem) {

                var headerWidth = $blockHeaderElem.width();

                // Keep about 100px for more dropdown indicator
                var availableWidth = headerWidth - 100;

                var headingWidth = 0;

                if ($blockHeaderElem.find('.lvca-heading').length) {

                    if ($blockHeaderElem.find('.lvca-heading a').length)
                        headingWidth = $blockHeaderElem.find('.lvca-heading a').eq(0).width();
                    else
                        headingWidth = $blockHeaderElem.find('.lvca-heading span').eq(0).width();

                }

                if (availableWidth > headingWidth)
                    availableWidth = availableWidth - headingWidth;
                else
                    availableWidth = 0;

                return availableWidth;
            },

            _resizeFilters: function ($blockHeaderElem, availableWidth) {

                var self = this;

                var spaceRequired = 0;

                var $mainListElem = $blockHeaderElem.find('ul.lvca-block-filter-list');

                // Do not proceed if there is no main list as is the case with few header styles
                if ($mainListElem.length == 0)
                    return;

                var $mainListElem = $mainListElem.eq(0);

                var $dropdownListElem = $blockHeaderElem.find('ul.lvca-block-filter-dropdown-list').eq(0);

                var $mainListFilterColl = $mainListElem.find('li.lvca-block-filter-item');

                var $dropdownListFilterColl = $dropdownListElem.find('li.lvca-block-filter-item');

                var filterIndex = 0;

                var dropdownModified = false;

                $mainListFilterColl.each(function () {

                    var $filter = $(this);

                    spaceRequired = spaceRequired + self.filterWidths[filterIndex];

                    if (spaceRequired >= availableWidth) {

                        self._moveFilterToDropdownList($filter, $dropdownListElem);

                        dropdownModified = true;
                    }

                    filterIndex++;
                });

                $dropdownListFilterColl.each(function () {

                    var $filter = $(this);

                    /* If dropdown was modified earlier, we need to rearrange the list to maintain initial ordering of filters by
                    adding the elements back to the list.
                    Also no question of adding to main list if dropdownlist was modified earlier due to lack of space. */
                    if (dropdownModified) {

                        self._moveFilterToDropdownList($filter, $dropdownListElem);

                    }
                    else {

                        // takes into consideration the space required for existing items as calculated in previous loop
                        spaceRequired = spaceRequired + self.filterWidths[filterIndex];

                        // move if enough space is available
                        if (spaceRequired < availableWidth) {

                            self._moveFilterToMainList($filter, $mainListElem);

                        }
                    }

                    filterIndex++;
                });

                self._toggleMoreDropdownList($blockHeaderElem, $dropdownListElem);

            },

            _moveFilterToDropdownList: function ($filter, $dropdownFilterList) {

                $filter.detach();

                $dropdownFilterList.append($filter);

            },

            _moveFilterToMainList: function ($filter, $mainFilterList) {

                $filter.detach();

                $mainFilterList.append($filter);

            },

            _toggleMoreDropdownList: function ($blockHeaderElem, $dropdownListElem) {

                var moreFilter = $blockHeaderElem.find('.lvca-block-filter-more').eq(0);

                if ($dropdownListElem.find('li.lvca-block-filter-item').length == 0)
                    moreFilter.hide();
                else
                    moreFilter.show();

            },

            // Restore focus to the top of the block to make new elements visible
            _ensureBlockObjectsAreVisible: function ($blockElem, userAction) {

                if (userAction.match(/^(next|prev|load_page)$/)) {

                    var viewportTop = $(window).scrollTop();

                    var blockElemTop = $blockElem.offset().top;

                    // If top of block element is hidden above viewport when pagination in invoked,
                    // bring it back down and make it visible in viewport about 50 pixels from top
                    if (blockElemTop < viewportTop) {

                        $('html,body').animate({ scrollTop: blockElemTop - 60}, 800);

                    }

                }
            }

        };


        lvcaBlocks = {

            getBlockObjById: function (blockId) {

                var blockIndex = this._getBlockIndex(blockId);

                if (blockIndex !== -1)
                    return lvcaBlockObjColl[blockIndex];

                var blockObj = new LVCA_Block(blockId);

                lvcaBlockObjColl.push(blockObj); // add to the array for instant retrieval later

                return blockObj;
            },

            _getBlockIndex: function (blockId) {

                var blockIndex = -1;

                $.each(lvcaBlockObjColl, function (index, lvcaBlock) {

                    if (lvcaBlock.blockId === blockId) {

                        blockIndex = index;

                        return false; // breaks out of $.each only

                    }
                });

                return blockIndex;
            },

        };  //end lvcaBlocks


        /* ---------------------------- START Grid Block --------------------------------- */

        function LVCA_Grid() {

            LVCA_Block.apply(this, arguments);

        }

        // inherit LVCA_Block
        LVCA_Grid.prototype = Object.create(LVCA_Block.prototype);

        LVCA_Grid.prototype.constructor = LVCA_Grid;

        LVCA_Grid.prototype._doAjaxBlockLoadingStart = function (cacheHit, userAction) {

            var self = this;

            self.is_ajax_running = true;

            var $blockElem = jQuery('#' + self.blockId);

            $blockElem.addClass('lvca-processing');

            if (userAction == 'next' || userAction == 'prev' || userAction == 'filter' || userAction == 'load_page') {

                if (cacheHit == false) {

                    $blockElem.addClass('lvca-fetching');

                    $blockElem.append('<div class="lvca-loader-gif"></div>');
                }

            }
        };


        LVCA_Grid.prototype._doAjaxBlockLoadingEnd = function (cacheHit, userAction) {

            var self = this;

            self.is_ajax_running = false;

            var $gridElem = jQuery('#' + self.blockId);

            $gridElem.removeClass('lvca-processing');

            $('.lvca-loader-gif').remove();

            self._ensureBlockObjectsAreVisible($gridElem, userAction);

        };

        LVCA_Grid.prototype._doAjaxBlockProcessResponse = function (cacheHit, response, userAction) {

            var self = this;

            //read the server response
            var responseObj = $.parseJSON(response); //get the data object

            if (this.blockId !== responseObj.blockId)
                return; // not mine

            var $blockElem = $('#' + this.blockId); // we know the response is for this grid

            if ('load_more' === userAction) {

                var $blockElementInner = $blockElem.find('.lvca-block-inner');

                var $response = $('<div></div>').html(responseObj.data);

                $response.imagesLoaded(function () {

                    if (cacheHit == false)
                        $blockElem.removeClass('lvca-fetching');

                    var $new_items = $response.children('.lvca-block-column');

                    $blockElementInner.isotope('insert', $new_items);
                });

            } else {

                var $blockElementInner = $blockElem.find('.lvca-block-inner');

                var $existing_items = $blockElementInner.children('.lvca-block-column');

                var $response = $('<div></div>').html(responseObj.data);

                $response.imagesLoaded(function () {

                    if (cacheHit == false)
                        $blockElem.removeClass('lvca-fetching');

                    $blockElementInner.isotope('remove', $existing_items);

                    var $new_items = $response.children('.lvca-block-column');

                    $blockElementInner.isotope('insert', $new_items);
                });

            }

            $blockElem.attr('data-current', responseObj.paged);

            $blockElem.attr('data-maxpages', responseObj.maxpages);


            $blockElem.find('.lvca-pagination .lvca-page-nav.lvca-current-page').removeClass('lvca-current-page');

            $blockElem.find('.lvca-page-nav[data-page="' + parseInt(responseObj.paged) + '"]').addClass('lvca-current-page');

            $blockElem.find('.lvca-page-nav[data-page="next"]').removeClass('lvca-disabled');
            $blockElem.find('.lvca-page-nav[data-page="prev"]').removeClass('lvca-disabled');

            //hide or show prev
            if (true === responseObj.hidePrev) {
                $blockElem.find('.lvca-page-nav[data-page="prev"]').addClass('lvca-disabled');
            }

            //hide or show next
            if (true === responseObj.hideNext) {
                $blockElem.find('.lvca-page-nav[data-page="next"]').addClass('lvca-disabled');
            }

            var maxpages = parseInt(responseObj.maxpages);

            // If the query is being filtered by a specific taxonomy term - the All option is not chosen
            if (responseObj.filterTerm.length) {

                if (maxpages == 1) {
                    // Hide everything if no pagination is required
                    $blockElem.find('.lvca-page-nav').hide();
                }
                else {

                    // hide all pages which are irrelevant in filtered results
                    $blockElem.find('.lvca-page-nav').each(function () {

                        var page = $(this).attr('data-page'); // can return next and prev too

                        if (page.match('prev|next')) {
                            $(this).show(); // could have been hidden with earlier filter if maxpages == 1
                        }
                        else if (parseInt(page) > maxpages) {
                            $(this).hide();
                        }
                        else {
                            $(this).show(); // display the same if hidden due to previous filter
                        }
                    });
                }
            }
            else {
                // display all navigation if it was hidden before during filtering
                $blockElem.find('.lvca-page-nav').show();
            }

            // Reorganize the pagination if there are too many pages to display navigation for
            this._processNumberedPagination();

            var remaining_posts = parseInt(responseObj.remaining);

            // Set remaining posts to be loaded and hide the button if we just loaded the last page
            if (self.settings['show_remaining'] && remaining_posts !== 0) {
                $blockElem.find('.lvca-pagination a.lvca-load-more span').text(remaining_posts);
            }

            if (remaining_posts === 0) {
                $blockElem.find('.lvca-pagination a.lvca-load-more').addClass('lvca-disabled');
            }
            else {
                $blockElem.find('.lvca-pagination a.lvca-load-more').removeClass('lvca-disabled');
            }

        };

        lvcaGrids = Object.create(lvcaBlocks);

        lvcaGrids.getBlockObjById = function (blockId) {

            var blockIndex = this._getBlockIndex(blockId);

            if (blockIndex !== -1)
                return lvcaBlockObjColl[blockIndex];

            var blockObj = new LVCA_Grid(blockId);

            lvcaBlockObjColl.push(blockObj); // add to the array for instant retrieval later

            return blockObj;

        };

    }(jQuery));

}