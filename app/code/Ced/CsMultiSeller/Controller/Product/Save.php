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

class Save extends \Ced\CsMarketplace\Controller\Vendor
{
	public $mode='';
	
	/**
	 *  \Ced\CsMarketplace\Controller\Vendor::dispatch() 
	 */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
    	if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store'))
    
    		$this->_objectManager->get('Magento\Framework\Registry')->unRegister('ced_csmarketplace_current_store');
    
    	if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website'))
    
    		$this->_objectManager->get('Magento\Framework\Registry')->unRegister('ced_csmarketplace_current_website');
    
    	$this->_objectManager->get('Magento\Framework\Registry')->register('ced_csmarketplace_current_store',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId());
    
    	$this->_objectManager->get('Magento\Framework\Registry')->register('ced_csmarketplace_current_website',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId());
    	return parent::dispatch($request);
    
    }
	
    /**
	 * Create product duplicate
	 */
    public function execute()
    {	
	    if(!$this->_getSession()->getVendorId())        
        	return;
                
    	if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled()) {        
        	$this->_redirect('\Ced\CsMarketplace\Controller\Vendor\Index');        
        	return;        
        }
        
        $this->mode = \Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE;
        
        $customData = $this->getRequest()->getPost();       
        $productErrors=array();        
        $vproductModel = $this->_objectManager->get('Ced\CsMultiSeller\Model\Multisell');        
        $vproductModel->addData(isset($customData['product'])?$customData['product']:'');        
        $vproductModel->addData(isset($customData['product']['stock_data'])?$customData['product']['stock_data']:'');        
        $productErrors=$vproductModel->validate();
        
        
        
        if (!empty($productErrors)) {        
        	foreach ($productErrors as $message) {        
        		$this->messageManager->addError($message);    
        	}
           
        	$this->_redirect('*/*/edit', array('id'=>$this->getRequest()->getParam('id',''),'store'=>$this->getRequest()->getParam('store',0)));
           	return;        
        }
        
        $product = $this->_initProduct();
        
        try {
        
        	if(count($customData)>0){
        
        		$store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        		$this->_objectManager->get('Ced\CsMultiSeller\Model\Multisell')->setStoreId($product->getStoreId())->saveProduct($this->mode,$product,0);
        	       
        		$this->setCurrentStore($store);        
        		$this->messageManager->addSuccess(__('The product has been saved.'));    	        
        	}        
        	else{        
        		$this->messageManager->addError($this->__('Unable to save Product.'));        
        	}        
        	$this->_redirect('*/*/index', array('store'=>$product->getStoreId()));        
        }
        
        catch (Exception $e) {        
        	$this->messageManager->addError($e->getMessage());        
        	$this->_redirect('*/*/index', array('store'=>$product->getStoreId()));        
        }  
    }
    
    
    /**    
    * Initialize product from request parameters  
    * @return Magento\Catalog\Model\Product
    */
    
    protected function _initProduct()
    {
    	if(!$this->_getSession()->getVendorId())
    		return;
    
    	$productId  = (int) $this->getRequest()->getParam('id');
    	$store = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        
    	$product = $this->_objectManager->get('Magento\Catalog\Model\Product');
         
    	if (!$productId) {
    		$product->setStoreId(0);
    		if ($setId = (int) $this->getRequest()->getParam('set')) {
    			$product->setAttributeSetId($setId);
    		}
    
    		if ($typeId = $this->getRequest()->getParam('type')) {
    			$product->setTypeId($typeId);
    		}
    	}
    
    	$product->setData('_edit_mode', true);
    	if ($productId) {
    		$storeId = (int) $this->getRequest()->getParam('store', 0);
    		if($this->mode==\Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE && $this->getRequest()->getParam('store')){
    			$websiteId = $this->_objectManager->get('Magento\Store\Model\Store')->load($this->getRequest()->getParam('store'))->getWebsiteId();
               
    			if($websiteId){
    				if(in_array($websiteId,$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getAllowedWebsiteIds())){
    					$storeId=$this->getRequest()->getParam('store');
    				}
    			}
    		}

    		try {
    			$product->setStoreId($storeId)->load($productId);
    		} catch (Exception $e) {
    			$product->setTypeId(\Magento\Catalog\Model\Product\Type::DEFAULT_TYPE);
    			//Mage::logException($e);
    		}
    
    	}
    
    	$attributes = $this->getRequest()->getParam('attributes');
    	if ($attributes && $product->isConfigurable() &&
    			(!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
    				$product->getTypeInstance()->setUsedProductAttributeIds(
    						explode(",", base64_decode(urldecode($attributes)))
    				);
    			}
    			 
    			// Required attributes of simple product for configurable creation
    
    			if ($this->getRequest()->getParam('popup')
    					&& $requiredAttributes = $this->getRequest()->getParam('required')) {
    						$requiredAttributes = explode(",", $requiredAttributes);
    						foreach ($product->getAttributes() as $attribute) {
    							if (in_array($attribute->getId(), $requiredAttributes)) {
    								$attribute->setIsRequired(1);
    							}
    						}
    					}
    					 
    					if ($this->getRequest()->getParam('popup')
    							&& $this->getRequest()->getParam('product')
    							&& !is_array($this->getRequest()->getParam('product'))
    							&& $this->getRequest()->getParam('id', false) === false) {;
    								$configProduct = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection')
    								->setStoreId(0)
    								->load($this->getRequest()->getParam('product'))
    								->setTypeId($this->getRequest()->getParam('type'));
    
    
    								/* @var $configProduct Mage_Catalog_Model_Product */
    
    								$data = array();
    								foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {
    									 
    									/* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
    
    									if(!$attribute->getIsUnique()
    											&& $attribute->getFrontend()->getInputType()!='gallery'
    											&& $attribute->getAttributeCode() != 'required_options'
    											&& $attribute->getAttributeCode() != 'has_options'
    											&& $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
    												$data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
    											}
    								}
    								//print_r($configProduct->getWebsiteIds());die;
    								$product->addData($data)
    								->setWebsiteIds($configProduct->getWebsiteIds());
    							}
    								
    							$this->_objectManager->get('\Magento\Framework\Registry')->register('product', $product);
    							$this->_objectManager->get('\Magento\Framework\Registry')->register('current_product', $product);
    							$this->setCurrentStore();
    							return $product;
    }
    
    /**
     * Set current store
     */
    
    public function setCurrentStore(){
    
    	if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store')) {
    
    		$currentStoreId = $this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_store');
    
    		$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore($currentStoreId);
    
    	}
    
    }
}
