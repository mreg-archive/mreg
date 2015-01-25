/**
 * @fileOverview jQuery.ui.dialog extension
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
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
     * @desc Wrapper class to jquery.ui.dialog
     * @name Dialog
     * @class
     */
    function Dialog(){
        // jquery.ui.dialog options
        this.autoOpen = true;
        this.closeOnEscape = true;
        this.dialogClass = 'itb-ui-dialog';
        this.draggable = true;
        this.height = 200;
        this.minHeight = 200;
        this.minWidth = 400;
        this.modal = true;
        this.position = ['center', 100];
        this.resizable = true;
        this.title = 'no title';
        this.width = 400;
        
        // Destroy dialog on close
        this.close = function() {
            $(this).dialog("destroy").remove();
        }

        // Dialog options
        this.showCloseIcon = false;
        this.content = 'no content';
       
        var instance = this;

        this.btnOk = 'OK';
        this.btnOkClick = function(event){
            var $this =  $(this);
            $this.unbind("dialogclose");
            $this.dialog("close");
            instance.fnCallback.call(this, event);
            instance.fnAlways.call(this, event);
        }

        this.btnCancel = 'Cancel';
        this.btnCancelClick = function(event){
            $(this).dialog("close");
        }
        
        // User supplied callbacks
        this.fnCallback = function(){}
        this.fnCancel = function(){}
        this.fnAlways = function(){}
    }


    Dialog.prototype = {

        /**
         * @desc Extend dialog properties
         * @param {object} options Valid options are all options from jquery-ui
         * plus content.
         * @returns {Dialog} this instance to enable chaining
         * @methodOf Dialog
         * @name extend
         */
        extend: function(options){
            $.extend(this, options);
            return this;
        },
    
        /**
         * @desc Get buttons for this dialog
         * @returns {array}
         * @methodOf Dialog
         * @name getButtons
         */
        getButtons: function(){
            var arButtons = [];
            if ( this.btnOk ) arButtons.push({text: this.btnOk, click: this.btnOkClick});
            if ( this.btnCancel ) arButtons.push({text: this.btnCancel, click: this.btnCancelClick});
            return arButtons;
        },

        /**
         * @desc Create dialog using jquery ui .dialog()
         * @returns {jquery}
         * @methodOf Dialog
         * @name create
         */
        create: function(){
            var $el = $('<form>');
            $el.append(this.content);

            // Append a hidden submit that triggers button click
            $('<input>').attr('type', 'submit')
                .css({'visibility': 'hidden', height: '0px'})
                .click(function(){
                    $el.parent('.ui-dialog').find('.ui-dialog-buttonset button')[0].click();
                    return false;
                })
                .appendTo($el);

            // Do not send prototype methods to .dialog()
            $el.dialog($.extend({}, this, {extend: false, create: false, getButtons: false}));

            // Add buttons
            var arButtons = this.getButtons();
            $el.dialog('option', 'buttons', arButtons);

            // Bind dialog close event
            var instance = this;
            $el.bind("dialogclose", function(event){
                instance.fnCancel.call(this, event);
                instance.fnAlways.call(this, event);
            });

            // Disable close icon
            if ( !this.showCloseIcon ) {
                $el.parent('.ui-dialog').find('.ui-dialog-titlebar-close').hide();
            }
            
            return $el;
        }

    };


    // Visible functions


    /**
     * @desc Create alert dialog
     * @param {object|string} options (or alert text)
     * @returns {jquery}
     * @methodOf jQuery
     * @name alert
     */
    $.alert = function(options){
        if ( typeof(options) == 'string' ) options = {content: options};
        return new Dialog()
            .extend({
                title: 'Alert',
                btnCancel: false,
            })
            .extend(options)
            .create();
    }


    /**
     * @desc Create confirm dialog
     * @param {object|string} options (or confirm text)
     * @returns {jquery}
     * @methodOf jQuery
     * @name confirm
     */
    $.confirm = function(options){
        if ( typeof(options) == 'string' ) options = {content: options};
        return new Dialog()
            .extend({title: 'Confirm'})
            .extend(options)
            .create();
    }




    /**
     * @desc Create prompt dialog
     * @param {object|string} options (or title text). Defines extra option
     * 'text', text prepended befor input element.
     * @returns {jquery}
     * @methodOf jQuery
     * @name prompt
     */
    $.prompt = function(options){
        if ( typeof(options) == 'string' ) options = {title: options};

        // Create input element
        var $input = $('<input>')
            .attr('type', 'text')
            .addClass('text ui-widget-content ui-corner-all');
        var text = options.text ? options.text : '';
        var $content = $('<div>').html(text).append($input);

        return new Dialog()
            .extend({
                title: 'Prompt',
                content: $content,
                btnOkClick: function(event){
                    var $this =  $(this);
                    $this.unbind("dialogclose");
                    $this.dialog("close");
                    var data = $this.find('.text').val();
                    
                    if ( options.fnCallback ) {
                        options.fnCallback.call(this, data);
                    }
                    if ( options.fnAlways ) {
                        options.fnAlways.call(this, data);
                    }
                }
            })
            .extend(options)
            .create();
    }


    /**
     * @desc Create password prompt dialog
     * @param {object|string} options
     * @returns {jquery}
     * @see jQuery#prompt
     * @methodOf jQuery
     * @name promptPassword
     */
    $.promptPassword = function(options){
        // Create input element
        var $input = $('<input>').attr('type', 'password')
            .addClass('text ui-widget-content ui-corner-all');
        var text = options.text ? options.text : '';
        options.content = $('<div>').html(text).append($input);
        return $.prompt(options);
    }


    /**
     * @desc Create login dialog
     * @param {object} options. Defines extra options: 'uname' - name of user,
     * 'unameDesc' - text before user name input, 'pswdDesc' - text before
     * password input.
     * @returns {jquery}
     * @methodOf jQuery
     * @name promptLogin
     */
    $.promptLogin = function(options){
        // Create form
        var uname = options.uname ? options.uname : '';
        var unameDesc = options.unameDesc ? options.unameDesc : 'Username';
        var pswdDesc = options.pswdDesc ? options.pswdDesc : 'Password';
        var $unameEl = $('<input>').attr({'type': 'text', 'value': uname})
            .addClass('uname ui-widget-content ui-corner-all'); 
        var $pswdEl = $('<input>').attr({'type': 'password'})
            .addClass('pswd ui-widget-content ui-corner-all'); 
        var $content = $('<div>')
            .append(unameDesc)
            .append($unameEl)
            .append(pswdDesc)
            .append($pswdEl);

        return new Dialog()
            .extend({
                title: 'Unauthorized',
                height: 270,
                width: 450,
                closeOnEscape: false,
                showCloseIcon: false,
                btnCancel: false,
                content: $content,
                btnOkClick: function(event){
                    var $this =  $(this);
                    $this.unbind("dialogclose");
                    $this.dialog("close");
                    var data = {
                        uname: $this.find('.uname').val(),
                        pswd: $this.find('.pswd').val()
                    };
                    
                    if ( options.fnCallback ) {
                        options.fnCallback.call(this, data);
                    }
                    if ( options.fnAlways ) {
                        options.fnAlways.call(this, data);
                    }
                }
            })
            .extend(options)
            .create();
    }


    /**
     * @desc Create upload dialog
     * @param {object} options. Defines extra options: 'upload_path' - required,
     * 'upload_max_filesize' - filesize in bytes, 'upload_data' - object of
     * additional data to be included in the request.
     * @returns {jquery}
     * @methodOf jQuery
     * @name promptUpload
     */
    $.fileUpload = function(options){

        // Create target iframe
        var targetIframe = $('<iframe>')
            .attr({'name':'upload_target_frame'})
            .css('display', 'none')
            .appendTo($('body'))
            .one('load', function(){
                var $this = $(this);
                var txt = $this.contents().find('textarea');
                if ( txt.length != 0 ) {
                    var response = txt.val();
                } else {
                    var response = $this.contents().text();
                }
                $this.remove();
                // Send response data to callback                    
                if (options.fnCallback) {
                    options.fnCallback.call(this, response);
                }
            });


        // Create upload form
        var $form = $('<form/>')
            .attr({
                enctype: 'multipart/form-data',
                method: 'POST',
                action: options.upload_path,
                target: 'upload_target_frame'
            })
            .html(options.text ? options.text : '')
            .append($('<p>').text('Att ladda upp filer stöds för tillfället bara med Google chrome'))
            .append(
                $('<input>')
                    .attr({
                        'type': 'file',
                        'name': 'file'
                    })
                    .addClass('file ui-widget-content ui-corner-all')
            );

        // Append custom data to form
        if ( options.upload_data ) {
            $.each(options.upload_data, function(key, val){
                $form.append(
                    $('<input/>').attr({
                        'name': key,
                        value: val,
                        type: 'hidden'
                    })
                );
            });
        }
        
        // Append max filesize restriction
        if ( options.upload_max_filesize ) {
            $form.append(
                $('<input/>').attr({
                    type:'hidden',
                    'name':'MAX_FILE_SIZE',
                    value: options.upload_max_filesize,
                })            
            );
        }

        return new Dialog()
            .extend({
                title: 'Upload',
                height: 220,
                width: 450,
                content: $form,
                btnOk: 'Send',
                'modal': false,
                btnOkClick: function(event){
                    var $this =  $(this);
                    $this.unbind("dialogclose");
                    $this.dialog("close");
                    $form.submit();
                    if ( options.fnAlways ) {
                        options.fnAlways.call(this);
                    }
                }
            })
            .extend(options)
            .create();
    }

})(jQuery);
