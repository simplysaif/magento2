<?php
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
 * @package     Ced_AdvanceConfigurable
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\AdvanceConfigurable\Block\Product\View\Type;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Configurable extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct = null;

    /**
     * Current customer
     *
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Prices
     *
     * @var array
     */
    protected $_prices = [];

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Data $imageHelper
     */
    protected $helper;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ConfigurableAttributeData
     */
    protected $configurableAttributeData;

    protected $_scopeConfigManager;

    protected $_storeId = 0;

    public $allowed = array();

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param CurrentCustomer $currentCustomer
     * @param PriceCurrencyInterface $priceCurrency
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        CurrentCustomer $currentCustomer,
        PriceCurrencyInterface $priceCurrency,
        ConfigurableAttributeData $configurableAttributeData,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->jsonEncoder = $jsonEncoder;
        $this->catalogProduct = $catalogProduct;
        $this->currentCustomer = $currentCustomer;
        $this->configurableAttributeData = $configurableAttributeData;
        parent::__construct(
            $context,
            $arrayUtils,
            $jsonEncoder,
            $helper,
            $catalogProduct,
            $currentCustomer,
            $priceCurrency,
            $configurableAttributeData,
            $data
        );
    }

    /**
     * Get allowed attributes
     *
     * @return array
     */
    public function getAllowAttributes()
    {
        return $this->getProduct()->getTypeInstance()->getConfigurableAttributes($this->getProduct());
    }

    /**
     * Check if allowed attributes have options
     *
     * @return bool
     */
    public function hasOptions()
    {
        $attributes = $this->getAllowAttributes();
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute $attribute */
                if ($attribute->getData('options')) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get Allowed Products
     *
     * @return array
     */
    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = [];
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach ($allProducts as $product) {
                if ($product->isSaleable() || $skipSaleableCheck) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }

    /**
     * Retrieve current store
     *
     * @return \Magento\Store\Model\Store
     */
    public function getCurrentStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Returns additional values for js config, con be overridden by descendants
     *
     * @return array
     */
    protected function _getAdditionalConfig()
    {
        return [];
    }

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $store = $this->getCurrentStore();
        $currentProduct = $this->getProduct();

        $regularPrice = $currentProduct->getPriceInfo()->getPrice('regular_price');
        $finalPrice = $currentProduct->getPriceInfo()->getPrice('final_price');

        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        $attributesData = $this->configurableAttributeData->getAttributesData($currentProduct, $options);

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $this->getOptionPrices(),
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_registerJsPrice($regularPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_registerJsPrice(
                        $finalPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalPrice->getAmount()->getValue()),
                ],
            ],
            'productId' => $currentProduct->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($options['images']) ? $options['images'] : [],
            'index' => isset($options['index']) ? $options['index'] : [],
        ];

        if ($currentProduct->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $this->jsonEncoder->encode($config);
    }

    /**
     * @return array
     */
    protected function getOptionPrices()
    {
        $prices = [];
        foreach ($this->getAllowProducts() as $product) {
            $priceInfo = $product->getPriceInfo();

            $prices[$product->getId()] =
                [
                    'oldPrice' => [
                        'amount' => $this->_registerJsPrice(
                            $priceInfo->getPrice('regular_price')->getAmount()->getValue()
                        ),
                    ],
                    'basePrice' => [
                        'amount' => $this->_registerJsPrice(
                            $priceInfo->getPrice('final_price')->getAmount()->getBaseAmount()
                        ),
                    ],
                    'finalPrice' => [
                        'amount' => $this->_registerJsPrice(
                            $priceInfo->getPrice('final_price')->getAmount()->getValue()
                        ),
                    ]
                ];
        }
        return $prices;
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }

     public function getsingleconfigurableoption()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_scopeConfigManager = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $allowed_attributes = $this->_scopeConfigManager->getValue('configuration/advance/allowed_attributes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        $allowed = explode(',',$allowed_attributes);
        $json = json_decode($this->getJsonConfig());
        $configurable = json_decode(json_encode($json), True);
        foreach($configurable['attributes'] as $key => $value)
        {
            if(!in_array($key,$allowed))
            {
                return false;
            }
        }
        return $configurable;
        
    }

    public function getdoubleconfigurableoption()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_scopeConfigManager = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $allowed_attributes = $this->_scopeConfigManager->getValue('configuration/advance/allowed_attributes', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        $allowed = explode(',',$allowed_attributes);
        $json = json_decode($this->getJsonConfig());
        $configurable = json_decode(json_encode($json), True);
       foreach($configurable['attributes'] as $key => $value)
       {
            if(!in_array($key,$allowed))
            {
                return false;
            }
       }
       return $configurable;
        
    }

    public function getStore() 
    {
        if ($this->_storeId) { $storeId = (int)$this->_storeId; 
        }
        else { $storeId =  isset($_REQUEST['store'])?(int) $_REQUEST['store']:null; 
        }
        return $this->_storeManager->getStore($storeId);
    }

    public function isEnabled()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_scopeConfigManager = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        return $this->_scopeConfigManager->getValue('configuration/advance/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

}
