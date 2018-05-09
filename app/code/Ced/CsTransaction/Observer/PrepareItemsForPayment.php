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
 * @package     Ced_CsTransaction
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsTransaction\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PrepareItemsForPayment implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_csorderHelper;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsMarketplace\Model\Vorders $vorders,
        \Ced\CsOrder\Helper\Data $csorderHelper,
        \Ced\CsTransaction\Model\Items $vtorders,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_objectManager = $objectManager;
        $this->_vorders = $vorders;
        $this->_csorderHelper = $csorderHelper;
        $this->_vtorders = $vtorders;
        $this->request = $request;
    }


    /**
     * @param Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        if($this->_csorderHelper->isActive()){
        	try{
            	$invoice = $observer->getDataObject();
            	foreach($invoice->getAllItems() as $item){
                    if ($item->getOrderItem()->getParentItem()) 
                        continue;
        			$quoteItem = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($item->getOrderItemId());
                    //echo $quoteItem->getQtyOrdered().'----';
        			$itemCollection = $this->_vtorders->getCollection()
                                            ->addFieldToFilter('parent_id', $this->request->getParam('order_id'))
        			                        ->addFieldToFilter('order_item_id', $item->getOrderItemId());
                    $vorder = $this->_vorders->getCollection()
                                ->addFieldToFilter('order_id', $invoice->getOrder()->getIncrementId())
                                ->addFieldToFilter('vendor_id', $item->getVendorId())->getFirstItem();
                    if (empty($itemCollection->getData())) {
        				$this->saveOrderItem($item, $invoice, $vorder, $quoteItem);
        			} elseif (!empty($itemCollection->getData())) {
        				foreach ($itemCollection as $items) {
    						$itemsFee = json_decode($vorder->getItemsCommission(), true);
    						$saveItems = $this->_objectManager->create('Ced\CsTransaction\Model\Items')->load($items->getId());
    						$saveItems->setItemPaymentState(\Ced\CsTransaction\Model\Items::STATE_READY_TO_PAY);
    	        			$saveItems->setQtyReadyToPay($items->getQtyReadyToPay()+$item->getQty());
    	        			$saveItems->setTotalInvoicedAmount($items->getTotalInvoicedAmount()+$item->getRowTotal());
    	        			$total = $this->getRowTotalFeeAmount($item);
    	        			$itemCommission = $itemsFee[$quoteItem->getQuoteItemId()]['base_fee']/$quoteItem->getQtyOrdered();
    	        			$saveItems->setItemFee($saveItems->getItemFee()+($total -($itemCommission * $item->getQty())));
    	        			$saveItems->setItemCommission($saveItems->getItemCommission()+($itemCommission * $item->getQty()));
    	        			$saveItems->setBaseRowTotal($saveItems->getBaseRowTotal() + $item->getBaseRowTotal());
    	        			$saveItems->setRowTotal($saveItems->getRowTotal() + $item->getRowTotal());
    	        			$saveItems->setTotalInvoicedAmount($saveItems->getTotalInvoicedAmount() + $item->getRowTotal());
    	        			$saveItems->setQtyForPayNow($items->getQtyReadyToPay()+$item->getQty());
    	        			$saveItems->save();
        				}
        			}
        		}
        	} catch(\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        	}
        }
        return $this;
    }

    public function saveOrderItem($item, $invoice, $vorder, $quoteItem){
    	$itemsFee = json_decode($vorder->getItemsCommission(), true);
    	$vorderItem = $this->_objectManager->create('Ced\CsTransaction\Model\Items');
    	$vorderItem->setParentId($vorder->getId());
    	$vorderItem->setOrderItemId($item->getOrderItemId());
    	$vorderItem->setOrderId($invoice->getOrder()->getId());
    	$vorderItem->setOrderIncrementId($invoice->getOrder()->getIncrementId());
    	$vorderItem->setVendorId($vorder->getVendorId());
    	$vorderItem->setCurrency($vorder->getCurrency());
    	$vorderItem->setBaseRowTotal($item->getBaseRowTotal());
    	$vorderItem->setRowTotal($item->getRowTotal());
    	$vorderItem->setSku($item->getSku());
    	$vorderItem->setShopCommissionTypeId($vorder->getShopCommissionTypeId());
    	$vorderItem->setShopCommissionRate($vorder->getShopCommissionRate());
    	$vorderItem->setShopCommissionBaseFee($vorder->getShopCommissionBaseFee());
    	$vorderItem->setShopCommissionFee($vorder->getShopCommissionFee());
    	$vorderItem->setProductQty($item->getQtyOrdered());
    	$vorderItem->setItemPaymentState(false);
    	$total = $this->getRowTotalFeeAmount($item);
    	$itemCommission = $itemsFee[$quoteItem->getQuoteItemId()]['base_fee']/$quoteItem->getQtyOrdered();
    	$vorderItem->setItemFee($total-($itemCommission*$item->getQty()));
    	$vorderItem->setItemCommission($itemCommission*$item->getQty());
    	$vorderItem->setQtyOrdered($quoteItem->getQtyOrdered());
    	
    	$vorderItem->setItemPaymentState(\Ced\CsTransaction\Model\Items::STATE_READY_TO_PAY);
    	$vorderItem->setQtyReadyToPay($item->getQty());
    	$vorderItem->setTotalInvoicedAmount($item->getRowTotal());
    	$vorderItem->setIsRequested('0');
    	$vorderItem->setQtyForPayNow($item->getQty());
    	$vorderItem->save();
    }
    
    public function getRowTotalFeeAmount($item){
    	$amount = $item->getBaseRowTotal()+$item->getBaseTaxAmount()-$item->getBaseDiscountAmount();
    	return $amount;
    }
}
