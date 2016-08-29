/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.price');

MShop.panel.price.ItemPickerUi = Ext.extend(MShop.panel.AbstractListItemPickerUi, {

    title : MShop.I18n.dt('admin', 'Price'),

    initComponent : function() {

        Ext.apply(this.itemConfig, {
            title : MShop.I18n.dt('admin', 'Associated prices'),
            xtype : 'MShop.panel.listitemlistui',
            domain : 'price',
            getAdditionalColumns : this.getAdditionalColumns.createDelegate(this)
        });

        Ext.apply(this.listConfig, {
            title : MShop.I18n.dt('admin', 'Available prices'),
            xtype : 'MShop.panel.price.listuismall'
        });

        MShop.panel.price.ItemPickerUi.superclass.initComponent.call(this);
    },

    getAdditionalColumns : function() {
        /** admin/extjs/panel/price/taxrate
         * Display the tax rate column in all price panels by default
         *
         * Due to the limited size in the panels, the tax rate of prices is
         * hidden by default. Editors can unhide the column nevertheless but this
         * only lasts as long as the panel is not closed.
         *
         * By setting this option to true, the column will be always displayed
         * in all panels.
         *
         * @param boolean True to always show the taxrate column, false to hide it by default
         * @since 2014.03
         * @category Developer
         * @category User
         */
        var showTaxrate = MShop.Config.get('admin/extjs/panel/price/taxrate', false);

        /** admin/extjs/panel/price/itempickerui/taxrate
         * Display the tax rate column in the price picker UI by default
         *
         * Due to the limited size in the picker UI, the tax rate of prices is
         * hidden by default. Editors can unhide the column nevertheless but this
         * only lasts as long as the panel is not closed.
         *
         * By setting this option to true, the column will be always displayed.
         *
         * @param boolean True to always show the taxrate column, false to hide it by default
         * @since 2014.03
         * @category Developer
         * @category User
         */
        showTaxrate = MShop.Config.get('admin/extjs/panel/price/itempickerui/taxrate', showTaxrate);

        var conf = this.itemConfig;
        this.listTypeStore = MShop.GlobalStoreMgr.get(conf.listTypeControllerName, conf.domain);

        return [
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'typeid',
                header : MShop.I18n.dt('admin', 'List type'),
                id : 'listtype',
                width : 70,
                renderer : this.typeColumnRenderer.createDelegate(this, [this.listTypeStore, conf.listTypeLabelProperty], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Status'),
                id : 'refstatus',
                width : 50,
                align : 'center',
                renderer : this.refStatusColumnRenderer.createDelegate(this, ['price.status'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Type'),
                id : 'reftype',
                width : 70,
                renderer : this.refColumnRenderer.createDelegate(this, ['price.typename'], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Label'),
                id : 'reflabel',
                width : 100,
                renderer : this.refColumnRenderer.createDelegate(this, ["price.label"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Currency'),
                id : 'refcurrency',
                width : 50,
                renderer : this.refColumnRenderer.createDelegate(this, ["price.currencyid"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Quantity'),
                id : 'refquantiy',
                width : 70,
                align : 'right',
                renderer : this.refColumnRenderer.createDelegate(this, ["price.quantity"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Price'),
                id : 'refcontent',
                align : 'right',
                renderer : this.refDecimalColumnRenderer.createDelegate(this, ["price.value"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Rebate'),
                id : 'refrebate',
                width : 70,
                align : 'right',
                hidden : true,
                renderer : this.refDecimalColumnRenderer.createDelegate(this, ["price.rebate"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Costs'),
                sortable : false,
                id : 'refshipping',
                width : 70,
                align : 'right',
                hidden : true,
                renderer : this.refDecimalColumnRenderer.createDelegate(this, ["price.costs"], true)
            },
            {
                xtype : 'gridcolumn',
                dataIndex : conf.listNamePrefix + 'refid',
                header : MShop.I18n.dt('admin', 'Tax rate'),
                sortable : false,
                id : 'reftaxrate',
                width : 70,
                align : 'right',
                hidden : !showTaxrate,
                renderer : this.refDecimalColumnRenderer.createDelegate(this, ["price.taxrate"], true)
            }];
    }
});

Ext.reg('MShop.panel.price.itempickerui', MShop.panel.price.ItemPickerUi);
