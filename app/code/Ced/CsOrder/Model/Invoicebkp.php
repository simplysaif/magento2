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
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsOrder\Model;
use Magento\Framework\Api\AttributeValueFactory;
class Invoice extends \Ced\CsMarketplace\Model\FlatAbstractModel
{
    /**
     * Payment states
     */
    const STATE_OPEN       = 1;
    const STATE_PAID       = 2;
    const STATE_CANCELED   = 3;
    const STATE_REFUND     = 4;
    const STATE_REFUNDED   = 5;

    
    const ORDER_NEW_STATUS=1;
    const ORDER_CANCEL_STATUS=3;
    const STATE_PARTIALLY_PAID = 6;
    
    protected $_items = null;
    
    protected static $_states;
    
    protected $_eventPrefix      = 'csorder_invoice';
    protected $_eventObject      = 'vinvoice';
    public $_vendorstatus=null;
    protected $customerSession;
    protected $_dataHelper;
    protected $_aclHelper;
    protected $_objectManager;
    
    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory       $extensionFactory
     * @param AttributeValueFactory                                   $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {


        $this->_objectManager=$objectInterface;

        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        

           
    }
    
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        
        $this->_init('Ced\CsOrder\Model\ResourceModel\Invoice');
    }
    
    /**
     * Getting Can include shipping amount Invoice
     *
     * @return boolean
     */
    public function canInvoiceIncludeShipment($invoice)
    {
        if(is_object($invoice)) {

            $vendorId = $this->_objectManager->get('Magento\Customer\Model\Session')->getVendorId();
            $invoicedCollection = $this->getCollection()
                ->addFieldTofilter('invoice_order_id', $invoice->getOrderId())
                ->addFieldTofilter('vendor_id', $vendorId)
                ->addFieldTofilter('shipping_code', array('notnull' => true));
            if(count($invoicedCollection)==0) {
                return true; 
            }
        }
        return false;                        
    }
    
    /**
     * Update Total
     *
     * @return object
     */
    public function updateTotal($invoice, $view=true)
    {
          
        $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->setVendorId($this->getVendorId())->getVorderByInvoice($invoice);

        if (!$this->_registry->registry('current_vorder')) {
            $this->_registry->register('current_vorder', $vorder);
        }
        
            
        $helperData = $this->_objectManager->get('Ced\CsOrder\Helper\Data');
        if(!is_object($vorder)) {
            return $invoice; 
        }
            
            
        if(!$vorder->isAdvanceOrder() && $vorder->getCode()) {
            $invoice->setOrder($vorder->getOrder(false, true));
            $shippingCost = 0;
            if($view && $vInvoice = $this->updateInvoiceGridTotal($invoice)) {
                $invoice->setShippingAmount($vInvoice->getShippingAmount());
                $invoice->setBaseShippingAmount($vInvoice->getBaseShippingAmount());
            }else if($this->canInvoiceIncludeShipment($invoice)) {
                $invoice->setShippingAmount($vorder->getShippingAmount());
                $invoice->setBaseShippingAmount($vorder->getShippingAmount());
            }
                
            $subtotal = $this->getItemSubtotalByInvoice($invoice);
            $invoice->setSubtotal($subtotal);
            $tax = $this->getItemTaxByInvoice($invoice);
            $invoice->setTaxAmount($tax);
            $invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
        }

        if(!$helperData->canShowShipmentBlock($vorder)) {
            $invoice->setShippingAmount(0);
            $invoice->setBaseShippingAmount(0);
            $subtotal = $this->getItemSubtotalByInvoice($invoice);
            $invoice->setSubtotal($subtotal);
            $tax = $this->getItemTaxByInvoice($invoice);
            $invoice->setTaxAmount($tax);


            $invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
        }
        return $invoice;
    }
    
    /**
     * Getting Can include shipping amount Invoice
     *
     * @return boolean
     */
    public function updateInvoiceGridTotal($invoice)
    {
        if(is_object($invoice)) {
            $vendorId = $this->getVendorId();
            $invoicedCollection = $this->getCollection()
                ->addFieldTofilter('invoice_id', $invoice->getId())
                ->addFieldTofilter('vendor_id', $vendorId)
                ->addFieldTofilter('shipping_code', array('notnull' => true));
            if(count($invoicedCollection)>0) {
                return $invoicedCollection->getFirstItem(); 
            }
        }
        return false;                        
    }
    
    
        
    /**
     * Update Total
     *
     * @return object
     */
    public function updateTotalGrid($invoice)
    {
          
        $vorder =  $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getVorderByInvoice($invoice);
            
        if(!is_object($vorder)) {
            return $invoice; 
        }
            
            
        if(!$vorder->isAdvanceOrder() && $vorder->getCode()) {
            $invoice->setOrder($vorder->getOrder(false, true));
            $shippingCost = 0;
            if($vInvoice = $this->updateInvoiceGridTotal($invoice)) {
                $invoice->setShippingAmount($vInvoice->getShippingAmount());
                $invoice->setBaseShippingAmount($vInvoice->getBaseShippingAmount());
            }
            $subtotal = $this->getItemSubtotalByInvoice($invoice);
            $invoice->setSubtotal($subtotal);
            $tax = $this->getItemTaxByInvoice($invoice);
            $invoice->setTaxAmount($tax);
            $invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
        }

        if(!Mage::helper('csorder')->canShowShipmentBlock($vorder)) {
            $invoice->setShippingAmount(0);
            $invoice->setBaseShippingAmount(0);
            $subtotal = $this->getItemSubtotalByInvoice($invoice);
            $invoice->setSubtotal($subtotal);
            $tax = $this->getItemTaxByInvoice($invoice);
            $invoice->setTaxAmount($tax);


            $invoice->setGrandTotal($subtotal+ $tax + $invoice->getShippingAmount());
        }
            
        return $invoice;
    }
    
    
    /**
     * Getting Item Subtotal By Invoice
     *
     * @return decimal
     */
    public function getItemSubtotalByInvoice($invoice)
    {
        $items = $invoice->getAllItems();

        $vendorId = $this->getVendorId();
        $total = 0;
        foreach($items as $_item){
            $vendorIdProductId =  $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ((is_object($_item->getOrderItem()) && $_item->getOrderItem()->getParentItem()) || $vendorIdProductId!=$vendorId ) { continue; 
            }
                

            $total += $_item->getRowTotal();
        }
        return $total;
    }

    /**
     * Getting Item Tax By Invoice
     *
     * @return decimal
     */
    public function getItemTaxByInvoice($invoice)
    {
        $items = $invoice->getAllItems();
        $vendorId = $this->getVendorId();
        $total = 0;
        foreach($items as $_item){
            $vendorIdProductId = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')
                ->getVendorIdByProduct($_item->getProductId());
            if ((is_object($_item->getOrderItem()) && $_item->getOrderItem()->getParentItem()) || $vendorIdProductId!=$vendorId ) { continue; 
            }
            $total += $_item->getTaxAmount();
        }
        return $total;
    }

    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => 'Pending',
                self::STATE_PAID       => 'Paid',
                self::STATE_CANCELED   => 'Canceled',
                self::STATE_PARTIALLY_PAID       => 'Partially Paid',
            );
        }
        return self::$_states;
    }
        
}

?>
