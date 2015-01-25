/**
 * @fileOverview Utility functions for mreg.js
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */

window.MREG = window.MREG || {};
window.MREG.dialog = window.MREG.dialog || {};


/**
 * @desc Fire dialog to create a new accountant
 *
 * @param {string} url
 *
 * @returns {void}
 */
MREG.dialog.createAccountant = function(url){
    var onSubmit = function(oSaveData){
        $.ajax({
            'type': 'POST',
            'url': url,
            'data': oSaveData
        }).done(MREG.notify);
    };

    MREG.dialog.newDialog({
        'template': 'tmpl/create-accountant.html',
        'okButton': 'Skapa',
        'dialog': {
            'title': 'Skapa ny bokhållare'
        },
        'onSubmit': onSubmit
    });
};


/**
 * @desc Fire dialog to bill members
 *
 * @param {string} url
 *
 * @returns {void}
 */
MREG.dialog.bill = function(url){
    var onSubmit = function(oSaveData){
        $.ajax({
            'type': 'POST',
            'url': url,
            'data': oSaveData
        }).done(MREG.notify);
    };

    MREG.dialog.newDialog({
        'template': 'tmpl/accountant-bill.html',
        'okButton': 'Fakturera',
        'dialog': {
            'title': 'Skapa nya fakturor'
        },
        'onSubmit': onSubmit
    });
};


/**
 * @desc Fire dialog to export member invoices
 *
 * @param {string} url
 *
 * @returns {void}
 */
MREG.dialog.exportInvoices = function(url){
    var onSubmit = function(oSaveData){
        $.ajax({
            'type': 'POST',
            'url': url,
            'data': oSaveData
        }).done(MREG.notify);
    };

    MREG.dialog.newDialog({
        'template': 'tmpl/accountant-export.html',
        'okButton': 'Fakturera',
        'dialog': {
            'title': 'Exportera betalda fakturor till bokföring'
        },
        'onSubmit': onSubmit
    });
};


/**
 * @desc Fire dialog to edit and save entity
 *
 * If any params are void the corresponding value is read from bound element
 * data-name and href attributes
 *
 * @param {Event} event
 *
 * @param {string} sName
 *
 * @param {string} sUrl
 *
 * @returns {bool} Always returns false to stop link navigation
 */
MREG.dialog.edit = function(event, sName, sUrl){
    var $this = $(this);
    if (! sName) {
        sName = $this.attr('data-name');
    }
    if (! sUrl) {
        sUrl = $this.attr('href');
    }

    var onSubmit = function(oSaveData){
        $.rcache.item(oSaveData.link).put(oSaveData).done(function(msg){
            if (msg) {
                MREG.notify(msg);
            } else {
                MREG.notify(oSaveData.title+" sparades");
            }
            // Update tab
            if (event) {
                try {
                    var oTab = MREG.tabsets.right.getTab(
                        $(event.target).parents('.ui-tabs-panel').first().attr('id')
                    );
                } catch (e) {
                    var oTab = MREG.tabsets.right.getActiveTab();
                }
                oTab.update();
            }
        });
    };

    $.rcache.item(sUrl).get().done(function(oData){
        MREG.dialog.newDialog({
            'element': $.tmplManager.render(sUrl + '/edit', oData),
            'okButton': 'Spara/Validera',
            'dialog': {
                'title': 'Redigera ' + sName
            },
            'onSubmit': onSubmit
        });
    });
    
    return false;
}


/**
 * Fire dialog to create a new main entity
 *
 * @returns {bool} Always returns false to stop link navigation
 */
MREG.dialog.createMain = function(){
    var onSubmit = function(oData){
        $.rcache.item(oData.url).post(oData).done(function(data, textStatus, jqXHR){
            MREG.notify("Posten skapades");
            // Display created entity
            MREG.openEntity(jqXHR.getResponseHeader('Content-Location'));
        });
    };

    MREG.dialog.newDialog({
        'template': 'tmpl/create-main-entity.html',
        'formSelector': '.ui-accordion-content-active form',
        'okButton': 'Skapa',
        'dialog': {
            'title': 'Skapa ny'
        },
        'onSubmit': onSubmit
    });

    return false;
};


/**
 * Fire dialog to create a new contact
 *
 * POST urls are read from data-addresses, data-phones and data-mails attributes
 *
 * @param {Event} event Event that triggered create
 *
 * @returns {bool} Always returns false to stop link navigation
 */
MREG.dialog.createContact = function(event){
    var $this = $(this);

    var onSubmit = function(oData){
        $.rcache.item(oData.url).post(oData).done(function(){
            MREG.notify(oData.name+" skapades");
            // Update tab
            MREG.tabsets.right.getTab(
                $(event.target).parents('.ui-tabs-panel').first().attr('id')
            ).update();
        });
    };

    MREG.dialog.newDialog({
        'template': 'tmpl/create-contact.html',
        'templateVars': {
            'addresses': $this.attr('data-addresses'),
            'phones': $this.attr('data-phones'),
            'mails': $this.attr('data-mails')
        },
        'okButton': 'Skapa',
        'dialog': {
            'title': 'Skapa ny kontakt'
        },
        'onSubmit': onSubmit
    });

    return false;
};


/**
 * @desc Create and fire edit dialog
 *
 * @returns {object} jQuery-ui dialog
 */
MREG.dialog.newDialog = function(opts){
    opts = opts || {};
    opts.template = opts.template || '';
    opts.templateVars = opts.templateVars || {};
    opts.formSelector = opts.formSelector || 'form';
    opts.element = opts.element || $.tmplManager.renderUsingName(opts.template, opts.templateVars);

    // Callback on submit
    var fnSubmit = function(){
        // Validate form
        $form = opts.element.find(opts.formSelector);
        $form.data('bValidator').validate();
        // If form is valid call callback with data
        if ($form.data('bValidator').isValid()) {
            var oData = {};
            $.each($form.serializeArray(), function(key, obj){
                oData[obj.name] = obj.value;
            });
            opts.element.dialog('close');
            opts.onSubmit(oData);
       }
    };
    
    // Default settings
    opts.cancel = opts.cancel || 'Avbryt';
    opts.submit = opts.submit || 'OK';
    opts.onSubmit = opts.onSubmit || function(oData){};
    opts.dialog = opts.dialog || {};
    opts.dialog.width = opts.dialog.width || 550;
    opts.dialog.buttons = opts.dialog.buttons || [
        {
            'text': opts.cancel,
            'click': function(){
                opts.element.dialog('close');
            }
        },
        {
            'text': opts.submit,
            'click': fnSubmit
        }
    ];

    // Create jquery-ui dialog
    opts.element.dialog(opts.dialog).on("dialogclose", function(){
        opts.element.dialog('destroy').remove();
    });
    
    return opts.element;
};
