/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.attribute');

MShop.panel.attribute.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Attribute',
    idProperty : 'attribute.id',
    siteidProperty : 'attribute.siteid',
    itemUiXType : 'MShop.panel.attribute.itemui',
    exportMethod : 'Attribute_Export_Text.createJob',

    autoExpandColumn : 'attribute-list-label',

    filterConfig : {
        filters : [{
            dataIndex : 'attribute.label',
            operator : '=~',
            value : ''
        }]
    },


    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Attribute');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.attribute.ListUi.superclass.initComponent.call(this);
    },


    getColumns : function() {
        // make sure type store gets loaded in same batch as this grid data
        this.typeStore = MShop.GlobalStoreMgr.get('Attribute_Type');

        return [{
            xtype : 'gridcolumn',
            dataIndex : 'attribute.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            width : 50,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'attribute.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 70,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'attribute.typeid',
            header : MShop.I18n.dt('admin', 'Type'),
            width : 100,
            renderer : this.typeColumnRenderer.createDelegate(this, [this.typeStore, "attribute.type.label"], true)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'attribute.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 100
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'attribute.label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false,
            id : 'attribute-list-label'
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'attribute.position',
            header : MShop.I18n.dt('admin', 'Position'),
            sortable : true,
            width : 50,
            editable : false
        }, {
            xtype : 'datecolumn',
            dataIndex : 'attribute.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'attribute.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'attribute.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.attribute.listui', MShop.panel.attribute.ListUi);
