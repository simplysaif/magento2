<?php 

/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMultiSeller
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsMultiSeller\Controller\Product;

class Assign extends \Ced\CsMarketplace\Controller\Vendor
{
	
	
    
	/* public function dispatch(\Magento\Framework\App\RequestInterface $request)
	{
	
		 
		if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store'))
	
			$this->_objectManager->get('Magento\Framework\Registry')->unRegister('ced_csmarketplace_current_store');
	
		if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website'))
	
			$this->_objectManager->get('Magento\Framework\Registry')->unRegister('ced_csmarketplace_current_website');
	
		$this->_objectManager->get('Magento\Framework\Registry')->register('ced_csmarketplace_current_store',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId());
	
		$this->_objectManager->get('Magento\Framework\Registry')->register('ced_csmarketplace_current_website',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId());
		parent::dispatch($request);
	
	} */
		
	/**	
	* Create product Form	
	*/
    public function execute()
    {
		$vproducts = $this->_objectManager->get('Ced\CsMultiSeller\Model\Multisell')->getVendorProductIds();
	
        $productCount = count($this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProductIds($this->_getSession()->getVendorId()))+count($vproducts);
        if($productCount >= $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getVendorProductLimit()){        
        	$this->messageManager->addError(__('Product Creation limit has Exceeded'));        
        	$this->_redirect('*/*/index',array('store'=>$this->getRequest()->getParam('store', 0)));        
        	return;        
        }
        if(!$this->_getSession()->getVendorId())        
        	return;    

      
        if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled()) {        
        	$this->_redirect('\Ced\CsMarketplace\Controller\Vendor\Index');        
        	return;        
        }
        
        if($this->getRequest()->getParam('id','')==''){        
        	$this->_redirect ('\Ced\CsMultiSeller\Controller\Product\New');        
        	return;        
        }    
  		 $resultPage = $this->resultPageFactory->create();	
  		// $this->_initLayoutMessages ( 'customer/session' );
  		/*  $navigationBlock = $this->getLayout()->getBlock('csmarketplace_vendor_navigation');
  		 if ($navigationBlock) {
  		 	$navigationBlock->setActive('csmultiseller/product/new');
  		 }	 */
        $resultPage->getConfig()->getTitle()->set(__('Add Product'));   
        return $resultPage;            
        
    }
}
