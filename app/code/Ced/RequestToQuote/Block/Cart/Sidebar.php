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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\RequestToQuote\Block\Cart;

use Magento\Store\Model\ScopeInterface;

/**
 * Cart sidebar block
 */
class Sidebar extends \Magento\Checkout\Block\Cart\Sidebar
{
    /**
     * Xml pah to checkout sidebar display value
     */
    const XML_PATH_CHECKOUT_QUOTE_SIDEBAR_DISPLAY = 'quote/sidebar/display';

    /**
     * Xml pah to checkout sidebar count value
     */
    const XML_PATH_CHECKOUT_QUOTE_SIDEBAR_COUNT = 'quote/sidebar/count';

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    
    public $_objectManager;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Helper\Image $imageHelper,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManager $storeManager,	
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        array $data = []
    ) {
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($data['jsLayout']);
            unset($data['jsLayout']);
        } 
        parent::__construct($context, $customerSession, $checkoutSession, $imageHelper, $jsLayoutDataProvider, $data);
        $this->_isScopePrivate = false;
        $this->imageHelper = $imageHelper;
        $this->storeManager = $storeManager;
        $this->_objectManager = $objectManager;
       // $this->getSessionaData();
    }

    /**
     * Returns minicart config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'shoppingCartUrl' => $this->getShoppingCartUrl(),
            'quoteCartUrl' => $this->getQuoteCartUrl(),
            'checkoutUrl' => $this->getCheckoutUrl(),
            'updateItemQtyUrl' => $this->getUpdateItemQtyUrl(),
            'removeItemUrl' => $this->getRemoveItemUrl(),
            'imageTemplate' => $this->getImageHtmlTemplate(),
            'baseUrl' => $this->getBaseUrl(),
            'minicartMaxItemsVisible' => $this->getMiniCartMaxItemsCount(),
            'websiteId' => $this->_storeManager->getStore()->getWebsiteId()
        ];
    }

    /**
     * @return string
     */
    public function getImageHtmlTemplate()
    {
        return $this->imageHelper->getFrame()
            ? 'Magento_Catalog/product/image'
            : 'Magento_Catalog/product/image_with_borders';
    }

    /**
     * Get one page checkout page url
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout');
    }

    /**
     * Get shopping cart page url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getQuoteCartUrl()
    {
        return $this->getUrl('requesttoquote/cart/index',array('quote'=>'quotedata'));
    }

    /**
     * Get update cart item url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getUpdateItemQtyUrl()
    {
        return $this->getUrl('checkout/sidebar/updateItemQty', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Get remove cart item url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getRemoveItemUrl()
    {
        return $this->getUrl('checkout/sidebar/removeItem', ['_secure' => $this->getRequest()->isSecure()]);
    }

    /**
     * Define if Mini Shopping Cart Pop-Up Menu enabled
     *
     * @return bool
     * @codeCoverageIgnore
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsNeedToDisplaySideBar()
    {
        return (bool)$this->_scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_QUOTE_SIDEBAR_DISPLAY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Return totals from custom quote if needed
     *
     * @return array
     */
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $quote = $this->getCustomQuote() ? $this->getCustomQuote() : $this->getQuote();
            $this->_totals = $quote->getTotals();
        }
        return $this->_totals;
    }

    /**
     * Retrieve subtotal block html
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getTotalsHtml()
    {
        return $this->getLayout()->getBlock('checkout.cart.minicart.totals')->toHtml();
    }

    /**
     * Return base url.
     *
     * @codeCoverageIgnore
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Return max visible item count for minicart
     *
     * @return int
     */
    private function getMiniCartMaxItemsCount()
    {
        return (int)$this->_scopeConfig->getValue('checkout/sidebar/count', ScopeInterface::SCOPE_STORE);
    }
    public function getSessionaData(){
    	return $this->_customerSession->getData('quoteData');
    	
    }
    public function getProduct($productid){
    	return $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productid);
    	 
    }
    
    public function getFormattedPrice($price){
    	
    	$priceHelper = $this->_objectManager->create('Magento\Framework\Pricing\Helper\Data');
    	return  $priceHelper->currency($price, true, false);
    }
    public function getQuoteSubtotal(){
    	
    	$quotedata = $this->_customerSession->getData('quoteData');
    	$subtotal = 0;
    	if(!empty($quotedata)){
    		
    		foreach($quotedata as $_quoteData){
    			
    			$subtotal+= $_quoteData['quote_price'];
    		}
    	}
    	$priceHelper = $this->_objectManager->create('Magento\Framework\Pricing\Helper\Data');
    	return  $priceHelper->currency($subtotal, true, false);
    	
    }

    public function getImage($product_id)
    {
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
        $image = $product->getThumbnail();
                if(isset($image))
                {                     
                    $pimage = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).'catalog/product'.$product->getThumbnail();
                }
                else
                {        
                    $pimage = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC ).'frontend/Magento/luma/en_US/Magento_Catalog/images/product/placeholder/small_image.jpg';
                    
                }
                return $pimage;
    }
    
    public function getAllowedCustomerGroups(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configvalue = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $configvalue->getValue('requesttoquote_configuration/active/custgroups');
        $custgroups = explode(',',$value);

        return $custgroups;

    }
}
