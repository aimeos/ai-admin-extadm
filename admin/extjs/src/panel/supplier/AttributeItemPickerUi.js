/*!
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos.org, 2015-2017
 */


Ext.ns('MShop.panel.supplier');

// hook attribute picker into the supplier ItemUi
Ext.ux.ItemRegistry.registerItem('MShop.panel.supplier.ItemUi', 'MShop.panel.supplier.AttributeItemPickerUi', {

	xtype : 'MShop.panel.attribute.itempickerui',
    itemConfig : {
        recordName : 'Supplier_Lists',
        idProperty : 'supplier.lists.id',
        siteidProperty : 'supplier.lists.siteid',
        listDomain : 'supplier',
        listNamePrefix : 'supplier.lists.',
        listTypeIdProperty : 'supplier.lists.type.id',
        listTypeLabelProperty : 'supplier.lists.type.label',
        listTypeControllerName : 'Supplier_Lists_Type',
        listTypeCondition : {
            '&&' : [{
                '==' : {
                    'supplier.lists.type.domain' : 'attribute'
                }
            }]
        },
        listTypeKey : 'supplier/lists/type/attribute'
    },
    listConfig : {
        domain : 'supplier',
        prefix : 'attribute.'
    }
}, 60);
