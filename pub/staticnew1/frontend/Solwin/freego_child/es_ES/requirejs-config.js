(function(require){
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    'waitSeconds': 0,
    'map': {
        '*': {
            'ko': 'knockoutjs/knockout',
            'knockout': 'knockoutjs/knockout',
            'mageUtils': 'mage/utils/main',
            'rjsResolver': 'mage/requirejs/resolver'
        }
    },
    'shim': {
        'jquery/jquery-migrate': ['jquery'],
        'jquery/jquery.hashchange': ['jquery', 'jquery/jquery-migrate'],
        'jquery/jstree/jquery.hotkeys': ['jquery'],
        'jquery/hover-intent': ['jquery'],
        'mage/adminhtml/backup': ['prototype'],
        'mage/captcha': ['prototype'],
        'mage/common': ['jquery'],
        'mage/new-gallery': ['jquery'],
        'mage/webapi': ['jquery'],
        'jquery/ui': ['jquery'],
        'MutationObserver': ['es6-collections'],
        'tinymce': {
            'exports': 'tinymce'
        },
        'moment': {
            'exports': 'moment'
        },
        'matchMedia': {
            'exports': 'mediaCheck'
        },
        'jquery/jquery-storageapi': {
            'deps': ['jquery/jquery.cookie']
        }
    },
    'paths': {
        'jquery/validate': 'jquery/jquery.validate',
        'jquery/hover-intent': 'jquery/jquery.hoverIntent',
        'jquery/file-uploader': 'jquery/fileUploader/jquery.fileupload-fp',
        'jquery/jquery.hashchange': 'jquery/jquery.ba-hashchange.min',
        'prototype': 'legacy-build.min',
        'jquery/jquery-storageapi': 'jquery/jquery.storageapi.min',
        'text': 'mage/requirejs/text',
        'domReady': 'requirejs/domReady',
        'tinymce': 'tiny_mce/tiny_mce_src'
    },
    'deps': [
        'jquery/jquery-migrate'
    ],
    'config': {
        'mixins': {
            'jquery/jstree/jquery.jstree': {
                'mage/backend/jstree-mixin': true
            }
        },
        'text': {
            'headers': {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    }
};

require(['jquery'], function ($) {
    'use strict';

    $.noConflict();
});

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'rowBuilder':             'Magento_Theme/js/row-builder',
            'toggleAdvanced':         'mage/toggle',
            'translateInline':        'mage/translate-inline',
            'sticky':                 'mage/sticky',
            'tabs':                   'mage/tabs',
            'zoom':                   'mage/zoom',
            'collapsible':            'mage/collapsible',
            'dropdownDialog':         'mage/dropdown',
            'dropdown':               'mage/dropdowns',
            'accordion':              'mage/accordion',
            'loader':                 'mage/loader',
            'tooltip':                'mage/tooltip',
            'deletableItem':          'mage/deletable-item',
            'itemTable':              'mage/item-table',
            'fieldsetControls':       'mage/fieldset-controls',
            'fieldsetResetControl':   'mage/fieldset-controls',
            'redirectUrl':            'mage/redirect-url',
            'loaderAjax':             'mage/loader',
            'menu':                   'mage/menu',
            'popupWindow':            'mage/popup-window',
            'validation':             'mage/validation/validation',
            'welcome':                'Magento_Theme/js/view/welcome',
            'breadcrumbs':            'Magento_Theme/js/view/breadcrumbs'
        }
    },
    paths: {
        'jquery/ui': 'jquery/jquery-ui'
    },
    deps: [
        'jquery/jquery.mobile.custom',
        'mage/common',
        'mage/dataPost',
        'mage/bootstrap'
    ],
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Theme/js/view/add-home-breadcrumb': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            checkoutBalance:    'Magento_Customer/js/checkout-balance',
            address:            'Magento_Customer/address',
            changeEmailPassword: 'Magento_Customer/change-email-password',
            passwordStrengthIndicator: 'Magento_Customer/js/password-strength-indicator',
            zxcvbn: 'Magento_Customer/js/zxcvbn',
            addressValidation: 'Magento_Customer/js/addressValidation'
        }
    }
};

