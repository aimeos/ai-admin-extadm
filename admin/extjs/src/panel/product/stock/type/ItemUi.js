/*!
 * LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * Copyright Aimeos (aimeos.org), 2016
 */

Ext.ns('MShop.panel.product.stock.type');

MShop.panel.product.stock.type.ItemUi = Ext.extend(MShop.panel.AbstractTypeItemUi, {
    siteidProperty : 'stock.type.siteid',
    typeDomain : 'stock.type',

    initComponent : function() {
        MShop.panel.AbstractTypeItemUi.prototype.setSiteCheck(this);
        MShop.panel.product.type.ItemUi.superclass.initComponent.call(this);
    },

    afterRender : function() {
        var label = this.record ? this.record.data['stock.type.label'] : MShop.I18n.dt('admin', 'new');
        //#: Product stock type item panel title with type label ({0}) and site code ({1)}
        var string = MShop.I18n.dt('admin', 'Stock type: {0} ({1})');
        this.setTitle(String.format(string, label, MShop.config.site["locale.site.label"]));

        MShop.panel.product.stock.type.ItemUi.superclass.afterRender.apply(this, arguments);
    }
});

Ext.reg('MShop.panel.product.stock.type.itemui', MShop.panel.product.stock.type.ItemUi);
