/**
 * Mreg entity class
 *
 * Create from object, array or HTMLTableRowElement
 *
 * @param Object/Array/HTMLTableRowElement data
 */
function MregEntity(data){
    options = {};

    if (jQuery.isArray(data)) {
        options.title = data[1];
        options.id = data[2];
        options.type = data[3];
        options.uri = data[4];
        options.description = data[5];
    } else if (jQuery.isPlainObject(data)) {
        options = data;
    } else {
        var msg = 'Unable to create MregEntity. Use array or plain object';
        console.error(msg);
    }

    options = jQuery.extend(
        {
            'title': '',
            'id': '',
            'type': '',
            'uri': '',
            'description': '',
        },
        options
    );

    this.title = options.title;
    this.id = options.id;
    this.type = options.type;
    this.uri = options.uri;
    this.description = options.description;
}


/**
 * MregDataTable class
 *
 * Creates a DataTable on nElement.find('table') and a TableTools instance on
 * the created DataTable. Adds interface methods for interaction with
 * DataTable/TableTools.
 *
 * @param jQuery nElement The jQuery element to process. Must include a valid
 * table structure
 *
 * @param object oOptions
 */
function MregDataTable(nElement, oOptions){
    if (!nElement.jquery) {
        nElement = $(nElement);
    }


    /**
     * Options
     *
     * @var object
     */
    this.oOptions = jQuery.extend(
        {
            "sName": '',
            "sLangUrl": "lib/datatables.swedish.lang",
            "sSwfPath": "lib/TableTools-2.0.3/media/swf/copy_csv_xls_pdf.swf",
            "aButtons": ["csv", "pdf", "export", "import"],
            "fnOpenEntity": MregDataTable.fnDefaultOpenEntity,
            "fnDepositSet": MregDataTable.fnDefaultDepositSet,
            "fnDepositGet": MregDataTable.fnDefaultDepositGet
        },
        oOptions
    );


    /**
     * Containing jquery element
     *
     * @var jQuery
     */
    this.container = nElement;


    /**
     * Shortcut to table name
     *
     * @var string
     */
    this.name = this.oOptions.sName;


    /**
     * Reference for use in closures
     *
     * @var MregDataTable
     */
    var oMregDataTable = this;


    /**
     * Find the table node
     */
    if (this.container.is('table')) {
        var nTable = this.container;
    } else {
        var nTable = this.container.find('table');
    }
    if (!nTable) {
        console.error("Unable to create MregDataTable, no table node found");
        return;
    }


    /**
     * DataTables instance
     *
     * @var DataTable
     */
    this.oDataTable = nTable.dataTable({
        "bJQueryUI": true,
        "oLanguage": {"sUrl": this.oOptions.sLangUrl},
        "sDom": '<"H"lfrip>t<"F"ip>',
        "aoColumnDefs": [
            {
                "bSortable": false,
                "aTargets": [0]
            },
            {
                "bVisible": false,
                "aTargets": [2, 3, 4]
            }
        ],
        "fnCreatedRow": function(nRow){
            // Set navigational content to the first td element
            var oMregEntity = new MregEntity(this.fnGetData(nRow));
            jQuery('td:eq(0)', nRow)
                .empty()
                .append(
                    jQuery('<span>')
                        .addClass('ui-icon')
                        .addClass(oMregEntity.type)
                        .attr('title', oMregEntity.type)
                )
                .append(
                    jQuery('<a>')
                        .addClass('ui-icon ui-icon-close')
                        .text('[radera]')
                        .attr('title', 'Ta bort')
                        .click(function(event){
                            event.stopPropagation();
                            oMregDataTable.fnDeleteRow(nRow);
                        })
                ).append(
                    jQuery('<a>')
                        .addClass('ui-icon ui-icon-pencil')
                        .text('[öppna]')
                        .attr('title', 'Öppna')
                        .click(function(event){
                            event.stopPropagation();
                            oMregDataTable.oOptions.fnOpenEntity(oMregEntity);
                        })
                );
        },
        "fnInitComplete": function(){
            $(this).parent('.dataTables_wrapper').addClass('mreg-datatable');
        }
    });


    /**
     * The TableTools buttons to create
     *
     * @var array
     */
    var aTableToolButtons = [];
    
    if (jQuery.inArray('csv', this.oOptions.aButtons) != -1) {
        aTableToolButtons.push(
            {
                "sExtends": "csv",
                "fnClick": function (nButton, oConfig, oFlash) {
                    if (this.fnGetSelected().length) {
                        oConfig.bSelectedOnly = true;
                    } else {
                        oConfig.bSelectedOnly = false;
                    }
        			this.fnSetText(oFlash, this.fnGetTableData(oConfig));
                }
            }
        );
    }

    if (jQuery.inArray('pdf', this.oOptions.aButtons) != -1) {
        aTableToolButtons.push(
            {
                "sExtends": "pdf",
                "fnClick": function (nButton, oConfig, oFlash) {
                    if (this.fnGetSelected().length) {
                        oConfig.bSelectedOnly = true;
                    } else {
                        oConfig.bSelectedOnly = false;
                    }
	                this.fnSetText(
	                    oFlash, 
		                "title:"+ this.fnGetTitle(oConfig) +"\n"+
		                "message:"+ oConfig.sPdfMessage +"\n"+
		                "colWidth:"+ this.fnCalcColRatios(oConfig) +"\n"+
		                "orientation:"+ oConfig.sPdfOrientation +"\n"+
		                "size:"+ oConfig.sPdfSize +"\n"+
		                "--/TableToolsOpts--\n" +
		                this.fnGetTableData(oConfig)
	                );
                }
            }
        );
    }

    if (jQuery.inArray('export', this.oOptions.aButtons) != -1) {
        aTableToolButtons.push(
            {
                "sExtends": "text",
    	        "sButtonText": "Kopiera",
                "fnClick": function( nButton, oConfig ) {
                    if (this.fnGetSelected().length) {
                        var aData = this.fnGetSelectedData();
                    } else {
                        var aData = this.s.dt.oInstance.fnGetData();
                    }
                    var aToDeposit = [];
                    jQuery.each(aData, function(key, aNodeData){
                        if (!aNodeData) return;
                        aToDeposit.push(new MregEntity(aNodeData));
                    });
                    oMregDataTable.oOptions.fnDepositSet(aToDeposit);
                }
            }
        );
    }

    if (jQuery.inArray('import', this.oOptions.aButtons) != -1) {
        aTableToolButtons.push(
            {
                "sExtends": "text",
    	        "sButtonText": "Lägg till",
                "fnClick": function( nButton, oConfig ) {
                    var aData = oMregDataTable.oOptions.fnDepositGet();
                    jQuery.each(aData, function(key, oEntity){
                        oMregDataTable.fnAddEntity(oEntity);
                    });
                }
            }
        );
    }


    /**
     * TableTools instance
     *
     * @var TableTools
     */
    this.oTableTools = new TableTools(this.oDataTable, {
        "sSwfPath": this.oOptions.sSwfPath,
		"sRowSelect": "multi",
        "aButtons": aTableToolButtons
    });

    // Add MregDataTable class
    $(this.oTableTools.dom.container).addClass('mreg-datatable-buttonset');

    // Write TableTools to DOM
    this.oDataTable.before(this.oTableTools.dom.container);


    // Write select toolbar to DOM
    this.oDataTable.after(
        $('<div>').addClass('mreg-datatable-selection-toolbar')
        .append(
            $('<span>Markering:</span>')
        )
        .append(
            $('<a>')
                .prop("title", "Markera alla")
                .text('[alla]')
                .addClass('ui-icon ui-icon-arrow-4-diag')
                .click(function(){
                    oMregDataTable.fnSelectAll();
                })
        ).append(
            $('<a>')
                .prop("title", "Avmarkera alla")
                .text('[inga]')
                .addClass('ui-icon ui-icon-cancel')
                .click(function(){
                    oMregDataTable.fnSelectNone();
                })
        ).append(
            $('<a>')
                .prop("title", "Invertera markering")
                .text('[invertera]')
                .addClass('ui-icon ui-icon-shuffle')
                .click(function(){
                    oMregDataTable.fnInvertSelection();
                })
        ).append(
            $('<span>Utför:</span>')
        ).append(
            $('<a>')
                .prop("title", "Ta bort alla markerade rader från listan")
                .text('[ta bort]')
                .addClass('ui-icon ui-icon-close')
                .click(function(){
                    oMregDataTable.fnDeleteSelected();
                })
        ).after(
            $('<div>').css('clear', 'both')
        )
    );

}


