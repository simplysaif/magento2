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

class Index extends \Ced\CsMarketplace\Controller\Vendor
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
	* Product list page	
	*/
    public function execute()
    {		        
        if(!$this->_getSession()->getVendorId()){
          $this->_redirect('csmarketplace/vendor/index');        
        	return;    

        }
        if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled()) {        
        	$this->_redirect('\Ced\CsMarketplace\Controller\Vendor\Index');        
        	return;        
        }        
      
  	    $resultPage = $this->resultPageFactory->create();	
  
        $resultPage->getConfig()->getTitle()->set(__('Product List'));
        return $resultPage;
        
    }
}
