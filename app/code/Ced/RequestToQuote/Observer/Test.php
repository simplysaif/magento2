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

namespace Ced\RequestToQuote\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
 
class Test implements ObserverInterface
{
	protected $_objectManager;
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, \Magento\Framework\App\RequestInterface $request) {
		$this->request = $request;
		$this->_objectManager = $objectManager;
	}
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $quote = $observer->getEvent()->getQuote(); 
        $quoteid = $this->request->getParam('po_incId');
        $enabled=$this->_objectManager->create('Ced\RequestToQuote\Model\Po')->getCollection()->addFieldToFilter('po_increment_id', $quoteid)->addFieldToFilter('status', '1')->getData();
        if(sizeof($enabled)>0){
          
            $items = $quote->getAllItems();
        
            foreach($items as $item){
                $quote=$this->_objectManager->create('Ced\RequestToQuote\Model\PoDetail')->getCollection()->addFieldToFilter('po_id',$quoteid)->addFieldToFilter('product_id',$item->getProductId())->getData();
                if(!empty($quote[0])){
                    $price=$quote[0]['po_price'];
                    $new_price = $quote[0]['po_price'] / $quote[0]['product_qty'];
                    $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                    $item->setCustomPrice($new_price);
                    $item->setOriginalCustomPrice($new_price);
                    $item->getProduct()->setIsSuperMode(true);
                }
            }
        }
    }
}