require.config(config);
})();
(function() {

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_CsImportExport
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

var config = {
    map: {
        '*': {
            jqueryform:   'Ced_CsImportExport/js/jquery.form',
        }
    }
   
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            compareList:            'Magento_Catalog/js/list',
            relatedProducts:        'Magento_Catalog/js/related-products',
            upsellProducts:         'Magento_Catalog/js/upsell-products',
            productListToolbarForm: 'Magento_Catalog/js/product/list/toolbar',
            catalogGallery:         'Magento_Catalog/js/gallery',
            priceBox:               'Magento_Catalog/js/price-box',
            priceOptionDate:        'Magento_Catalog/js/price-option-date',
            priceOptionFile:        'Magento_Catalog/js/price-option-file',
            priceOptions:           'Magento_Catalog/js/price-options',
            priceUtils:             'Magento_Catalog/js/price-utils',
            catalogAddToCart:       'Magento_Catalog/js/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Magento_Theme/js/view/breadcrumbs': {
                'Magento_Catalog/js/product/breadcrumbs': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
var config = {
"bundles": {
        "js/theme": [
            "globalNavigation",
            "globalSearch",
            "modalPopup",
            "useDefault",
            "loadingPopup",
            "collapsable"
        ]
    },
    map: {
        '*': {
        	
        	"Magento_CatalogInventory/js/components/use-config-settings":"Ced_CsProduct/js/components/use-config-settings",
        	"Magento_ConfigurableProduct/js/components/container-configurable-handler":"Ced_CsProduct/js/components/container-configurable-handler",
        	"Magento_CatalogInventory/js/components/qty-validator-changer":"Ced_CsProduct/js/components/qty-validator-changer",
        	"Magento_ConfigurableProduct/js/components/dynamic-rows-configurable":"Ced_CsProduct/js/components/dynamic-rows-configurable",
        	"Magento_ConfigurableProduct/js/components/associated-product-insert-listing":"Ced_CsProduct/js/components/associated-product-insert-listing",
        	"Magento_CatalogInventory/js/components/use-config-min-sale-qty":"Ced_CsProduct/js/components/use-config-min-sale-qty",
        	"Magento_ConfigurableProduct/js/components/custom-options-warning":"Ced_CsProduct/js/components/custom-options-warning",
        	"Magento_Downloadable/js/components/is-downloadable-handler":"Ced_CsProduct/js/components/is-downloadable-handler",
        	"Magento_Bundle/js/components/bundle-input-type":"Ced_CsProduct/js/components/bundle-input-type",
        	"Magento_Catalog/js/bundle-proxy-button":"Ced_CsProduct/js/bundle-proxy-button",	
        	"Magento_Catalog/js/custom-options-type":"Ced_CsProduct/js/custom-options-type",
        	"Magento_Catalog/component/static-type-input":"Ced_CsProduct/component/static-type-input",
        	"Magento_ConfigurableProduct/js/components/custom-options-price-type":"Ced_CsProduct/js/components/custom-options-price-type",
        	"Magento_Downloadable/js/components/file-uploader":"Ced_CsProduct/js/components/file-uploader",
        	"Magento_Downloadable/js/components/upload-type-handler":"Ced_CsProduct/js/components/upload-type-handler",
        	"Magento_Downloadable/js/components/price-handler":"Ced_CsProduct/js/components/price-handler",
        	"Magento_Bundle/js/components/bundle-checkbox":"Ced_CsProduct/js/components/bundle-checkbox",
        	"Magento_Bundle/js/components/bundle-option-qty":"Ced_CsProduct/js/components/bundle-option-qty",
        	"Magento_ConfigurableProduct/js/components/file-uploader":"Ced_CsProduct/js/components/file-uploader",
        	//"Magento_Ui/js/form/client":"Ced_CsProduct/js/form/client",
        	
        	"Magento_Backend/js/media-uploader":"Ced_CsProduct/js/media-uploader",
        	"Magento_Catalog/js/components/new-category":"Ced_CsProduct/js/components/new-category",
        	"Magento_Catalog/js/components/dynamic-rows-import-custom-options":"Ced_CsProduct/js/components/dynamic-rows-import-custom-options",
        	"Magento_Catalog/js/components/attribute-set-select":"Ced_CsProduct/js/components/attribute-set-select",
        	"Magento_Catalog/js/components/import-handler":"Ced_CsProduct/js/components/import-handler",
        	//"mage/tabs" : "mage/backend/tabs",
          "Magento_Catalog/catalog/type-events" : "Ced_CsProduct/catalog/type-events",
	     "Magento_Catalog/js/product/weight-handler" : "Ced_CsProduct/js/product/weight-handler",
	     "Magento_Catalog/catalog/apply-to-type-switcher" : "Ced_CsProduct/catalog/apply-to-type-switcher",
	     form : "mage/backend/form",
	     calendar : "mage/calendar",
	     productGallery : 'Ced_CsProduct/js/product-gallery',
	     newCategoryDialog:  'Ced_CsProduct/js/new-category-dialog',
	     baseImage : 'Ced_CsProduct/catalog/base-image-uploader',
	     suggest : "mage/backend/suggest",
	     "floatingHeader" : "mage/backend/floating-header",
	     "button" : "mage/backend/button",
	   
	   // "jquery/jquery.tabs" : "jquery/jquery.tabs",
	     //"toolbar_entry" : "Ced_CsProduct/catalog/toolbar_entry",
	     "backend/bootstrap" : "mage/backend/bootstrap",
	     "globals" : "mage/adminhtml/globals",
	     "dropdown-old" : "mage/dropdown_old",
	     "product-attributes" : "Ced_CsProduct/catalog/product-attributes",
	     "select" : "Magento_Ui/js/form/element/select",
	     "integration" : "Ced_CsProduct/catalog/integration",
	     "notification" : "mage/backend/notification",
	     "productAttributes" : "Ced_CsProduct/catalog/product-attributes",
	     
	     "Magento_ConfigurableProduct/js/advanced-pricing-handler" : "Ced_CsProduct/js/ConfigurableProduct/advanced-pricing-handler",
	     "Magento_ConfigurableProduct/js/configurable-type-handler" : "Ced_CsProduct/js/ConfigurableProduct/configurable-type-handler",
	     "Magento_ConfigurableProduct/js/options/price-type-handler" : "Ced_CsProduct/js/ConfigurableProduct/options/price-type-handler",
	     "Magento_ConfigurableProduct/js/variations/product-grid" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/product-grid",
	     "Magento_ConfigurableProduct/js/variations/variations" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/variations",
	     "Magento_ConfigurableProduct/js/variations/steps/attributes_values" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/attributes_values",
	     "Magento_ConfigurableProduct/js/variations/steps/bulk" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/bulk",
	     "Magento_ConfigurableProduct/js/variations/steps/select_attributes" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/select_attributes",
	     "Magento_ConfigurableProduct/js/variations/steps/summary" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/summary",
	     "Magento_Downloadable/template/components/file-uploader.html":"Ced_CsProduct/template/components/file-uploader.html",
	     "Magento_ConfigurableProduct/js/components/modal-configurable":"Ced_CsProduct/js/components/modal-configurable",
	     "Magento_Downloadable/downloadable-type-handler" : "Ced_CsProduct/js/DownloadableProduct/downloadable-type-handler",

	    "mage/adminhtml/browser":"Ced_CsProduct/js/adminhtml/browser",
	     
	     "Magento_Bundle/js/bundle-product" : "Ced_CsProduct/js/BundleProduct/bundle-product",
          "Magento_Bundle/js/bundle-type-handler" : "Ced_CsProduct/js/BundleProduct/bundle-type-handler",

	     "groupedProduct" : "Ced_CsProduct/js/GroupedProduct/grouped-product",

	     "Magento_Theme/js/sortable" : "Ced_CsProduct/js/Theme/sortable",

	     //"js/theme" : "Ced_CsProduct/js/Theme_admin_backend/theme",

	     "Magento_Catalog/js/custom-options" : "Ced_CsProduct/js/custom-options",

	     "mediabrowser" : "jquery/jstree/jquery.jstree",
	     "folderTree" : "Ced_CsProduct/js/folder-tree",

	     "Magento_Variable/variables" : "Ced_CsProduct/js/variables",
	     "treeSuggest" : "mage/backend/tree-suggest",
	     "jstree" : "jquery/jstree/jquery.jstree",
	     "actionLink" : "mage/backend/action-link",
	   
	     newVideoDialog : "Ced_CsProduct/js/video/new-video-dialog",
	     openVideoModal:  "Ced_CsProduct/js/video/video-modal",
	     "Magento_ProductVideo/js/get-video-information" : "Ced_CsProduct/js/video/get-video-information",

	     "global" : "mage/adminhtml/globals"
        }
    }
    
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            addToCart: 'Magento_Msrp/js/msrp'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_CsVendorProductAttribute
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

var config = {
    map: {
        '*': {
             "Magento_Catalog/js/options" : "Ced_CsVendorProductAttribute/js/options",
	        "global" : "mage/adminhtml/globals",
	        "Magento_Catalog/catalog/product/attribute/unique-validate":"Ced_CsVendorProductAttribute/js/unique-validate"
	        
        }
    }
};

require.config(config);
})();
(function() {
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_RequestToQuote
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

var config = {
    map: {
        '*': {
            jqueryform:   'Ced_RequestToQuote/js/jquery.form',
        }
    }
   
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            creditCardType: 'Magento_Payment/cc-type'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftMessage:    'Magento_Sales/gift-message',
            ordersReturns:  'Magento_Sales/orders-returns'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            discountCode:           'Magento_Checkout/js/discount-codes',
            shoppingCart:           'Magento_Checkout/js/shopping-cart',
            regionUpdater:          'Magento_Checkout/js/region-updater',
            sidebar:                'Magento_Checkout/js/sidebar',
            checkoutLoader:         'Magento_Checkout/js/checkout-loader',
            checkoutData:           'Magento_Checkout/js/checkout-data',
            proceedToCheckout:      'Magento_Checkout/js/proceed-to-checkout'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            orderBySkuFailure:  'Magento_AdvancedCheckout/js/order-by-sku-failure',
            fileChooser:        'Magento_AdvancedCheckout/js/file-chooser'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            quickSearch: 'Magento_Search/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            transparent: 'Magento_Payment/transparent'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            fileElement: 'Magento_CustomerCustomAttributes/file-element'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            bundleOption:   'Magento_Bundle/bundle',
            priceBundle:    'Magento_Bundle/js/price-bundle',
            slide:          'Magento_Bundle/js/slide',
            productSummary: 'Magento_Bundle/js/product-summary'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            captcha: 'Magento_Captcha/captcha'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

var config = {
    map: {
        '*': {
            amazonLogout: 'Amazon_Login/js/amazon-logout',
            amazonOAuthRedirect: 'Amazon_Login/js/amazon-redirect',
            amazonCsrf: 'Amazon_Login/js/amazon-csrf'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    paths: {
        'ui/template': 'Magento_Ui/templates'
    },
    map: {
        '*': {
            uiElement:      'Magento_Ui/js/lib/core/element/element',
            uiCollection:   'Magento_Ui/js/lib/core/collection',
            uiComponent:    'Magento_Ui/js/lib/core/collection',
            uiClass:        'Magento_Ui/js/lib/core/class',
            uiEvents:       'Magento_Ui/js/lib/core/events',
            uiRegistry:     'Magento_Ui/js/lib/registry/registry',
            consoleLogger:  'Magento_Ui/js/lib/logger/console-logger',
            uiLayout:       'Magento_Ui/js/core/renderer/layout',
            buttonAdapter:  'Magento_Ui/js/form/button-adapter'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            catalogSearch: 'Magento_CatalogSearch/form-mini'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            configurable: 'Magento_ConfigurableProduct/js/configurable'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            downloadable: 'Magento_Downloadable/downloadable'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            toggleGiftCard: 'Magento_GiftCard/toggle-gift-card'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Magento_CheckoutAgreements/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Magento_CheckoutAgreements/js/model/set-payment-information-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            recentlyViewedProducts: 'Magento_Reports/js/recently-viewed'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            wishlist:       'Magento_Wishlist/js/wishlist',
            addToWishlist:  'Magento_Wishlist/js/add-to-wishlist',
            wishlistSearch: 'Magento_Wishlist/js/search'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            requireCookie: 'Magento_Cookie/js/require-cookie',
            cookieNotices: 'Magento_Cookie/js/notices'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            transparent: 'Magento_Payment/transparent'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            orderReview: 'Magento_Paypal/order-review',
            paypalCheckout: 'Magento_Paypal/js/paypal-checkout'
        }
    },
    paths: {
        paypalInContextExpressCheckout: 'https://www.paypalobjects.com/api/checkout'
    },
    shim: {
        paypalInContextExpressCheckout: {
            exports: 'paypal'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            transparent: 'Magento_Payment/transparent'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'taxToggle': 'Magento_Weee/tax-toggle'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftCard:       'Magento_GiftCardAccount/js/gift-card',
            paymentMethod:  'Magento_GiftCardAccount/js/payment-method'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            advancedSearch: 'Magento_GiftRegistry/advanced-search',
            giftRegistry: 'Magento_GiftRegistry/gift-registry',
            addressOption: 'Magento_GiftRegistry/address-option',
            searchByChanged: 'Magento_GiftRegistry/js/search-by-changed'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftOptions:    'Magento_GiftMessage/gift-options',
            extraOptions:   'Magento_GiftMessage/extra-options'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            giftWrapping: 'Magento_GiftWrapping/gift-wrapping'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            pageCache:  'Magento_PageCache/js/page-cache'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            braintree: 'https://js.braintreegateway.com/js/braintree-2.32.0.min.js'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            ticker:     'Magento_CatalogEvent/js/ticker',
            carousel:   'Magento_CatalogEvent/js/carousel'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multipleWishlist: 'Magento_MultipleWishlist/js/multiple-wishlist'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            multiShipping: 'Magento_Multishipping/js/multi-shipping',
            orderOverview: 'Magento_Multishipping/js/overview',
            payment: 'Magento_Multishipping/js/payment',
            billingLoader: 'Magento_Checkout/js/checkout-loader'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
var config = {
    map: {
        '*': {
            csjquery: "Ced_CsMarketplace/dist/js/jquery.min",
            csnoconflict: "Ced_CsMarketplace/js/ced/csmarketplace/noconflict",
            csvendor: "Ced_CsMarketplace/js/ced/csmarketplace/vendor",
            csbootstrap: "Ced_CsMarketplace/bower_components/bootstrap/dist/js/bootstrap",
            metismenu : "Ced_CsMarketplace/bower_components/metisMenu/dist/metisMenu.min",
            csvendorpanel: "Ced_CsMarketplace/dist/js/sb-admin-2",
            checkoutbalance:    'Magento_Customer/js/checkout-balance',
            captcha: 'Magento_Captcha/captcha',
            flot: "Ced_CsMarketplace/js/ced/csmarketplace/flot/jquery.flot",
            flotResize: "Ced_CsMarketplace/js/ced/csmarketplace/flot/jquery.flot.resize.min",
            raphael : "Ced_CsMarketplace/bower_components/raphael/raphael-min",
            morrisMin : "Ced_CsMarketplace/js/ced/csmarketplace/morris.min",
            csvmap : "Ced_CsMarketplace/js/ced/csmarketplace/jqvmap/jquery.vmap",
            csvmapworld : "Ced_CsMarketplace/js/ced/csmarketplace/jqvmap/maps/jquery.vmap.world",
            ceddropdown : "Ced_CsMarketplace/js/view/header"    
        }
    },
    deps: [
        "jquery",
        "jquery/ui",
        "jquery/validate",
        "mage/translate"
    ]
};



require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            loadPlayer: 'Magento_ProductVideo/js/load-player',
            fotoramaVideoEvents: 'Magento_ProductVideo/js/fotorama-add-video-events'
        }
    },
    shim: {
        vimeoAPI: {}
    }
};

require.config(config);
})();
(function() {

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_CsMultiShipping
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

var config = {
    map: {
        '*': {
            regionUpdater:   'Magento_Checkout/js/region-updater'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsOrder
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */


var config = {
    map: {
        '*': {
            "mage/backend/tabs" : 'mage/backend/tabs',
            "floatingHeader":       "mage/backend/floating-header",
            "Magento_Sales/order/giftoptions_tooltip" : "Ced_CsOrder/js/giftoptions_tooltip"
        }
    },
    deps: [
        "jquery",
        "jquery/ui",
        "jquery/validate",
        "mage/translate"
    ]
};



require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            rmaTrackInfo:   'Magento_Rma/rma-track-info',
            rmaCreate:      'Magento_Rma/rma-create'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * This file is part of the Klarna KP module
 *
 * (c) Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/get-payment-information': {
                'Klarna_Kp/js/action/override': true
            }
        }
    },
    map: {
        '*': {
            klarnapi: 'https://x.klarnacdn.net/kp/lib/v1/api.js'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    'paths': {
        'dmpt': 'Dotdigitalgroup_Email/js/dmpt'
    },
    'shim': {
        'dmpt': {
            exports: '_dmTrack'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright 2016 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */
var config = {
    map: {
        '*': {
            amazonCore: 'Amazon_Payment/js/amazon-core',
            amazonWidgetsLoader: 'Amazon_Payment/js/amazon-widgets-loader',
            amazonButton: 'Amazon_Payment/js/amazon-button',
            amazonProductAdd: 'Amazon_Payment/js/amazon-product-add',
            bluebird: 'Amazon_Payment/js/lib/bluebird.min',
            amazonPaymentConfig: 'Amazon_Payment/js/model/amazonPaymentConfig',
            sjcl: 'Amazon_Payment/js/lib/sjcl.min',
            //this is a fix for Magento 2.1 (ajax / validation fails on add to cart)
            catalogAddToCart: 'Amazon_Payment/js/catalog-add-to-cart'
        }
    },
    config: {
        mixins: {
            'Amazon_Payment/js/action/place-order': {
                'Amazon_Payment/js/model/place-order-mixin': true
            }
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            editTrigger: 'mage/edit-trigger',
            addClass: 'Magento_Translation/add-class'
        }
    },
    deps: [
        'mage/translate-inline'
    ]
};

require.config(config);
})();
(function() {
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            'Magento_Checkout/js/model/shipping-rates-validation-rules':'Magestore_OneStepCheckout/js/model/shipping-rates-validation-rules'
        }
    }
};

require.config(config);
})();
(function() {
var config = {
    map: {
        '*': {
            cpowlcarousel: 'Solwin_Cpanel/js/owl.carousel',
        }
    }
};
require.config(config);
})();
(function() {
var config = {
    paths: {
        temandoCheckoutFieldsDefinition: 'Temando_Shipping/js/model/fields-definition',
        temandoCustomerAddressRateProcessor: 'Temando_Shipping/js/model/shipping-rate-processor/customer-address',
        temandoNewAddressRateProcessor: 'Temando_Shipping/js/model/shipping-rate-processor/new-address',
        temandoShippingRatesValidator: 'Temando_Shipping/js/model/shipping-rates-validator/temando',
        temandoShippingRatesValidationRules: 'Temando_Shipping/js/model/shipping-rates-validation-rules/temando'
    },
    map: {
        'Magento_Checkout/js/model/shipping-rate-service': {
            'Magento_Checkout/js/model/shipping-rate-processor/customer-address' : 'temandoCustomerAddressRateProcessor',
            'Magento_Checkout/js/model/shipping-rate-processor/new-address' : 'temandoNewAddressRateProcessor'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * @copyright  Vertex. All rights reserved.  https://www.vertexinc.com/
 * @author     Mediotype                     https://www.mediotype.com/
 */

var config = {
    map: {
        '*': {
            'set-checkout-messages': 'Vertex_Tax/js/model/set-checkout-messages'
        }
    }
};

require.config(config);
})();
(function() {
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    deps: [
        'Magento_Theme/js/responsive',
        'Magento_Theme/js/theme'
    ]
};

require.config(config);
})();



})(require);