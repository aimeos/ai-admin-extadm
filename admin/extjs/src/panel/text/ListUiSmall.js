/*!
 * Copyright (c) Metaways Infosystems GmbH, 2011
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

Ext.ns('MShop.panel.text');

MShop.panel.text.ListUiSmall = Ext.extend(MShop.panel.AbstractListUi, {

    recordName : 'Text',
    idProperty : 'text.id',
    siteidProperty : 'text.siteid',
    itemUiXType : 'MShop.panel.text.itemui',

    autoExpandColumn : 'text-list-label',

    filterConfig : {
        filters : [{
            dataIndex : 'text.label',
            operator : '=~',
            value : ''
        }]
    },

    getColumns : function() {
        return [{
            xtype : 'gridcolumn',
            dataIndex : 'text.id',
            header : MShop.I18n.dt('admin', 'ID'),
            sortable : true,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.status',
            header : MShop.I18n.dt('admin', 'Status'),
            sortable : true,
            width : 50,
            align : 'center',
            renderer : this.statusColumnRenderer.createDelegate(this)
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.typename',
            header : MShop.I18n.dt('admin', 'Type'),
            width : 70
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.languageid',
            header : MShop.I18n.dt('admin', 'Language'),
            sortable : true,
            width : 50,
            renderer : MShop.elements.language.renderer
        }, {
            xtype : 'gridcolumn',
            id : 'text-list-label',
            dataIndex : 'text.label',
            header : MShop.I18n.dt('admin', 'Label'),
            sortable : true,
            editable : false
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.content',
            header : MShop.I18n.dt('admin', 'Content'),
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.ctime',
            header : MShop.I18n.dt('admin', 'Created'),
            sortable : true,
            width : 120,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.mtime',
            header : MShop.I18n.dt('admin', 'Last modified'),
            sortable : true,
            width : 120,
            editable : false,
            hidden : true
        }, {
            xtype : 'gridcolumn',
            dataIndex : 'text.editor',
            header : MShop.I18n.dt('admin', 'Editor'),
            sortable : true,
            width : 120,
            editable : false,
            hidden : true
        }];
    }
});

Ext.reg('MShop.panel.text.listuismall', MShop.panel.text.ListUiSmall);
