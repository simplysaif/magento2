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
  * @category  Ced
  * @package   Ced_CsRequestToQuote
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsRequestToQuote\Controller\Quotes; 

use \Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
 
class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    public function __construct(
      \Magento\Framework\App\Action\Context $context, 
      \Magento\Customer\Model\Session $session,
      PageFactory $resultPageFactory,
      UrlFactory $urlFactory,
      \Magento\Framework\Module\Manager $moduleManager,
      FileFactory $fileFactory,
      ForwardFactory $resultForwardFactory,
      Registry $coreRegistry, 
      Date $dateFilter,
      \Ced\RequestToQuote\Model\Message $chats, 
      \Ced\RequestToQuote\Model\QuoteDetail $quotedesc,              
      \Ced\RequestToQuote\Model\Quote $quote
      ){
          $this->chats = $chats;
          $this->_session = $session;
          $this->quotedesc = $quotedesc;
          $this->quote = $quote;
          parent::__construct ($context, $session, $resultPageFactory, $urlFactory, $moduleManager, $coreRegistry, $dateFilter, $chats, $quotedesc, $quote);
      }

    public function execute()
    { 
        $data = $this->getRequest()->getPostValue();
        $quote_id = $data['id'];
        $customer_email = $data['customer_email'];
        $quoteData = $this->quote->load($quote_id);
        $quotedqty = $quoteData->getQuoteTotalQty();
        $quotedPrice = $quoteData->getQuoteTotalPrice();
        $customerId = $quoteData->getCustomerId();
        $vendor = $quoteData->getVendorId();
        $quoteUpdprice = $data['subtotalofpo'];
        $quoteMessage = $this->_objectManager->create ('\Ced\RequestToQuote\Model\Message');
        
        try {
            
            $totalproducts = 0;
            foreach ($data['quoteproducts'] as $key=>$value) {
               $quotecollectiondata = $this->quotedesc->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToFilter('product_id',$key);
                foreach ($quotecollectiondata as $item) {
                    $item->setData('quote_updated_qty',$value);
                    $item->setData('last_updated_by','Vendor');
                    $item->save();
                    $totalproducts = $totalproducts + $value;

                }   
            }
            $item_info = array();
            $totals = array();
            
            foreach ($data['unitprice'] as $prod=>$uprice) {
                $quoteCollectiondata = $this->quotedesc->getCollection()->addFieldToFilter('quote_id',$quote_id)->addFieldToFilter('product_id',$prod);
                foreach ($quoteCollectiondata as $itemvalue) {
                    
                    $itemqty = $itemvalue->getData('quote_updated_qty');
                    $quoteupdprice = $itemqty*$uprice;
                    $itemvalue->setData('updated_price', $quoteupdprice);
                    $itemvalue->setData('unit_price', $uprice);
                    $itemvalue->save();
                }
                $item_info[$prod]['name'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($prod)->getName();
                $item_info[$prod]['qty'] = $itemqty;
                $item_info[$prod]['sku'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($prod)->getSku();
                $item_info[$prod]['price'] = $quoteupdprice;
            }
            
            if($quotedqty>0){
                if($totalproducts == $quotedqty){
                    $quoteData->setQuoteUpdatedQty($quotedqty);
                }
                else{
                    $quoteData->setQuoteUpdatedQty($totalproducts);
                }
            }
            else{
                $quoteData->setQuoteUpdatedQty($totalproducts);
            }
            if($quoteUpdprice=='0.00') {
                $quoteData->setQuoteUpdatedPrice($quotedPrice);
            }
            else{
                
                $quoteData->setQuoteUpdatedPrice($quoteUpdprice);
            }
            if($data['status']==0){
                $quoteData->setStatus(1);
            }
            else{
                $quoteData->setStatus($data['status']);
            }

            $quoteData->setData('last_updated_by','Vendor');
            //print_r($quoteData->getData());die('aaaa');
            
            if($quoteData->getStatus() == 2){
                $quoteData->setData('remaining_qty',$quoteData->getQuoteUpdatedQty());
            }
            $quoteData->save();

            if(!empty($data['message'])){

                $quoteMessage->setData('quote_id', $quote_id);
                $quoteMessage->setData('customer_id',$customerId);
                $quoteMessage->setData('vendor_id', $vendor);
                $quoteMessage->setData('message', $data['message']);
                $quoteMessage->setData('sent_by','Vendor');
                $quoteMessage->save();
            }
            $status = $quoteData->getStatus();
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
            $email = $quoteData->getCustomerEmail();
            $totals['subtotal'] = $this->quote->load($quote_id)->getQuoteUpdatedPrice();
            $totals['shipping'] = $this->quote->load($quote_id)->getShippingAmount();
            $totals['grandtotal'] = $totals['subtotal'] + $totals['shipping'];
            $template = 'quote_update_email_template';
            $template_variables = array('quote_id' => '#'.$this->quote->load($quote_id)->getQuoteIncrementId(),
                                        'quote_status' => $label,
                                        'item_info' => $item_info,
                                        'totals' => $totals);
            $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendEmail($template, $template_variables, $email);
            $this->messageManager->addSuccessMessage(__('Quote # %1 has been successfully updated', $quote_id));
            $this->_redirect('csrequesttoquote/quotes/index');
            return;
                 
        } catch (Exception $e) {
            echo $e->getMessage();die("skfjhdsf");
        }
    }
}