/**
 * Method used if 'fnDepositSet' is not passed to the constructor
 *
 * @param array aData
 */
MregDataTable.fnDefaultDepositSet = function(aData){};


/**
 * Method used if 'fnDepositGet' is not passed to the constructor
 */
MregDataTable.fnDefaultDepositGet = function(){};


/**
 * Method used if 'fnOpenEntity' is not passed to the constructor
 *
 * @param MregEntity oMregEntity
 */
MregDataTable.fnDefaultOpenEntity = function(oMregEntity){};


/**
 * Select all rows in table
 */
MregDataTable.prototype.fnSelectAll = function(){
    this.oTableTools.fnSelectAll();
};


/**
 * Deselect all rows in table
 */
MregDataTable.prototype.fnSelectNone = function(){
    this.oTableTools.fnSelectNone();
};


/**
 * Invert selected rows in table
 */
MregDataTable.prototype.fnInvertSelection = function(){
    var oTable = this;
    jQuery.each(this.oDataTable.fnGetNodes(), function(key, node){
        var pos = oTable.oDataTable.fnGetPosition(node);
        if (oTable.oTableTools.fnIsSelected(node)) {
            oTable.oTableTools.fnDeselect(node);
        } else {
            oTable.oTableTools.fnSelect(node);
        }
    });
};


