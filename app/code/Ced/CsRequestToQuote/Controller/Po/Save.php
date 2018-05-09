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
namespace Ced\CsRequestToQuote\Controller\Po; 

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
      \Ced\RequestToQuote\Model\Po $po,
      \Ced\RequestToQuote\Model\Quote $quote,
      \Ced\RequestToQuote\Model\QuoteDetail $quotedetail,
      \Ced\RequestToQuote\Model\PoDetail $podetail
      ){
          $this->chats = $chats;
          $this->_session = $session;
          $this->_po = $po;
          $this->_quote = $quote;
          $this->_quotedetail = $quotedetail;
          $this->_podetail = $podetail; 
          parent::__construct ($context, $session, $resultPageFactory, $urlFactory, $moduleManager, $fileFactory);
      }

    public function execute()
    { 
        $data = $this->_request->getPostValue();
        

        /*$currenturl = $this->_storeManager->getStore()->getCurrentUrl(true);
        $spliturl = explode('quote_id/', $currenturl);
        $quoteurl = explode('/key', $spliturl[1]);
        $quote_id = $quoteurl[0];*/
        $quote_id = $data['quote_id'];
        $quoteData = $this->_quote->load($quote_id);
        $customer_id = $quoteData->getCustomerId();
        $qty = $quoteData->getQuoteUpdatedQty(); 
        $price = $quoteData->getQuoteUpdatedPrice();
        $store_id = $quoteData->getStoreId();
        $quotetotalproducts = 0;
        $vendor_id = $this->_quote->load($quote_id)->getVendorId();
        $po_qty = 0;
       

        $pocollection = $this->_po->getCollection();
        try {
          if(sizeof($pocollection)>0){
              $po_id =  $pocollection->getLastItem()->getPoId();
              $poincId = 'PO000'.$store_id.'0'.$po_id;
          }
          else{
              $poincId = 'PO000'.$store_id.'01';
          }
          $link = $this->_url->getBaseUrl().'requesttoquote/quotes/addtocart/po_incId/'.$poincId;
          $item_info = array();
          $cancel = $this->_url->getBaseUrl().'requesttoquote/customer/cancelpo/po_incId/'.$poincId;
          foreach ($data['quoteproducts'] as $key=>$value) {
            $po_detail = $this->_objectManager->create('Ced\RequestToQuote\Model\PoDetail');
            $po_detail->setData('po_id',$poincId);
            $po_detail->setData('vendor_id',$vendor_id);
            $po_detail->setData('quote_id', $quote_id);
            $po_detail->setData('product_id',$key);
            $po_detail->setData('product_qty', $value);
            $qdetail = $this->_quotedetail->getCollection();
            $qqty = $qdetail->addFieldToFilter('quote_id', $quote_id)->addFieldToFilter('product_id',$key)->getFirstItem()->getQuoteUpdatedQty();
            $qdetId = $qdetail->addFieldToFilter('quote_id', $quote_id)->addFieldToFilter('product_id',$key)->getFirstItem()->getId();
            $quotedetData = $this->_quotedetail->load($qdetId);
            if($qqty != $value){
              $quotedetData->setStatus(2);
            }
            $qprice = $qdetail->addFieldToFilter('quote_id', $quote_id)->addFieldToFilter('product_id',$key)->getFirstItem()->getUpdatedPrice();
            $po_detail->setData('quoted_qty', $qqty );
            $po_detail->setData('quoted_price', $qprice);
            if($value < $qqty){
              if($quotedetData->getRemainingQty()){
                $po_detail->setData('product_qty', $value);
                $remqty = $quotedetData->getRemainingQty() - $value;
                $quotedetData->setRemainingQty($remqty);
                $po_detail->setData('remaining_qty', $remqty);
                $po_qty += $value;
              }elseif($quotedetData->getRemainingQty() === '0'){
              }else{
                $po_detail->setData('product_qty', $value);
                $remqty = $qqty - $value;
                $quotedetData->setRemainingQty($remqty);
                $po_detail->setData('remaining_qty', $remqty);
                $po_qty += $value;
              }
            }else{
              if($quotedetData->getRemainingQty()){
                $po_detail->setData('product_qty', $quotedetData->getRemainingQty());
                $po_qty += $quotedetData->getRemainingQty();
                $quotedetData->setRemainingQty(0);
                $po_detail->setData('remaining_qty', 0);
              }
              else{
                $po_detail->setData('product_qty', $qqty);
                $quotedetData->setRemainingQty(0);
                $po_detail->setData('remaining_qty', 0);
                $po_qty += $qqty;
              }
            }
            $po_detail->setData('status', 0);
            foreach ($data['row_total'] as $pid=>$total){
              if($key==$pid){
                $po_detail->setData('po_price', $total);
              }
            }
            $quotedetData->save();
            $po_detail->save();
            $quotetotalproducts = $quotetotalproducts + $value;  
            $item_info[$key]['name'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($key)->getName();
            $item_info[$key]['qty'] = $qqty;
            $item_info[$key]['sku'] = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($key)->getSku();
            $item_info[$key]['price'] = $qprice; 
          }
          if($price>$data['grandtotalofpo']){
            $remaining_price = $price-$data['grandtotalofpo'];
          }
          else{
            $remaining_price = $data['grandtotalofpo']-$price;
          }
          $this->_po->setData('quote_id',$quote_id);  
          $this->_po->setData('vendor_id',$vendor_id); 
          $this->_po->setData('po_increment_id',$poincId);
          $this->_po->setData('quote_updated_qty',$qty);
          $this->_po->setData('quote_updated_price',$price);
          $this->_po->setData('po_qty',$po_qty);
          $this->_po->setData('po_price',$data['grandtotalofpo']);
          $this->_po->setData('remaining_price',$remaining_price);
          $this->_po->setData('po_customer_id',$customer_id);
          $this->_po->setData('status', '0');
          $quoteData = $this->_quote->load($quote_id);
          if($qty == $quotetotalproducts){
            $quoteData->setStatus(4);
          }elseif($quoteData->getStatus() == '6')
            $quoteData->setStatus(6);
          else{
            $quoteData->setStatus(5);
          }
          $quoteData->setRemainingQty($quoteData->getRemainingQty() - $po_qty);
          $quoteData->save();                         
          $this->_po->save();
          $pdf = $this->_objectManager->create('\Ced\RequestToQuote\Model\Invoicepdf')->getInvoice($data,$this->_po);
          $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
          $path = $mediaDirectory->getAbsolutePath('requesttoquote/invoices/'.$poincId.'/');
          if (!file_exists($path))
          {
            mkdir($path, 0777, true);
          }
          try{
            file_put_contents($path.$poincId.'.pdf', $pdf->render());
              //$pomodel = $this->_objectManager->create('Ced\CsPurchaseOrder\Model\Purchaseorder')->load($this->getRequest()->getParam('id'))->setInvoiceStatus(1);
               
          }catch(\Exception $e)
          {
            echo $e->getMessage();
          }
          $ipath ='';
          if(file_exists($path.$poincId.'.pdf'))
          {
            $ipath = $path.$poincId.'.pdf';
               //$this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendPdf($a,$ipath);
          }
          $message =  $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendPoCreatedMail($customer_id, $quoteData->getQuoteIncrementId(), $poincId, $link, $po_qty, $data['subtotalofpo'], $cancel,$ipath);
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
          $totals['subtotal'] = $this->_quote->load($quote_id)->getQuoteUpdatedPrice();
          $totals['shipping'] = $this->_quote->load($quote_id)->getShippingAmount();
          $totals['grandtotal'] = $totals['subtotal'] + $totals['shipping'];
          $template = 'quote_update_email_template';
          $template_variables = array('quote_id' => '#'.$this->_quote->load($quote_id)->getQuoteIncrementId(),
                                      'quote_status' => $label,
                                      'item_info' => $item_info,
                                      'totals' => $totals);
          $this->_objectManager->create('Ced\RequestToQuote\Helper\Data')->sendEmail($template, $template_variables, $email);
          $this->messageManager->addSuccess ( __ ( 'Po was successfully created.'.$message ) );
          $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
          return $resultRedirect->setPath('csrequesttoquote/po/index');    
        }
        catch(\Exception $e){

        }
    }
}

