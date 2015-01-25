/**
 * @fileOverview Javascript client for mreg
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */

window.MREG = window.MREG || {};


$(document).ready(function(){
    $.jsclient.init({
        'servicePath': '',
        'templates': MREG.templates,
        'ready': MREG.init,
        'fnWarning': MREG.notifyError
    });
});


/**
 * Array of templates to load on jsclient init
 *
 * @var {array}
 */
MREG.templates = [
    {href:'tmpl/README.html'},
    {href:'tmpl/HELP.html'},

    {href:'tmpl/worklist.html'},
    {href:'tmpl/search.html'},
    {href:'tmpl/toolbar.html'},

    {href:'tmpl/edit-mail.html', regexp:/mails\/\d+\/edit$/},
    {href:'tmpl/edit-phone.html', regexp:/phones\/\d+\/edit$/},
    {href:'tmpl/edit-address.html', regexp:/addresses\/\d+\/edit$/},
    {href:'tmpl/edit-faction.html', regexp:/factions\/\d+\/edit$/},
    {href:'tmpl/edit-member.html', regexp:/members\/\d+\/edit$/},
    {href:'tmpl/edit-sysgroup.html', regexp:/sys_groups\/[^\/]+\/edit$/},
    {href:'tmpl/edit-user.html', regexp:/users\/[^\/]+\/edit$/},
    {href:'tmpl/edit-invoice.html', regexp:/member-invoices\/\d+\/edit$/},

    {href:'tmpl/create-contact.html'},
    {href:'tmpl/create-main-entity.html'},
    {href:'tmpl/create-accountant.html'},

    {href:'tmpl/unlink-member.html'},
    {href:'tmpl/unlink-faction.html'},

    {href:'tmpl/list-contacts.html'},
    {href:'tmpl/list-meta.html'},

    {href:'tmpl/table-invoices.html'},
    {href:'tmpl/table-revisions.html'},
    {href:'tmpl/table-factions.html'},
    {href:'tmpl/table-members.html'},
    {href:'tmpl/table-history.html', regexp:/\/history$/},

    {href:'tmpl/display-workplace.html', regexp:/workplaces\/\d+$/},
    {href:'tmpl/display-sysgroup.html', regexp:/sys_groups/},
    {href:'tmpl/display-user.html', regexp:/users/},
    {href:'tmpl/display-invoice.html', regexp:/member-invoices\/\d+$/},

    {href:'tmpl/display-admin-panel.html', renderCallback:function($el){
        $el.find('.do-empty-server-cache').on('click.mreg', function(){
            $.ajax({
                'type': 'POST',
                'url': $.jsclient.getSetting('links').clearCache
            }).done(function(){
                MREG.notify('Cache tömdes');
            });
        });
        $el.find('.do-create-accountant').on('click.mreg', function(){
            MREG.dialog.createAccountant(
                $.jsclient.getSetting('links').createAccountant
            );
        });
    }},

    {href:'tmpl/accountant-bill.html'},
    {href:'tmpl/accountant-export.html'},

    {href:'tmpl/display-accountant.html', renderCallback:function($el){
        // Export templates
        $el.find('.do-export-templates').on('click.mreg', function(){
            $.ajax({
                'type': 'GET',
                'url': $.jsclient.getSetting('links').templates,
                'cache': false
            });
        });
        
        // Export accounts
        $el.find('.do-export-accounts').on('click.mreg', function(){
            $.ajax({
                'type': 'GET',
                'url': $.jsclient.getSetting('links').accounts,
                'cache': false
            });
        });
        
        // Import templates
        $el.find('.do-import-templates').on('click.mreg', function(){
            $.fileUpload({
                title: 'Ladda upp konteringsmallar',
                btnOk: 'Skicka',
                btnCancel: 'Avbryt',
                upload_path: $.jsclient.getSetting('links').templates,
                upload_max_filesize: $.jsclient.getSetting('upload_max_filesize'),
                upload_data: {
                    fingerprint: $.jsclient.getSetting('ajax').data.fingerprint
                },
                fnCallback: function(msg){
                    MREG.notify(msg + ' Uppdatera för att visa ändringar.');
                }
            });
        });
        
        // Import accounts
        $el.find('.do-import-accounts').on('click.mreg', function(){
            $.fileUpload({
                title: 'Ladda upp kontoplan',
                btnOk: 'Skicka',
                btnCancel: 'Avbryt',
                upload_path: $.jsclient.getSetting('links').accounts,
                upload_max_filesize: $.jsclient.getSetting('upload_max_filesize'),
                upload_data: {
                    fingerprint: $.jsclient.getSetting('ajax').data.fingerprint
                },
                fnCallback: function(msg){
                    MREG.notify(msg + ' Uppdatera för att visa ändringar.');
                }
            });
        });
        
        // Bill members
        $el.find('.do-bill-members').on('click.mreg', function(){
            MREG.dialog.bill($.jsclient.getSetting('links').billMembers);
        });

        // Print invoices to csv
        $el.find('.do-print-to-csv').on('click.mreg', function(){
            $.ajax({
                'type': 'POST',
                'url': $.jsclient.getSetting('links').printInvoices
            });
        });
        
        // Export invoices
        $el.find('.do-export-invoices').on('click.mreg', function(){
            MREG.dialog.exportInvoices(
                $.jsclient.getSetting('links').exportInvoices
            );
        });
    }},


    {href:'tmpl/display-member.html', regexp:/members\/\d+$/, renderCallback:function($el){
        // History table
        var sHistoryUrl = $el.find('input[name=mreg-history-url]').val();
        var nHistoryTarget = $el.find('.mreg-history-content');
        $el.find('.mreg-show-history').on('click.mreg.ajax', function(){
            var $that = $(this);
            $.ajax({
                'type': 'GET',
                'url': sHistoryUrl
            }).done(function(oData){
                nHistoryTarget.html($.tmplManager.render(sHistoryUrl, oData));
                // Swap click handler
                $that.off('click.mreg.ajax');
                MREG.toggleSection.call($that);
                $that.on('click.mreg.subsection', MREG.toggleSection);
            });
        });

        // Create editable factions table
        MREG.createEditableTable({
            'element': $el.find('.mreg-factions-table'),
            'name': $el.find('.name').text().trim(),
            'perfix': 'factions',
            'collectionUrl': $el.find('input[name=mreg-factions-url]').val(),
            'collectionType': /^type_faction_/,
            'unlinkTemplate': 'tmpl/unlink-member.html',
        });
    }},

    {href:'tmpl/display-faction.html', regexp:/factions\/\d+/, renderCallback:function($el){
        var sHistoryUrl = $el.find('input[name=mreg-history-url]').val();
        var nHistoryTarget = $el.find('.mreg-history-content');
        var sMembersUrl = $el.find('input[name=mreg-members-url]').val();
        var nMembersTarget = $el.find('.mreg-members-content');

        // History table
        $el.find('.mreg-show-history').on('click.mreg.ajax', function(){
            var $that = $(this);
            $.ajax({
                'type': 'GET',
                'url': sHistoryUrl
            }).done(function(oData){
                nHistoryTarget.html($.tmplManager.render(sHistoryUrl, oData));
                // Swap click handler
                $that.off('click.mreg.ajax');
                MREG.toggleSection.call($that);
                $that.on('click.mreg.subsection', MREG.toggleSection);
            });
        });

        // Members table
        $el.find('.mreg-show-members').on('click.mreg.ajax', function(){
            var $that = $(this);
            $.ajax({
                'type': 'GET',
                'url': sMembersUrl
            }).done(function(oData){
                nMembersTarget.html($.tmplManager.renderUsingName('tmpl/table-members.html', oData));
                // Create editable members table
                MREG.createEditableTable({
                    'element': nMembersTarget,
                    'name': $el.find('.name').text().trim(),
                    'perfix': 'members',
                    'collectionUrl': sMembersUrl,
                    'collectionType': /^type_member/,
                    'unlinkTemplate': 'tmpl/unlink-member.html',
                });
                // Swap click handler
                $that.off('click.mreg.ajax');
                MREG.toggleSection.call($that);
                $that.on('click.mreg.subsection', MREG.toggleSection);
            });
        });

        // Create editable factions table
        MREG.createEditableTable({
            'element': $el.find('.mreg-factions-table'),
            'name': $el.find('.name').text().trim(),
            'perfix': 'factions',
            'collectionUrl': $el.find('input[name=mreg-factions-url]').val(),
            'collectionType': /^type_faction_/,
            'unlinkTemplate': 'tmpl/unlink-faction.html',
        });
    }}
];


