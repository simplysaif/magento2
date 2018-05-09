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
  * @category  Ced
  * @package   Ced_CsRequestToQuote
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsRequestToQuote\Block\Po;

class Create extends \Magento\Sales\Block\Adminhtml\Order\View
{
    
    protected $_helperData;

    protected $_quote;

    protected $_customer;

    protected $_custgroup;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Sales\Model\Config           $salesConfig
     * @param \Magento\Sales\Helper\Reorder         $reorderHelper
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Ced\RequestToQuote\Model\Quote $quote,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Group $custgroup,
        \Ced\RequestToQuote\Model\QuoteDetail $quotedesc,
        \Magento\Catalog\Model\Product $catalog,
        \Ced\RequestToQuote\Model\Po $po,
        \Ced\RequestToQuote\Model\PoDetail $podesc,
        array $data = []
    ) {
        $this->_quote = $quote;
        $this->_po =$po;
        $this->_customer = $customer;
        $this->_custgroup = $custgroup;
        $this->_catalog = $catalog;
        $this->_quotedesc = $quotedesc;
        $this->_podesc = $podesc;
        $this->po_id = $context->getRequest()->getParam('id');
        $this->quote_id = $context->getRequest()->getParam('quote_id');
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);

    }

     /**
     * Constructor
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'order_id';
       
        $this->setId('sales_order_view');

        $this->buttonList->update(
                'back',
                'onclick',
                'setLocation(\'' . $this->getUrl('*/*/') . '\')'
            );
        $id = $this->getRequest()->getParam('id');
        $status = $this->_quote->load($id)->getStatus();
    
    }

    public function getPOUrl(){
        return $this->getUrl('csrequesttoquote/po/save',array('quote_id'=>$this->quote_id));
    }

    public function getBackUrl(){

        return $this->getUrl('csrequesttoquote/quotes/index',array('quote_id'=>$this->quote_id));
    }

    public function getQuoteId()
    { 
        return $this->quote_id;
    }

    public function getQuoteData(){
       return $this->_quote->load($this->quote_id);
    }

    public function getCustomerGroup($customer_id)
    {
       
        $customergrp = $this->getCustomer($customer_id)->getGroupId();
        $custgroupName = $this->_custgroup->load($customergrp)->getCustomerGroupCode();

        return $custgroupName;
    }

    public function getCustomer(){
        $id = $this->_quote->load($this->quote_id)->getCustomerId();
        return $this->_customer->load($id);

    }

    public function getCustomerAddress()
    {
        $address = [];
        
        $addressdata = $this->_quote->load($this->quote_id);
        $address['country'] = $addressdata->getCountry();
        $address['state'] = $addressdata->getState(); 
        $address['city'] = $addressdata->getCity(); 
        $address['pincode'] = $addressdata->getPincode();
        $address['street'] = $addressdata->getAddress();
        $address['telephone'] = $addressdata->getTelephone();

        return $address;
    }

    public function getNoOfProducts(){
      $no_of_products = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id',$this->quote_id)->addFieldToSelect('product_id');
      return count($no_of_products);
    }

    public function getItems()
    {  
        $quotedetails = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id', $this->quote_id)->addFieldToSelect('*');
        return $quotedetails;
    }

    public function getProduct($product_id)
    {
        $product = $this->_catalog->load($product_id);        
        return $product;
    }

    public function getProductIndividualPrice($product_id)
    {
        $productprice = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id', $this->quote_id)->addFieldToFilter('product_id', $product_id)->addFieldToSelect('unit_price');
        foreach($productprice->getData() as $price){
            $value = $price['unit_price'];
        }

        return $value;
    }

    public function getCurrencyCode(){

       $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
       $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
       return $currency->getCurrencySymbol();
    }
}
