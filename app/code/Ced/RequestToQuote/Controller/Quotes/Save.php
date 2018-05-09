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

use Magento\Backend\App\Action;

class Save extends \Magento\Framework\App\Action\Action {
	public function __construct(
					\Magento\Framework\App\Action\Context $context, 
					\Magento\Customer\Model\Session $customerSession, 
					\Magento\Store\Model\StoreManagerInterface $storeManager,
					\Magento\Catalog\Model\Product $product,
					array $data = []
					) 

	{	
		$this->_storeManager = $storeManager;
		$this->_getSession = $customerSession;
		$this->_product = $product;
		parent::__construct ( $context, $storeManager, $data );
	}
	
	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		if (! $this->_getSession->isLoggedIn ()) {
			$this->messageManager->addError ( __ ( 'Please login first' ) );
			echo "error";
			return $this->_redirect('customer/account/login');
			
		}

		if ($this->getRequest ()->isPost ()) {

			if ($this->getRequest ()->getPost () != Null) {
				$data = $this->getRequest ()->getParams ();
				$customerId = $this->_getSession->getId();
				$custemail = $this->_getSession->getCustomer()->getEmail();
				$storeId = $this->_storeManager->getStore()->getId();
				$quoteqty = $this->getRequest ()->getParam('quote_qty');
				$quoteprice = $this->getRequest ()->getParam('quote_price');
				$quotecommments = $this->getRequest ()->getParam('comments');
				$productId = $this->getRequest ()->getParam('product_id');
				$productData = $this->_product->load($productId);
                $productname = $productData->getName();
                $vendor_id = '0';
				$quoteArray = array();
				$qty = "0";
				$price = "0";

				if(isset($quoteprice))
				{
					if(is_float($quoteprice))
					{
						$price = $quoteprice;
					}
					elseif (is_numeric($quoteprice)) {
						$price = $quoteprice;
					}
				}
				if(isset($quoteqty))
				{
						$qty = round($quoteqty);
				}

				$quoteData = $this->_getSession->getData('quoteData');
				$vendors = $this->_getSession->getData('vendors');
				if(isset($quoteData)){

					foreach($quoteData as$key=> $quotedata){
						if($quotedata['product_id']==$productId){

							unset($quoteData[$key]);
						}
						
					}
					
					$quote = [		
									'product_id' =>$productId,
									'productname' =>$productname,
									'customer_id' =>$customerId,
									'customer_email'=>$custemail,
									'store_id'=>$storeId,
									'quote_qty'=>$qty,
									'quote_price'=>$price,
                                    'vendor_id'=> $vendor_id,
									'comments'=>$quotecommments,

							 	];
                    array_push ($quoteData,$quote);
                    array_push($vendors,$vendor_id);
					 $quoteArray = $quoteData;
				}
				else{
					$quote = [
									'product_id' =>$productId,
									'productname' =>$productname,
									'customer_id' =>$customerId,
									'customer_email'=>$custemail,
									'store_id'=>$storeId,
									'quote_qty'=>$qty,
									'quote_price'=>$price,
                                    'vendor_id'=> $vendor_id,
									'comments'=>$quotecommments,
								 ];
					array_push ($quoteArray,$quote);
					$vendors = [];
                    array_push($vendors,$vendor_id);

				}

				$this->_getSession->setData('quoteData',$quoteArray);
				$vendor = array_unique($vendors);
                $this->_getSession->setData('vendors',$vendor);

				$quoteData = $this->_getSession->getData('quoteData');
							 				
				$this->messageManager->addSuccess ( __ ( 'Quote was saved successfully' ) ); 
				return;
			}
		}
		
	}
}