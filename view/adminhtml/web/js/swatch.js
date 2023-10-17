define(
[
    'Magento_Swatches/js/visual',
    'jquery',
    'uiRegistry'
], function (Component, jQuery, registry) {
        'use strict';
        return function (config) {
            Component(config);
            var attributeOptions = registry.get('swatch-visual-options-panel');
            if (typeof attributeOptions === 'object') {
                jQuery('#swatch-visual-options-panel').trigger('render');
            }
        }
    }
);
