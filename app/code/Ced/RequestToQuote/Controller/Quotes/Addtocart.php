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

class Addtocart extends \Magento\Framework\App\Action\Action {
	protected $cart;
	public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Customer\Model\Session $customerSession,\Magento\Framework\App\Request\Http $requestInterface,CustomerCart $cart, array $data = []) {
		$this->resultPageFactory = $resultPageFactory;
		$this->_getSession = $customerSession;
		$this->_requestInterface = $requestInterface;
		$this->cart = $cart;
		parent::__construct ( $context, $resultPageFactory, $customerSession,$requestInterface, $cart, $data );
		
	}
	public function execute() {
		$poIncid=$this->getRequest()->getParam('po_incId');
		if (! $this->_getSession->isLoggedIn ()) {
			$this->_getSession->setBeforeAuthUrl($this->_url->getUrl('requesttoquote/quotes/addtocart', array('po_incId' => $poIncid)));
			$this->messageManager->addError ( __ ( 'Please login first' ) );			
			return $this->_redirect('customer/account/login');	
		}
		if($this->_getSession->getData('po_id')){
			$this->_getSession->uns('po_id');
			$this->_getSession->setData('po_id', $poIncid);
		}
		else{
			$this->_getSession->setData('po_id', $poIncid);
		}
		$poData=$this->_objectManager->create('Ced\RequestToQuote\Model\Po');		
		$poentityId = $this->_objectManager->create('Ced\RequestToQuote\Model\Po')->load($poIncid, 'po_increment_id')->getData('po_id');
		$status = $poData->load($poentityId)->getStatus();

		$quote_id = $this->_objectManager->create('Ced\RequestToQuote\Model\Po')->load($poIncid, 'po_increment_id')->getData('quote_id');
		$customeremail = $this->_objectManager->create('Ced\RequestToQuote\Model\Quote')->load($quote_id)->getCustomerEmail();
		if($customeremail == $this->_getSession->getCustomer()->getEmail()){
			if($status == '1' || $status == '0'){
				$setValue = $this->_objectManager->create('Ced\RequestToQuote\Model\Po')->load($poentityId);
				$setValue->setData('status', '1');
				$setValue->save();
				
				$podetail = $poData->getCollection()->addFieldToFilter('po_increment_id', $poIncid)->addFieldToFilter('status', '1')->getData();				
		        
		        if(sizeof($podetail)>0){
					$poProd=$this->_objectManager->create('Ced\RequestToQuote\Model\PoDetail')->getCollection()->addFieldToFilter('po_id',$poIncid)->getData();
					
					
					$cart = $this->_objectManager->create('\Magento\Checkout\Model\Cart');
					$cartItems = $cart->getQuote()->getAllItems();
					if(!empty($cartItems)){
						$cart->truncate();
					}
					$prod_price = array();
					foreach($poProd as $data){
						if($data['product_qty'] > 0){
							$productid = $data['product_id'];
							$quantity = $data['product_qty'];
							$price = $data['po_price'];
							$unit_price = floatval($data['po_price']/$data['product_qty']); 

							$productobj = $this->_objectManager->create ( 'Magento\Catalog\Model\Product' )->load ( $productid );
							$prod_price[$productid]['prev_price'] = $productobj->getPrice();
							$prod_price[$productid]['new_price'] = $unit_price;
							$productobj->setPrice($unit_price);
							$this->_objectManager->create('Magento\Customer\Model\Session')->setRfqPrice($prod_price);
							//print_r($unit_price);die;
	      					// $cart->addProduct ( $productobj, ['product'=> $productid, 'qty'=> intval ( $data['product_qty'] )] );
							$cart->addProduct ( $productobj, $data['product_qty'] );
							//$this->_objectManager->create('Magento\Checkout\Model\Session')->setCartWasUpdated(true);
						}					
					}
					$cart->save();
					return $this->_redirect ( "checkout/cart/index" );
		        }else{
		        	$this->messageManager->addError(__("Invalid Request"));
		        	return $this->_redirect ( "requesttoquote/customer/po" );
		        }
		    }
		    else{
			    if($status == '3'){
			    	$this->messageManager->addError(__("This PO has already been ordered."));
			        return $this->_redirect ( "/" );
			    }
			    elseif($status == '2'){
			    	$this->messageManager->addError(__("This PO has been already cancelled."));
			        return $this->_redirect ( "/" );
			    }
		    }
		 }
	    else{
	    	$this->messageManager->addError(__("You can't proceed with other customer data"));
	        	return $this->_redirect ( "customer/account/index" );
	    }


	}
}