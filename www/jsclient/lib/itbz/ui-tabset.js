/**
 * @fileOverview jquery.ui.tabset
 *
 * @requires jQuery 1.7
 *
 * @requires jQuery-ui tabs
 *
 * @requires ui.tabs.closable Optional for creating tabsets with closable tabs
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */

/**
 * See (http://jquery.com/).
 *
 * @name jQuery
 *
 * @class 
 *
 * See the jQuery Library  (http://jquery.com/) for full details.  This just
 * documents the function and classes that are added to jQuery by this plug-in.
 */
(function($){

    /**
     * @desc Construct a Tabset object.
     *
     * Options: closable (bool), sortable (bool), id (string) and the standard
     * list of jquery-ui tabs options.
     *
     * @param {object} options
     *
     * @returns {Tabset}
     */
    function Tabset(options){
        if ( !options ) options = {};

        // Created tabs
        var arTabs = {};

        options = $.extend({
            closable: true,
            sortable: false,
            collapsible: false,
            rounded: false,
            show: function(){},
            remove: function(){},
            id: this.fnGenId('ui-tabset'),
        }, options);

        // Save show() as a custom callback and wrap it
        options.onShow = options.show;
        options.show = function(event, ui){
            // Call custom callback
            options.onShow(event, ui);
            // Call tab callback
            if ( typeof arTabs[ui.panel.id] != 'undefined' ) {
                arTabs[ui.panel.id].onShow();
            }
        }

        // Save remove() as a custom callback and wrap it
        options.onRemove = options.remove;
        options.remove = function(event, ui){
            // Call custom callback
            options.onRemove(event, ui);
            // Call tab callback
            if ( typeof arTabs[ui.panel.id] != 'undefined' ) {
                arTabs[ui.panel.id].onRemove();
            }
            // Remove from tabs store
            delete arTabs[ui.panel.id];
        }

        // Create tabset
        var $tabset = $('<div>')
            .attr('id', options.id)
            .addClass('itb-tabset')
            .append( $('<ul>') )
            .tabs(options);

        if (options.sortable) {
            $tabset.find(".ui-tabs-nav").sortable({ axis: "x" });
        }

        if (! options.rounded) {
            $tabset.find(".ui-corner-all").removeClass('ui-corner-all');
        }

        /**
         * @desc Get jquery tabset object
         *
         * @returns {jquery}
         */
        this.getElement = function(){
            return $tabset;
        };

        /**
         * @desc Create and add a new tab to this tabset. Options are passed
         * on to the Tab constructor.
         *
         * @param {object} options
         *
         * @returns {Tab}
         */
        this.createTab = function(options){
            var id = this.fnGenId(this.getId());
            $tabset.tabs('add', '#' + id);
            arTabs[id] = new Tab(id, options);

            return arTabs[id];
        };
        
        /**
         * @desc Get tab object for id
         *
         * @param {string} sId
         *
         * @returns {Tab}
         *
         * @throws {string} if tab does not exist
         */
        this.getTab = function(sId){
            if (sId in arTabs) {
                return arTabs[sId];
            }
            throw "Tab id '" + sId + "' does not exist.";
        };
        
    }

    /**
     * @desc Get tab object for active tab
     *
     * @returns {Tab}
     *
     * @throws {string} if tab does not exist
     */
    Tabset.prototype.getActiveTab = function(){
        var id = this.getElement().find('> div').not('.ui-tabs-hide').attr('id');
        return this.getTab(id);
    };

    /**
     * @desc Get number of tabs in tabset
     *
     * @returns {int}
     */
    Tabset.prototype.count = function(){
        var $el = this.getElement();
        return $el.tabs('length');
    };

    /**
     * @desc Get tabset id
     *
     * @returns {string}
     */
    Tabset.prototype.getId = function(){
        var $el = this.getElement();
        return $el.attr('id');
    };

    /**
     * @desc Generate id using a static counter
     *
     * @param {string} base
     *
     * @returns {string}
     */
    Tabset.prototype.fnGenId = function(base){
        if ( typeof this.fnGenId.c == 'undefined' ) {
            this.fnGenId.c = 0;
        }
        this.fnGenId.c++;
        return base + '-' + this.fnGenId.c;
    };


    /**
     * @desc Construct a single Tab object.
     * 
     * Do not call directly. Use Tabset.createTab()
     *
     * Options: title (string), content (html string), selected (bool),
     * show (func) show callback, remove (func) remove callback and fSetAltered
     * (bool) flag if tab should be marked as altered when content changes.
     *
     * @param {string} id
     *
     * @param {object} options
     *
     * @returns {Tab}
     */
    function Tab(id, options){
        if (!options) {
            options = {};
        }

        // Rewrite id as a css selector
        id = '#' + id;

        options = $.extend({
            title: 'no title',
            content: 'no content',
            selected: true,
            show: function(){},
            remove : function(){},
            fSetAltered: true,
        }, options);

        // Make show and remove callbacks visible
        this.onShow = options.show;
        this.onRemove = options.remove;

        // Cache jquery objects
        this.$panel = $(id);
        this.$tabset = this.$panel.parent('.itb-tabset');
        this.$selector = this.$tabset.find('.ui-tabs-nav a[href="' + id + '"]');

        // Set content
        this.fSetAltered = options.fSetAltered;
        this.setTitle(options.title);
        this.setContent(options.content);
        if (options.selected) {
            this.select();
        }

        /**
         * @desc Check if tab still exists in DOM
         *
         * @returns {bool}
         */
        this.exists = function(){
            return !!$(id).length;
        };
    }

    /**
     * @desc Set tab html contents
     *
     * @param {string|jQuery} sContent
     *
     * @returns void
     */
    Tab.prototype.setContent = function(sContent){
        this.$panel.html(sContent);
        // Set altered when content changes
        var tab = this;
        if ( this.fSetAltered ) {
            this.$panel.find('*').change(function(){
                tab.setAltered(true);
            });
        }
    };

    /**
     * @desc Set tab title
     *
     * @param {string} sTitle
     *
     * @returns void
     */
    Tab.prototype.setTitle = function(sTitle){
        this.$selector.find('span').text(sTitle);
    };

    /**
     * @desc Get tabset index of this tab
     *
     * @returns {int}
     */
    Tab.prototype.getIndex = function(){
        //save current index
        var current = this.$tabset.tabs('option', 'selected');
        //get index of this tab
        this.select();
        var index = this.$tabset.tabs('option', 'selected');
        //return focus to current index
        this.$tabset.tabs('select', current);
        return index;
    };

    /**
     * @desc Select this tab
     *
     * @returns {void}
     */
    Tab.prototype.select = function(){
        this.$selector.click();
        this.onShow();
    };

    /**
     * @desc Remove this tab
     *
     * @returns {void}
     */
    Tab.prototype.remove = function(){
        this.$tabset.tabs('remove', this.getIndex());
    };

    /**
     * @desc Mark tab content as altered.
     *
     * If you want altered graphics to display this method must be called
     * explicitly.
     *
     * @param {bool} bFlag Flag if tab should be marked as altered or not
     *
     * @returns {void}
     */
    Tab.prototype.setAltered = function(bFlag){
        if (bFlag) {
            this.$selector.addClass('fAltered');
        } else {
            this.$selector.removeClass('fAltered');
        }
    };

    /**
     * @desc Mark tab content as altered.
     *
     * If you want altered graphics to display this method must be called
     * explicitly.
     *
     * @param {bool} bFlag Flag if tab should be marked as outdated or not
     *
     * @returns {void}
     */
    Tab.prototype.setOutdated = function(bFlag){
        if (bFlag) {
            this.$selector.addClass('fOutdated');
            this.$selector.parent().addClass('ui-state-error');
        } else {
            this.$selector.removeClass('fOutdated');
            this.$selector.parent().removeClass('ui-state-error');
        }
    };


    /**
     * @desc jquery bindings. Create new tabset
     *
     * @param {object} oOptions
     *
     * @returns {Tabset}
     */
    $.createTabset = function(oOptions){
        return new Tabset(oOptions);
    };

})(jQuery);
