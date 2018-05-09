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

use Magento\Backend\App\Action;

class SaveQuotes extends \Magento\Framework\App\Action\Action {
	public function __construct(
		\Magento\Framework\App\Action\Context $context, 
		\Magento\Customer\Model\Session $customerSession, 
		\Ced\RequestToQuote\Model\Quote $quote,
		\Ced\RequestToQuote\Model\QuoteDetail $quoteDetail,
		\Ced\RequestToQuote\Model\Message $message,
		\Ced\RequestToQuote\Model\Po $po,
		\Ced\RequestToQuote\Model\PoDetail $podetail,
		array $data = []
	) 
	{
		$this->_getSession = $customerSession;
		$this->quote = $quote;
		$this->quoteDetail = $quoteDetail;
		$this->message = $message;
		$this->po = $po;
		$this->podetail = $podetail;
		parent::__construct ( $context, $data );
	}
	
	/**
	 * Save action
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute() {
		if (! $this->_getSession->isLoggedIn ()) {
			$this->messageManager->addError ( __ ( 'Please login first' ) );
			//echo "error";
			$this->_redirect('customer/account/login');
			return;
		}

		$customer_id = $this->_getSession->getCustomer()->getId();
		try{
			if ($this->getRequest()->isPost()){
				if ($this->getRequest()->getPost () != Null) {
				$postdata = $this->getRequest()->getParams();
					if(isset($postdata['quote_id'])){
						$item_info = array();
						$totals = array();
						$quoteDescription = $this->quoteDetail->getCollection()->addFieldToFilter('quote_id', $postdata['quote_id']);
						$customerId = $this->quote->load($postdata['quote_id'])->getCustomerId();
						if($customer_id == $customerId){
							$quote_total_qty = 0;
							foreach ($quoteDescription as $value) {
								$data = $value->getData();
								$product_id = $data['product_id'];
								$updateduprice = $postdata['quoteproducts'][$product_id];
								$updatedqty = $postdata['quoteupdqty'][$product_id];
								$quote_total_qty += $updatedqty;
								$updatedprice = $postdata['row'][$product_id];
								$value->setData('quote_updated_qty', $updatedqty);
								$value->setData('updated_price', $updatedprice);
								$value->setData('unit_price', $updateduprice);
								$value->save();
		
								$item_info[$product_id]['name'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id)->getName();
								$item_info[$product_id]['sku'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product_id)->getSku();
								$item_info[$product_id]['qty'] = $updatedqty;
								$item_info[$product_id]['price'] = $updatedprice;
							    						
							}
							$this->quote->load($postdata['quote_id'])
										->setQuoteUpdatedQty($quote_total_qty)
										->setQuoteUpdatedPrice($postdata['quote_total']);
							$this->quote->save();
							$this->message->setData('customer_id', $customer_id);
							$this->message->setData('quote_id', $postdata['quote_id']);
							$this->message->setData('vendor_id', 0);
							$this->message->setData('product_id', $data['product_id']);
							$this->message->setData('message', $postdata['message']);
							$this->message->setData('sent_by', 'Customer');
							$this->message->save();
						}

						else{
							$this->messageManager->addError(__('You are not allowed to update this quote. Kindly update your quotes only.'));
                 					return $this->_redirect ('customer/account/index' );
                  
						}


					}/*$quoteDescription->setData*/
				}
			}
			$status = $this->quote->load($postdata['quote_id'])->getStatus();
			if($status == 0)
				$label = __('Pending');
			elseif($status == 1)
				$label = __('Processing');
			elseif($status == 2)
				$label = __('Approved');
			elseif($status == 3)
				$label = __('Cancelled');
			elseif($status == 4)
				$label = __('PO created');
			elseif($status == 5)
				$label = __('Partial Po');
			elseif($status == 6)
				$label = __('Ordered');
			else
				$label = __('Complete');
			$totals['subtotal'] = $this->quote->load($postdata['quote_id'])->getQuoteUpdatedPrice();
			$totals['shipping'] = $this->quote->load($postdata['quote_id'])->getShippingAmount();
			$totals['grandtotal'] = $totals['subtotal'] + $totals['shipping'];
			$email = $this->quote->load($postdata['quote_id'])->getCustomerEmail();
			$template = 'quote_update_email_template';
                    $template_variables = array('quote_id' => '#'.$this->quote->load($postdata['quote_id'])->getQuoteIncrementId(),
                                                'quote_status' => $label,
                                                'item_info' => $item_info,
                                                'totals' => $totals);
                    $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendEmail($template, $template_variables, $email);

			$this->messageManager->addSuccessMessage(__('Quote has been successfully updated'));
                 $this->_redirect ('requesttoquote/customer/quotes' );
                 return ;	
		}
		catch (Exception $e) {
            
        }
	}
		
		
}
		