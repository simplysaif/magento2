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

class EditPo extends \Magento\Framework\View\Element\Template {
	

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
			\Magento\Store\Model\StoreManager $storeManager,
			\Magento\Customer\Model\Customer $customerData
			
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
		$this->po_id = $this->_request->getParam('poId');
		$this->_customerData = $customerData;
		parent::__construct ( $context );
	}

	public function _construct() {

		$this->setTemplate ( 'customer/editpo.phtml' );
		$this->getUrl();
		$customer = $this->_getSession->getCustomer();
		$customer_Id = $customer->getId ();
		$po_increment_id = $this->_po->load($this->po_id)->getPoId();
		$poModel = $this->_podetail->getCollection ()->addFieldtoFilter('po_id', $po_increment_id);
		$this->setCollection ( $poModel );
		
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
		$this->pageConfig->getTitle ()->set ( "View PO #".$this->_po->load($this->po_id)->getPoIncrementId());
		return $this;
	}


	public function getPagerHtml() {
		return $this->getChildHtml ( 'pager' );
	}

	public function getSendUrl(){

        return $this->getUrl('requesttoquote/customer/savequotes', ['quoteId'=> $this->po_id]);
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


    /*public function getQuote(){
    	return $this->_quote->load($this->quote_id);
    }*/

     public function getCurrencyCode(){

       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
       $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
       return $currency->getCurrencySymbol();
    }

    public function getPoStatus(){
    	$po_status = $this->_po->load($this->po_id)->getStatus();
    	if($po_status == 0){
    		return __('pending');
    	}elseif($po_status == 1){
    		return __('Confirmed');
    	}
    	elseif($po_status == 2){
    		return __('Declined');
    	}else{
    		return __('Ordered');
    	}
    }

    public function getPoData(){
        $po_increment_id = $this->_po->load($this->po_id,'po_id')->getData('po_increment_id');
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $collection = $this->_podetail->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToFilter('po_id', $po_increment_id);
        return $collection;
    }

    public function getRemainingData($product_id){
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $remcollection = $this->_podetail->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToFilter('product_id', $product_id)->addFieldToFilter('status', '1')->getLastItem();
        $remqty = $remcollection->getRemainingQty();
        return $remqty;
    }

    public function getShippingAmount(){
    	$quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
    	$shipping_amount = $this->_quote->load($quote_id)->getShippingAmount();
    	return $shipping_amount;
    }

    public function getGrandTotal(){
    	return $this->_po->load($this->po_id)->getShippingAmount();
    }

    public function getShippingMethod(){
    	$quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
    	$shipping_method = $this->_quote->load($quote_id)->getShipmentMethod();
    	return $shipping_method;
    }

    public function getCustomerId()
    {
        $customer_id = $this->_po->load($this->po_id,'po_id')->getData('po_customer_id');
        return $customer_id;
    }

    public function getCustomer($customer_id)
    {
        $customer = $this->_customerData->load($customer_id);
        return $customer;
    }

    public function getCustomerAddress()
    {
        $address = [];
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $addressdata = $this->_quote->load($quote_id);
        $address['country'] = $addressdata->getCountry();
        $address['state'] = $addressdata->getState();
        $address['city'] = $addressdata->getCity();
        $address['pincode'] = $addressdata->getPincode();
        $address['street'] = $addressdata->getAddress();
        $address['telephone'] = $addressdata->getTelephone();
        return $address;
    }

    public function getPoInfo(){
        return $this->_po->load($this->po_id);
    }
}