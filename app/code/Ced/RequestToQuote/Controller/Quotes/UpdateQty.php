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
namespace Ced\RequestToQuote\Controller\Quotes;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Store\Model\ScopeInterface;
class UpdateQty extends \Magento\Checkout\Controller\Cart\UpdatePost {

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        CustomerCart $cart
    ) {
    
        $this->_formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeConfig;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->cart = $cart;
        $this->_session = $customerSession;
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
    }

	protected function _emptyShoppingCart()
    {
        try {
            $this->cart->truncate()->save();
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->messageManager->addError($exception->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addException($exception, __('We can\'t update the shopping cart.'));
        }
    }

    /**
     * Update customer's shopping cart
     *
     * @return void
     */
    protected function _updateShoppingCart()
    {
        try {
        	
            $cartData = $this->getRequest()->getParam('cart');
            //print_r($cartData);die;

            if (is_array($cartData)) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    ['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                    	 $cartData[$index]['qty'] = $filter->filter(trim($data['qty']));
                    }
                }
                if (!$this->cart->getCustomerSession()->getCustomerId() && $this->cart->getQuote()->getCustomerId()) {
                    $this->cart->getQuote()->setCustomerId(null);
                }
                $module_enable = $this->_scopeConfig->getValue('requesttoquote_configuration/active/enable', ScopeInterface::SCOPE_STORE);
                if((int)$module_enable){
                    if($this->_session->getData('po_id')){
                        $this->messageManager->addError(__("You cannot edit the quote item"));
        				/*foreach ($cartData as $index => $data) {
                            if (isset($data['qty'])) {
                                 $items = $this->_objectManager->create('\Magento\Checkout\Model\Cart')->getItems();
                            	 foreach($items as $key=>$item){
        							if($index==$item->getId()){
        								$pid=$item->getProduct()->getId();
        								$price=$item->getPrice();
        								$qty=$item->getQty();
        								$enabled=$this->_objectManager->create('Ced\RequestToQuote\Model\PoDetail')->getCollection()->addFieldToFilter('product_id', $pid)->getData();
        								if(!empty($enabled)){
        									$this->messageManager->addError(__("You cannot edit the quote item"));
        								}else{
        									$cartData = $this->cart->suggestItemsQty($cartData);
                        					$this->cart->updateItems($cartData)->save();
        								}
        							}
                            	 }
                            }
                        }*/
                    }
                }
                else{
                    $cartData = $this->cart->suggestItemsQty($cartData);
                    $this->cart->updateItems($cartData)->save();
                }
                
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(
                $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
            );
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t update the shopping cart.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
    }

    /**
     * Update shopping cart data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $updateAction = (string)$this->getRequest()->getParam('update_cart_action');

        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }

        return $this->_goBack();
    }
}