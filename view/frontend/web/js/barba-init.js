/**
 * Copyright © Ronangr1. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'barbajs',
    'Magento_Customer/js/customer-data',
    'mage/cookies',
    'uiRegistry',
    'mage/mage'
], function ($, barba, customerData, cookies, registry) {
    'use strict';

    return {
        config: {
            excludedPages: ['/checkout', '/cart', '/customer/account'],
            debug: true
        },

        init: function () {
            if (this.isPageExcluded(window.location.pathname)) {
                if (this.config.debug) console.log('Barba.js: Initialisation prevented on excluded page.');
                return;
            }
            this.preventOnExcludedLinks();

            if (barba.prefetch) {
                barba.use(barba.prefetch);
            }

            barba.init({
                debug: this.config.debug,
                transitions: [{
                    name: 'default-transition',
                    leave: (data) => {
                        this.destroyMagentoComponents(data.current.container);
                        return $(data.current.container).animate({opacity: 0}, 250).promise();
                    },
                    enter: (data) => {
                        window.scrollTo(0, 0);
                        $(data.next.container).css({opacity: 0, visibility: 'hidden'});
                    },
                    after: (data) => {
                        this.updatePageSpecifics(data);
                        $(data.next.container).css('visibility', 'visible').animate({opacity: 1}, 250);
                    }
                }]
            });

            $(document).on('contentUpdated', () => this.preventOnExcludedLinks());
        },

        updatePageSpecifics: function (data) {
            this.updateHead(data);
            this.updateBodyClass(data);
            this.updateFormKeys();
            this.reloadPrivateContent();
        },

        updateHead: function (data) {
            const newPageRawHead = data.next.html.match(/<head[^>]*>([\s\S]*)<\/head>/i);
            if (!newPageRawHead || !newPageRawHead[0]) return;

            const newPageHead = document.createElement('head');
            newPageHead.innerHTML = newPageRawHead[0];

            document.title = newPageHead.querySelector('title')?.innerText || '';
        },

        destroyMagentoComponents: function (container) {
            registry.get((component) => {
                if (component.elems && component.elems().length > 0) {
                    if ($.contains(container, component.elems()[0])) {
                        component.destroy();
                    }
                }
            });
        },

        isPageExcluded(path) {
            return this.config.excludedPages.some(excludedPath => path.includes(excludedPath));
        },

        preventOnExcludedLinks: function () {
            this.config.excludedPages.forEach(path => {
                $(`a[href*="${path}"]`).attr('data-barba-prevent', 'all');
            });
        },

        updateBodyClass: function (data) {
            const bodyClasses = data.next.html.match(/<body[^>]*class="([^"]*)"/);
            document.body.className = (bodyClasses && bodyClasses[1]) ? bodyClasses[1] : '';
        },

        updateFormKeys: function () {
            const newFormKey = $.mage.cookies.get('form_key');
            if (newFormKey) {
                $('input[name="form_key"]').val(newFormKey);
            }
        },

        reloadPrivateContent: function () {
            customerData.reload(['cart', 'customer'], false);
        }
    };
});
