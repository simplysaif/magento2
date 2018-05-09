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

/**
 * Vendor Multiseller Product model
 *
 * @category    Ced
 * @package     Ced_CsMultiSeller
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 */
namespace Ced\CsMultiSeller\Model;
class Multisell extends \Ced\CsMarketplace\Model\FlatAbstractModel {
	
	
	protected $_vproducts=array();
	protected $_objectManager;
	
	/**
	 * @return void
	 */
	public function __construct(
			\Magento\Framework\ObjectManagerInterface $objectManager
	)
	{//die('die here');
	$this->_objectManager = $objectManager;
	}
	
	/**
	 * get Current vendor Product Ids
	 *
	 * @return array $productIds
	 */
	public function getVendorProductIds($vendorId=0){
		if(!empty($this->_vproducts)){
			return $this->_vproducts;
		}else {
			$vendorId=$vendorId?$vendorId:$this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId();
			//print_r($this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getColloection()->getData());die;
			$vcollection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('',$vendorId,0,1);
			//print_r($vcollection->getData());die;
			$productids=array();
			if(count($vcollection)>0){
				foreach($vcollection as $data){
					if($data->getIsMultiseller()==1)
						array_push($productids,$data->getProductId());
				}
				$this->_vproducts=$productids;
			}
		}
		//print_r($this->_vproducts);die;
		return $this->_vproducts;
	}
	
	/**
	 * Authenticate vendor-products association
	 *
	 * @param int $vendorId,int $productId
	 * @return boolean
	 */
	public function isAssociatedProduct($vendorId = 0, $productId = 0) {
	
		if(!$vendorId || !$productId)
			return false;
	
		$vproducts=$this->getVendorProductIds($vendorId);
		if(in_array($productId,$vproducts))
			return true;
			
		return false;
	
	}
	
	/**
	 * Validate csmarketplace product attribute values.
	 * @return array $errors
	 */
	public function validate(){
		$errors = array();
		$helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
		if (!\Zend_Validate::is( trim($this->getSku()) , 'NotEmpty')) {
			$errors[] = __('The Product SKU cannot be empty');
		}
	
		$qty=trim($this->getQty());
		if (!\Zend_Validate::is( $qty, 'NotEmpty')) {
			$errors[] = __('The Product Stock cannot be empty');
		}
		else if(!is_numeric($qty))
			$errors[] = __('The Product Stock must be a valid Number');
		
		$price=trim($this->getPrice());
		if (!\Zend_Validate::is( $price, 'NotEmpty')) {
			$errors[] = __('The Product Price cannot be empty');
		}
		else if(!is_numeric($price)&&!($price>0))
			$errors[] = __('The Product Price must be 0 or Greater');
		
		return $errors;
	}
	
	/**
	 * Save Product
	 * @params $mode
	 * @return int product id
	 */
	public function saveProduct($mode,$product,$origProduct) {
		//$this->request = $this->_objectManager->create('Magento\Framework\App\RequestInterface')->getParams();
		//$newProduct= $duplicateProduct;
		$productData = $this->_objectManager->create('Magento\Framework\App\RequestInterface')->getPost();
		$parentId= 0;
		if($origProduct)
			$parentId = $productData['id'];
		//$originalimage = $product->getImage();
		
		//echo $product->getId().'------';
		//echo $parentId;die;
		//$small_image = $product->getSmallImage();
		//$thumbnail = $product->getThumbnail();
		
		$productId=$product->getId();
		
		if(isset($productData['product']['price']))
			$product->setPrice($productData['product']['price']);
		if(isset($productData['product']['sku']))
			$product->setSku($productData['product']['sku']);
		if(isset($productData['product']['stock_data']))
			$this->saveStockData($productId,$productData['product']['stock_data']);
		$product->setData('media_gallery', array());
		$product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_SEARCH);
		if(!$this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->isSharingEnabled()){
			//echo $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId();die;
			$websiteIds =array($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website'));
			//print_r($websiteIds);die;
			$product->setWebsiteIds($websiteIds);
		}
		///print_r($productData['product']);die;
		if($mode==\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE){
			$product->setName($origProduct->getName());
			if($this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isProductApprovalRequired())
				$product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
			else 
				$product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
		}
		else if($mode==\Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE && isset($productData['product']['status']) 
				&& $this->_objectManager->get('\Ced\CsMarketplace\Model\Vproducts')->isApproved($product->getId())){
				$product->setStatus ($productData['product']['status']);
		}//die('dsdc');
		$product->getResource()->save($product);
		

		/**
		 * Relate Product data
		 * @params int mode,int $productId,array $productData
		*/
		$this->processPostSave($mode,$product,$productData,$parentId);
	
	
		/**
		 * Send Product Mails
		 * @params array productid,int $status
		*/
		if(!$this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isProductApprovalRequired() && $mode==\Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE){
			//die(get_class($this));
			$this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')
		 		->sendProductNotificationEmail(array($productId),\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS);
		}
	
	}
	