/**
 * Last step of the bootstrap process. Called when jsclient is ready
 *
 * @return {void}
 */
MREG.init = function(){
    MREG.clipboard.init();
    MREG.tabsets.init();

    MREG.toolbar.init({
        'logoutPath': this.getSetting('links').logout,
        'user': this.getSetting('user'),
    });

    // Open entities from MregDataTables
    MregDataTable.fnDefaultOpenEntity = function(oMregEntity){
        MREG.openEntity(oMregEntity.uri);
    };

    // Notify last login
    MREG.notify('Föregående aktivitet för ' + humaneDate(new Date(this.getSetting('user').loginBeforeLast)));

    // Bind ajax notify
    $('body')
        .ajaxStart(function(){
            MREG.notify('Hämtar data', 'mreg-ajax-loading', true);
        })
        .ajaxStop(function(){
            MREG.unnotify('mreg-ajax-loading');
        });


    // <UPLOAD TEST>
    // TODO: fungerar i chrome, men inte i firefox
    var that = this;
    $('*').live('keydown', 'Alt+Ctrl+U', function(){
        $.fileUpload({
            title: 'Ladda upp fil',
            btnOk: 'Skicka',
            btnCancel: 'Avbryt',
            upload_path: that.getSetting('links').upload,
            upload_max_filesize: that.getSetting('upload_max_filesize'),
            upload_data: {
                fingerprint: that.getSetting('ajax').data.fingerprint
            },
            fnCallback: function(data){
                data = $.parseJSON(data);
                if ($.isPlainObject(data)) {
                    $.each(data, function(name, status){
                        if (status === true) {
                            MREG.notify(name + " laddades upp");
                        } else {
                            MREG.notifyError(name + ": " + status);
                        }
                    });
                } else {
                    MREG.notifyError(data);
                }
            }
        });
        return false;
    }); // </UPLOAD TEST>

    // Set render callback
    $.tmplManager.globalRenderCallback = MREG.renderCallback;

    // Items on init
    //MREG.openEntity('~/factions/2');
    //MREG.openEntity('~/members/525');
    //MREG.openEntity('~/member-invoices/34');
    MREG.tabsets.left.newSearch();
    MREG.tabsets.right.createTab({
        title: 'README',
        content: $.tmplManager.renderUsingName('tmpl/README.html'),
    });
}



