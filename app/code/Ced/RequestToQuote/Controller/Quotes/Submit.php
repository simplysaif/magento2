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
namespace Ced\RequestToQuote\Controller\Quotes;

use Magento\Backend\App\Action;

class Submit extends \Magento\Framework\App\Action\Action {
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Ced\RequestToQuote\Model\Quote $quote,
        \Ced\RequestToQuote\Model\QuoteDetail $quotedetail,
        \Ced\RequestToQuote\Model\Message $message,

        array $data = []
    )

    {
        $this->_storeManager = $storeManager;
        $this->_getSession = $customerSession;
        $this->_product = $product;
        $this->_quote = $quote;
        $this->_quotedetail = $quotedetail;
        $this->_message = $message;
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
            //echo "error";
            return $this->_redirect('customer/account/login');

        }
        if ($this->getRequest ()->isPost ()) {
            $vendors = [];
            $quoteData = $this->_getSession->getData('quoteData');

            $vendors = $this->_getSession->getData('vendors');


            if ($this->getRequest ()->getPost () != Null) {
                $data = $this->getRequest()->getParams();

                if(!isset($data['region'])  || !isset($data['region_id'])){
                    $this->messageManager->addError(__('Something went wrong while saving the quote. Kindly enter the state.'));
                    return  $this->_redirect('requesttoquote/cart/index');
                    //$this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
                }

                if(empty($data['city'])){
                    $this->messageManager->addError(__('Something went wrong while saving the quote. Kindly enter the city.'));
                    return $this->_redirect('requesttoquote/cart/index');
                }


                if(empty($data['street'])){
                    $this->messageManager->addError(__('Something went wrong while saving the quote. Kindly enter the correct address.'));
                    return $this->_redirect('requesttoquote/cart/index');
                }

                if(empty($data['zipcode']) && is_numeric($data['zipcode'])){
                    $this->messageManager->addError(__('Something went wrong while saving the quote. Kindly enter the correct zipcode.'));
                    return $this->_redirect('requesttoquote/cart/index');
                }

                if(empty($data['telephone']) && is_numeric($data['telephone'])){
                    $this->messageManager->addError(__('Something went wrong while saving the quote. Kindly enter the correct data.'));
                    return $this->_redirect('requesttoquote/cart/index');
                }

                $quotes = [];
                $final = [];
                foreach ($vendors as $vendordata) {
                    $quoteqty = 0;
                    $quoteprice = 0;
                    $vendorquote=[];
                    foreach ($quoteData as $quoteValues){
                        if($vendordata == $quoteValues['vendor_id']){
                            $quoteqty =$quoteqty+$quoteValues['quote_qty'];
                            $quoteprice =$quoteprice+$quoteValues['quote_price'];
                            array_push($vendorquote,$quoteValues);

                        }

                    }

                    $quote = [
                        'vendor_id'=>$vendordata,
                        'quoteqty' => $quoteqty,
                        'quoteprice' => $quoteprice,
                    ];
                    array_push($quotes,$quote);
                    $final[$vendordata] =  $vendorquote;


                }
                if(isset($data['method'])){
                    $country_name =$this->_objectManager->create('\Magento\Directory\Model\Country')->load($data['country_id'])->getName();
                    $code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
                    $currency = $this->_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($code)->getCurrencySymbol();
                    $shipping = explode('/'.$currency,$data['method']);
                }
                else{
                    $this->messageManager->addError(__('Something went wrong while saving the quote. Kindly select the shiping method.'));
                    return $this->_redirect('requesttoquote/cart/index');
                }

                try {
                    $item_info = array();
                    $totals = array();
                    $region = '';

                    $post = $this->getRequest()->getPostValue();
                    if($post['region'] !== ''){
                        $region = $post['region'];
                    }else{
                        $region = $this->_objectManager->create('Magento\Directory\Model\Region')->load($post['region_id'])->getName();
                    }
                   
                    foreach ($final as $vendor_id=>$vquotes){

                        foreach ($quotes as $key => $quoteValue){
                            $quote_collection = $this->_objectManager->create('Ced\RequestToQuote\Model\Quote')
                                                        ->getCollection();
                                if(sizeof($quote_collection)>0){
                                   $qo_id =  $quote_collection->getLastItem()->getQuoteId();
                                   $qo_id = $qo_id + 1;
                                   $qoincId = 'QO'.sprintf("%05d", $qo_id);
                                  
                                }
                                else{
                                    $qoincId = 'QO00001';
                                }

                            if($vendor_id == $quoteValue['vendor_id']){
                                $quotemodel = $this->_objectManager->create('Ced\RequestToQuote\Model\Quote');
                                $quotemodel->setData('customer_id', $data['customerId']);
                                $quotemodel->setData('quote_increment_id', $qoincId);
                                $quotemodel->setData('vendor_id', $vendor_id);
                                $quotemodel->setData('customer_email', $data['customeremail']);
                                $quotemodel->setData('country', $country_name);
                                $quotemodel->setData('state', $region);
                                $quotemodel->setData('city', $data['city']);
                                $quotemodel->setData('pincode', $data['zipcode']);
                                $quotemodel->setData('address', $data['street'].','.$data['area']);
                                $quotemodel->setData('telephone', $data['telephone']);
                                $quotemodel->setData('store_id', $this->_storeManager->getStore()->getStoreId());
                                $quotemodel->setData('quote_total_qty', $quoteValue['quoteqty']);
                                $quotemodel->setData('quote_total_price', $quoteValue['quoteprice']);
                                $quotemodel->setData('quote_updated_qty', $quoteValue['quoteqty']);
                                $quotemodel->setData('quote_updated_price', $quoteValue['quoteprice']);
                                $quotemodel->setData('shipping_amount', $shipping[1]);
                                $quotemodel->setData('shipment_method', $shipping[0]);
                                $quotemodel->setData('status', 'Created');
                                $quotemodel->setData('comments', $data['message']);
                                $quotemodel->setData('last_updated_by', 'Customer');
                                $quotemodel->save();

                                foreach ($vquotes as $quotedesc){
                                
                                    $prunit_price = $quotedesc['quote_price']/$quotedesc['quote_qty'];
                                    $prunit_price = sprintf('%0.2f', $prunit_price);
                                    $quoteDetails = $this->_objectManager->create('Ced\RequestToQuote\Model\QuoteDetail');
                                    $quoteDetails->setData('quote_id', $quotemodel->getQuoteId());
                                    $quoteDetails->setData('customer_id', $data['customerId']);
                                    $quoteDetails->setData('product_id', $quotedesc['product_id']);
                                    $quoteDetails->setData('vendor_id', $quotedesc['vendor_id']);
                                    $quoteDetails->setData('store_id', $quotedesc['store_id']);
                                    $quoteDetails->setData('actual_unit_price', $this->_product->load($quotedesc['product_id'])->getPrice());
                                    $quoteDetails->setData('product_qty', $quotedesc['quote_qty']);
                                    $quoteDetails->setData('price', $quotedesc['quote_price']);
                                    $quoteDetails->setData('quote_updated_qty', $quotedesc['quote_qty']);
                                    $quoteDetails->setData('updated_price', $quotedesc['quote_price']);
                                    $quoteDetails->setData('unit_price', $prunit_price);
                                    $quoteDetails->setData('status', '0');
                                    $quoteDetails->setData('additional_data', '');
                                    $quoteDetails->setData('remarks', $quotedesc['comments']);
                                    $quoteDetails->setData('last_updated_by', 'Customer');
                                    $quoteDetails->save();
                                    $item_info[$quotedesc['product_id']]['prod_id'] = $quotedesc['product_id'];
                                    $item_info[$quotedesc['product_id']]['name'] = $this->_product->load($quotedesc['product_id'])->getName();
                                    $item_info[$quotedesc['product_id']]['qty'] = $quotedesc['quote_qty'];
                                    $item_info[$quotedesc['product_id']]['sku'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($quotedesc['product_id'])->getSku();
                                    $item_info[$quotedesc['product_id']]['price'] = $quotedesc['quote_price'];
                                }
                            }
                         }
                    }
                    foreach ($quoteData as $key => $value) {
                       unset($quoteData[$key]);
                    }
            

                    $this->_getSession->setData('quoteData',$quoteData);
                    $totals['subtotal'] = $this->_quote->load($quotemodel->getQuoteId())->getQuoteUpdatedPrice();
                    $totals['shipping'] = $this->_quote->load($quotemodel->getQuoteId())->getShippingAmount();
                    $totals['grandtotal'] = $totals['subtotal'] + $totals['shipping'];
                    $this->_getSession->unsAddress();
                    $template = 'quote_submit_email_template';
                    $template_variables = array('quote_id' => '#'.$quotemodel->getQuoteIncrementId(),
                                                'quote_status' => 'Pending',
                                                'item_info' => $item_info,
                                                'totals' => $totals);
                    $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendEmail($template, $template_variables, $data['customeremail']);
                    
                    

                }

                catch (Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the quote. Kindly enter the correct data.'));
                }
            }
            //$link = $this->_urlInterface->getBaseUrl().'requesttoquote/customer/quotes/quoteId/'.$quotemodel->getQuoteId();
            //$message =  $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendQuoteCreatedMail($data['customerId'], $quotemodel->getQuoteId(), $link,  $cancel,$ipath);
            
            $this->messageManager->addSuccess ( __ ( 'Quote was saved successfully' ) );
            
            return $this->_redirect('requesttoquote/customer/quotes/');


                //return

            }
        }


}