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

namespace Ced\RequestToQuote\Block\Customer;

use Magento\Framework\View\Element\Template\Context;

class EditQuote extends \Magento\Framework\View\Element\Template {
	

	public function __construct(
			Context $context, 
			\Magento\Customer\Model\Session $customerSession, 
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Framework\App\Request\Http $request,
			\Ced\RequestToQuote\Model\Quote $quote,
			\Ced\RequestToQuote\Model\QuoteDetail $quoteDetail,
			\Ced\RequestToQuote\Model\Message $message,
			\Ced\RequestToQuote\Model\Po $po,
			\Ced\RequestToQuote\Model\PoDetail $podetail,
			\Magento\Catalog\Model\Product $catalog,
			\Magento\Store\Model\StoreManager $storeManager
			
		) {

		$this->_getSession = $customerSession;
		$this->_objectManager = $objectManager;
		$this->_request = $request;
		$this->_quote = $quote;
		$this->_quoteDetail = $quoteDetail;
		$this->_message = $message;
		$this->_po = $po;
		$this->_podetail = $podetail;
		$this->_catalog = $catalog;
		$this->storeManager = $storeManager;
		$this->quote_id = $this->_request->getParam('quoteId');
		parent::__construct ( $context );
	}

	public function _construct() {

		$this->setTemplate ( 'customer/editquote.phtml' );
		$this->getUrl();
		$customer = $this->_getSession->getCustomer();
		$customer_Id = $customer->getId ();
		$quoteModel = $this->_quoteDetail->getCollection ()->addFieldtoFilter('quote_id', $this->quote_id);
		$this->setCollection ( $quoteModel );
		
	}   
	

	/**
	 * Prepare Pager Layout
	 */
	protected function _prepareLayout() {

		parent::_prepareLayout ();
		if ($this->getCollection ()) {
			$pager = $this->getLayout ()->createBlock ( 'Magento\Theme\Block\Html\Pager', 'my.custom.pager' )->setLimit ( 5 )->setCollection ( $this->getCollection () );
			$this->setChild ( 'pager', $pager );
		}
		$this->pageConfig->getTitle ()->set ( "Edit Quote #".$this->_quote->load($this->quote_id)->getQuoteIncrementId());
		return $this;
	}


	public function getPagerHtml() {
		return $this->getChildHtml ( 'pager' );
	}

	public function getSendUrl(){

        return $this->getUrl('requesttoquote/customer/savequotes', ['quoteId'=> $this->quote_id]);
    }


    public function getBackUrl(){

        return $this->getUrl('carttoquote/myquote/index');
    }


    public function getVendor($id){
    	return "Admin Product";
    }


    public function getProduct($product_id){
        $product = $this->_catalog->load($product_id);        
        return $product;
    }

    public function getProducturl($product_id){
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $producturl = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id)->getProductUrl();
        return $producturl;
    }

    

    public function getProductImage($product_id){
        
        $product = $this->_catalog->load($product_id);        
        $image = $product->getThumbnail();
        
        if(isset($image)) {                     
            $productImage = $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA ).'catalog/product'.$product->getThumbnail();
        }

        else {        
            $productImage = $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_STATIC ).'frontend/Magento/luma/en_US/Magento_Catalog/images/product/placeholder/small_image.jpg';            
        }
        return $productImage;
    }

     public function getChatHistory(){
		$chatData = $this->_message->getCollection()->addFieldtoFilter('quote_id', $this->quote_id);
		return $chatData;
    }


    public function getQuote(){
    	return $this->_quote->load($this->quote_id);
    }

     public function getCurrencyCode(){

       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
       $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
       return $currency->getCurrencySymbol();
    }
}