/**
 * Post-hook to process all rendered templates
 * 
 * @param {object} $el The rendered jQuery element
 *
 * @return {void}
 */
MREG.renderCallback = function($el){
    // Hack to delete contacts, must get using rcache
    $el.find('.mreg-delete-contact')
        .addClass('mreg-delete')
        .on('click.mreg', function(oEvent){
            var $anchor = $(this);
            var sName = $anchor.attr('data-name');
            var sUrl = $anchor.attr('href');
            var fnSuccess = function(){
                $anchor.parent('li').slideUp(400, function(){
                    $(this).remove();
                });
            }
            if ($.rcache.has(sUrl)) {

                MREG.deleteEntity(sName, sUrl, fnSuccess);
                return false;
            }
            $.rcache.item(sUrl).get().done(function(){
                MREG.deleteEntity(sName, sUrl, fnSuccess);
            });

            return false;
        });

    // Edit contacts
    $el.find('.mreg-edit-contact')
        .addClass('mreg-edit')
        .on('click.mreg', MREG.dialog.edit);

    // Create new contacts
    $el.find('.mreg-create-contact').on('click.mreg', MREG.dialog.createContact);

    // Add jquery ui classes to mreg classes
    $el.find('.mreg-strong').addClass('ui-widget-content ui-corner-all');
    $el.find('.mreg-delete').addClass('ui-icon ui-icon-close');
    $el.find('.mreg-edit').addClass('ui-icon ui-icon-pencil');

    // Prettyprint dates
    $el.find('time').humaneDates();

    // Bind getlinks
    $el.find('.getlink').on('click.mreg', function(){
        MREG.openEntity($(this).attr('href'));
        return false;
    });

    // Bind toggle section buttons
    $el.find('.mreg-toggle-section')
        .on('click.mreg.subsection', MREG.toggleSection);

    // Bind float parent buttons
    $el.find('.mreg-float-parent').on('click', function(){
        $parent = $(this).parent();
        if ($parent.css('float') == 'left') {
            $parent.css({'float':'none','clear':'both'});       
        } else {
            window.setTimeout(
                "$parent.css({'float':'left','clear':'none'})",
                500
            );
        }
    });

    // Create simple datatables
    $el.find('table.mreg-table-simple').dataTable({
        "bJQueryUI": true,
        "sDom": '<ip>t',
        "oLanguage": {"sUrl": 'lib/datatables.swedish.lang'},
    });
    
    // Create accordions
    $el.find(".mreg-accordion").accordion({
        header: "h3",
        collapsible: false,
        autoHeight: false
    });

    // Create datepickers
    $el.find('.mreg-datepicker').datepicker();
    $el.find('.mreg-datepicker-today').datepicker().datepicker('setDate', '+0d');
    
    // Create bValidator om forms
    $el.find('form.mreg-validate').bValidator({
        'offset': {x:-40, y:-4},
        'validateOnSubmit': false,
        'lang': 'sv',
        'errorMessages': {
            'sv': {
                'required': 'Obligatoriskt fält',
                'email': 'Ange giltig mailadress',
                'digit': 'Endast siffror tillåtna',
                'number': 'Ange giltig nummer',
                'date': 'Ange datum som {0}',
                'alphanum': 'Endast alfanumeriska tecken',
                'alpha': 'Endast bokstäver tillåtna',
                'url': 'Ange giltig url',
                'maxlength': 'Max {0} tecken',
                'minlength': 'Minst {0} tecken',
                'validAmount': 'Ange giltig summa',
                'validPlusgiro': 'Ange giltigt plusgironummer',
                'validBankgiro': 'Ange giltigt bankgironummer',
                'validPersonalId': 'Ange giltigt personnummer',
                'validAccount': 'Ange giltigt kontonummer'
            }
        }
    });
}


