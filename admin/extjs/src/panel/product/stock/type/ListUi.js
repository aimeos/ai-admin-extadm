/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright (c) Metaways Infosystems GmbH, 2011
 */

Ext.ns('MShop.panel.product.stock.type');

MShop.panel.product.stock.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Stock_Type',
    idProperty : 'stock.type.id',
    siteidProperty : 'stock.type.siteid',
    itemUiXType : 'MShop.panel.product.stock.type.itemui',

    autoExpandColumn : 'product-stock-type-label',

    sortInfo : {
        field : 'stock.type.label',
        direction : 'ASC'
    },

    filterConfig : {
        filters : [{
            dataIndex : 'stock.type.label',
            operator : '=~',
            value : ''
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Stock type');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.product.stock.type.ListUi.superclass.initComponent.call(this);
    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'stock.type.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'stock.type.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'stock.type.domain',
            header : MShop.I18n.dt('admin', 'Domain'),
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'stock.type.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 150,
            align : 'center',
            editable : false
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'stock.type.label',
            id : 'product-stock-type-label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false
        }, {
            xtype : 'datecolumn',
            dataIndex : 'stock.type.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'stock.type.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'stock.type.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 130,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.product.stock.type.listui', MShop.panel.product.stock.type.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.type.tabUi', 'MShop.panel.product.stock.type.listui',
    MShop.panel.product.stock.type.ListUi, 90);
