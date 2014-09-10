;
(function($, _, window, document, undefined) {

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

            templates: {
                option: '<div>#<%- id %> - <%- title %></div>',
                item: '<div>#<%- id %> - <%- title %></div>'
            },

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
        _create: function() {

            var widget = this,
                selectedValues = this.element.val();

            widget.options = $.extend(widget.options, widget.element.data('entitySearchOptions'));

            /**
             * Compile and cache the templates
             */
            this.optionTemplate = _.template(this.options.templates.option);
            this.itemTemplate = _.template(this.options.templates.item);

            /**
             * Load selected option data onload
             */
            widget.options.selectizeOptions.onInitialize = function() {

                var selectize = this,
                    queryParams = {},
                    jqXHR;

                queryParams[widget.options.searchIdParam] = selectedValues;

                jqXHR = widget._queryRemote(queryParams);

                jqXHR.done(function(data, textStatus, jqXHR) {

                    if (data.total === 0) {
                        return;
                    }

                    // Loop over all the returned values
                    // and set the selected values on selectize
                    $.each(data._embedded.items, function(k, v) {

                        selectize.addOption(v);

                        // Check if the selected values contains the id
                        // Convert all ids to string given selected values are always strings
                        if ($.inArray(v.id.toString(), selectedValues) !== -1) {
                            selectize.addItem(v.id);
                        }
                    });

                    selectize.refreshItems();
                });
            };

            /**
             * Load more options based on the search query param
             * @param query
             * @param callback
             * @returns {*}
             */
            widget.options.selectizeOptions.load = function(query, callback) {

                var queryParams = {},
                    jqXHR;

                queryParams[this.options.searchQueryParam] = encodeURIComponent(query);

                jqXHR = widget._queryRemote(queryParams);

                jqXHR.done(function(data, textStatus, jqXHR) {
                    if (data.total === 0) {
                        return callback();
                    }
                    callback(data._embedded.items);
                });

                jqXHR.fail(function(jqXHR, textStatus, errorThrown) {
                    callback();
                });

                return callback();
            };

            /**
             * Set renderer methods
             */
            widget.options.selectizeOptions.render = {
                option: function(data, escape) {
                    return widget.optionTemplate(data);
                },
                item: function(data, escape) {
                    return widget.itemTemplate(data);
                }
            };

            /**
             * Initialise selectize
             */
            this.selectize = this.element.selectize(this.options.selectizeOptions);
        },

        /**
         * Query Remote Source
         *
         * @param queryParams
         * @returns {*}
         * @private
         */
        _queryRemote: function(queryParams) {

            var jqXhr;

            jqXhr = $.ajax({
                url: this.options.endpointIndex,
                dataType: 'json',
                data: queryParams
            });

            return jqXhr;
        }

    });

})
(jQuery, _, window, document);
