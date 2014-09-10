;
(function ($, window, document, undefined) {

    /**
     * Flyout Widget
     */
    $.widget('nsm.buttonFlyout', {

        /**
         * Options
         */
        options: {
            submitOnChange: false,
            selectors: {
                actions: "select",
                submitTrigger: "button",
                buttonGroupFlyoutTrigger: ".ButtonGroup-flyoutTrigger",
                moreNavItem: ".NavItem"
            },
            templates: {
                buttonGroupContainer: '<div class="ButtonGroup">',
                buttonGroupFlyoutTrigger: '<span class="Button ButtonGroup-flyoutTrigger">More…</span>',
                moreNavContainer: "<div class='Nav--block'></div>",
                moreNavItem: "<div class='NavItem' />"
            },
            flyoutOptions: {
                closeOnClick: true
            }
        },

        /**
         * Create the widget
         *
         * @private
         */
        _create: function () {

            var options = $.extend(this.options, this.element.data('buttonFlyoutOptions')),
                flyout = $(options.templates.moreNavContainer).hide(),
                selectBox = this.element.find(options.selectors.actions).hide(),
                selectBoxOptions = selectBox.find('option'),
                submitTrigger = this.element.find(options.selectors.submitTrigger),
                buttonGroup = $(this.options.templates.buttonGroup),
                buttonGroupButton = this.element.find(options.selectors.submitTrigger),
                buttonGroupFlyoutTrigger = $(options.templates.buttonGroupFlyoutTrigger)
                ;

            buttonGroup.wrap(buttonGroupButton);
            buttonGroupFlyoutTrigger.insertAfter(buttonGroupButton);

            selectBoxOptions.each(function(){
                $(options.templates.moreNavItem)
                    .text(this.text)
                    .appendTo(flyout)
                ;
            });

            buttonGroupFlyoutTrigger.flyoutTrigger(
                $.extend(options.flyoutOptions, {
                        flyout: flyout
                    }
                ));

            flyout.on('click', this.options.selectors.moreNavItem, $.proxy(this, '_onFlyoutClick'));

            this._on(selectBox, {
                'change': '_onSelectBoxChange'
            });

            this.element.append(buttonGroup, flyout);

            this.options = options;
            this.selectBox = selectBox;
            this.selectBoxOptions = selectBoxOptions;
            this.buttonGroupButton = buttonGroupButton;
            this.submitTrigger = submitTrigger;

            this.selectBox.trigger('change');
        },

        /**
         * Handle Flyout Click
         *
         * @param event
         * @private
         */
        _onFlyoutClick: function (event) {
            var index = $(event.target).index(this.flyout);
            this.selectBox.prop('selectedIndex', index).trigger('change');
            if(this.options.submitOnChange) {
                this.submitTrigger.trigger('click');
            }
        },

        /**
         * Handle Window Click
         *
         * @param event
         * @private
         */
        _onSelectBoxChange: function (event) {
            var $el = $(event.target);

            this.buttonGroupButton.html($el.find(':selected').text());
        }

    });

})(jQuery, window, document);
