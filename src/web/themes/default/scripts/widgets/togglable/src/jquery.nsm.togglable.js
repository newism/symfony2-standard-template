;
(function ($, window, document, undefined) {

    /**
     * Flyout Widget
     */
    $.widget('nsm.togglable', {

        /**
         * Options
         */
        options: {
            active: true,
            activateTransition: 'fadeIn',
            activateTransitionDuration: 200,
            deactivateTransition: 'fadeOut',
            deactivateTransitionDuration: 0,
            trigger: false
        },

        _documentEvents: {},

        /**
         * Create the widget
         *
         * @private
         */
        _create: function () {
            this.options = $.extend(this.options, this.element.data('togglableOptions'));

            this.triggers = $(this.options.trigger);

            this._documentEvents['click ' + this.options.trigger] = "_onTriggerClick";
            this._on(document, this._documentEvents);
        },

        /**
         * Initalize the widget and force an activation or deactivation
         * @private
         */
        _init: function(){
            this.options.active ? this.activate() : this.deactivate();
        },

        /**
         * Handle Trigger Click
         *
         * @param event
         * @private
         */
        _onTriggerClick: function (event) {
            this._toggle(event);
        },

        /**
         * Toggle the flyout
         *
         * @param event
         * @private
         */
        _toggle: function (event) {
            this[ this.options.active ? "deactivate" : "activate" ]();
        },

        /**
         * Show the flyout
         */
        activate: function () {
            this.options.active = true;
            this.triggers.addClass('is-active');
            this.element.velocity("stop").velocity(this.options.activateTransition, this.options.activateTransitionDuration);
        },

        /**
         * Hide the flyout
         */
        deactivate: function () {
            this.options.active = false;
            this.triggers.removeClass('is-active');
            this.element.velocity("stop").velocity(this.options.deactivateTransition, this.options.deactivateTransitionDuration);
        }
    });

})(jQuery, window, document);
