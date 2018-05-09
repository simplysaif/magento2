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
namespace Ced\RequestToQuote\Controller\Cart;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Store\Model\ScopeInterface;

class Delete extends \Magento\Checkout\Controller\Cart\Delete {
	public function __construct(
		\Magento\Framework\App\Action\Context $context, 
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
		\Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
		array $data = []) 
	{
		$this->resultPageFactory = $resultPageFactory;
		$this->_scopeConfig = $scopeConfig;
		$this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->cart = $cart;
		$this->_session = $customerSession;
		parent::__construct ( $context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $data );
	}
	public function execute(){
		if($this->_session->isLoggedIn()){
			$module_enable = $this->_scopeConfig->getValue('requesttoquote_configuration/active/enable', ScopeInterface::SCOPE_STORE);
			if($module_enable){
				if($this->_session->getData('po_id')){
					$this->messageManager->addError(__("You cannot delete quote item"));
					$this->_redirect('checkout/cart/');
					return;
				}
				
			}
			return parent::execute();
		}
		return parent::execute();
	}
}
