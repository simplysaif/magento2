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

 namespace Ced\CsMultiSeller\Helper;
class Data extends \Magento\Framework\App\Helper\AbstractHelper{
	
	protected $_scopeConfig;
	protected $_objectManager;
	
	/**
	 * @return void
	 */
	public function __construct(
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		
		$this->_scopeConfig = $scopeConfig;
		$this->_storeManager = $storeManager;
		$this->_objectManager = $objectManager;
	}
	/**
	 * Check Product Admin Approval required
	 */
	public function isProductApprovalRequired(){
		//return Mage::getStoreConfig('ced_csmultiseller/general/approval',Mage::app()->getStore()->getId());
		return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_csmarketplace/ced_csmultiseller/approval',$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());
	}
	
	/**
	 * Check Product Admin Approval required
	 */
	public function isEnabled()
	{
		if($this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Ced_CsMarketplace')){
			return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_csmultiseller/general/activation_csmultiseller',0);
		}		
		return false;
	}
	
}
