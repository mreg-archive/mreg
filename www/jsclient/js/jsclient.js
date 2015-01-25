/**
 * @fileOverview jsclient ...
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @requires jQuery 1.7
 */

/**
 * See (http://jquery.com/).
 * @name jQuery
 * @class 
 * See the jQuery Library  (http://jquery.com/) for full details.  This just
 * documents the function and classes that are added to jQuery by this plug-in.
 */

(function($){

    /**
     * @desc jsclient default settings
     * @namespace
     */
    var settings = {

        /**
         * @desc Service base url.
         */
        serviceUrl: '',

        /**
         * @desc Additional path appended to serviceUrl at init
         */
        servicePath : '',
     
        /**
         * @desc Path to initialize session with server. Defaults to serviceUrl.
         * Server must respond with a json formated dictionary of settings.
         */
        initPath: '~/',

        /**
         * @desc Max upload filesize in bytes
         */
        upload_max_filesize: 2048,

        /**
         * @desc If set to TRUE jsclient will authenticate using Auth header,
         * if FALSE credentials will be sent as http request data. True is
         * recommended as it better complies with the HTTP standard. False
         * might be necessary if your backend is unable to access the auth
         * header (eg. PHP with FCGI sapi).
         * @default true
         */
        useAuthHeader: true,

        /**
         * @desc List of warn-codes whose content will be presented to the user
         * @default [199, 299]
         */
        displayHttpWarnings: [199, 299],

        /**
         * @desc If set to TRUE jsclient will only run with cookies enabled.
         * @default true
         */
        requireCookies: true,

        /**
         * @desc List of required browser features. See jQuery.support for more
         * info. Each item is a tuple of feature name and error message.
         */
        requiredFeatures: [
            ['ajax', 'Unable to create jqXHR'],
            ['hrefNormalized', 'hrefNormalized must be true']
        ],

        /**
         * @desc Array of objects describing templates loaded during init.
         * See http://http://api.jquery.com/category/plugins/templates/
         *
         * <p>Each object must contain a href. It can be absolute, relative (in
         * wich case it is relative the script url) or prepended with a ~ (in
         * wich case it is relative the service url.</p>
         *
         * <p>If urls contains <b>&</b> you must use to full
         * HTML code <b>$amp;</b>, or the script will not run.</p>
         *
         * <p>Each object may also take a renderCallback value. The
         * renderCAllback is called each time template is rendered, with the
         * created element as parameter.</p>
         *
         * <p>Each object may also take a regexp value. The regexp will be
         * matched against the urls of resources to select template.</p>
         *
         * @default []
         */
        templates: [],

        /**
         * @desc A collection of hrefs to be preloaded and written to the cache.
         * For some notes on hrefs se settings.templates.
         * @default []
         */
        cache: [],

        /**
         * @desc Callback function called when init is done
         */
        ready: function(){},

        /**
         * @desc Display a warning. Override to support fancy libraries.
         * @default console.error
         */
        fnWarning: function(msg){
            console.error(msg);
        },

        /**
         * @desc User information. 
         */
        user: null,

        /**
         * @desc Standard ajax settings.
         * @see http://api.jquery.com/jQuery.ajax/ For more information on
         * writing ajax statusCode functions.
         */
        ajax: {
            dataType: 'json',
            data: {
                'fingerprint': '',
            },
            statusCode: {
                401: doBasicAuth,
                503: httpServiceDown,
            }
        },
        
        /**
         * @desc Seconds between cache auto updates
         * Only used if usrOptions.auto_update_cache is set to true.
         */
        auto_update_cache_timeout: 300,

    }; // </settings>


    /**
     * @desc User defined options
     * @namespace
     */
    var usrOptions = {

        /**
         * @desc Turn on or off cache auto update
         */
        auto_update_cache: false,

        /**
         * @desc Log out after seconds of inactivity
         */
        auto_logout_after: 1800,

    }; // </usrOptions>


    // Check browser support when DOM is ready
    $(document).ready(function(){
        // Validate browser features
        $.each(settings.requiredFeatures, function(i, f){
            if ( jQuery.support[f[0]] == false ) {
                $.alert({
                    title: 'ERROR: Your browser is not supported',
                    content: f[1]
                });
            }
        });

        // Validate cookie support
        if ( settings.requireCookies === true ) {
            document.cookie="testCookie=valid";
            if ( document.cookie == '' ) {
                $.alert({
                    title: 'ERROR: Cookies are required',
                    content: 'Activate to continue.'
                });
                return;
            } else {
                // Remove cookie
                document.cookie = 
                    "testCookie=remove; expires=Fri, 27 Jul 2001 02:47:11 UTC"
            }
        }

        // Log ajax errors
        $(document).ajaxError(function(e, jqXHR, opts, err){
            if ( window.console != undefined ) {
                console.error('AJAX SETTINGS');
                console.error(opts);
                console.error('HTTP RESPONSE');
                console.error(jqXHR.getAllResponseHeaders());
                console.error('Body: ' + jqXHR.responseText);
            }
        });
    });


    /**
     * @desc The main jsclient object
     * @memberOf jQuery
     * @name jQuery.jsclient
     */
    $.jsclient = new function(){

        /**
         * @desc Custom usage of jQuery.ajaxSetup breaks jsclient.
         * Use this method instead.
         * @param {dict} opts
         * @returns {void}
         */
        this.ajaxSetup = function(opts){
            if ( !opts ) opts = {};
            opts = $.extend(true, settings.ajax, opts);
            $.ajaxSetup(opts);
        }


        /**
         * @desc Get current setting
         * @param {string} key
         * @returns {mixed}
         */
        this.getSetting = function(key){
            if ( !key || !settings[key] ) return '';
            return settings[key];
        }


        /**
         * @desc Parse link header and return array oj value objects
         * @param {jqXHR} jqXHR
         * @returns {array}
         */
        this.parseLinkHeader = function(jqXHR){
            var headers = jqXHR.getResponseHeader('Link');
            var returnArr = [];
            if ( headers ) {
                $.each(headers.split(","), function(index, header){
                    var values = header.split(';');
                    var url = values.shift().replace(/^\s*<?([^>]*)>?\s*$/, '$1');

                    var parsedVals = {};
                    $.each(values, function(index, value){
                        var arr = value.split('=');
                        if ( arr[0] == '' ) return;
                        if ( arr.length < 2 ) arr.push('');
                        parsedVals[arr[0]] = arr[1].replace(/^"?([^"]*)"?$/, '$1');
                    });

                    parsedVals = $.extend({url: url, title:'', type: ''}, parsedVals);
                    returnArr.push(parsedVals);
                });
            }
            return returnArr;
        }


        /**
         * @desc Init app. Do not call before DOM is ready.
         * @param {dict} opts
         * @param {dict} usrOpts
         * @returns {void}
         */
        this.init = function(opts, usrOpts){
            if ( !opts ) opts = {};
            if ( !usrOpts ) usrOpts = {};

            // Reference to this instance
            var instance = this;
            
            // Extend settings
            $.extend(true, settings, opts);
            if ( !settings.serviceUrl ) {
                settings.serviceUrl =
                    document.location.protocol
                    + '//'
                    + document.location.host;
            }
            settings.serviceUrl += settings.servicePath;

            // Read fingerprint from fragment id
            settings.ajax.data.fingerprint = document.location.hash.substring(1);

            // Setup ajax with settings and fingerprint
            this.ajaxSetup();
            $.ajaxPrefilter(function(opts, origOpts, jqXHR){
                ajaxPrefilter.call(instance, opts, origOpts, jqXHR);
            });
     
            // Init session with server
            jQuery.ajax({
                type: 'GET',
                url: settings.initPath,
                onFail: function(jqXHR){
                    // Display error if no action is registered for response
                    if ( !settings.ajax.statusCode[jqXHR.status] ) {
                        $.alert({
                            title: 'ERROR',
                            content: "Unable to initialize server session. Using url: '"
                                    + expandPath.call(instance, settings.initPath)
                                    + "'."
                        });
                    }
                },

                // Init successful, load templates and cache items
                onDone: function(data, status, jqXHR){
                    $.extend(true, settings, data);

                    // Display progressbar
                    var pbar = $('<div>').addClass('jsclient-progressbar').append($('<div>')).appendTo('body');
                    var c = settings.cache.length + settings.templates.length;
                    var p = 100/c;
                    pbar.tic = function(){
                        var $this = $(this);
                        div = $('<div>').css('width', p+'%');
                        $this.find('> div').append(div);
                        if ( --c == 0 ) $this.remove();
                    }        

                    // Requests sent during preload
                    var requests = [];

                    // Load templates
                    $.each(settings.templates, function(index, item) { 
                        var r = $.ajax({
                            type: 'GET',
                            url: item.href,
                            context: item,
                            dataType: 'text',
                        });
                        r.always(function(){
                            pbar.tic();
                        });
                        r.done(function(template){
                            this.template = template;
                            $.tmplManager.load(this);
                        });
                        requests.push(r);
                    });

                    // Warm up cache
                    $.each(settings.cache, function(index, url) { 
                        var r = $.rcache.item(url).get();
                        r.always(function(){
                            pbar.tic();
                        });
                        requests.push(r);
                    });

                    // Apply user options
                    $.extend(true, usrOptions, usrOpts);
                    /*
                        TODO: håller på med AUTO-LOGOUT, jag behöver:
                            - en funktino som helt enkelt är logout, den ska också köras från toolbar logout
                            - en "global" variabel där jag kan spara timeout
                            - ett system för att förnya timeout när användaren gör någon som ska blockera logout..
                    */
                    //setTimeout("alert('logout');", usrOptions.auto_logout_after * 1000);

                    // Set cache auto-update
                    if ( usrOptions.auto_update_cache ) {
                        $.jsclient.triggerAutoUpdateCache();
                    }

                    // Call ready function when all is loaded
                    $.when.apply(null, requests).then(function(){
                        settings.ready.call(instance);
                    });
                }
            });
        };// </init>


        /**
         * @desc Start timed cache updates
         * @param {dict} opts
         * @param {dict} usrOpts
         * @returns {void}
         */
        this.triggerAutoUpdateCache = function(){
            $.rcache.updateAll();
            var time = settings.auto_update_cache_timeout * 1000;
            // TODO timeout borde sparas i "global" variabel så att cykeln kan brytas!
            setTimeout('jQuery.jsclient.triggerAutoUpdateCache()', time);
        }

    }// </$.jsclient>


    /* AJAX RESPONSE FUNCTIONS */


    /**
     * @desc Prefilter called for every ajax request
     * @param {dict} options
     * @param {dict} originalOptions
     * @param {jqXHR} jqXHR
     * @returns {void}
     */
    function ajaxPrefilter(options, originalOptions, jqXHR){
        // Reference to this instance
        var instance = this;

        // Write original options to jqXHR
        jqXHR.originalOptions = originalOptions;

        // Register custom fail and done callbacks
        if ( $.isFunction(originalOptions.onFail) ) {
            jqXHR.fail(originalOptions.onFail);
        }
        if ( $.isFunction(originalOptions.onDone) ) {
            jqXHR.done(originalOptions.onDone);
        }

        // Expand url
        options.url = expandPath.call(instance, options.url);
        if (!options.url.match(/\?/)) {
            options.url += '?fingerprint=' + settings.ajax.data.fingerprint;
        }

        // Save fingerprint if X-Session-Fingerprint header is present
        jqXHR.done(function(body, status, jqXHR){
            var fp = jqXHR.getResponseHeader('X-Session-Fingerprint')
            if ( fp ) {
                settings.ajax.data.fingerprint = fp;
                document.location.hash = fp;
                // Re-setup ajax to get new fingerprint
                instance.ajaxSetup();
            }
        });

        // Download files linked with rel=download
        jqXHR.done(function(x, xx, jqXHR){
            var arDownloads = parseDownloadLinkHeaders.call(instance, jqXHR);
            $.each(arDownloads, function(i, link){
                download.call(instance, link.url, link.title, link.type, (i+1)*1000);
            });
        });

        // Display http warning headers
        jqXHR.done(function(x, xx, jqXHR){
            displayHttpWarnings.call(instance, jqXHR);
        });
        jqXHR.fail(function(jqXHR){
            displayHttpWarnings.call(instance, jqXHR);
        });
    }


    /**
     * @desc Query user credentials and resend request with
     * Authorization header. Used with response code 401
     * @param {jqXHR} jqXHR
     * @returns {void}
     */
    function doBasicAuth(jqXHR){
        // Use stored options to resend request
        var options = jqXHR.originalOptions;
        
        if ( !options.headers ) options.headers = {}
        if ( !options.data ) options.data = {}

        // Get current user name
        var uname = '';
        if ( settings.user && settings.user.uname ) {
            uname = settings.user.uname;
        }

        window.fShortcutsEnabled = false;
        $.promptLogin({
            title: $.parseJSON(jqXHR.responseText),
            uname: uname,
            unameDesc: 'Användarnamn',
            pswdDesc: 'Lösenord',
            btnOk: 'Autentisera',
            fnAlways: function(){
                window.fShortcutsEnabled = true;
            },
            fnCallback: function(values){
                // AuthType is read from header
                var authType = jqXHR.getResponseHeader('WWW-Authenticate');
                if ( !authType ) {
                    authType = 'BASIC';
                } else {
                    authType = authType.split(' ')[0]
                }
                
                // BASIC authentication is assumed
                var cred =
                    authType
                    + ' '
                    + $.base64.encode(values.uname + ":" + values.pswd);

                if ( settings.useAuthHeader == true ) {
                    // Send credentials as http header
                    options.headers['Authorization'] = cred;
                } else {
                    // Send credentials as http data
                    options.data['Authorization'] = cred;
                }

                // Resend request
                $.ajax(options);
            }
        });
    }


    /**
     * @desc Display Service Unavailable message. Used with response code 503.
     * @param {jqXHR} jqXHR
     * @returns {void}
     */
    function httpServiceDown(jqXHR){
        // Get Retry-After datetime
        var retryAfter = jqXHR.getResponseHeader('Retry-After');
        if ( !retryAfter ) {
            retryAfter = '';
        } else {
            if ( retryAfter.match(/^\d*$/) ) {
                // Create full datetime from seconds in the future
                var now = new Date();
                now.setSeconds(now.getSeconds() + parseInt(retryAfter));
                retryAfter = now.toString();
            }
            retryAfter = '<p>Försök igen efter: ' + retryAfter + '</p>';
        }
        
        var txt = '<p>Medlemsregistret är ej tillgängligt för tillfället.</p>';
        var txt = txt + retryAfter;
        var txt = txt +'<p>(HTTP status 503 från ' + settings.serviceUrl + ')</p>';

        $.alert({
            title: 'Service Unavailable',
            content : txt,
            height: 280
        });
    }


    /* PRIVATE FUNCTIONS */


    /**
     * @desc Replace ~ at the start of path with settings.serviceUrl
     * @param {string} path
     * @returns {string}
     */
    function expandPath(path){
        if ( !path ) return '';
        if ( path.match(/^~\//) ) {
            path = path.replace(/^(~\/)?/, settings.serviceUrl)
        }
        return path;
    }


    /**
     * @desc Display warning headers from jqXHR if warn-code
     * is listed in settings.displayHttpWarnings
     * @param {jqXHR} jqXHR
     * @returns {void}
     */
    function displayHttpWarnings(jqXHR){
        var headers = jqXHR.getResponseHeader('Warning');
        if ( headers ) {
            $.each(headers.split(','), function(index, header){
                var code = parseInt(header);
                var msg = header.replace(/^\s*\d+\s*(.*)$/, "$1");
                if ( $.inArray(code, settings.displayHttpWarnings) != -1 ) {
                    // Display warning
                    settings.fnWarning(msg);
                }
            });
        }
    }


    /**
     * @desc Scan all link headers for rel=download.
     * @param {jqXHR} jqXHR
     * @returns {array} List of link values
     */
    function parseDownloadLinkHeaders(jqXHR){
        var returnArr = [];
        var arLinks = this.parseLinkHeader(jqXHR);
        $.each(arLinks, function(i, headerObj){
            if ( headerObj.rel == 'download' ) {
                returnArr.push(headerObj);
            }
        });
        return returnArr;
    }


    /**
     * @desc Download a resource. To avoid erraneous page redirects a HEAD request
     * is sent, if it returns OK and Content-Disposition is set to attachment or
     * inline the browser is directed to download.
     * @param {string} url Url to download (~ are expanded)
     * @param {string} title 
     * @param {string} type Expected content type
     * @param {int} timeout Milliseconds to wait before downloading link
     * @returns {bool} false to prevent browser to follow links
     * @TODO type is not used
     */
    function download(url, title, type, timeout){
        if ( !timeout ) timeout = 1000;

        // Expand url and append fingerprint
        url = expandPath.call(this, url);
        url += '?fingerprint=' + settings.ajax.data.fingerprint;

        // Check if resource exists
        $.ajax({
            type: 'HEAD',
            url: url,
            onDone: function(x, xx, jqXHR){
                // Perform download
                var header = jqXHR.getResponseHeader('Content-Disposition');
                if ( header ) {
                    var disp = header.split(';')[0];
                    if ( disp == 'attachment' || disp == 'inline' ) {
                        setTimeout("document.location = '" + url + "';", timeout);
                        return;
                    }
                }
                console.error('Download error (Conent-Disposition must be inline or attachment)');
                consolr.error(jqXHR.getAllResponseHeaders());

                window.fShortcutsEnabled = false;
                $.alert({
                    title: "ERROR: Kunde ej ladda ner '" + title + "'",
                    content: 'Felaktig Content-Disposition header för: ' + url,
                    fnAlways: function(){
                        window.fShortcutsEnabled = true;
                    }
                });
            },
            onFail: function(jqXHR){
                window.fShortcutsEnabled = false;
                $.alert({
                    title: "ERROR: Kunde ej ladda ner '" + title + "'",
                    content: url,
                    fnAlways: function(){
                        window.fShortcutsEnabled = true;
                    }
                });
            }
        });
        return false;
    }

})(jQuery);
