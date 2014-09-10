(function ($, window) {

    /**
     * Overflow Nav Widget
     */
    $.widget("nsm.overflowNav", {

        options: {
            flyoutOptions: {},
            selectors: {
                "navItem": "> *",
                "navItemCurrent": "> .current"
            },
            templates: {
                moreNavTrigger: '<span>More</span>',
                moreNavContainer: "<div class='Nav--block' />"
            },
            spacingBuffer: 50,
            throttleDuration: 100
        },

        _windowEvents: {
            "resize": "_onWindowResize"
        },

        /**
         *
         * @private
         */
        _create: function () {
            this.isThrottled = false;

            this.options = $.extend(this.options, this.element.data('resizeNavOptions'));

            this.moreNav = $(this.options.templates.moreNavContainer).hide();
            this.moreNav.insertAfter(this.element);
            this.moreNavTrigger = $(this.options.templates.moreNavTrigger).appendTo(this.element).flyoutTrigger(
                $.extend(this.options.flyoutOptions, {
                    flyout: this.moreNav
                }
            ));

            this._on(window, this._windowEvents);
            this.refresh();
        },

        /**
         * On window resize
         * @private
         */
        _onWindowResize: function () {
            if (this.isThrottled) {
                return;
            }
            this.isThrottled = true;
            setTimeout($.proxy(function () {
                this.isThrottled = false;
            }, this), this.options.throttleDuration);
            this.repaint();
        },

        /**
         * Refresh the nav items
         * @private
         */
        refresh: function () {
            this.currentItem = this.element.find(this.options.selectors.navItemCurrent);
            this.navItems = this.element.find(this.options.selectors.navItem).not(this.moreNavTrigger).not(this.currentItem);
            this.moreNavItems = this.navItems.clone();
            this.moreNav.append(this.moreNavItems);
            this.repaint();
        },

        /**
         * Resize the nav
         */
        repaint: function () {
            var navWidth = this.element.width(),
                currentItemWidth = this.currentItem.outerWidth(true),
                moreNavTriggerWidth = this.moreNavTrigger.outerWidth(true),
                availableWidth = navWidth - currentItemWidth - moreNavTriggerWidth - this.options.spacingBuffer,
                hiddenItems = false,
                navItem,
                navItemWidth,
                moreNavItem
                ;

            for (var i = 0; i < this.navItems.length; i++) {

                navItem = this.navItems.eq(i);

                if (navItem.is(this.currentItem)) {
                    continue;
                }

                navItemWidth = navItem.outerWidth();
                availableWidth -= navItemWidth;

                moreNavItem = this.moreNavItems.eq(i);

                if (availableWidth > 0) {
                    navItem.show();
                    moreNavItem.hide();
                } else {
                    navItem.hide();
                    moreNavItem.show();
                    this.moreNavTrigger.show();
                    hiddenItems = true;
                }
            }

            if (!hiddenItems) {
                this.moreNavTrigger.hide();
            }
        }

    });
})(jQuery, window, document);
