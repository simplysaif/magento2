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
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\RequestToQuote\Controller\Customer;
use Magento\Checkout\Model\Cart as CustomerCart;

class Poapprove extends \Magento\Framework\App\Action\Action {
	protected $cart;
	public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Model\Session $customerSession,\Magento\Framework\App\Request\Http $requestInterface,CustomerCart $cart, array $data = []) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_getSession = $customerSession;
		$this->_requestInterface = $requestInterface;
		$this->cart = $cart;
		parent::__construct ( $context, $resultPageFactory, $customerSession,$requestInterface, $cart, $data );
		
	}
	public function execute() {
		$poIncid=$this->getRequest()->getParam('po_id');
		if (! $this->_getSession->isLoggedIn ()) {
			$this->_getSession->setBeforeAuthUrl($this->_url->getUrl('requesttoquote/quotes/addtocart', array('po_incId' => $poIncid)));
			$this->messageManager->addError ( __ ( 'Please login first' ) );		
			return $this->_redirect('customer/account/login');	
		}
		$poData=$this->_objectManager->create('Ced\RequestToQuote\Model\Po');		
		$poentityId = $this->_objectManager->create('Ced\RequestToQuote\Model\Po')->load($poIncid, 'po_increment_id')->getData('po_id');
		$po = $this->_objectManager->create('Ced\RequestToQuote\Model\Po')->load($poentityId);
		if($po->getStatus() == 0){
			$po->setData('status', '1');
			$po->save();
		}
		$this->_redirect ('requesttoquote/customer/po');
		return;		
	}
}