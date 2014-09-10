;
(function ($) {

    /**
     * NSM Multi Checkboxes
     */
    $.widget("nsm.multiCheckboxes", {
        /**
         * Options
         */
        options: {
            selectors: {
                masters: 'thead :checkbox',
                slaves: 'tbody :checkbox',
                parentContainer: 'tr'
            },
            dom: {
                masters: {},
                slaves: {}
            },
            classes: {
                parentContainerSelected: 'is-selected'
            }
        },

        /**
         * Fired on create (once only)
         * @private
         */
        _create: function () {

            this.options = $.extend(this.options, this.element.data('multiCheckbox-options'));

            var eventHandlers = {};
            eventHandlers['change ' + this.options.selectors.masters] = '_onMasterChange';
            eventHandlers['change ' + this.options.selectors.slaves] = '_onSlaveChange';
            eventHandlers['contentcreated'] = '_refresh';

            // Register events
            this._on(eventHandlers);

            this._refresh();
        },

        /**
         * Refresh the dom storage
         *
         * @private
         */
        _refresh: function(){
            this.options.dom.masters = this.element.find(this.options.selectors.masters);
            this.options.dom.slaves = this.element.find(this.options.selectors.slaves);
        },

        /**
         * Fire on master change
         *
         * @param event
         * @private
         */
        _onMasterChange: function(event){
            var master = event.target,
                checked = master.checked;

            this._toggleSlaves(this.options.dom.slaves, checked);

        },

        /**
         * Fire on slave change
         *
         * @param event
         * @private
         */
        _onSlaveChange: function(event){
            var slave = event.target,
                checked = slave.checked,
                totalChecked = this.options.dom.slaves.filter(':checked').length,
                checkMaster = (this.options.dom.slaves.length == totalChecked);

            this._toggleSlaves(slave, checked);
            this.options.dom.masters.prop('checked', checkMaster);
        },

        /**
         *
         * @param slaves
         * @param checked
         * @private
         */
        _toggleSlaves: function(slaves, checked) {
            var $slaves = $(slaves),
                $parentContainer = $slaves.closest(this.options.selectors.parentContainer);

            $parentContainer.toggleClass(this.options.classes.parentContainerSelected, checked);
            $slaves.prop('checked', checked);
        }

    });

})(jQuery, window, document);
