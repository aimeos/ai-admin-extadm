/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * Copyright (c) Aimeos (aimeos.org), 2015
 */

Ext.ns('MShop.panel.product.stock');

MShop.panel.product.stock.ListUi = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Product_Stock',
    idProperty : 'product.stock.id',
    siteidProperty : 'product.stock.siteid',
    itemUiXType : 'MShop.panel.product.stock.itemui',

    autoExpandColumn : 'product-stock-warehouse',

    filterConfig : {
        filters : [{
            dataIndex : 'product.stock.warehouse.label',
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
                    'product.stock.parentid' : this.itemUi.record ? this.itemUi.record.id : null
                }
            }]
        };

    },

    getColumns : function() {
        this.typeStore = MShop.GlobalStoreMgr.get('Product_Stock_Warehouse');

        return [
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.stock.id',
                header : MShop.I18n.dt('admin', 'ID'),
                sortable : true,
                width : 50,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.stock.parentid',
                header : MShop.I18n.dt('admin', 'Product ID'),
                width : 50,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.stock.warehouseid',
                header : MShop.I18n.dt('admin', 'Warehouse'),
                align : 'center',
                id : 'product-stock-warehouse',
                renderer : this.typeColumnRenderer.createDelegate(this, [
                    this.typeStore,
                    "product.stock.warehouse.label"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.stock.stocklevel',
                header : MShop.I18n.dt('admin', 'Quantity'),
                sortable : true,
                align : 'center',
                width : 80
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.stock.dateback',
                header : MShop.I18n.dt('admin', 'Date back'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 130
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.stock.ctime',
                header : MShop.I18n.dt('admin', 'Created'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 130,
                editable : false,
                hidden : true
            },
            {
                xtype : 'datecolumn',
                dataIndex : 'product.stock.mtime',
                header : MShop.I18n.dt('admin', 'Last modified'),
                format : 'Y-m-d H:i:s',
                sortable : true,
                width : 130,
                editable : false,
                hidden : true
            },
            {
                xtype : 'gridcolumn',
                dataIndex : 'product.stock.editor',
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
