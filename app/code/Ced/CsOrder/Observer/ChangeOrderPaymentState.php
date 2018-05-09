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
namespace Ced\CsOrder\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ChangeOrderPaymentState implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    protected $_vorders;
    
    public function __construct(
    	\Magento\Framework\ObjectManagerInterface $objectManager,
      \Ced\CsMarketplace\Model\Vorders $vorders,
      \Magento\Framework\App\Request\Http $request
    ) {
        $this->_objectManager = $objectManager;
        $this->_vorders=$vorders;
        $this->_request = $request;
    }
    /**
     *Set vendor naem and url to product incart
     *
     *@param $observer
     */
      public function execute(\Magento\Framework\Event\Observer $observer) {
          if($this->_objectManager->get('\Ced\CsOrder\Helper\Data')->isActive()){

            $invoice = $observer->getDataObject();
            foreach ($invoice->getAllItems() as $item) {
              $product_id=$item->getProductId();
              $vendors[]=$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($product_id);
            }
            if(isset($vendors)){
              $vendors=array_unique($vendors);
            }

            
            $order = $invoice->getOrder();

            $this->_objectManager->get('\Ced\CsMarketplace\Helper\Data')->logProcessedData($order->getData('increment_id'), \Ced\CsMarketplace\Helper\Data::SALES_ORDER_PAYMENT_STATE_CHANGED);

            /*$vorders = $this->_vorders
                      ->getCollection()
                      ->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()))->addFieldToFilter('vendor_id', array('eq' =>$vendors));*/
             $vorders = $this->_vorders
                      ->getCollection()
                      ->addFieldToFilter('order_id',array('eq'=>$order->getIncrementId()));         
              $invoiced_item=$this->_request->getPost('invoice');
              if (count($vorders) > 0) {
                foreach ($vorders as $vorder) {
                  try{

                      $qtyOrdered=0;
                      $qtyInvoiced=0;
                      $invoiced=0;

                      $vendorId=$vorder->getData('vendor_id');

                      $vorderItems=$this->_objectManager->get('\Magento\Sales\Model\Order\Item')->getCollection()->addFieldToSelect('*')
                      ->addFieldToFilter('vendor_id',$vendorId)
                      ->addFieldToFilter('order_id',$order->getId());

                foreach($vorderItems as $item)
                {
                    if(isset($invoiced_item)){
                      foreach ($invoiced_item as $item_id) {
                        if(isset($item_id[$item->getItemId()])){
                            $invoiced=$item_id[$item->getItemId()]+(int)$item->getData('qty_invoiced');
                        }
                      }
                    }

                
                    if($invoiced==0){
                      $invoiced=(int)$item->getData('qty_invoiced');
                    }
                    
                    $qtyOrdered+=(int)$item->getQtyOrdered();
                    $qtyInvoiced+=(int)$invoiced;
                    
                }

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
                $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $tableName = $resource->getTableName('ced_csmarketplace_vendor_sales_order'); //gives table name with prefix
                
                  if($qtyOrdered>$qtyInvoiced)
                  {
                    /*echo "<br>";
                    echo $qtyOrdered."compare".$qtyInvoiced.$vendorId."partially paid";
                    echo "<br>";*/
                    if($qtyInvoiced!=0){
                      $sql = "Update " . $tableName . " Set order_payment_state = ".\Ced\CsOrder\Model\Invoice::STATE_PARTIALLY_PAID." where order_id = {$vorder->getOrderId()} and vendor_id = {$vorder->getVendorId()}";
                    }else{
                      $sql = "Update " . $tableName . " Set order_payment_state = ".\Ced\CsOrder\Model\Invoice::ORDER_NEW_STATUS." where order_id = {$vorder->getOrderId()} and vendor_id = {$vorder->getVendorId()}";
                    }
                    
                    //$vorder->setOrderPaymentState(\Ced\CsOrder\Model\Invoice::STATE_PARTIALLY_PAID);
                  }
                  else{
                   /* echo "<br>";
                    echo $qtyOrdered."compare".$qtyInvoiced.$vendorId."paid";
                    echo "<br>";*/
                    $sql = "Update " . $tableName . " Set order_payment_state = ".\Magento\Sales\Model\Order\Invoice::STATE_PAID." where order_id = {$vorder->getOrderId()} and vendor_id = {$vorder->getVendorId()}";
                    //$vorder->setOrderPaymentState(\Magento\Sales\Model\Order\Invoice::STATE_PAID);
                  }

                  $connection->query($sql);
                  //$vorder->save();
                       // die("fdsfs");
                      $this->_objectManager->get('\Ced\CsMarketplace\Helper\Data')->logProcessedData($vorder->getData(), \Ced\CsMarketplace\Helper\Data::VORDER_PAYMENT_STATE_CHANGED);

                    }
                    catch(Exception $e){
                      $this->_objectManager->get('\Ced\CsMarketplace\Helper\Data')->logException($e);echo $e->getMessage();die;
                    }
                }
                
              }
            return $this;
          }
          else
          {
            parent::changeOrderPaymentState($observer);
          }
  }
}  
