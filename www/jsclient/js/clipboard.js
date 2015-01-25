/**
 * @fileOverview MREG.clipboard is a MregDataTable instance wrapped in a
 * jquery-ui dialog
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */

window.MREG = window.MREG || {};
window.MREG.clipboard = window.MREG.clipboard || {};


/**
 * Init clipboard
 *
 * Appends public methods show() and getName()
 *
 * Options are:
 * template: name clipboad template
 * templateVars: any values needed to render template
 * name: name of clipboard
 * buttons: array of MregDataTable buttons
 * dialog: jquery-ui dialog options
 *
 * @param {object} opts
 */
MREG.clipboard.init = function(opts){
    opts = opts || {};
    opts.template = opts.template || 'tmpl/worklist.html';
    opts.templateVars = opts.templateVars || {};
    opts.name = opts.name || 'Urklipp';
    opts.buttons = opts.buttons || ["csv", "pdf"];
    opts.dialog = opts.dialog || {};
    opts.dialog.title = opts.dialog.title || opts.name;
    opts.dialog.width = opts.dialog.width || '50%';
    opts.dialog.autoOpen = opts.dialog.autoOpen || false;

    var clipboard = new MregDataTable(
        $.tmplManager.renderUsingName(opts.template, opts.templateVars),
        {
            "sName": opts.name,
            "aButtons": opts.buttons,
        }
    );

    clipboard.container.dialog(opts.dialog);

    // Display clipboard
    clipboard.show = function(){
        clipboard.container.dialog('open');
    };

    // Get name of clipboard
    clipboard.getName = function(){
        return opts.name;
    };

    // Register MregDataTable default methods using clipboard
    MregDataTable.fnDefaultDepositSet = function(aData){
        $.each(aData, function(key, oMregEntity){
            clipboard.fnAddEntity(oMregEntity);
        });
        MREG.notify('Kopierade '+aData.length+' rader till ' + clipboard.getName());
    };

    MregDataTable.fnDefaultDepositGet = function(){
        var aData = clipboard.fnGetAllEntities();
        clipboard.fnClearTable();
        MREG.notify('Flyttade '+aData.length+' rader från ' + clipboard.getName());
        return aData;
    };

    MREG.clipboard = clipboard;
};