	/**
	 * Relate Product Data
	 * @params $mode,int $productId,array $productData
	 *
	 */
	public function processPostSave($mode,$product,$productData,$parentId=0){
					$websiteIds='';
					$productId=$product->getId();
					$storeId=$this->getStoreId();
					if($this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website')!='')
						$websiteIds=$this->_objectManager->get('Magento\Framework\Registry')->registry('ced_csmarketplace_current_website');
					else
						$websiteIds=implode(",",$product->getWebsiteIds());
					
					switch($mode) { 
						case \Ced\CsMarketplace\Model\Vproducts::NEW_PRODUCT_MODE:
							$prodata = isset($productData['product'])?$productData['product']:array();
							//echo $parentId;die;
							$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->setStoreId($storeId)->setData($prodata)
							->setQty(isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:0)
							->setIsInStock(isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:1)
							->setPrice($product->getPrice())
							->setName($product->getName())
							->setSpecialPrice($product->getSpecialPrice())
							->setCheckStatus ($this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isProductApprovalRequired()?\Ced\CsMarketplace\Model\Vproducts::PENDING_STATUS:\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS )
							->setProductId ($productId)
							->setVendorId($this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId())
							->setType(isset($productData['type'])?$productData['type']:$product->getTypeId())
							->setWebsiteId($websiteIds)
							->setStatus($this->_objectManager->get('Ced\CsMultiSeller\Helper\Data')->isProductApprovalRequired()?\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED:\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
							->setIsMultiseller(1)
							->setParentId($parentId)
							->save();
	
						case \Ced\CsMarketplace\Model\Vproducts::EDIT_PRODUCT_MODE:
							$model = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->setStoreId($storeId)->loadByField(array('product_id'),array($product->getId()));
							//print_r($productData['product']);die;
							//print_r($websiteIds);die('in else');
							if($model && $model->getId()){
								if(isset($productData['product']['price']))
									$model->setPrice($productData['product']['price']);
								if(isset($productData['product']['sku']))
									$model->setSku($productData['product']['sku']);
								$model->setQty( isset($productData['product']['stock_data']['qty'])?$productData['product']['stock_data']['qty']:array() );
								$model->setIsInStock( isset($productData['product']['stock_data']['is_in_stock'])?$productData['product']['stock_data']['is_in_stock']:array() );
								if($model->getCheckStatus()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS)
									$model->setStatus(isset($productData['product']['status'])?$productData['product']['status']:\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
								$model->save();
							}
					}
	}
	
	
	/**
	 * Save Product Stock data
	 * @params int $productId,array $productData
	 * @return int product id
	 */
	private function saveStockData($productId,$stockData){
		//$stockItem = Mage::getModel('cataloginventory/stock_item');
		$stockItem = $this->_objectManager->create('Magento\CatalogInventory\Model\Stock\Item');
		$stockItem->load($productId, 'product_id');
	//	$stockItem->loadByProductId($productId);
		//print_r($stockItem->getData());die('saveStockData');
		if(!$stockItem->getId()){$stockItem->setProductId($productId)->setStockId(1);}
		$stockItem->setProductId($productId)->setStockId(1);
	
		$manage_stock = isset($stockData['manage_stock'])?$stockData['manage_stock']:1;
		$stockItem->setData('manage_stock', $manage_stock);
		
		$is_in_stock = isset($stockData['is_in_stock'])?$stockData['is_in_stock']:1;
		$stockItem->setData('is_in_stock', $is_in_stock);
	
		$savedStock = $stockItem->save();
	
		$qty = isset($stockData['qty'])?$stockData['qty']:0;
		$stockItem->load($savedStock->getId())->setQty($qty)->save();
	
		$is_in_stock = isset($stockData['is_in_stock'])?$stockData['is_in_stock']:1;
		$stockItem->setData('is_in_stock', $is_in_stock);
	
		$use_config_manage_stock = isset($stockData['use_config_manage_stock'])?$stockData['use_config_manage_stock']:0;
		$stockItem->setData('use_config_manage_stock', $use_config_manage_stock);
		
		$manage_stock = isset($stockData['manage_stock'])?$stockData['manage_stock']:1;
		$stockItem->setData('manage_stock', $manage_stock);
	
		$is_decimal_divided = isset($stockData['is_decimal_divided'])?$stockData['is_decimal_divided']:0;
		$stockItem->setData('is_decimal_divided', $is_decimal_divided);
	
		$savedStock = $stockItem->save();
	}
}

?>