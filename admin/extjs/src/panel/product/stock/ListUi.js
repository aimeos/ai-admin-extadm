/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * Copyright (c) Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.product.stock');

MShop.panel.product.stock.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Stock',
    idProperty : 'stock.id',
    siteidProperty : 'stock.siteid',
    itemUiXType : 'MShop.panel.product.stock.itemui',

    autoExpandColumn : 'product-stock-stocktype',

    filterConfig : {
        filters : [{
            dataIndex : 'stock.type.label',
            operator : '=~',
            value : ''
        }]
    },

    initComponent : function() {
        this.title = MShop.I18n.dt('admin', 'Stock');

        MShop.panel.AbstractListUi.prototype.initActions.call(this);
        MShop.panel.AbstractListUi.prototype.initToolbar.call(this);

        MShop.panel.product.stock.ListUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        this.itemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });

        MShop.panel.product.stock.ListUi.superclass.afterRender.apply(this, arguments);
    },

    onBeforeLoad : function(store, options) {
        this.setSiteParam(store);

        if(this.domain) {
            this.setDomainFilter(store, options);
        }

        options.params = options.params || {};
        options.params.condition = {
            '&&' : [{
                '==' : {
                    'stock.productcode' : this.itemUi.record ? this.itemUi.record.data['product.code'] : ''
                }
            }]
        };

    },

    getColumns : function() {
        this.typeStore = MShop.GlobalStoreMgr.get('Stock_Type');

        return [
            {
                xtype : 'gridcolumn',
                dataIndex : 'stock.id',
                header : MShop.I18n.dt('admin', 'ID'),
                sortable : true,
                width : 50,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'stock.productcode',
                header : MShop.I18n.dt('admin', 'Product code'),
                width : 50,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'stock.typeid',
                header : MShop.I18n.dt('admin', 'Type'),
                align : 'center',
                id : 'product-stock-stocktype',
                renderer : this.typeColumnRenderer.createDelegate(this, [
                    this.typeStore,
                    "stock.type.label"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'stock.stocklevel',
                header : MShop.I18n.dt('admin', 'Stock level'),
                sortable : true,
                align : 'center',
                width : 80
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'stock.dateback',
                header : MShop.I18n.dt('admin', 'Date back'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 130
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'stock.ctime',
                header : MShop.I18n.dt('admin', 'Created'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 130,
                editable : false,
                hidden : true
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'stock.mtime',
                header : MShop.I18n.dt('admin', 'Last modified'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 130,
                editable : false,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'stock.editor',
                header : MShop.I18n.dt('admin', 'Editor'),
                sortable : true,
                width : 130,
                editable : false,
                hidden : true
            }];
    }
});

Ext.reg('MShop.panel.product.stock.listui', MShop.panel.product.stock.ListUi);

Ext.ux.ItemRegistry.registerItem('MShop.panel.product.ItemUi', 'MShop.panel.product.stock.listui', MShop.panel.product.stock.ListUi, 2);
