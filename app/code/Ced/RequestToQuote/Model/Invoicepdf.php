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

namespace Ced\RequestToQuote\Model;

use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;

/**
 * Sales Order Invoice PDF model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Invoicepdf extends \Magento\Sales\Model\Order\Pdf\Invoice
{
     /**
     * Return PDF document
     *
     * @param  array|Collection $invoices
     * @return \Zend_Pdf
     */
    public function getInvoice($data ,$poobject)
    {
    	//print_r($poobject->getData());die;
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $page = $this->newPage();
        // $order = $invoice->getOrder();
        /* Add image */
        $this->insertLogo($page, $this->_storeManager->getStore());
        /* Add address */
        $this->insertAddress($page,$this->_storeManager->getStore());
        /* Add head */
        $this->insertOrder(
            $page,
            $poobject,
            $this->_scopeConfig->isSetFlag(
                self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_storeManager->getStore()->getId()                )
        ); 
        /* Add document text and number */
        $this->insertDocumentNumber($page, __('PO #'.$poobject->getPoIncrementId()) );
        /* Add table */
        $this->_drawHeader($page);     
        $this->_afterGetPdf();
        return $pdf;
    }
    
    
    /**
     * Insert order to pdf page
     *
     * @param \Zend_Pdf_Page &$page
     * @param \Magento\Sales\Model\Order $obj
     * @param bool $putOrderId
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $pomodel = $objectManager->get('Ced\RequestToQuote\Model\Po')->load($obj->getPoIncrementId(),'po_increment_id');
        $registry = $objectManager->get('Magento\Framework\Registry');
        $vorder = $registry->registry('current_vorder');
        $quotemodel = $objectManager->create('Ced\RequestToQuote\Model\Quote')->load($obj->getQuoteId());
        $this->y = $this->y ? $this->y : 815;
        $top = $this->y;

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $top, 570, $top - 55);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->setDocHeaderCoordinates([25, $top, 570, $top - 55]);
        $this->_setFontRegular($page, 10);

        if ($putOrderId) {
        	$page->drawText(__('Quote') .' #'. $quotemodel->getQuoteIncrementId(), 35, $top -= 30, 'UTF-8');
        }
        $page->drawText(
            __('PO Date: ') .
            $this->_localeDate->formatDate(
                $this->_localeDate->scopeDate(
                    $this->_storeManager->getStore(),
                    $pomodel->getCreatedAt(),
                    true
                ),
                \IntlDateFormatter::MEDIUM,
                false
            ),
            35,
            $top -= 15,
            'UTF-8'
        );

        $top -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $top, 275, $top - 25);
        $page->drawRectangle(275, $top, 570, $top - 25);

        /* Calculate blocks info */

        /* Billing Address */
        $customername = $objectManager->create('\Magento\Customer\Model\Customer')->load($quotemodel->getCustomerId())->getName();
        $address =[];
        $address['customername'] = $customername;
        $address['country'] = $quotemodel->getCountry();
        $address['state'] = $quotemodel->getState();
        $address['city'] = $quotemodel->getCity();
        $address['pincode'] = $quotemodel->getPincode();
        $address['street'] = $quotemodel->getAddress();
        $address['telephone'] = $quotemodel->getTelephone();
        
        
        $billingAddress =  $address;
        $shippingAddress = $billingAddress;
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontBold($page, 12);
        $page->drawText(__('Sold to:'), 35, $top - 15, 'UTF-8');
        $page->drawText(__('Ship to:'), 285, $top - 15, 'UTF-8');

        $addressesHeight = $this->_calcAddressHeight($billingAddress);
        if (isset($shippingAddress)) {
            $addressesHeight = max($addressesHeight, $this->_calcAddressHeight($shippingAddress));
        }

        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $page->drawRectangle(25, $top - 25, 570, $top - 33 - $addressesHeight);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->_setFontRegular($page, 10);
        $this->y = $top - 40;
        $addressesStartY = $this->y;

        foreach ($billingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 35, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }
        $addressesEndY = $this->y;
        $this->y = $addressesStartY;
        foreach ($shippingAddress as $value) {
            if ($value !== '') {
                $text = [];
                foreach ($this->string->split($value, 45, true, true) as $_value) {
                    $text[] = $_value;
                }
                foreach ($text as $part) {
                    $page->drawText(strip_tags(ltrim($part)), 285, $this->y, 'UTF-8');
                    $this->y -= 15;
                }
            }
        }

        $addressesEndY = min($addressesEndY, $this->y);
        $this->y = $addressesEndY;

        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 275, $this->y - 25);
        $page->drawRectangle(275, $this->y, 570, $this->y - 25);

        $this->y -= 15;
        $this->_setFontBold($page, 12);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $page->drawText(__('Payment Mehod'), 35, $this->y, 'UTF-8');
        $page->drawText(__('Shipping Method'), 285, $this->y, 'UTF-8');

        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));

        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $paymentLeft = 35;
        $yPayments = $this->y - 15;
        
        foreach ($this->string->split("Payment Method will be applicable at time of order placing.", 45, true, true) as $_value) {
            $page->drawText(strip_tags(trim($_value)), $paymentLeft, $yPayments, 'UTF-8');
            $yPayments -= 15;
        }
            
        $topMargin = 15;
        $methodStartY = $this->y;
        $this->y -= 15;
            
        foreach ($this->string->split($quotemodel->getShipmentMethod(), 45, true, true) as $_value) {
            $page->drawText(strip_tags(trim($_value)), 285, $this->y, 'UTF-8');
            $this->y -= 15;
        }
            
        $yShipments = $this->y;
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $totalShippingChargesText = '(Estimated Shipping Charges '.$priceHelper->currency($quotemodel->getShippingAmount(), true, false).')';
        $page->drawText($totalShippingChargesText, 285, $yShipments - $topMargin, 'UTF-8');
        $yShipments -= $topMargin + 10; 
        $tracks = [];
        $yShipments -= $topMargin - 5;
        $currentY = min($yPayments, $yShipments);
            
        // replacement of Shipments-Payments rectangle block
        $page->drawLine(25, $methodStartY, 25, $currentY);
        //left
        $page->drawLine(25, $currentY, 570, $currentY);
        //bottom
        $page->drawLine(570, $currentY, 570, $methodStartY);
        //right
        $this->y = $currentY;
        $this->y -= 15;
            
        $podetail = $objectManager->create('Ced\RequestToQuote\Model\PoDetail')->getCollection()->addFieldToFilter('po_id', $obj->getPoIncrementId());
        $addresscontent = $this->y;
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $subtotal = 0;
        foreach($podetail as $_podetail){
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($_podetail->getProductId());
            $page->drawText($product->getName(), 40, $addresscontent-40, 'UTF-8');
            $page->drawText($product->getSku(), 250, $addresscontent-40, 'UTF-8');
            $page->drawText($priceHelper->currency($_podetail->getPoPrice(), true, false), 330, $addresscontent-40, 'UTF-8');
            $page->drawText($_podetail->getProductQty(), 420, $addresscontent-40, 'UTF-8');
            $page->drawText($priceHelper->currency($_podetail->getPoPrice(), true, false), 480, $addresscontent-40, 'UTF-8');
            $addresscontent -= 21;
            $subtotal += $_podetail->getPoPrice();
        }
        $addresscontent -= 50;
        $page->drawText(__('Subtotal:'), 420, $addresscontent, 'UTF-8');
        $page->drawText($priceHelper->currency($subtotal, true, false), 480, $addresscontent, 'UTF-8');

        $addresscontent -= 21;

        $page->drawText(__('Shipping & Handling:'), 368, $addresscontent, 'UTF-8');
        $page->drawText($priceHelper->currency($quotemodel->getShippingAmount(), true, false), 480, $addresscontent, 'UTF-8');

        $addresscontent -= 21;

        $page->drawText(__('Grand Total:'), 403, $addresscontent, 'UTF-8');
        $page->drawText($priceHelper->currency($quotemodel->getShippingAmount() + $subtotal, true, false), 480, $addresscontent, 'UTF-8');
    }
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
    	/* Add table head */
    	$this->_setFontRegular($page, 10);
    	$page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
    	$page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
    	$page->setLineWidth(0.5);
    	$page->drawRectangle(25, $this->y, 570, $this->y - 15);
    	$this->y -= 10;
    	$page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));
    
    	//columns headers
    	$lines[0][] = ['text' => __('Products'), 'feed' => 35];
    
    	$lines[0][] = ['text' => __('SKU'), 'feed' => 290, 'align' => 'right'];
    
    	$lines[0][] = ['text' => __('Quoted Qty'), 'feed' => 435, 'align' => 'right'];
    
    	$lines[0][] = ['text' => __('Quoted Price'), 'feed' => 360, 'align' => 'right'];
    
    	$lines[0][] = ['text' => __('Subtotal'), 'feed' => 495, 'align' => 'right'];
    
    	$lineBlock = ['lines' => $lines, 'height' => 5, 'align' => 'right'];
    
    	$this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
    	$page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
    	$this->y -= 20;
    }
    
}
