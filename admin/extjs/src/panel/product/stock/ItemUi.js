/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.product.stock');

/**
 * Concrete ItemUi
 *
 * @extends Mshop.panel.AbstractListItemUi
 */
MShop.panel.product.stock.ItemUi = Ext.extend(MShop.panel.AbstractItemUi, {

    siteidProperty : 'stock.siteid',

    initComponent : function() {

        this.title = MShop.I18n.dt('admin', 'Product stock');

        MShop.panel.AbstractItemUi.prototype.setSiteCheck(this);

        this.items = [{
            xtype : 'tabpanel',
            activeTab : 0,
            border : false,
            itemId : 'MShop.panel.product.stock.ItemUi',
            plugins : ['ux.itemregistry'],
            items : [{
                xtype : 'panel',
                title : MShop.I18n.dt('admin', 'Basic'),
                border : false,
                layout : 'hbox',
                layoutConfig : {
                    align : 'stretch'
                },
                itemId : 'MShop.panel.product.stock.ItemUi.BasicPanel',

                plugins : ['ux.itemregistry'],
                defaults : {
                    bodyCssClass : this.readOnlyClass
                },
                items : [{
                    xtype : 'form',
                    title : MShop.I18n.dt('admin', 'Details'),
                    flex : 1,
                    ref : '../../mainForm',
                    autoScroll : true,
                    items : [{
                        xtype : 'fieldset',
                        style : 'padding-right: 25px;',
                        border : false,
                        labelAlign : 'top',
                        defaults : {
                            readOnly : this.fieldsReadOnly,
                            anchor : '100%'
                        },
                        items : [{
                            xtype : 'hidden',
                            name : 'stock.productcode'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'ID'),
                            name : 'stock.id'
                        }, {
                            xtype : 'combo',
                            fieldLabel : MShop.I18n.dt('admin', 'Type'),
                            name : 'stock.typeid',
                            mode : 'local',
                            store : MShop.GlobalStoreMgr.get('Stock_Type', this.domain),
                            displayField : 'stock.type.label',
                            valueField : 'stock.type.id',
                            forceSelection : true,
                            triggerAction : 'all',
                            typeAhead : true,
                            listeners : {
                                'render' : {
                                    fn : function() {
                                        var record, index = this.store.find('stock.type.code', 'default');
                                        if((record = this.store.getAt(index))) {
                                            this.setValue(record.id);
                                        }
                                    }
                                }
                            }
                        }, {
                            xtype : 'numberfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Stock level'),
                            name : 'stock.stocklevel',
                            emptyText : MShop.I18n.dt('admin', 'Quantity or empty if unlimited (optional)')
                        }, {
                            xtype : 'datefield',
                            fieldLabel : MShop.I18n.dt('admin', 'Back in stock'),
                            name : 'stock.dateback',
                            format : 'Y-m-d H:i:s',
                            emptyText : MShop.I18n.dt('admin', 'YYYY-MM-DD hh:mm:ss (optional)')
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Created'),
                            name : 'stock.ctime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Last modified'),
                            name : 'stock.mtime'
                        }, {
                            xtype : 'displayfield',
                            fieldLabel : MShop.I18n.dt('admin', 'Editor'),
                            name : 'stock.editor'
                        }]
                    }]
                }]
            }]
        }];

        MShop.panel.product.stock.ItemUi.superclass.initComponent.call(this);
    },

    onSaveItem : function() {
        // validate data
        if(!this.mainForm.getForm().isValid() && this.fireEvent('validate', this) !== false) {
            Ext.Msg.alert(MShop.I18n.dt('admin', 'Invalid data'), MShop.I18n.dt('admin',
                'Please recheck your data'));
            return;
        }

        this.saveMask.show();
        this.isSaveing = true;

        // force record to be saved!
        this.record.dirty = true;

        if(this.fireEvent('beforesave', this, this.record) === false) {
            this.isSaveing = false;
            this.saveMask.hide();
        }

        this.mainForm.getForm().updateRecord(this.record);
        this.record.data['stock.productcode'] = this.listUI.itemUi.record.data['product.code'];

        if(this.action == 'add' || this.action == 'copy') {
            this.store.add(this.record);
        }

        // store async action is triggered. {@see onStoreWrite/onStoreException}
        if(!this.store.autoSave) {
            this.onAfterSave();
        }
    },

    onStoreException : function() {
        this.store.remove(this.record);
        MShop.panel.product.stock.ItemUi.superclass.onStoreException.apply(this, arguments);
    }
});

Ext.reg('MShop.panel.product.stock.itemui', MShop.panel.product.stock.ItemUi);
