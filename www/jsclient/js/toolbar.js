/**
 * @fileOverview MREG.toolbar
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */

window.MREG = window.MREG || {};
window.MREG.toolbar = window.MREG.toolbar || {};


/**
 * Init toolbar
 *
 * Options are:
 * template: name of toolbar template
 * plus any values template needs to render
 *
 * Creates two public methods:
 * disableActionButtons: disables tab bound actions
 * enableActionButtons: takes a Tab object and bind action buttons to tab
 *
 * @param {object} options
 */
MREG.toolbar.init = function(options){
    options = options || {};
    options.template = options.template || 'tmpl/toolbar.html';
    options.user = options.user || {};

    var toolbar = $.tmplManager.renderUsingName(options.template, options);
    var helpTab, accountantTab, adminTab;

    toolbar.toolbar();

    toolbar.find('.create-new-search').on('click.mreg', MREG.tabsets.left.newSearch);
    toolbar.find('.create-new').on('click.mreg', MREG.dialog.createMain);
    toolbar.find('.display-clipboard').on('click.mreg', MREG.clipboard.show);

    // Display accountant
    toolbar.find('.open-accountant').on('click.mreg', function(){
        if (accountantTab && accountantTab.exists()) {
            accountantTab.select();
        } else {
            $.ajax({
                'type': 'GET',
                'url': '~/accountant'
            }).done(function(data){
                accountantTab = MREG.tabsets.right.createTab({
                    'title': data.title,
                    'content': $.tmplManager.renderUsingName(
                        'tmpl/display-accountant.html',
                        data
                    )
                });
            });
        }
    });

    // Display admin panel
    toolbar.find('.open-admin').on('click.mreg', function(){
        if (adminTab && adminTab.exists()) {
            adminTab.select();
        } else {
            adminTab = MREG.tabsets.right.createTab({
                'title': 'Administrationspanel',
                'content': $.tmplManager.renderUsingName(
                    'tmpl/display-admin-panel.html'
                )
            });
        }
    });

    // Display user "my" page
    toolbar.find('.display-user').on('click.mreg', function(){
        MREG.openEntity($(this).attr('href'));
        return false;
    });

    // Create worklist
    toolbar.find('.create-new-worklist').on('click.mreg', function(){
        MREG.tabsets.left.newWorklist();
    });

    // Display help
    toolbar.find('.display-help').on('click.mreg', function(){
        if (helpTab && helpTab.exists()) {
            helpTab.select();
        } else {
            helpTab = MREG.tabsets.right.createTab({
                'title': 'MANUAL',
                'content': $.tmplManager.renderUsingName('tmpl/HELP.html')
            });
        }

        return false;
    
    });

    // Logout
    toolbar.find('.do-logout').on('click.mreg', function(){
        $.ajax({
            'type': 'GET',
            'url': $(this).attr('href')
        }).done(function(){
            document.location.hash = '';
            document.location.reload();
        });
        
        return false;
    });

    // Public method: Disable and unbind toolbar action buttons
    toolbar.disableActionButtons =  function(){
        this.find('.mreg-action-button').button('disable').off('click.mreg');
    };

    // Public method: Bind toolbar action buttons to Tab
    toolbar.enableActionButtons = function(oTab){
        // oTab must be an instance of Tab
        if (oTab.constructor.name !== 'Tab') {
            throw {
                'name': 'TypeError',
                'message': 'oTab must be a Tab instance'
            };
        }

        // Save tab contents using PUT request
        this.find('.action-edit').button('enable').on('click.mreg', function(oEvent){
            var oMregEntity = oTab.getMregEntity();
            MREG.dialog.edit(oEvent, oMregEntity.title, oMregEntity.uri);
        });

        // Copy tab contents to clipboard
        this.find('.action-copy').button('enable').on('click.mreg', function(){
            var oMregEntity = oTab.getMregEntity();
            MREG.clipboard.fnAddEntity(oMregEntity);
            MREG.notify('Kopierade '+oMregEntity.title+' till ' + MREG.clipboard.getName());
        });

        // Update tab contents using unconditional GET request
        this.find('.action-update').button('enable').on('click.mreg', function(){
            oTab.update();
        });

        // Delete tab using DELETE request
        this.find('.action-delete').button('enable').on('click.mreg', function(){
            var oMregEntity = oTab.getMregEntity();
            MREG.deleteEntity(oMregEntity.title, oMregEntity.uri, function(){
                oTab.remove();
            });
        });
    };
    
    toolbar.disableActionButtons();

    MREG.toolbar = toolbar;
    MREG.toolbar.prependTo('body');
};
