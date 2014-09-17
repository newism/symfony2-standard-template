;
(function ($, window, document, undefined) {

    /**
     * Collection Widget
     */
    $.widget('nsm.collection', {

        /**
         * Options
         */
        options: {
            id: false,
            name: false,
            fullName: false,
            allowAdd: false,
            prototypeName: '__name__',
            allowDelete: false,
            allowSort: false,
            sortControl: 'order',
            classes: {
                addTrigger: 'js-add',
                deleteTrigger: 'js-delete',
                sortHandle: 'js-sortHandle',
                sortPlaceholder: 'js-sortPlaceholder',
                sortHelper: 'js-sortHelper'
            },

            selectors: {
                collectionHeader: '> .Collection-header',
                collectionBody: '> .Collection-body',
                collectionFooter: '> .Collection-footer',
                sortControl: false
            },

            templates: {
                addTrigger: '<button class="Button">Add</button>',
                prototype: false,

                deleteHeader: '<th class="Collection-headerRowItem">',
                deleteFooter: '<td />',
                deleteTrigger: '<td class="ControlGroup ControlGroup--singleControl TableCell--icon">' +
                                '<button class="Button">' +
                                '<svg class="Button-icon" viewBox="0 0 24 24"><g id="delete"><path d="M6,19c0,1.1,0.9,2,2,2h8c1.1,0,2-0.9,2-2V7H6V19z M19,4h-3.5l-1-1h-5l-1,1H5v2h14V4z"></path></g></svg>' +
                                '</button>' +
                                '</td>',

                sortHeader: '<th/>',
                sortFooter: '<td/>',
                sortHandle: '<span class="Button">' +
                            '<svg class="Button-icon" viewBox="0 0 24 24" style="pointer-events: none; width: 24px; height: 24px; display: block;"><g id="swap-vert"><path d="M16,17v-7h-2v7h-3l4,4l4-4H16z M9,3L5,7h3v7h2V7h3L9,3z"></path></g></svg>' +
                            '</span>',
                sortHelper: '<tr/>',
                sortPlaceHolder: '<div />'
            },

            sortableOptions: {
                cursor: 'move',
                axis: 'y',
                tolerance: 'pointer',
                forceHelperSize: true,
                forcePlaceholderSize: true
            }
        },

        /**
         * Create the widget
         *
         * @private
         */
        _create: function () {

            var deleteTrigger,
                sortHandle,
                deleteTriggerEvents = {},
                placeHolderClass;

            this.options = $.extend(this.options, this.element.data('collectionOptions'));

            // Allow some settings to be set via data attributes
            this.options.id = this.element.data('collectionId') || this.options.id;
            this.options.name = this.element.data('collectionName') || this.options.name;
            this.options.fullName = this.element.data('collectionFullName') || this.options.fullName;
            this.options.allowAdd = this.element.data('collectionAllowAdd') || this.options.allowAdd;
            this.options.prototypeName = this.element.data('collectionPrototypeName') || this.options.prototypeName;
            this.options.allowDelete = this.element.data('collectionAllowDelete') || this.options.allowDelete;
            this.options.allowSort = this.element.data('collectionAllowSort') || this.options.allowSort;
            this.options.sortControl = this.element.data('collectionSortControl') || this.options.sortControl;
            this.options.templates.prototype = this.element.data('collectionPrototype') || this.options.templates.prototype;
            this.collectionHeader = this.element.find(this.options.selectors.collectionHeader);
            this.collectionBody = this.element.find(this.options.selectors.collectionBody);
            this.collectionItems = this.collectionBody.children();
            this.collectionItemIndex = this.collectionItems.length;
            this.collectionFooter = this.element.find(this.options.selectors.collectionFooter);

            this.sortControlRegex = new RegExp(this._escapeRegExp(this.options.fullName) + "\\[\\d+\\]\\[" + this.options.sortControl + "\\]", "g");
            this.prototypeNameRegx = new RegExp(this.options.prototypeName + "(label__)?", "g");

            /**
             * Allow Add
             */
            if (this.options.allowAdd) {
                this._createAddCollectionItemTrigger();
            }

            /**
             * Allow Delete
             */
            if (this.options.allowAdd || this.options.allowDelete) {
                this.collectionHeader.children().append(this.options.templates.deleteHeader);
                this.collectionFooter.children().append(this.options.templates.deleteFooter);
                deleteTrigger = this.deleteTrigger = $(this.options.templates.deleteTrigger).addClass(this.options.classes.deleteTrigger);
                this.collectionItems.each(function () {
                    $(this).append(deleteTrigger.clone());
                });
                deleteTriggerEvents['click .' + this.options.classes.deleteTrigger] = '_onDeleteCollectionItemTriggerClick';
                this._on(deleteTriggerEvents);
            }

            /**
             * Allow Sort
             */
            if (this.options.allowSort) {

                sortHandle = $(this.options.templates.sortHandle).addClass(this.options.classes.sortHandle);
                sortHelper = this.options.classes.sortHelper;

                this.sortHandle = sortHandle;
                this.sortHeader = this.collectionHeader.find("#" + this.options.id + "_" + this.options.sortControl + "_header");
                this.sortHeader.children().hide();

//                this.collectionHeader.children().prepend(this.options.templates.sortHeader);
//                this.collectionFooter.children().prepend(this.options.templates.sortFooter);

                this.collectionItems.find('.ControlGroup--' + this.options.sortControl).each(function () {
                    $(this).children().hide();
                    $(this).prepend(sortHandle.clone());
                });

                this.options.sortableOptions = $.extend(this.options.sortableOptions, {
                    items: this.options.selectors.collectionBody + " > *",
                    handle: '.' + this.options.classes.sortHandle,
                    forcePlaceholderSize: true,
                    appendTo: this.element.closest("div"),
                    helper: function () {
                        return $("<div />").addClass('js-sortHelper');
                    }
                });

                this.element.sortable(this.options.sortableOptions);

                this.element.on('sortstart', $.proxy(this._onSortStart, this));
                this.element.on('sortstop', $.proxy(this._onSortStop, this));

                this.refresh();
            }
        },

        /**
         * Create the add collection item trigger and insert it after the collection
         * @private
         */
        _createAddCollectionItemTrigger: function () {
            this.addCollectionItemTrigger = $(this.options.templates.addTrigger).addClass(this.options.classes.addTrigger);
            this.addCollectionItemTrigger.insertAfter(this.element.parent());
            this._on(this.addCollectionItemTrigger, {
                click: '_onAddCollectionItemTriggerClick'
            });
        },

        /**
         * Handle the click event of the add collection item trigger
         * @param event
         * @private
         */
        _onAddCollectionItemTriggerClick: function (event) {
            event.preventDefault();
            this.addCollectionItem();
        },

        /**
         * Add a collection item.
         */
        addCollectionItem: function () {

            var newPrototypeString = this.options.templates.prototype.replace(this.prototypeNameRegx, this.collectionItemIndex),
                $newPrototype = $(newPrototypeString),
                $sortHandle = this.sortHandle;

            if (this.options.allowAdd || this.options.allowDelete) {
                $newPrototype.append(this.deleteTrigger.clone());
            }

            if (this.options.allowSort) {
                $newPrototype.find('.ControlGroup--' + this.options.sortControl).each(function () {
                    $(this).children().hide();
                    $(this).prepend($sortHandle.clone());
                });
            }

            $newPrototype.appendTo(this.collectionBody).trigger('contentcreated');

            this.collectionItemIndex++;

            this.refresh();
        },

        /**
         * Handle delete event
         * @param event
         * @private
         */
        _onDeleteCollectionItemTriggerClick: function (event) {
            event.preventDefault();
            var $item = $(event.currentTarget).parent();
            this.deleteCollectionItem($item);
        },

        /**
         * Delete collection item
         *
         * @param item
         */
        deleteCollectionItem: function (item) {
            $(item).remove();
            this.refresh();
        },

        /**
         * On Sort Start
         *
         * @param event
         * @param ui
         * @private
         */
        _onSortStart: function (event, ui) {
            var $item = $(ui.item[0]),
                colCount = 0,
                colspan;

            if ($item.is('tr')) {
                $item.children().each(function () {
                    colCount += this.colSpan;
                });
                ui.placeholder.html("<td colspan='" + colCount + "'></td>").addClass('js-sortPlaceholder');
            }
        },

        /**
         * On Sort Stop
         *
         * @param event
         * @param ui
         * @private
         */
        _onSortStop: function (event, ui) {
            this.refreshSortOrder();
        },

        /**
         * Refresh the collection
         */
        refresh: function () {
            this.collectionItems = this.collectionBody.children();
            if (this.options.allowSort) {
                this.refreshSortOrder();
                this.element.sortable("refresh");
            }
            this.element.toggle(!!this.collectionItems.length);
        },

        /**
         * Refresh the sort order inputs
         */
        refreshSortOrder: function () {

            if (this.options.allowSort) {

                var index = 1,
                    sortControlRegex = this.sortControlRegex;

                $(this.collectionBody).find("input[name$='\[" + this.options.sortControl + "\]']").each(function (i) {
                    if (this.name.match(sortControlRegex)) {
                        $(this).val(index);
                        index++;
                    }
                });
            }

        },

        /**
         * Escape a regular expression string
         *
         * @param str
         * @returns {*}
         * @private
         */
        _escapeRegExp: function (str) {
            return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
        }

    });

})
(jQuery, window, document);
