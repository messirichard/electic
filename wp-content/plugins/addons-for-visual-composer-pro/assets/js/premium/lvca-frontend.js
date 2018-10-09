/**
 * Reviews JS
 */
if (typeof (jQuery) != 'undefined') {

    jQuery.noConflict(); // Reverts '$' variable back to other JS libraries

    (function ($) {
        "use strict";

        $(function () {

            var LVCA_Tabs_Mgr = {

                init: function () {

                    var self = this;

                    if ($('.lvca-tabs').length === 0) return; // no tabs here

                    /* Triggered when someone pastes a URL with #tab-link into browser address bar and there is a browser refresh. */
                    self.initHash();

                    /* Triggered when an internal link is clicked which points to a tab - eg. a primary menu item which links to a tab */
                    self.initAnchor();

                    /*
                    Triggered when someone pastes a URL with #tab-link into browser address bar and there is NO browser refresh.
                    Only the hash part of the URL changed and hence browser window was not refreshed.
                    */
                    $(window).on("hashchange.lvca.tabs", function () {
                        self.initHash();
                    });

                },

                initAnchor: function () {

                    var self = this;

                    $('a[href*="#"]').not('.lvca-tab-label').click(function (event) {

                        var hash = $(this).attr('href').split('#').pop();

                        if ('' !== hash) {

                            var $element = $('#' + hash);

                            if ($element.length > 0) {

                                if ($element.hasClass('lvca-tab-pane')) {

                                    // Do not allow the anchor to navigate to the tab - we will smooth scroll to the same
                                    event.preventDefault();

                                    self.displayTab($element);
                                }
                            }
                        }

                    });

                },

                initHash: function () {

                    var self = this;

                    var hash = window.location.hash.replace('#', '').split('/').shift();

                    if ('' !== hash) {

                        var $element = $('#' + hash);

                        if ($element.length > 0) {

                            if ($element.hasClass('lvca-tab-pane')) {

                                setTimeout(function () {

                                    self.displayTab($element);

                                }, 100);
                            }
                        }
                    }

                },

                displayTab: function ($tabPane) {

                    var index, offset, speed, $tabs, $mobileMenu;

                    offset = .2;

                    speed = 300;

                    $tabs = $tabPane.closest('.lvca-tabs');

                    $mobileMenu = $tabs.find('.lvca-tab-mobile-menu');

                    // opens the mobile menu
                    $mobileMenu.trigger('click');

                    index = $tabs.find('.lvca-tab-pane').index($tabPane);

                    var $tabNav = $tabs.find('.lvca-tab-nav > .lvca-tab').eq(index);

                    // closes the mobile menu after selecting the required tab
                    $tabNav.trigger('click');

                    $("html, body").animate({
                        scrollTop: Math.round($tabs.offset().top - $(window).height() * offset)
                    }, speed);
                }

            };

            var LVCA_Accordion_Mgr = {

                init: function () {

                    var self = this;

                    if ($('.lvca-accordion').length === 0) return; // no accordion here

                    /* Triggered when someone pastes a URL with #accordion-link into browser address bar and there is a browser refresh. */
                    self.initHash();

                    /* Triggered when an internal link is clicked which points to a accordion - eg. a primary menu item which links to an accordion */
                    self.initAnchor();

                    /*
                    Triggered when someone pastes a URL with #accordion-link into browser address bar and there is NO browser refresh.
                    Only the hash part of the URL changed and hence browser window was not refreshed.
                    */
                    jQuery(window).on("hashchange.lvca.accordion", function () {
                        self.initHash();
                    });

                },

                initAnchor: function () {

                    var self = this;

                    jQuery('a[href*="#"]').click(function (event) {

                        var hash = jQuery(this).attr('href').split('#').pop();

                        if ('' !== hash) {

                            var $element = jQuery('#' + hash);

                            if ($element.length > 0) {

                                if ($element.hasClass('lvca-panel')) {

                                    // Do not allow the anchor to navigate to the tab - we will smooth scroll to the same
                                    event.preventDefault();

                                    self.displayPanel($element);
                                }
                            }
                        }

                    });

                },

                initHash: function () {

                    var self = this;

                    var hash, $element;

                    hash = window.location.hash.replace('#', '').split('/').shift();

                    if ('' !== hash) {

                        $element = jQuery('#' + hash);

                        if ($element.length > 0) {

                            if ($element.hasClass('lvca-panel')) {

                                setTimeout(function () {

                                    self.displayPanel($element);

                                }, 100);
                            }
                        }
                    }

                },

                displayPanel: function ($panel) {

                    var self = this;

                    var offset, speed;

                    offset = .2;

                    speed = 300;

                    // Only trigger click if the panel is not already open. Do not close the same if already open
                    if (!$panel.hasClass('lvca-active')) {

                        var $panelLabel = $panel.find('.lvca-panel-title').eq(0);

                        $panelLabel.trigger('click');

                    }

                    // Delay the scrolling to enable click action to be complete ensuring all elements are in place
                    setTimeout(function () {

                        jQuery("html, body").animate({
                            scrollTop: $panel.offset().top - jQuery(window).height() * offset
                        }, speed);

                    }, 300);

                }

            };

            LVCA_Tabs_Mgr.init();

            LVCA_Accordion_Mgr.init();

        });

    }(jQuery));

}