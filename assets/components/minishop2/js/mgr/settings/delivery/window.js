miniShop2.window.CreateDelivery = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        title: _('ms2_delivery'),
        width: 600,
        baseParams: {
            action: 'mgr/settings/delivery/create',
        },
    });
    miniShop2.window.CreateDelivery.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.CreateDelivery, miniShop2.window.Default, {

    getFields: function (config) {
        return [{
            xtype: 'hidden',
            name: 'id',
            id: config.id + '-id'
        }, {
            layout: 'column',
            items: [{
                columnWidth: .7,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('ms2_name'),
                    name: 'name',
                    anchor: '99%',
                    id: config.id + '-name'
                }]
            }, {
                columnWidth: .3,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('ms2_add_cost'),
                    name: 'price',
                    description: _('ms2_add_cost_help'),
                    anchor: '99%',
                    id: config.id + '-price'
                }],
            }]

        }, {
            layout: 'column',
            items: [{
                columnWidth: .3,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'numberfield',
                    fieldLabel: _('ms2_weight_price'),
                    description: _('ms2_weight_price_help'),
                    name: 'weight_price',
                    decimalPrecision: 2,
                    anchor: '99%',
                    id: config.id + '-weight-price'
                }]
            }, {
                columnWidth: .3,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'numberfield',
                    fieldLabel: _('ms2_distance_price'),
                    description: _('ms2_distance_price_help'),
                    name: 'distance_price',
                    decimalPrecision: 2,
                    anchor: '99%',
                    id: config.id + '-distance-price'
                }],
            }, {
                columnWidth: .3,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'numberfield',
                    fieldLabel: _('ms2_cache_time'),
                    description: _('ms2_cache_time_help'),
                    name: 'cache_time',
                    decimalPrecision: 0,
                    anchor: '99%',
                    id: config.id + '-cache-time'
                }],
            }]
        }, {
            layout: 'column',
            items: [{
                columnWidth: .5,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'textfield',
                    fieldLabel: _('ms2_time'),
                    description: _('ms2_time_help'),
                    name: 'time',
                    anchor: '99%',
                    id: config.id + '-time'
                }]
            }, {
                columnWidth: .5,
                layout: 'form',
                defaults: {msgTarget: 'under'},
                items: [{
                    xtype: 'minishop2-combo-classes',
                    type: 'delivery',
                    fieldLabel: _('ms2_class'),
                    name: 'class',
                    anchor: '99%',
                    id: config.id + '-class'
                }],
            }]
        }, {
            xtype: 'minishop2-combo-browser',
            fieldLabel: _('ms2_logo'),
            name: 'logo',
            anchor: '99%',
            id: config.id + '-logo'
        }, {
            xtype: 'textarea',
            fieldLabel: _('ms2_description'),
            name: 'description',
            anchor: '99%',
            id: config.id + '-description'
        }, {
            xtype: 'textfield',
            fieldLabel: _('ms2_order_fields'),
            description: _('ms2_order_fields_help'),
            name: 'fields',
            anchor: '99%',
            id: config.id + '-fields'
        }, {
            xtype: 'textfield',
            fieldLabel: _('ms2_order_requires'),
            description: _('ms2_order_requires_help'),
            name: 'requires',
            anchor: '99%',
            id: config.id + '-requires'
        }, {
            xtype: 'xcheckbox',
            boxLabel: _('ms2_active'),
            hideLabel: true,
            name: 'active',
            id: config.id + '-active'
        }];
    },
});
Ext.reg('minishop2-window-delivery-create', miniShop2.window.CreateDelivery);


miniShop2.window.UpdateDelivery = function (config) {
    config = config || {};

    Ext.applyIf(config, {
        baseParams: {
            action: 'mgr/settings/delivery/update',
        },
        bodyCssClass: 'tabs',
    });
    miniShop2.window.UpdateDelivery.superclass.constructor.call(this, config);
};
Ext.extend(miniShop2.window.UpdateDelivery, miniShop2.window.CreateDelivery, {

    getFields: function (config) {
        return [{
            xtype: 'modx-tabs',
            items: [{
                title: _('ms2_delivery'),
                layout: 'form',
                items: miniShop2.window.CreateDelivery.prototype.getFields.call(this, config),
            }, {
                title: _('ms2_payments'),
                items: [{
                    xtype: 'minishop2-grid-delivery-payments',
                    record: config.record,
                }]
            }]
        }];
    }

});
Ext.reg('minishop2-window-delivery-update', miniShop2.window.UpdateDelivery);