// bValidator custom validators

function validAmount(amount){
    var pattern = /^(\d|\s)+,?\d{0,2}$/;
    return pattern.test(amount);
}

function validPlusgiro(number){
    var pattern = /^(\d\s?){1,7}-\d$/;
    return pattern.test(number);
}

function validBankgiro(number){
    var pattern = /^(\d\s?){3,4}-(\d\s?){4}$/;
    return pattern.test(number);
}

function validPersonalId(number){
    var pattern = /^\d{6}(\d\d)?(-|\+)(x|\d){4}$/;
    return pattern.test(number);
}

function validAccount(number){
    var pattern = /^\d{4},\d*$/;
    return pattern.test(number);
}


/**
 * Display notification message
 * Optionally specify message state, enables additional css styling and unnotify
 *
 * @param {string} message
 * @param {string} state
 * @param {bool} keep
 */
MREG.notify = function(message, state, keep){
    state = state || '';
    $('#notify-wrap').notify({
        'text': message,
        'remove': !keep,
        'state': state + ' ui-state-highlight'
    });
};


/**
 * Display error notification message
 * Optionally specify message state, enables additional css styling and unnotify
 *
 * @param {string} message
 * @param {string} state
 */
MREG.notifyError = function(message, state){
    state = state || '';
    $('#notify-wrap').notify({
        'text': message,
        'remove': false,
        'icon': 'ui-icon-alert',
        'state': state + ' ui-state-error'
    });
};


/**
 * Remove notification messages based on state
 *
 * @param {string} state
 */
MREG.unnotify = function(state){
    $('#notify-wrap .' + state).click();
};


/**
 *Toggle visibility of .mreg-section siblings
 */
MREG.toggleSection = function(){
    $that = $(this);
    $that.siblings('.mreg-section').slideToggle('50', function(){
        if ($(this).is(":visible")) {
            $that.text('-');
        } else {
            $that.text('+');
        }
    });

    return false;
};


/**
 * @desc Get entity from service and display in new tab
 *
 * @param {string} sUrl
 *
 * @returns {void}
 */
