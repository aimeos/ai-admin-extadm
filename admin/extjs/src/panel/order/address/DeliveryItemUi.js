/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


Ext.ns('MShop.panel.order.base.address');

MShop.panel.order.base.address.DeliveryItemUi = Ext.extend(Ext.FormPanel, {

    title : MShop.I18n.dt('admin', 'Delivery address'),
    flex : 1,
    autoScroll : true,
    recordName : 'Order_Base_Address',
    idProperty : 'order.base.address.id',
    siteidProperty : 'order.base.address.siteid',

    initComponent : function() {

        this.initStore();

        this.items = [{
            xtype : 'fieldset',
            style : 'padding-right: 25px;',
            border : false,
            autoWidth : true,
            labelAlign : 'left',
            defaults : {
                anchor : '100%'
            },
            items : [{
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'ID'),
                name : 'order.base.address.id'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Address ID'),
                name : 'order.base.address.addressid'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Company'),
                name : 'order.base.address.company'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Vat ID'),
                name : 'order.base.address.vatid'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Salutation'),
                name : 'order.base.address.salutation'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Title'),
                name : 'order.base.address.title'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Firstname'),
                name : 'order.base.address.firstname'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Lastname'),
                name : 'order.base.address.lastname'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Address 1'),
                name : 'order.base.address.address1'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Address 2'),
                name : 'order.base.address.address2'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Address 3'),
                name : 'order.base.address.address3'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Postal code'),
                name : 'order.base.address.postal'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'City'),
                name : 'order.base.address.city'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'State'),
                name : 'order.base.address.state'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Country'),
                name : 'order.base.address.countryid'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Telephone'),
                name : 'order.base.address.telephone'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Telefax'),
                name : 'order.base.address.telefax'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'E-Mail'),
                name : 'order.base.address.email'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Website'),
                name : 'order.base.address.website'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Created'),
                name : 'order.base.address.ctime'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                name : 'order.base.address.mtime'
            }, {
                xtype : 'displayfield',
                fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                name : 'order.base.address.editor'
            }]
        }];

        MShop.panel.order.base.address.DeliveryItemUi.superclass.initComponent.call(this);
    },

    initStore : MShop.panel.ListItemListUi.prototype.initStore,
    onStoreException : MShop.panel.AbstractListUi.prototype.onStoreException,
    onBeforeLoad : MShop.panel.AbstractListUi.prototype.setSiteParam,
    onBeforeWrite : Ext.emptyFn,

    onDestroy : function() {
        this.store.un('beforeload', this.setFilters, this);
        this.store.un('beforeload', this.onBeforeLoad, this);
        this.store.un('load', this.onStoreLoad, this);
        this.store.un('beforewrite', this.onBeforeWrite, this);
        this.store.un('write', this.onStoreWrite, this);
        this.store.un('exception', this.onStoreException, this);

        MShop.panel.order.base.address.DeliveryItemUi.superclass.onDestroy.apply(this, arguments);
    },

    afterRender : function() {
        // fetch ItemUI
        this.itemUi = this.findParentBy(function(c) {
            return c.isXType(MShop.panel.AbstractItemUi, false);
        });

        this.store.load({});

        MShop.panel.order.base.address.DeliveryItemUi.superclass.afterRender.apply(this, arguments);
    },

    onStoreLoad : function() {
        if(this.store.getCount() === 0) {
            var recordType = MShop.Schema.getRecord(this.recordName);
            this.record = new recordType({});

            this.store.add(this.record);
        } else {
            this.record = this.store.getAt(0);
        }

        this.getForm().loadRecord(this.record);
    },

    setFilters : function(store, options) {
        if(!this.itemUi.record || this.itemUi.record.phantom) {
            // nothing to load
            this.onStoreLoad();
            return false;
        }

        // filter for refid
        options.params = options.params || {};
        options.params.condition = {
            '&&' : [{
                '==' : {
                    'order.base.address.baseid' : this.itemUi.record.data['order.baseid']
                }
            }, {
                '==' : {
                    'order.base.address.type' : 'delivery'
                }
            }]
        };

        return true;
    }
});

Ext.reg('MShop.panel.order.base.address.deliveryitemui', MShop.panel.order.base.address.DeliveryItemUi);

//hook order base address into the order ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.order.base.address.ItemUi',
    'MShop.panel.order.base.address.DeliveryItemUi', MShop.panel.order.base.address.DeliveryItemUi, 20);