/**
 * Delete row from table
 *
 * Calls registered callback with the corresponding MregEntity object for
 * further actions
 *
 * @param node nRow The node to delete
 *
 * @return MregEntity the entity deleted
 */
MregDataTable.prototype.fnDeleteRow = function(nRow){
    var oMregEntity = new MregEntity(this.oDataTable.fnGetData(nRow));
    this.oTableTools.fnDeselect(nRow);
    this.oDataTable.fnDeleteRow(this.oDataTable.fnGetPosition(nRow));

    return oMregEntity;
};


/**
 * Delete all selected rows
 */
MregDataTable.prototype.fnDeleteSelected = function(){
    var oTable = this;
    jQuery.each(this.oDataTable.$('tr.DTTT_selected'), function(key, nRow){
        oTable.fnDeleteRow(nRow);
    });
};


/**
 * Add entity to table
 *
 * @param MregEntity oMregEntity The entity to add
 */
MregDataTable.prototype.fnAddEntity = function(oMregEntity){
    this.oDataTable.fnAddData([
        '',
        oMregEntity.title,
        oMregEntity.id,
        oMregEntity.type,
        oMregEntity.uri,
        oMregEntity.description,
    ]);
};


/**
 * Count number of rows in table
 *
 * @return int
 */
MregDataTable.prototype.fnCount = function(){
    return this.oDataTable.fnGetNodes().length;
};


/**
 * Check if table is empty
 *
 * @return bool
 */
MregDataTable.prototype.fnIsEmpty = function(){
    return this.fnCount() == 0;
};


/**
 * Remove and return one entity from the table
 *
 * If table is empty null is returned and an error message is written
 * to console.error()
 *
 * Note that is a delete callback is registered it will be called for each poped
 * object.
 *
 * @return MregEntity
 */
MregDataTable.prototype.fnPopEntity = function(){
    if (nRow = this.oDataTable.fnGetNodes(0)) {

        return this.fnDeleteRow(nRow);
    }

    var msg = "Unable to pop data from MregDataTable '";
    msg += this.oOptions.sName;
    msg += "'. Table is empty.";
    console.error(msg);

    return null;
};


/**
 * Get an array of all MregEntities in table
 *
 * @return array
 */
MregDataTable.prototype.fnGetAllEntities = function(){
    var aEntities = [];
    jQuery.each(this.oDataTable.fnGetData(), function(key, aData){
        aEntities.push(new MregEntity(aData));
    });

    return aEntities;
};


/**
 * Clear all rows from table
 *
 * Note that is a delete callback is registered it will be called for each row
 */
MregDataTable.prototype.fnClearTable = function(){
    var oTable = this;
    jQuery.each(this.oDataTable.fnGetNodes(), function(key, nRow){
        oTable.fnDeleteRow(nRow);
    });
};
