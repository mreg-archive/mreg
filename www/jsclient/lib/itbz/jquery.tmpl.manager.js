/*
 * @fileOverview jQuery extension for managing templates. Requires jquery.tmpl.
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */


(function($){

    /**
     * @desc Simple object to managa collections of templates.
     * Render resources based on their url.
     */
    $.tmplManager = new function(){
    
        /**
         * @desc Array with stored template values
         * @var {array} store
         */
        this.store = [];
    
        /**
         * @desc Default template values
         * @var {object} defaultObj
         */
        this.defaultObj = {
            name: false,
            regexp: false,
            template: '',
            renderCallback: function(element){},
            defaultData: {},
        };

        /**
         * @desc Callback function called after every render. Defaults to
         * nothing, overwrite if needed.
         * @var {func} globalRenderCallback
         */
        this.globalRenderCallback = function(element){};
    
        /**
         * @desc Load a template into the template manager
         * @param {object} tmplValues
         * @returns {void}
         */
        this.load = function(tmplValues){
            var tmplObj = {};
            $.extend(tmplObj, this.defaultObj, tmplValues);
            
            if ( !tmplObj.name ) {
                if ( tmplObj.href ) {
                    tmplObj.name = tmplObj.href;
                } else {
                    tmplObj.name = 'tmpl_' + this.store.length;
                }
            }
            
            tmplObj.name = tmplObj.name.toString();
            
            //compile template
			jQuery.template(tmplObj.name, tmplObj.template);

            this.store.push(tmplObj);
        }
    
        /**
         * @desc Render a template using data. Template is found matching url
         * and template regexp.
         * @param {string} url
         * @param {object} data
         * @returns {jQuery} rendered template wrapped in a div, FALSE on error
         */
        this.render = function(url, data){
            var tmplObj = this.getTmplObjByUrl(url);
            return this.renderUsingObj(tmplObj, data);
        }

        /**
         * @desc Render a template using data. Template is found matching name.
         * @param {string} name
         * @param {object} data
         * @returns {jQuery} rendered template wrapped in a div, FALSE on error
         */
        this.renderUsingName = function(name, data){
            var tmplObj = this.getTmplObjByName(name);
            return this.renderUsingObj(tmplObj, data);
        }


        /**
         * @desc Render a template using data. And a fixed template object.
         * @param {object} tmplObj
         * @param {object} data
         * @returns {jQuery} rendered template wrapped in a div, FALSE on error
         */
        this.renderUsingObj = function(tmplObj, data){
            if ( !tmplObj ) {
                console.log('TmplManager.renderUsingObj: unable to render');
                return false;
            }

            if ( !data ) data = {};

            var usingData = {};
            $.extend(usingData, tmplObj.defaultData, data);
            
            //Render template to div
            $obj = $('<div>');
            $obj.html( $.tmpl(tmplObj.name, usingData) );
            
            //Trigger callback
            tmplObj.renderCallback($obj);
            
            //Trigger global callback
            this.globalRenderCallback($obj);
            
            return $obj;
        }


        /**
         * @desc Get template values for loaded template. Template is found
         * matching url and template regexp.
         * @param {string} url
         * @returns {object} FALSE on error
         */
        this.getTmplObjByUrl = function(url){
            for ( i=0; i<this.store.length; i++ ) {
                if ( !this.store[i].regexp ) continue;
                regexp = this.store[i].regexp;
                if ( url.search(regexp) != -1 ) {
                    return this.store[i];
                }
            }
            console.log('TmplManager.getTmplObjByUrl: no matching template was found');
            return false;
	    }


        /**
         * @desc Get template values for loaded template. Template is found
         * matching name.
         * @param {string} name
         * @returns {object} FALSE on error
         */
        this.getTmplObjByName = function(name){
            for ( i=0; i<this.store.length; i++ ) {
                if ( this.store[i].name == name ) {
                    return this.store[i];
                }
            }
            console.log('TmplManager.getTmplObjByName: no matching template was found for: ' + name);
            return false;
        }

    }

})(jQuery);
