/**
 * Copyright (c) 2012 Hannes Forsgård
 * Licensed under the WTFPL (http://sam.zoy.org/wtfpl/)
 * @fileOverview rcache: observable and active ajax cacher for jQuery
 * @version 1.1.1
 * @requires jQuery
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */


/**
 * See (http://jquery.com/).
 * @name $
 * @class 
 * See the jQuery Library  (http://jquery.com/) for full details.  This just
 * documents the function and classes that are added to jQuery by this plug-in.
 */


(function($){

    /**
     * @desc Active and observable ajax cache for jQuery REST applications. Uses
     * jQuery.ajax when interacting with the server.
     * <p>A note on keys: Cache keys are case sensitive urls. Take care to use
     * consistent urls, even query parameters and hashes count.</p>
     * <p>rcache only cache 200 and 201 responses. Redirects are not followed
     * due to the risk of creating redirect loops. 404 and 410 responses
     * automatically removes item from cache. 205 responses resets item to
     * current 'reset-resource-to' value (see setUp()).</p>
     * @author Hannes Forsgård
     * @class
     */
    $.rcache = new function(){


        /* Settings */


        /**
         * @desc rcache settings. See setUp() for altering settings.
         * @var {object} settings
         */
        this.settings = {
            'update-on-read': true,
            'reset-resource-to': {},
            'max-size': 50
        };


        /**
         * @desc Set rcache settings. The following settings are available:
         * <dl>
         *  <dt>update-on-read</dt>
         *      <dd>Boolean. Send conditional get requests in the
         *      background on cache reads? Deafaults to true.</dd>
         *  <dt>reset-resource-to</dt>
         *      <dd>When the server responds with a 205 Reset content status code
         *      item data is reset to this value. Defaults to {}.</dd>
         *  <dt>max-size</dt>
         *      <dd>Cache max size.</dd>
         * </dl>
         * @param {object} settings
         * @returns {$.rcache} Return rcache instance for chaining purposes
         */
        this.setUp = function(settings){
            $.extend(this.settings, settings);
            return this;
        }

        
        /* Basic functionality */        
        

        /**
         * @desc The internal cache store
         * @var {object} cache
         */
        this.cache = {};


        /**
         * @desc Get number of items in cache
         * @returns {int}
         */
        this.count = function(){
            var count = 0;
            $.each(this.cache, function() { 
                count++;
            });
            return count;
        }


        /**
         * @desc Returns TRUE if cache includes item, FALSE otherwise
         * @param {string} key
         * @returns {bool}
         */
        this.has = function(key){
            return key in this.cache;
        }


        /**
         * @desc Get item for cache key. If item does not exist
         * an empty item is created.
         * @param {string} key
         * @returns {Item}
         */
        this.item = function(key){
            if ( !this.has(key) ) {
                this.cache[key] = {
                    item: new Item(key),
                    time: Date.now()
                };
                // Garbage collect if cache is to large
                if ( this.count() > this.settings['max-size'] ) {
                    this.gc();
                }
            }
            // Set access time
            this.cache[key].time = Date.now();
            return this.cache[key].item;
        }

        
        /**
         * @desc Garbage collection
         * @returns {$.rcache} Return rcache instance for chaining purposes
         */
        this.gc = function(){
            var arSort = [];

            $.each(this.cache, function(key, obj) { 
                arSort.push([obj.time, key]);
            });

            arSort.sort(function(a, b){
                return a[0] - b[0];
            });

            // Remove one fourth of max-size
            var targetSize = this.settings['max-size'] - Math.round(this.settings['max-size']/4);
            for ( var i = this.count()-targetSize; i>0; i-- ) {
                var post = arSort.shift();
                this.item(post[1]).remove();
            }
            
            return this;
        }


        /**
         * @desc Clear cache
         * @returns {$.rcache} Return rcache instance for chaining purposes
         */
       this.clear = function(){
            this.cache = {};
            return this;
        }


        /* AJAX */


        /**
         * @desc Update all items in cache where autoUpdate == true, using
         * conditional GET requests
         * @param {object} options Additional jQuery.ajax options
         * @returns {$.rcache} Return rcache instance for chaining purposes
         */
        this.updateAll = function(options){
            $.each(this.cache, function(key, obj) {
                if ( obj.item.autoUpdate ) {
                    obj.item.update(options);
                }
            });
            return this;
        }


        /**
         * @desc Settings used when creating ajax objects
         * @var {object} ajaxOpts
         */
        this.ajaxOpts = {
            // Default settings goes here ...
            cache: false
        }; 


        /**
         * @desc Set default ajax optins. By default rcache requests are created
         * with the 'cache' option set to false.
         * @param {object} options
         * @returns {$.rcache} Return rcache instance for chaining purposes
         */
        this.ajaxSetup = function(options){
            $.extend(this.ajaxOpts, options);
            return this;
        }


        /**
         * @desc Internal method to create and fire a jqXHR. On 404 Not Found or
         * 410 Gone responses the cache item is deleted. On 205 Reset Content
         * responses cache item is cleared to setting 'reset-resource-to'.
         * @param {object} options
         * @returns {jqXHR}
         */
        this.getJqXHR = function(options){
            var settings = {};
            $.extend(settings, this.ajaxOpts, options);
            var jqXHR = $.ajax(settings);

            jqXHR.fail(function(jqXHR){
                // Remove from cache on 404 and 410 fails
                if ( jqXHR.status == 404 || jqXHR.status == 410 ) {
                    $.rcache.item(settings.url).remove();
                }
            });

            jqXHR.done(function(x, xx, jqXHR){
                // Reset cache content on 205 response
                if ( jqXHR.status == 205 ) {
                    var empty = $.rcache.settings['reset-resource-to'];
                    $.rcache.item(settings.url).write(empty, jqXHR);
                }
            });

            return jqXHR;
        }

    }


    // Item internal class


    /**
     * @desc Each item in cache is represented by an Item object
     * @param {string} url Url of created item
     * @class
     * @name Item
     */
    function Item(url){

        /**
         * @desc Item url
         * @name Item.url
         * @type string
         */
        this.url = url;


        /**
         * @desc Resource item data
         * @name Item.data
         * @type mixed
         */
        this.data = false;

 
        /**
         * @desc Request object that fetched resource
         * @name Item.jqXHR
         * @type jqXHR
         */
        this.jqXHR = false;

        
        /**
         * @desc If true this item will be updated on updateAll calls.
         * @name Item.autoUpdate
         * @type bool
         */
        this.autoUpdate = false;


        /**
         * @desc Observer functions for write events
         * @name Item.writeObservers
         * @type array
         */
        this.writeObservers = [];
        

        /**
         * @desc Observer functions for remove events
         * @name Item.removeObservers
         * @type array
         */
        this.removeObservers = [];
    }


    // Extending the Item prototype


    /**
     * @desc Inspect if item contains data
     * @name Item.hasData
     * @function
     * @returns {bool}
     */
    Item.prototype.hasData = function(){
        return !!this.data;
    }


    /**
     * @desc Get ETag response header
     * @name Item.etag
     * @function
     * @return {string}
     */
    Item.prototype.etag = function(){
        if ( !this.jqXHR ) return '';
        var etag = this.jqXHR.getResponseHeader('ETag');
        return ( etag ) ? etag : '';
    }


    /**
     * @desc Get Last-Modified response header
     * @name Item.modified
     * @function
     * @return {string}
     */
    Item.prototype.modified = function(){
        if ( !this.jqXHR ) return '';
        var modified = this.jqXHR.getResponseHeader('Last-Modified');
        return ( modified ) ? modified : '';
    }


    /**
     * @desc Bind function to write event. Observer functions consume four
     * parameters: the response body, Etag header (if present), Last-Modofied
     * header (if present) and the jqXHR object.
     * @name Item.onWrite
     * @function
     * @param {func} func
     * @returns {Item} This item, for chaining purposes
     */
    Item.prototype.onWrite = function(func){
        this.writeObservers.push(func);
        return this;
    }


    /**
     * @desc Bind function to remove event. Observer functions consume four
     * parameters: the response body, Etag header (if present), Last-Modofied
     * header (if present) and the jqXHR object.
     * @name Item.onRemove
     * @function
     * @param {func} func
     * @returns {Item} This item, for chaining purposes
     */
    Item.prototype.onRemove = function(func){
        this.removeObservers.push(func);
        return this;
    }


    /**
     * @desc Fire a write event. Does not send requests or affect cache content.
     * @name Item.notifyWrite
     * @function
     * @returns {Item} This item, for chaining purposes
     */
    Item.prototype.notifyWrite = function(){
        var item = this;
        $.each(this.writeObservers, function(index, func){
            func(item.data, item.etag(), item.modified(), item.jqXHR);
        });
        return this;
    }


    /**
     * @desc Fire a remove event. Does not send requests or affect cache content.
     * @name Item.notifyRemove
     * @function
     * @returns {Item} This item, for chaining purposes
     */
    Item.prototype.notifyRemove = function(){
        var item = this;
        $.each(this.removeObservers, function(index, func){
            func(item.data, item.etag(), item.modified(), item.jqXHR);
        });
        return this;
    }


    /**
     * @desc Write new content to item. Triggers write event. Sets
     * autoUpdate to true, enabling this item to be updated on updateAll
     * @name Item.write
     * @function
     * @param {mixed} data
     * @param {jqXHR} jqXHR
     * @returns {Item} This item, for chaining purposes
     */
    Item.prototype.write = function(data, jqXHR){
        this.data = data;
        this.jqXHR = jqXHR;
        this.autoUpdate = true;
        this.notifyWrite();
        return this;
    }


    /**
     * @desc Remove item from cache
     * @name Item.remove
     * @function
     * @returns {void}
     */
    Item.prototype.remove = function(){
        this.notifyRemove();
        delete $.rcache.cache[this.url];
    }


    // AJAX


    /**
     * @desc If item is not in cache a http GET request is sent. If item is
     * already in cache the previous jqXHR object is returned.
     * @name Item.get
     * @function
     * @param {object} options Additional jQuery.ajax options
     * @returns {jqXHR}
     */
    Item.prototype.get = function(options){
        if ( this.hasData() && this.jqXHR ) {
            // Perform update in the background
            if ( $.rcache.settings['update-on-read'] ) {
                this.update(options);
            }
            // Return jqXHR of item already in cache
            return this.jqXHR;
        } else {
            // Create new ajax request
            return this.forceGet(options);
        }
    }


    /**
     * @desc Perform a conditional http GET request. If resource have
     * been updated changes are written to the cache.
     * @name Item.update
     * @function
     * @param {object} options Additional jQuery.ajax options
     * @returns {jqXHR}
     */
    Item.prototype.update = function(options){
        var opts = {
            type: 'GET',
            url: this.url,
            headers: {
                'If-None-Match': this.etag(),
                'If-Modified-Since': this.modified(),
            }
        };

        $.each(opts.headers, function(index, value){
            if ( !value ) delete opts.headers[index];
        });

        $.extend(true, opts, options);

        var jqXHR = $.rcache.getJqXHR(opts);
        var item = this;

        jqXHR.done(function(body, status, jqXHR){
            // Write to item on success
            // 304 == Not Modified
            if ( jqXHR.status == 200 ) {
                item.write(body, jqXHR);
            }
        });

        return jqXHR;
    }


    /**
     * @desc Force a http GET request, even if item is in cache
     * @name Item.forceGet
     * @function
     * @param {object} options Additional jQuery.ajax options
     * @returns {jqXHR}
     */
    Item.prototype.forceGet = function(options){
        if ( !options ) options = {};

        var opts = {
            type: 'GET',
            url: this.url,
            headers: {
                'If-None-Match': '',
                'If-Modified': '',
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache'
            }
        };

        $.extend(true, opts, options);

        var jqXHR = $.rcache.getJqXHR(opts);
        var item = this;

        jqXHR.done(function(body, status, jqXHR){
            // Write to item on success
            if ( jqXHR.status == 200 ) {
                item.write(body, jqXHR);
            }
        });

        return jqXHR;
    }


    /**
     * @desc Delete item using http DELETE and remove from cache
     * @name Item.del
     * @function
     * @param {object} options Additional jQuery.ajax options
     * @returns {jqXHR}
     */
    Item.prototype.del = function(options){
        if ( !options ) options = {};

        var opts = {
            type: 'DELETE',
            url: this.url,
            headers: {
                'If-Match': this.etag(),
                'If-Unmodified-Since': this.modified(),
            }
        };

        $.each(opts.headers, function(index, value){
            if ( !value ) delete opts.headers[index];
        });

        $.extend(true, opts, options);
 
        var jqXHR = $.rcache.getJqXHR(opts);
        var item = this;

        jqXHR.done(function(body, status, jqXHR){
            // Remove from cache on success
            if ( jqXHR.status == 200 || jqXHR.status == 204 ) {
                item.remove();
            }
        });
        return jqXHR;
    }


    /**
     * @desc Send item using http PUT. If the server returns status code 200
     * (OK) and a response body the returned resource is written to cache.
     * @name Item.put
     * @function
     * @param {mixed} data
     * @param {object} options Additional jQuery.ajax options
     * @returns {jqXHR}
     */
    Item.prototype.put = function(data, options){
        if ( !options ) options = {};

        var opts = {
            type: 'PUT',
            url: this.url,
            data: data,
            headers: {
                'If-Match': this.etag(),
                'If-Unmodified-Since': this.modified(),
            }
        };

        $.each(opts.headers, function(index, value){
            if ( !value ) delete opts.headers[index];
        });

        $.extend(true, opts, options);

        var jqXHR = $.rcache.getJqXHR(opts);
        var item = this;

        jqXHR.done(function(body, status, jqXHR){
            // Write to cache if these requirements are met
            if ( jqXHR.status == 200 && body ) {
                item.write(body, jqXHR);
            }
        });

        return jqXHR;
    }


    /**
     * @desc Send http POST request. If the server returns status code 200
     * (OK) or 201 (created), a response body and a Content-Location header
     * the resource is written to cache. Else if the server returns a
     * Location header a GET request for that url is triggered.
     * @name Item.post
     * @function
     * @param {mixed} data
     * @param {object} options Additional jQuery.ajax options
     * @returns {jqXHR}
     */
    Item.prototype.post = function(data, options){
        if ( !options ) options = {};

        var opts = {
            type: 'POST',
            url: this.url,
            data: data
        };

        $.extend(true, opts, options);
 
        var jqXHR = $.rcache.getJqXHR(opts);

        jqXHR.done(function(body, status, jqXHR){
            var contentLocation = jqXHR.getResponseHeader('Content-Location');
            var location = jqXHR.getResponseHeader('Location');

            // Write to cache if these requirements are met
            if (
                (jqXHR.status == 200 || jqXHR.status == 201)
                && body
                && contentLocation
            ) {
                $.rcache.item(contentLocation).write(body, jqXHR);
            
            // Else get a fresh copy
            } else if ( location ) {
                $.rcache.item(location).forceGet();
            }
        });
        
        return jqXHR;
    }

})(jQuery);
