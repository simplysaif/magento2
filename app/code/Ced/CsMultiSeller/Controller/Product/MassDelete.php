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

class MassDelete extends \Ced\CsMarketplace\Controller\Vproducts
{
   
	/**
	 * Mass Delete Products(s) 
	*/
    public function execute()
    {
		if(!$this->_getSession()->getVendorId())
			return;
		
		if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isEnabled()) {
			$this->_redirect('\Ced\CsMarketplace\Controller\Vendor\Index');
			return;
		}
		$vendorId=$this->_getSession()->getVendorId();

		$storeId    = (int)$this->getRequest()->getParam('store', 0);

		if(!$vendorId)
			return;

		$productIds = explode(',',$this->getRequest()->getParam('product'));

		if (!is_array($productIds)) {
			$this->messageManager->addError(__('Please select product(s).'));
		} else {
			if (!empty($productIds)) {
				try {
					$ids=array();
					$this->_objectManager->get('Magento\Framework\Registry')->register("isSecureArea", 1);
					$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
					//Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
					foreach ($productIds as $productId) {
						$vendorProduct=false;
						if($productId && $vendorId){
						//	echo $productId;die;
							$vendorProduct = $this->_objectManager->get('Ced\CsMultiSeller\Model\Multisell')->isAssociatedProduct($vendorId,$productId);
						}
						if(!$vendorProduct)
							continue;

						$product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($productId);
						//Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
						$this->_objectManager->get('Magento\Framework\Event\ManagerInterface')->dispatch('catalog_controller_product_delete', array('product' => $product));
						$product->delete();
						
						$ids[]=$productId;
					}

					$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->changeVproductStatus($ids,\Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS);

					$this->setCurrentStore();

					$this->messageManager->addSuccess(__('Total of # %1 record(s) have been deleted.', count($ids))

					);

				} catch (Exception $e) {

					 $this->messageManager->addError($e->getMessage());

				}

			}

		}

		$this->_redirect('*/*/index', array('store'=> $storeId));
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
