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
namespace Ced\RequestToQuote\Block;

use Magento\Customer\Model\Context;

/**
 * Shopping cart block
 */
class QuoteReview extends \Magento\Checkout\Block\Cart\AbstractCart
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Url
     */
    protected $_catalogUrlBuilder;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Catalog\Model\Product $catalog,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,        
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Directory\Block\Data $country,
        \Magento\Framework\Locale\Currency $localeCurrency,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
    	$this->session = $checkoutSession;
        $this->catalog = $catalog;
        $this->_cartHelper = $cartHelper;
        $this->_catalogUrlBuilder = $catalogUrlBuilder;
        $this->_localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_country = $country;
        parent::__construct($context, $customerSession, $checkoutSession, $data);
        $this->_isScopePrivate = true;
        $this->httpContext = $httpContext;
        $this->_customerSession = $customerSession;        
    }      
    
    /**
     * @codeCoverageIgnore
     * @return int
     */
    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    } 


    /**
     * @return string
     */
    public function getContinueShoppingUrl()
    {
        $url = $this->getData('continue_shopping_url');
        if ($url === null) {
            $url = $this->_checkoutSession->getContinueShoppingUrl(true);
            if (!$url) {
                $url = $this->_urlBuilder->getUrl();
            }
            $this->setData('continue_shopping_url', $url);
        }
        return $url;
    }

    /**
     * Return list of available checkout methods
     *
     * @param string $alias Container block alias in layout
     * @return array
     */
    public function getMethods($alias)
    {
        $childName = $this->getLayout()->getChildName($this->getNameInLayout(), $alias);
        if ($childName) {
            return $this->getLayout()->getChildNames($childName);
        }
        return [];
    }

    /**
     * Return HTML of checkout method (link, button etc.)
     *
     * @param string $name Block name in layout
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMethodHtml($name)
    {
        $block = $this->getLayout()->getBlock($name);
        if (!$block) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid method: %1', $name));
        }
        return $block->toHtml();
    }

    /**
     * Return customer quote items
     *
     * @return array
     */
    public function getItems()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $requestedQuote = [];
        $quoteData = $this->_customerSession->getData('quoteData');

        if(isset($quoteData)){
            foreach ($quoteData as $data) {

                $productId = $data['product_id'];
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                $productType = $product->getTypeId();
                $storeId = $product->getStoreId();
                $visible = $product->isVisibleInSiteVisibility();
                $products[$product->getId()] = $storeId;
                $image = $product->getThumbnail();
                if(isset($image))
                {                     
                    $data['image'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).'catalog/product'.$product->getThumbnail();
                }
                else
                {        
                    $data['image'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC ).'frontend/Magento/luma/en_US/Magento_Catalog/images/product/placeholder/small_image.jpg';
                    
                }
                $data['product_url'] = $product->getProductUrl();
                $data['sku'] = $product->getSku();
                $data['price'] = $product->getPrice();
                if($data['quote_qty']>0 && $data['quote_price']>0)
                {
                    array_push($requestedQuote, $data);
                }
                
            }
        }
        
        return $requestedQuote;

    }

    public function getQuoteData(){
    	$quote = $this->session->getQuote();
    	foreach ($quote->getAllItems() as $item) {
    		//echo $item->getName();
    	}
    	//die;
    }

    public function getCustomerName(){
        return $this->_customerSession->getCustomer()->getName();
    }

    public function getCustomerEmail(){
        return $this->_customerSession->getCustomer()->getEmail();
    }

    public function getId(){
        return $this->_customerSession->getCustomer()->getId();
    }

    public function getSubtotal(){
        $subtotal = 0;
        $quoteData = $this->_customerSession->getData('quoteData');
        foreach ($quoteData as $value){
            $subtotal = $subtotal+$value['quote_price'];
        }

        return sprintf("%.2f", $subtotal);
    }

    public function getCountryCollection()
    {
        return $this->_country->getCountryHtmlSelect();

    }

    public function getActionUrl()
    {
        return $this->getUrl('requesttoquote/cart/getrate', ['_secure' => $this->getRequest()->isSecure()]);

    }

    public function getItemsCount()
    {
        $count = $this->_customerSession->getData('quoteData');
        return count($count);
    }

    public function getShippingRates(){
        $shippingRates = $this->_customerSession->getData('shipment');
        if(isset($shippingRates)){
            return $shippingRates;
        }
        else{
            return ;
        }
    }

    public function getToCurrency($amt)
    {
        return $this->_localeCurrency->getCurrency($this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getBaseCurrencyCode())->toCurrency($amt);
    }

    public function getAddress(){
        $address = $this->_customerSession->getData('address');
        if(isset($address)){
            return $address;
        }
        else{
            return ;
        }
    }

    public function getCurrencyCode(){

       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
       $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
       return $currency->getCurrencySymbol();
    }

    public function getRegionId()
    {
        $address = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $address = $objectManager->get('Magento\Customer\Model\Session')->getData('address');
        if(!empty($address)){
            $region = $address['region_id'];
            return $region === null ? 0 : $address['region_id'];
        }
        return null;
    }
}