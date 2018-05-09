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

class EditPo extends \Magento\Framework\App\Action\Action {

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
		
		//print_r($poId); die("dkf");
		if (! $this->_getSession->isLoggedIn ()) {
			$this->messageManager->addError ( __ ( 'Please login first' ) );
			$this->_redirect ( 'customer/account/login' );
			return;
		}
		$poId = $this->getRequest()->getParam('poId');
		$quote_id = $this->_po->load($poId)->getQuoteId();
		$customerId = $this->_quote->load($quote_id)->getCustomerId();
		
		$customer_id = $this->_getSession->getCustomer()->getId();

		if($customer_id == $customerId){
			$resultPage = $this->resultPageFactory->create ();
			$navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
	        if ($navigationBlock) {
	            $navigationBlock->setActive('requesttoquote/customer/po');
	        }
			return $resultPage;
		}
		else{
				$this->messageManager->addError(__('You are not allowed to update this po. Kindly update your PO only.'));
                 return $this->_redirect ('customer/account/index' );
                  
		}
	}
}