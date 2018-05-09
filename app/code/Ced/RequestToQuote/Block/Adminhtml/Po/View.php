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

namespace Ced\RequestToQuote\Block\Adminhtml\Po;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class View extends \Magento\Framework\View\Element\Template
{

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
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Ced\RequestToQuote\Model\Po $po,
        \Ced\RequestToQuote\Model\Quote $quote,
        \Ced\RequestToQuote\Model\PoDetail $podesc,
        \Ced\RequestToQuote\Model\QuoteDetail $quotedesc,
        \Magento\Customer\Model\Group $custgroup,
        \Magento\Customer\Model\Customer $customerData,
        \Magento\Catalog\Model\Product $catalog,
        array $data = []
    ) {

        $this->_paymentHelper = $paymentHelper;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_po =$po;
        $this->_quote =$quote;
        $this->_quotedesc =$quotedesc;
        $this->_podesc = $podesc;
        $this->_customerData = $customerData;
        $this->_custgroup = $custgroup;
        $this->_catalog = $catalog;
        $this->po_id = $context->getRequest()->getParam('id');

        /*print_r($this->po_id);
        print_r($this->_po->load($this->po_id,'po_id')->getData('po_increment_id'));
        die("sdhfjd");*/

        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    public function getPoIncrementId()
    {

        return $this->_po->load($this->po_id,'po_id')->getData('po_increment_id');
    }

    public function getCreatedAt()
    {

        return $this->_po->load($this->po_id,'po_id')->getData('created_at');
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

    public function getCustomerGroup($customer_id)
    {

        $customergrp = $this->getCustomer($customer_id)->getGroupId();
        $custgroupName = $this->_custgroup->load($customergrp)->getCustomerGroupCode();

        return $custgroupName;
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


    public function getProductId()
    {
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $qproducts = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToSelect('product_id');
        foreach ($qproducts->getData() as $value) {
            $prod[] = $value['product_id'];
        }
        return $prod;
    }


    public function getProduct($product_id)
    {
        $product = $this->_catalog->load($product_id);
        return $product;
    }

    public function getQuoteData()
    {
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $poData = $this->_quote->load($quote_id);
        return $poData;
    }

    public function getProductIndividualPrice($product_id)
    {
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $productprice = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id', $quote_id)->addFieldToFilter('product_id', $product_id)->addFieldToSelect('unit_price');
        foreach($productprice->getData() as $price){
            $value = $price['unit_price'];
        }

        return $value;
    }



    public function getCancelUrl(){

        return $this->getUrl('requesttoquote/quotes/view');
    }

    public function getNoOfProducts(){
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $no_of_products = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToSelect('product_id');
        return count($no_of_products);

    }

    public function getCurrencyCode(){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
        return $currency->getCurrencySymbol();
    }

    public function getGrandTotal(){
        $total = $this->_po->load($this->po_id,'po_id')->getData('po_price');
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $shipping = $this->_quote->load($quote_id)->getShippingAmount();
        //return $total+$shipping;
        return $total;
    }

    public function getRemainingData($product_id){
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $remcollection = $this->_podesc->getCollection()
                                ->addFieldToFilter('quote_id',$quote_id)
                                ->addFieldToFilter('product_id', $product_id)
                                //->addFieldToFilter('status', '1')
                                ->getLastItem();
        //print_r($remcollection->getData());
        $remqty = $remcollection->getRemainingQty();
        return $remqty;

    }

    public function getPoData(){
        $po_increment_id = $this->_po->load($this->po_id,'po_id')->getData('po_increment_id');
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $collection = $this->_podesc->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToFilter('po_id', $po_increment_id);
        return $collection;
    }

    public function getStatus(){
        $status = $this->_po->load($this->po_id,'po_id')->getData('status');
        if($status == 0){
            return __('Pending');
        }
        elseif($status == 1){
            return __('Confirmed');
        }
        elseif($status == 2){
            return __('Declined');
        }
        else{
            return __('Ordered');
        }
    }

}