MREG.openEntity = function(sUrl){
    $.rcache.item(sUrl).get().done(function(oData){
        var sOriginalEtag = oData.etag;

        // Create tab
        var tab = MREG.tabsets.right.createTab({
            'title': oData.title,
            'content': $.tmplManager.render(sUrl, oData),
            'show': function(){
                MREG.toolbar.enableActionButtons(this);
                document.title = oData.title + " | Mreg";
            }
        });

        // Get MregEntity from tab contents
        tab.getMregEntity = function(){
            return new MregEntity({
                'title'      : this.$panel.find('[name="mreg-entity-title"]').val(),
                'id'         : this.$panel.find('[name="mreg-entity-id"]').val(),
                'type'       : this.$panel.find('[name="mreg-entity-type"]').val(),
                'uri'        : this.$panel.find('[name="mreg-entity-uri"]').val(),
                'description': this.$panel.find('[name="mreg-entity-description"]').val()
            });
        }

        // Update tab using mreg-entry-url
        tab.update = function(){
            tab.__skipNextWarning = true;
            $.rcache.item(sUrl).forceGet().done(function(oData){
                sOriginalEtag = oData.etag;
                tab.setContent($.tmplManager.render(sUrl, oData));
                tab.setOutdated(false);
                tab.setAltered(false);
            });
        };

        // Register onWrite callback
        $.rcache.item(sUrl).onWrite(function(oNewData, sNewEtag){
            if (!tab.exists()) {
                return;
            }
            if (tab.__skipNextWarning == true) {
                tab.__skipNextWarning = false;
            } else if (sNewEtag != sOriginalEtag) {
                tab.setOutdated(true);
                if (oNewData.title) {
                    MREG.notifyError('<strong>'+oNewData.title+'</strong> har ändrats. Uppdatera för att få senaste versionen.');
                }
            }
        });

        // Register onRemove callback
        /*$.rcache.item(sUrl).onRemove(function(oRemovedData){
            if (!tab.exists()) {
                return;
            }
            if (tab.__skipNextWarning == true) {
                tab.__skipNextWarning = false;
            } else {
                tab.setOutdated(true);
                MREG.notifyError('<strong>'+oRemovedData.title+'</strong> har tagits bort från minnet. Uppdatera för att arbeta vidare.');
            }
        });*/
    });
};


/**
 * @desc Delete entity
 *
 * @param {string} sName
 *
 * @param {string} sUrl
 *
 * @param {function} fnSuccess On success callback
 *
 * @returns {void}
 */
MREG.deleteEntity = function(sName, sUrl, fnSuccess){
    if (confirm('Vill du verkligen radera '+sName+'? Kan inte ångras.')) {
        $.rcache.item(sUrl).del().done(function(){
            if (fnSuccess) {
                fnSuccess();
            }
            MREG.notify(sName + " raderades");
        });
    }
}


/**
 * @desc Create datatable with editing callbacks
 *
 * @returns {MregDataTable}
 */
MREG.createEditableTable = function(options){
    options = options || {};

    var oTable = new MregDataTable(
        options.element,
        {
            'sName': options.prefix + '_' + options.name,
            "aButtons": ["export", "import"],
        }
    );

    // Add to table should trigger POST request
    oTable.fnAddEntity = function(oMregEntity){
        if (!oMregEntity.type.match(options.collectionType)) {
            MREG.notifyError("Kunde ej skapa länk mellan "+oMregEntity.title+ " och "+options.name+", felaktig typ");
        } else {
            if (confirm('Skapa länk mellan '+options.name+' och '+oMregEntity.title+'?')) {
                $.ajax({
                    'type': 'POST',
                    'url': options.collectionUrl,
                    'data': {'id': oMregEntity.id}
                }).done(function(){
                    // Add item to datatable
                    oTable.__proto__.fnAddEntity.call(oTable, oMregEntity);
                    // Noify success
                    MREG.notify("Skapade länk mellan " +options.name+' och '+oMregEntity.title);
                });
            }
        }
    };

    // Delete from table should trigger PUT request
    oTable.fnDeleteRow = function(nRow){
        var oMregEntity = new MregEntity(this.oDataTable.fnGetData(nRow));

        var onSubmit = function(oData){
            $.ajax({
                'type': 'PUT',
                'url': options.collectionUrl+'/'+oMregEntity.id,
                'data': oData
            }).done(function(){
                // Remove item from datatable
                oTable.__proto__.fnDeleteRow.call(oTable, nRow);
                MREG.notify("Tog bort länk mellan "+options.name+' och '+oMregEntity.title);
            });
        };

        MREG.dialog.newDialog({
            'template': options.unlinkTemplate,
            'okButton': 'Skapa',
            'dialog': {
                'title': 'Ta bort länk mellan '+options.name+' och '+oMregEntity.title
            },
            'onSubmit': onSubmit
        });
    };
    
    return oTable;
}
