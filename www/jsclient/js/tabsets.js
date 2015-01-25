/**
 * @fileOverview MREG.tabsets
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */

window.MREG = window.MREG || {};
window.MREG.tabsets = window.MREG.tabsets || {};


/**
 * Init left and right tabsets
 */
MREG.tabsets.init = function(){
    // Create left side tabset
    var left = MREG.tabsets.createTabset();
    left.getElement().attr('id', 'tabset-left').appendTo('body');
    MREG.tabsets.left = left;

    // Create right side tabset
    var right = MREG.tabsets.createTabset({
        'show': function(){
            MREG.toolbar.disableActionButtons();
            document.title = "Mreg";
        }
    });
    right.getElement().attr('id', 'tabset-right').appendTo('body');
    MREG.tabsets.right = right;
};


/**
 * Create new tabset
 *
 * @param {object} options
 *
 * @returns {object} Created tabset
 */
MREG.tabsets.createTabset = function(options){
    options = options || {};

    var tabset = $.createTabset(options);

    // Create new search tab and append to tabset
    tabset.newSearch = function(){
        var table = new MregDataTable(
            $.tmplManager.renderUsingName('tmpl/search.html', {}),    
            {"sName": "Sökning"}
        );
        var tab = tabset.createTab({
            title: 'Ny sökning',
            content: table.container,
            fSetAltered: false
        });
        tab.$panel.find('input[type="submit"]').button();
        tab.$panel.find('input[type="search"]').focus();
        tab.$panel.find('form').submit(function(){
            $.ajax({
                'type': 'GET',
                'url': '~/search',
                'data': {'q': tab.$panel.find('input[type="search"]').val()}
            }).done(function(data){
                var items = [];
                $.each(data.items, function(key, oItem){
                    items.push([
                        '',
                        oItem.title,
                        oItem.id,
                        oItem.type,
                        oItem.uri,
                        oItem.description,
                    ]);
                });
                tab.setTitle(data.title);
                table.fnClearTable();
                table.oDataTable.fnAddData(items);
            });

            return false;
        });
    };

    // Create new worklist and append to tab
    tabset.newWorklist = function(title){
        title = title || 'Arbetslista';
         tabset.createTab({
            'title': title,
            'content': new MregDataTable(
                $.tmplManager.renderUsingName('tmpl/worklist.html', {}),    
                {"sName": title}
            ).container
        });
    };

    return tabset;
};
