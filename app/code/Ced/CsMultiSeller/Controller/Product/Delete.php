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

class Delete extends \Ced\CsMarketplace\Controller\Vproducts
{
	/**	
	* Delete product action	
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

		if(!$vendorId)
			return;

		$id=$this->getRequest()->getParam('id');

		$vendorProduct=false;

		if($id && $vendorId){
			$vendorProduct = $this->_objectManager->get('Ced\CsMultiSeller\Model\Multisell')->isAssociatedProduct($vendorId,$id);
		}

		if(!$vendorProduct){
			$redirectBack=true;
		}

		else if ($id) {
			$this->_objectManager->get('Magento\Framework\Registry')->register("isSecureArea", 1);
			$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
			$product = $this->_objectManager->get('Magento\Catalog\Model\Product')->load($id);
			$sku = $product->getSku();
			try {

				if($product && $product->getId()) {
					$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
					$product->delete();
					$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->changeVproductStatus(array($id),\Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS);

					$this->messageManager->addSuccess(__('Your Product Has Been Sucessfully Deleted'));

				}

			}

			catch (Exception $e) {

				$this->messageManager->addError($e->getMessage());

			}

			$this->setCurrentStore();

		}

		/* if ($redirectBack) {

			$this->messageManager->addError(__('Unable to delete the product'));

		}
 		*/
	
		$this->_redirect('*/*/index', array('store'=> $this->getRequest()->getParam('store')));
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
