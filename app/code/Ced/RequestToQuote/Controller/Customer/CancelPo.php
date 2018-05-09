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
namespace Ced\RequestToQuote\Controller\Customer;

class CancelPo extends \Magento\Framework\App\Action\Action {

	public function __construct(
			\Magento\Framework\App\Action\Context $context, 
			\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
			\Magento\Customer\Model\Session $customerSession, 
			\Magento\Framework\App\Request\Http $request,
			\Ced\RequestToQuote\Model\Quote $quote,
			\Ced\RequestToQuote\Model\Po $po,
			
			array $data = []
		) {

		$this->resultPageFactory = $resultPageFactory;
		$this->_getSession = $customerSession;
		$this->_request = $request;
		$this->_quote = $quote;
		$this->_po = $po;
		parent::__construct ( $context, $data );
	}


	public function execute() {
		
		 try {
				$poIncid = $this->getRequest()->getParam('po_incId');
				$poData = $this->_po->load($poIncid, 'po_increment_id');
				$status = $poData->getStatus();
				if($status == '0'|| $status == '1'){
					$poData = $poData->setStatus(2);
					$quoteId = $poData->getQuoteId();
					$quoteData = $this->_quote->load($quoteId);
					if($quoteData->getRemainingQty() == '')
						$quoteData->setStatus(\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_APPROVED);
					elseif($poData->getPoQty() == $quoteData->getQuoteUpdatedQty()){
						$quoteData->setStatus(\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_APPROVED);
					}
					elseif(($poData->getPoQty() + $quoteData->getRemainingQty()) ==  $quoteData->getQuoteUpdatedQty()){
						$quoteData->setStatus(\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_APPROVED);
					}
					else
						$quoteData->setStatus(\Ced\RequestToQuote\Model\Quote::QUOTE_STATUS_PARTIAL_PO);
					$poData->save();
					$quoteData->save();
					$po_items = $this->_objectManager->create('Ced\RequestToQuote\Model\PoDetail')
													 ->getCollection()
													 ->addFieldToFilter('po_id', $poIncid);
					$remaining_qty = 0;
					$quote_id = 0;
					foreach($po_items as $po_item){
						$quote_id = $po_item->getQuoteId();
						$quote_detail = $this->_objectManager->create('Ced\RequestToQuote\Model\QuoteDetail')
													 ->getCollection()
													 ->addFieldToFilter('quote_id', $po_item->getQuoteId())
													 ->addFieldToFilter('product_id', $po_item->getProductId())
													 ->getData()[0];
						$rem = $quote_detail['remaining_qty'];
						$q_id = $quote_detail['id'];
											
						$rem1 = $rem + $po_item->getProductQty();
						$remaining_qty += $po_item->getProductQty();
						$this->_objectManager->create('Ced\RequestToQuote\Model\QuoteDetail')
													 ->load($q_id)
													 ->setRemainingQty($rem1)->save();

					}
					$quote_remanining = $this->_objectManager->create('Ced\RequestToQuote\Model\Quote')->load($quote_id)->getRemainingQty();
					$quote_remanining += $remaining_qty;
					$this->_objectManager->create('Ced\RequestToQuote\Model\Quote')->load($quote_id)->setRemainingQty($quote_remanining)->save();


					$this->messageManager->addSuccess ( __ ( 'Po '.$poIncid.' was successfully cancelled.' ) );
					$this->_redirect ( '/' );
				}
				else{
					if($status == '2'){
						$this->messageManager->addError ( __ ( 'PO '.$poIncid.' is already cancelled.' ) );
					$this->_redirect ( '/' );
					}
					elseif($status == '3'){
						$this->messageManager->addError ( __ ( 'PO '.$poIncid.' has already been ordered.' ) );
						$this->_redirect ( '/' );
					}
					
				}
		}
		catch(\Exception $e)
        {
            echo $e->getMessage();
        }
    }
		
}