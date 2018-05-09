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
 * @package     Ced_CsDeal
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
Class Updatedealproduct implements ObserverInterface
{
	
	protected $_objectManager;
	protected $_quoteFactory;
	protected $_advanceFactory;
	protected $_object;
	
	public function __construct(		
			\Ced\CsDeal\Model\DealFactory $advanceFactory,
			\Magento\Framework\DataObject $object,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Quote\Model\QuoteFactory $quoteFactory
	)
    {
    	$this->_object = $object;
        $this->_objectManager = $objectManager;
        $this->_quoteFactory = $quoteFactory;
       	$this->_advanceFactory = $advanceFactory;
    }
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
     	$deal = $observer->getDeal();
        $product=$this->_objectManager->create('Magento\Catalog\Model\Product')->load($deal->getProductId());
        try{
            if($deal->getStatus() == 'running' || $deal->getStatus() == 'enabled')
            {//print_r($deal->getStartDate());die('-------');
                $product->setSpecialPrice($deal->getDealPrice());
                $product->setSpecialFromDate($deal->getStartDate());
                //$product->setSpecialFromDateIsFormated(true);
                $product->setSpecialToDate($deal->getEndDate());
                //$product->setSpecialToDateIsFormated(true);
            }
            else{ 
                //print_r($product->getData());die;
                $product->setSpecialPrice(null);
                $product->getResource()->saveAttribute($product, 'special_price');
                $product->save();
               }   
            $product->save();
        }catch(Exception $e){
            $this->_eventManager->addError(__('%s',$e->getMessage()));
        } 
    }	

}