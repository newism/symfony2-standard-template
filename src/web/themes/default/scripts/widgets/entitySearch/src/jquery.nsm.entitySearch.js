;
(function ($, _, window, document, undefined) {

    /**
     * Entity Search Widget
     */
    $.widget('nsm.entitySearch', {

        /**
         * Options
         */
        options: {

            endpointIndex: null,
            endpointDetails: null,
            searchQueryParam: 'titleLike',
            searchIdParam: 'id',
            selectedOptions: false,
            templates: {},
            selectizeOptions: {
                valueField: 'id',
                titleField: 'title',
                searchField: 'title',
                sortField: 'title',
                options: []
            }
        },

        /**
         * Create the widget
         *
         * @private
         */
        _create: function () {

            var widget = this,
                selectedValues,
                templateNames,
                templateName,
                templateString
                ;

            widget.options = $.extend(widget.options, widget.element.data('entitySearchOptions'));

            /**
             * Load more options based on the search query param
             * @param query
             * @param callback
             * @returns {*}
             * @see https://github.com/brianreavis/selectize.js/blob/master/docs/usage.md#callbacks
             */
            widget.options.selectizeOptions.load = function (query, callback) {

                var queryParams = {},
                    jqXHR;

                queryParams[widget.options.searchQueryParam] = encodeURIComponent(query);

                jqXHR = widget._loadOptions(queryParams);

                jqXHR.done(function (data, textStatus, jqXHR) {
                    if (data.total === 0) {
                        return callback();
                    }
                    callback(data._embedded.items);
                });

                jqXHR.fail(function (jqXHR, textStatus, errorThrown) {
                    callback();
                });

                return callback();
            };

            /**
             * Set renderer methods for selectize
             * @see See https://github.com/brianreavis/selectize.js/blob/master/docs/usage.md#rendering
             */
            widget.options.selectizeOptions.render = {};
            widget.templates = {};
            // Loop over each of the template settings
            templateNames = ['optgroup', 'optgroup_header', 'option', 'item', 'option_create'];
            for (var i = 0, len = templateNames.length; i < len; i++) {
                templateName = templateNames[i];
                // Grab the template string string
                templateString = widget.options.templates[templateName] || false;
                if(templateString) {
                    // Set the compiled template as the render
                    // The template will recieve data as a parameter
                    widget.options.selectizeOptions.render[templateName] = _.template(templateString);
                }
            }

            /**
             * Initialise selectize
             */
            this.element.selectize(this.options.selectizeOptions);
            widget.selectize = this.element[0].selectize;

            // Get the selected values and add items to selectize, then refresh
            // At this stage we're assuming the data is already been set using
            // the "options" selectize option.
            // Previously we we're calling the _loadOptions method to load the selectedValues
            // data directly from the server.
            selectedValues = widget.options.selectedOptions || this.element.val();
            widget.selectize.addItems(selectedValues);
            widget.selectize.refreshItems();
        },

        /**
         * Load options and return a promise or true
         *
         * @param queryParams
         * @returns {*}
         * @private
         */
        _loadOptions: function (queryParams) {

            var widget = this,
                promise = true;

            if(!this.options.endpointIndex) {
                return promise;
            }

            promise = $.ajax({
                url: this.options.endpointIndex,
                dataType: 'json',
                data: queryParams
            });

            promise.done(function (data) {

                if (data.total === 0) {
                    return;
                }

                // Loop over all the returned values
                // and set the selected values on selectize
                for (var i = 0, len = data._embedded.items.length; i < len; i++) {
                    widget.selectize.addOption(data._embedded.items[i]);
                }
            });

            return promise;
        }

    });

})
(jQuery, _, window, document);
