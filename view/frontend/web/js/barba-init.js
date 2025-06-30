define([
    'jquery',
    'barbajs',
    'Magento_Customer/js/customer-data',
    'mage/cookies',
    'mage/apply/main'
], function($, barba, customerData, cookies, mage) {
    'use strict';

    return {
        config: {
            excludedPages: ['/checkout', '/cart', '/customer/account'],
            debug: false
        },

        init: function () {
            this.preventOnExcludedPages();

            if (barba.prefetch) {
                barba.use(barba.prefetch);
            }

            barba.init({
                debug: this.config.debug,
                transitions: [{
                    name: 'default-transition',
                    leave: (data) => {
                        return $(data.current.container).animate({opacity: 0}, 250).promise();
                    },
                    enter: (data) => {
                        window.scrollTo(0, 0);
                        $(data.next.container).css({opacity: 0, visibility: 'hidden'});
                    },
                    after: (data) => {
                        this.updatePageSpecifics(data, mage);
                        $(data.next.container).css('visibility', 'visible').animate({opacity: 1}, 250);
                    }
                }]
            });

            $(document).on('contentUpdated', () => this.preventOnExcludedPages());
        },

        updatePageSpecifics: function (data, mage) {
            this.updateBodyClass(data);
            this.updateFormKeys();
            this.reloadPrivateContent();
            this.triggerMagentoInit(data.next.container, mage);
        },

        updateBodyClass: function (data) {
            const bodyClasses = data.next.html.match(/<body[^>]*class="([^"]*)"/);
            if (bodyClasses && bodyClasses[1]) {
                document.body.className = bodyClasses[1];
            }
        },

        updateFormKeys: function () {
            const newFormKey = $.mage.cookies.get('form_key');
            if (newFormKey) {
                $('input[name="form_key"]').val(newFormKey);
            }
        },

        reloadPrivateContent: function () {
            customerData.reload(['cart', 'customer'], false);
        },

        triggerMagentoInit: function (container, mage) {
            $(container).trigger('contentUpdated');
            if (mage && typeof mage.apply === 'function') {
                mage.apply(container);
            }
        },

        preventOnExcludedPages: function () {
            this.config.excludedPages.forEach(path => {
                const baseUrl = window.BASE_URL || '/';
                $(`a[href*="${baseUrl.replace(/\/$/, '')}${path}"]`).attr('data-barba-prevent', 'all');
            });
        }
    };
});
