;
(function ($, window, document, undefined) {

    /**
     * Flyout Widget
     */
    $.widget('nsm.flyoutTrigger', {

        /**
         * Options
         */
        options: {
            hideOnClick: true,
            yAlignmentPriority: ['below', 'above'],
            xAlignmentPriority: ['left', 'center', 'right'],
            showTransition: 'fadeIn',
            hideTransition: 'fadeOut',
            showTransitionDuration: 0,
            hideTransitionDuration: 100,
            classes: {
                flyoutTriggerActive: 'is-active',
                flyoutVisible: 'is-visible',
                flyoutHidden: 'is-hidden'
            }
        },

        /**
         * Widget Events
         */
        _widgetEvents: {
            'click': '_onWidgetClick'
        },

        /**
         * Flyout Events
         */
        _flyoutEvents: {
            'click': '_onFlyoutClick'
        },

        /**
         * Window Events
         */
        _windowEvents: {
            'click': '_onWindowClick',
            'resize': '_onWindowResize'
        },

        /**
         * Create the widget
         *
         * @private
         */
        _create: function () {
            this.options = $.extend(this.options, this.element.data('flyoutTriggerOptions'));
            this.flyout = $(this.options.flyout).appendTo('body').css('position', 'absolute');

            this.flyoutVisible = false;

            this._on(this._widgetEvents);
            this._on(this.flyout, this._flyoutEvents);

            this.reposition();
        },

        /**
         * Handle Widget Click
         *
         * @param event
         * @private
         */
        _onWidgetClick: function (event) {
            this._toggle(event);
        },

        /**
         * Handle Flyout Click
         *
         * @param event
         * @private
         */
        _onFlyoutClick: function (event) {
            if(this.options.hideOnClick) {
                this.hide();
            }
        },

        /**
         * Handle Window Click
         *
         * @param event
         * @private
         */
        _onWindowClick: function (event) {

            if (
                this.flyoutVisible
                && 0 == $(event.target).closest(this.element).length
                && 0 == $(event.target).closest(this.flyout).length
            ) {
                this.hide();
            }

        },

        /**
         * Handle window Resize
         *
         * @private
         */
        _onWindowResize: function () {
            this.reposition();
        },

        /**
         * Repaint - Reposition the flyout
         */
        reposition: function () {

            if (false === this.flyoutVisible) {
                return;
            }

            var $window = $(window),

                viewportWidth = $window.width(),
                viewportHeight = $window.height(),
                viewportLeft = $window.scrollLeft(),
                viewportTop = $window.scrollTop(),
                viewportRight = viewportLeft + viewportWidth,
                viewportBottom = viewportTop + viewportHeight,

                flyoutTriggerWidth = this.element.outerWidth(),
                flyoutTriggerHeight = this.element.outerHeight(),
                flyoutTriggerOffset = this.element.offset(),
                flyoutTriggerLeft = flyoutTriggerOffset.left,
                flyoutTriggerTop = flyoutTriggerOffset.top,
                flyoutTriggerRight = flyoutTriggerLeft + flyoutTriggerWidth,
                flyoutTriggerBottom = flyoutTriggerTop + flyoutTriggerHeight,

                flyoutTriggerDistanceFromViewportLeft = flyoutTriggerLeft - viewportLeft,
                flyoutTriggerDistanceFromViewportTop = flyoutTriggerTop - viewportTop,
                flyoutTriggerDistanceFromViewportRight = viewportRight - flyoutTriggerRight,
                flyoutTriggerDistanceFromViewportBottom = viewportBottom - flyoutTriggerBottom,

                flyoutWidth = this.flyout.outerWidth(),
                flyoutHeight = this.flyout.outerHeight(),

                flyoutWidthOverhang = flyoutWidth - flyoutTriggerWidth,
                flyoutWidthOverhangHalf = flyoutWidthOverhang / 2,

                flyoutTests = {
                    canFitBelow: flyoutHeight < flyoutTriggerDistanceFromViewportBottom,
                    canFitAbove: flyoutHeight < flyoutTriggerDistanceFromViewportTop,
                    canFitAlignedLeft: flyoutWidth < (flyoutTriggerWidth + flyoutTriggerDistanceFromViewportRight),
                    canFitAlignedRight: flyoutWidth < (flyoutTriggerWidth + flyoutTriggerDistanceFromViewportLeft),
                    canFitAlignedCenter: (
                        flyoutWidthOverhangHalf < flyoutTriggerDistanceFromViewportLeft &&
                        flyoutWidthOverhangHalf < flyoutTriggerDistanceFromViewportRight
                    )
                },

                flyoutPositions = {
                    belowLeft: {
                        top: flyoutTriggerBottom,
                        left: flyoutTriggerLeft
                    },
                    belowCenter: {
                        top: flyoutTriggerBottom,
                        left: flyoutTriggerLeft - flyoutWidthOverhangHalf
                    },
                    belowRight: {
                        top: flyoutTriggerBottom,
                        left: flyoutTriggerRight - flyoutWidth
                    },
                    aboveLeft: {
                        top: flyoutTriggerTop - flyoutHeight,
                        left: flyoutTriggerLeft
                    },
                    aboveCenter: {
                        top: flyoutTriggerTop - flyoutHeight,
                        left: flyoutTriggerLeft - flyoutWidthOverhangHalf
                    },
                    aboveRight: {
                        top: flyoutTriggerTop - flyoutHeight,
                        left: flyoutTriggerRight - flyoutWidth
                    }
                },

                flyoutIsPositioned = false,
                yTest = '',
                xTest = '',
                positionString = '',
                yAlignment = this.options.yAlignmentPriority,
                xAlignment = this.options.xAlignmentPriority;

            yAlignmentLoop:
                for (var i = 0; i < yAlignment.length; i++) {

                    for (var j = 0; j < xAlignment.length; j++) {

                        yTest = 'canFit' + this._capitaliseFirstLetter(yAlignment[i]);
                        xTest = 'canFitAligned' + this._capitaliseFirstLetter(xAlignment[j]);

                        // console.log(yTest, xTest)

                        if (
                            true === flyoutTests[yTest] &&
                            true === flyoutTests[xTest]
                        ) {

                            positionString = yAlignment[i] + this._capitaliseFirstLetter(xAlignment[j]);
                            this.flyout.css(flyoutPositions[positionString]);
                            flyoutIsPositioned = true;
                            // console.log(positionString);
                            // console.log(flyoutTriggerOffsetFromTop, flyoutTriggerOffsetFromBottom);
                            break yAlignmentLoop;
                        }
                    }
                    ;
                }
            ;

            if (false === flyoutIsPositioned) {
                this.flyout.css(flyoutPositions[yAlignment[0] + this._capitaliseFirstLetter(xAlignment[0])]);
            }
        },

        /**
         * Toggle the flyout
         *
         * @param event
         * @private
         */
        _toggle: function (event) {
            this[ this.flyoutVisible ? "hide" : "show" ](event);
        },

        /**
         * Show the flyout
         *
         * @param event
         */
        show: function (event) {
            this._on($(window), this._windowEvents);
            this.flyout.velocity("stop").velocity(this.options.showTransition, this.options.showTransitionDuration);
            this.flyoutVisible = true;
            this.reposition();
            this._trigger("show", event);
        },

        /**
         * Hide the flyout
         *
         * @param event
         */
        hide: function (event) {
            this._off($(window));
            this.flyout.velocity("stop").velocity(this.options.hideTransition, this.options.hideTransitionDuration);
            this.flyoutVisible = false;
            this._trigger("hide", event);
        },

        /**
         * Capitalise first letter helper
         * @param string
         * @returns {string}
         * @private
         */
        _capitaliseFirstLetter: function (string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

    });

})(jQuery, window, document);
