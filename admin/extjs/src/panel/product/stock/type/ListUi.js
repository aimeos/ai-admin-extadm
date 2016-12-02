/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright (c) Metaways Infosystems GmbH, 2011
 */

Ext.ns('MShop.panel.product.stock.type');

MShop.panel.product.stock.type.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Product_Stock_Type',
    idProperty : 'product.stock.type.id',
    siteidProperty : 'product.stock.type.siteid',
    itemUiXType : 'MShop.panel.stock.type.itemui',

    autoExpandColumn : 'product-stocktype-list-label',

    sortInfo : {
        field : 'product.stock.type.label',
        direction : 'ASC'
    },

    filterConfig : {
        filters : [{
            dataIndex : 'product.stock.type.label',
            operator : '=~',
            value : ''
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Product stock type');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.product.stock.type.ListUi.superclass.initComponent.call(this);
    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.type.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.type.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.type.domain',
            header : MShop.I18n.dt('admin', 'Domain'),
            sortable : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.type.code',
            header : MShop.I18n.dt('admin', 'Code'),
            sortable : true,
            width : 150,
            align : 'center',
            editable : false
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.type.label',
            id : 'product-stock-type-label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.stock.type.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'datecolumn',
            dataIndex : 'product.stock.type.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 130,
            format : 'Y-m-d H:i:s',
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'product.stock.type.editor',
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
