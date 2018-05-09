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

namespace Ced\RequestToQuote\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
 
class Success implements ObserverInterface
{
	protected $_objectManager;
	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager, 
		\Magento\Framework\App\RequestInterface $request, 
		\Magento\Customer\Model\Session $customerSession
		) {
		$this->request = $request;
        $this->_getSession = $customerSession;
		$this->_objectManager = $objectManager;
	}
    public function execute(\Magento\Framework\Event\Observer $observer) {
        
        $order_id = $observer->getEvent()->getData('order_ids');
        $orderid = $order_id[0];
        $customer = $this->_getSession->getCustomer ();
        $customer_Id = $customer->getId ();
        //$ordercollection=$this->_objectManager->get('\Magento\Sales\Model\Order')->load($order_id)->getAllItems();
        $products = array();
        $poIncid = $this->_getSession->getData('po_id');
        
       
	if(isset($poIncid)){
	//foreach($ordercollection as $item){

		        $poCollection=$this->_objectManager->create('\Ced\RequestToQuote\Model\PoDetail')->getCollection()->addFieldToFilter('po_id', $poIncid)->getData();
		        
		        foreach ($poCollection as $key => $value) {
		            $po_id=$value['po_id'];
		            $id = $value['id'];
		            if(isset($po_id)){
		                $poload=$this->_objectManager->create('\Ced\RequestToQuote\Model\PoDetail')->load($id);
			            $poload->setData('status', '4');
			            $poload->setData('order_id', $orderid);
			            $poload->save();
		            }
		        }
		      
		        try {
			        if(isset($poIncid)){
			            /*$poload=$this->_objectManager->create('\Ced\RequestToQuote\Model\PoDetail')->load($id);
			            $poload->setData('status', '4');
			            $poload->setData('order_id', $orderid);
			            $poload->save();*/
			            
			            $po = $this->_objectManager->create('\Ced\RequestToQuote\Model\Po')->load($poIncid, 'po_increment_id')->getData('po_id');
			            
			            $podata = $this->_objectManager->create('\Ced\RequestToQuote\Model\Po')->load($po);
                        $quoteId = $podata->getQuoteId();
			            $podata->setStatus(3);
			            $podata->setOrderId($orderid);
			            $podata->save();
			            $quoteData = $this->_objectManager->create('\Ced\RequestToQuote\Model\Quote')->load($quoteId);
			            $po_datas = $this->_objectManager->create('\Ced\RequestToQuote\Model\Po')->getCollection()->addFieldToFilter('quote_id', $quoteId);
			            $quote_status = true;
			            foreach($po_datas as $po_data){
			            	if($po_data->getStatus() != 3){
			            		$quote_status = false;
			            		break;
			            	}
			            }
			            if($quoteData->getRemainingQty() === '0' && $quote_status){
			            	$quoteData->setStatus(\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_COMPLETE);
			            	$quoteData->save();
			            	$poCollectiondata = $this->_objectManager->create('\Ced\RequestToQuote\Model\PoDetail')->getCollection()->addFieldToFilter('quote_id',$quoteId)->addFieldToFilter('status',array('neq' => \Ced\RequestToQuote\Model\Po::PO_STATUS_DECLINED));
			            	$item_info = array();
			            	$email = $quoteData->getCustomerEmail();
				            $totals['subtotal'] = $quoteData->getQuoteUpdatedPrice();
				            $totals['shipping'] = $quoteData->getShippingAmount();
				            $totals['grandtotal'] = $totals['subtotal'] + $totals['shipping'];
				            
				            $template = 'quote_complete_email_template';
				            foreach($poCollectiondata as $key =>$po){
				            	$item_info[$key]['prod_id'] = $po->getProductId();
				            	$item_info[$key]['name'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($po->getProductId())->getName();
				            	$item_info[$key]['qty'] = $po->getProductQty();
				                $item_info[$key]['sku'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($po->getProductId())->getSku();
				                $item_info[$key]['price'] = $po->getPoPrice();
				                $item_info[$key]['po_id'] = $po->getPoId();

				            }

				            $template_variables = array('quote_id' => '#'.$quoteData->getQuoteIncrementId(),
	                                        			'quote_status' => $this->_objectManager->create('\Ced\RequestToQuote\Model\Quote')->getStatusArray()[$quoteData->getStatus()]->getText(),
	                                        			'item_info' => $item_info,
	                                        			'totals' => $totals);
				            $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendEmail($template, $template_variables, $email);

			            }
			            else{
			            	$quoteData->setStatus(\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_ORDERED);
			            	$quoteData->save();
			            	$quoteCollectiondata = $this->_objectManager->create('\Ced\RequestToQuote\Model\QuoteDetail')->getCollection()->addFieldToFilter('quote_id',$quoteId);
			            	$totals = array();
			            	$item_info = array();
				            foreach($quoteCollectiondata as $key => $quote){
				            	$item_info[$key]['prod_id'] = $quote->getProductId();
				            	$item_info[$key]['name'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($quote->getProductId())->getName();
				            	$item_info[$key]['qty'] = $quote->getQuoteUpdatedQty();
				                $item_info[$key]['sku'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($quote->getProductId())->getSku();
				                $item_info[$key]['price'] = $quote->getUpdatedPrice();
				            }
				            $email = $quoteData->getCustomerEmail();
				            $totals['subtotal'] = $quoteData->getQuoteUpdatedPrice();
				            $totals['shipping'] = $quoteData->getShippingAmount();
				            $totals['grandtotal'] = $totals['subtotal'] + $totals['shipping'];
				            $template = 'quote_update_email_template';
				            $template_variables = array('quote_id' => '#'.$quoteData->getQuoteIncrementId(),
	                                        'quote_status' => $this->_objectManager->create('\Ced\RequestToQuote\Model\Quote')->getStatusArray()[$quoteData->getStatus()]->getText(),
	                                        'item_info' => $item_info,
	                                        'totals' => $totals);
				            $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendEmail($template, $template_variables, $email);
			            }
			            

			        }
			    }
			    catch(\Exception $e){
            		echo $e->getMessage();
        		}
        		
	        //}
	   }
	
    }
}