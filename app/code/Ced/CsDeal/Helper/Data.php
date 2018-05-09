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
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
	protected $_objectManager;
	protected $_storeManager;
	protected $_scopeConfigManager;
	protected $_configValueManager;
	protected $_storeId = 0;
	protected $_statuses; 
	protected $_coreRegistry = null;
	protected $_scopeConfig;
	
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Registry $registry
		)
    {
		$this->_objectManager = $objectManager;
		parent::__construct($context);
		$this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
		$this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		$this->_configValueManager = $this->_objectManager->get('Magento\Framework\App\Config\ValueInterface');
		$this->_transaction = $this->_objectManager->get('Magento\Framework\DB\Transaction');
		$this->_coreRegistry = $registry;
	}
	
	/**
     * Set a specified store ID value
     *
     * @param int $store
     * @return $this
     */
    public function setStoreId($store){
        $this->_storeId = $store;
        return $this;
    }
	
	/**
	 * Get current store
	 * @return Mage_Core_Model_Store
	 */
	public function getStore() {
	 	if ($this->_storeId) $storeId = (int)$this->_storeId;
	 	else $storeId =  isset($_REQUEST['store'])?(int) $_REQUEST['store']:null;
		return $this->_storeManager->getStore($storeId);
	}

	
	public function isApprovalNeeded()
	{
		$approval = $this->_scopeConfigManager->getValue('ced_csmarketplace/csdeal/csdeal_approval', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
		if($approval)
			return true;
		else
			return false;
	}

	public function initDeal($product)
	{
		$this->_statuses=$product->getId();	
	}

	public function isModuleEnable()
	{	
		$product=$this->_statuses;
		$store_id = $this->getStore()->getStoreId();
		if($this->_coreRegistry->registry('current_vendor'))
		{
			$vendor_id=$this->_coreRegistry->registry('current_vendor')->getId();	
		}
		else
		{
			if($this->_coreRegistry->registry('product'))
			$product=$this->_coreRegistry->registry('product')->getId();
			if(!$product)
			$product=$this->_statuses;
			$vendor_id=$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($product);
		}
		$setting=$this->_objectManager->get('Ced\CsDeal\Model\Dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
		if(count($setting->getData())){
			$status=$setting->getStatus();
		}else{
			$status = $this->_scopeConfigManager->getValue('ced_csmarketplace/csdeal/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
		}
		if($status)
			return true;
		else
			return false;
	}
	
	public function ShowTimer(){
	$product=$this->_statuses;
		if(!$this->isModuleEnable()) return;
		$store_id = $this->getStore()->getStoreId();
		if($this->_coreRegistry->registry('current_vendor')){
			$vendor_id=$this->_coreRegistry->registry('current_vendor')->getId();	}else{
				if($this->_coreRegistry->registry('product'))
					$product=$this->_coreRegistry->registry('product')->getId();
				if(!$product)
					$product=$this->_statuses;
				$vendor_id=$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($product);
			}
			$setting=$this->_objectManager->get('Ced\CsDeal\Model\Dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
			$ShowTimer='';
			if(count($setting->getData())>0){
				$ShowTimer=$setting->getTimerList();
			}else{
				$ShowTimer = $this->_scopeConfigManager->getValue('ced_csmarketplace/csdeal/csdeal_timer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
			}
	
			switch ($ShowTimer) {
				case 'list':
					if($this->_objectManager->get('\Magento\Framework\App\Request\Http')->getFullActionName()=='csmarketplace_vshops_view'){
						return true;
					}
					return false;
					break;
				case 'view':
					if($this->_objectManager->get('\Magento\Framework\App\Request\Http')->getFullActionName()=='catalog_product_view'){
						return true;
					}
					return false;
					break;
						
				default:
					return true;
					break;
			}
	}
	
	public function ShowDeal(){
		$product=$this->_statuses;
		if(!$this->isModuleEnable()) 
			return;
		$store_id = $this->getStore()->getStoreId();
		if($this->_coreRegistry->registry('current_vendor')){
			$vendor_id=$this->_coreRegistry->registry('current_vendor')->getId();	}else{
				if($this->_coreRegistry->registry('product'))
					$product=$this->_coreRegistry->registry('product')->getId();
				if(!$product)
					$product=$this->_statuses;
				$vendor_id=$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($product);
			}
			$setting=$this->_objectManager->get('Ced\CsDeal\Model\Dealsetting')->getCollection()->addFieldToFilter('vendor_id',$vendor_id)->getFirstItem();
			if(count($setting->getData())){
				$ShowDeal=$setting->getDealList();
			}else{
				$ShowDeal = $this->_scopeConfigManager->getValue('ced_csmarketplace/csdeal/csdeal_show', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
			}
			return $ShowDeal;
	}
	
	public function getDealEnd($product_id)
	{
		if(!$this->isModuleEnable()) return;
		$deal=$this->_objectManager->get('Ced\CsDeal\Model\Deal')->getCollection()->addFieldToFilter('status',\Ced\CsDeal\Model\Status::STATUS_ENABLED)->addFieldToFilter('product_id',$product_id)->getFirstItem();
		return $deal->getEndDate();
	}

	public function getStartDate($product_id){
		if(!$this->isModuleEnable()) return;
		$deal=$this->_objectManager->get('Ced\CsDeal\Model\Deal')->getCollection()->addFieldToFilter('status',\Ced\CsDeal\Model\Status::STATUS_ENABLED)->addFieldToFilter('product_id',$product_id)->getFirstItem();
		return $deal->getStartDate();
	}
	
	public function getDealText()
	{
		$product=$this->_statuses;
		$store_id = $this->getStore()->getStoreId();
		if($this->_coreRegistry->registry('current_vendor')){
			$vendor_id=$this->_coreRegistry->registry('current_vendor')->getId();	
		}else{
			if($this->_coreRegistry->registry('product'))
				$product=$this->_coreRegistry->registry('product')->getId();
			if(!$product)
				$product=$this->_statuses;
			$vendor_id=$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($product);
		}
		$setting=$this->_objectManager->get('Ced\CsDeal\Model\Dealsetting')->load($vendor_id, 'vendor_id');
		if($setting->getDealMessage()){
			$dealtext=$setting->getDealMessage();
		}else{
			$dealtext=$this->_scopeConfigManager->getValue('ced_csmarketplace/csdeal/csdeal_default_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
		}
		if($dealtext)
			return $dealtext;
	}
	
	public function getDealDay($deal)
	{
		$days=$deal->getDays();
		if($days){
			return true;
		}else{
			$specifiday=$deal->getSpecificdays();
			$specifiday=explode(',', $specifiday);
			switch (date("l")) {
				case 'Monday':
					if(in_array('mon',$specifiday))
						return true;
						break;
				case 'Tuesday':
					if(in_array('tue',$specifiday))
						return true;
						break;
				case 'Wednesday':
					if(in_array('wed',$specifiday))
						return true;
						break;
				case 'Thursday':
					if(in_array('thu',$specifiday))
						return true;
						break;
				case 'Friday':
					if(in_array('fri',$specifiday))
						return true;
						break;
				case 'Saturday':
					if(in_array('sat',$specifiday))
						return true;
						break;
				case 'Sunday':
					if(in_array('sun',$specifiday))
						return true;
						break;
				default:
					return true;
					break;
			}
		}
	}
	
	public function canShowDeal($product_id)
	{
		$deal=$this->_objectManager->get('Ced\CsDeal\Model\Deal')->getCollection()->addFieldToFilter('admin_status',\Ced\CsDeal\Model\Deal::STATUS_APPROVED)->addFieldToFilter('status',\Ced\CsDeal\Model\Status::STATUS_ENABLED)->addFieldToFilter('product_id',$product_id)->getFirstItem();

		if(!empty($deal->getData())){
			$endDate = $deal->getEndDate();
			$startDate = $deal->getStartDate();
			$timezone = $this->_scopeConfigManager->getValue('general/locale/timezone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$tz_object = new \DateTimeZone($timezone);
			$datetime = new \DateTime();
			$datetime->setTimezone($tz_object);
			$currentDate = $datetime->format('Y-m-d H:i:s');
			//var_dump(strtotime($currentDate) >= strtotime($startDate));die;
			//print_r($currentDate);die;
			//echo 'currentDate = '.strtotime($currentDate);
			//echo 'startDate = '.strtotime($startDate);die;
			//$ifDealDay = $this->getDealDay($deal);
			if((strtotime($currentDate) >= strtotime($startDate) ||  strtotime($currentDate)<= strtotime($endDate))){
				switch ($this->ShowDeal()) {
					case 'list':
						if($this->_objectManager->get('Magento\Framework\App\Request\Http')->getFullActionName()=='csmarketplace_vshops_view'){
							return true;
						}
						return false;
						break;
					case 'view':
						if($this->_objectManager->get('Magento\Framework\App\Request\Http')->getFullActionName()=='catalog_product_view'){
							return true;
						}
						return false;
						break;
							
					default:
						return true;
						break;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	
	}

	public function isActive(){
		return $this->_scopeConfigManager->getValue('ced_csmarketplace/csdeal/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
	}
	
}
