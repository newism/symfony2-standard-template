;
(function ($, _, window, document, history, undefined) {

    if(window.history === 'undefined') {
        window.history = $.noop;
    }

    /**
     * DatGrid Widget
     */
    $.widget('nsm.dataGrid', {

        /**
         * Options
         */
        options: {
            classes: {
                loading: 'is-loading'
            },
            selectors: {
                links: '.Pagination a',
                searchForm: 'form',
                results: '.DataGrid-results'
            }
        },

        /**
         * Create the widget
         *
         * @private
         */
        _create: function () {

            var widget = this,
                eventHandlers;

            widget.options = $.extend(widget.options, widget.element.data('dataGridOptions'));

            this.resultsEl = this.element.find(this.options.selectors.results);

            eventHandlers = {};
            eventHandlers['click ' + this.options.selectors.links] = '_handleFilterLink';
            eventHandlers['submit ' + this.options.selectors.searchForm] = '_handleFilterForm';

            this._on(eventHandlers);
        },

        /**
         * Handle filter link click
         *
         * @param event
         * @private
         */
        _handleFilterLink: function (event) {

            event.preventDefault();

            var $link = $(event.currentTarget),
                url = $link.data('ajaxHref') || $link.prop('href');

            $.ajax({
                type: 'get',
                url: url,
                beforeSend: $.proxy(this._beforeSend, this),
                error: $.proxy(this._error, this),
                success: $.proxy(this._success, this),
                complete: $.proxy(this._complete, this)
            });

            window.history.pushState(null, event.target.textContent, url);
        },

        /**
         * Handle filter form submit
         *
         * @param event
         * @private
         */
        _handleFilterForm: function (event) {

            event.preventDefault();

            var $form = $(event.currentTarget),
                formData = $form.serializeArray();

            $.ajax({
                type: 'get',
                url: $form.data('ajaxAction') || $form.prop('action'),
                data: formData,
                beforeSend: $.proxy(this._beforeSend, this),
                error: $.proxy(this._error, this),
                success: $.proxy(this._success, this),
                complete: $.proxy(this._complete, this)
            });
        },

        /**
         * Fired before send
         *
         * @private
         */
        _beforeSend: function () {
            this.resultsEl.addClass(this.options.classes.loading);
            this.element.velocity("scroll", {offset: -50});
        },

        /**
         * Fired on Error
         *
         * @private
         */
        _error: function () {
        },

        /**
         * Fired on success
         *
         * @param data
         * @param status
         * @param xhr
         * @private
         */
        _success: function (data, status, xhr) {
            data = $.trim(data);
            var newResults = $(data).find(this.options.selectors.results).html();
            this.resultsEl.html(newResults);
            this.resultsEl.trigger('contentcreated');
        },

        /**
         * Fired on complete
         * @private
         */
        _complete: function () {
            this.resultsEl.removeClass(this.options.classes.loading);
        }

    });

})
(jQuery, _, window, document);
