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

class Edit extends \Magento\Sales\Block\Adminhtml\Order\View
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

    public function getPoData(){
        return $this->_po->load($this->po_id,'po_id');
    }

    public function getStatus(){
        $status = $this->_po->load($this->po_id,'po_id')->getData('status');
        if($status == \Ced\RequestToQuote\Model\Po::PO_STATUS_PENDING){
            return __('Pending');
        }
        elseif($status == \Ced\RequestToQuote\Model\Po::PO_STATUS_CONFIRMED){
            return __('Confirmed');
        }
        elseif($status == \Ced\RequestToQuote\Model\Po::PO_STATUS_DECLINED){
            return __('Declined');
        }
        else{
            return __('Ordered');
        }
    }

    public function getCustomerData(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $id = $this->_po->load($this->po_id,'po_id')->getPoCustomerId();
        return $objectManager->create('Magento\Customer\Model\Customer')->load($id);
    }

    public function getCustomerGroup($id)
    {
        $group_id = $this->_customer->load($id)->getGroupId();
        $group_code = $this->_custgroup->load($group_id)->getCustomerGroupCode();
        return $group_code;
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

    public function getPoItemsData(){
        $po_increment_id = $this->_po->load($this->po_id,'po_id')->getData('po_increment_id');
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $collection = $this->_podesc->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToFilter('po_id', $po_increment_id);
        return $collection;
    }

    public function getQuoteData()
    {
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $poData = $this->_quote->load($quote_id);
        return $poData;
    }

    public function getQuoteStatus()
    {
        $id = $this->getRequest()->getParam('id');
        $status = $this->_quote->load($id)->getStatus();
        switch ($status){
            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PENDING:
                echo "Pending";
                break;
            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PROCESSING:
                echo "Processing";
                break;
            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_APPROVED:
                echo "Approved";
                break;
            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_CANCELLED:
                echo "Cancelled";
                break;

            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PO_CREATED:
                echo "PO created";
                break;

            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PARTIAL_PO:
                echo "Partial Po Created";
                break;

            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_ORDERED:
                echo "Ordered";
                break;

            case \Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_COMPLETE:
                echo "Complete";
                break;

        }
    }

    public function getStoreDetails()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_quote->load($id)->getStoreId();
        $storename = $this->_storeManager->getStore()->getName();
        return $storename;
    }

    public function getGrandTotal(){
        $total = $this->_po->load($this->po_id,'po_id')->getData('po_price');
        $quote_id = $this->_po->load($this->po_id,'po_id')->getData('quote_id');
        $shipping = $this->_quote->load($quote_id)->getShippingAmount();
        //return $total+$shipping;
        return $total;
    }

    public function getItems()
    {
        $id = $this->getRequest()->getParam('id');
        $quotedetails = $this->_quotedesc->getCollection()->addFieldToFilter('quote_id', $id)->addFieldToSelect('*');
        return $quotedetails;
    }

    public function getProductStock($product_id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
        $StockState = $objectManager->create('\Magento\CatalogInventory\Api\StockStateInterface');
        $qty = $StockState->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
        return $qty;
    }

    public function getCurrencyCode(){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $code =  $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $currency = $objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code);
        return $currency->getCurrencySymbol();
    }

    public function getProduct($product_id)
    {
        $product = $this->_catalog->load($product_id);
        return $product;
    }

    public function getQuoteId()
    {
        return $this->getRequest()->getParam('id');
    }

    public function getSaveUrl(){
        return $this->getUrl('csrequesttoquote/quotes/save', ['quoteId'=> $this->getRequest()->getParam('id')]);
    }
    
}
