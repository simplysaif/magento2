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
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */ 
namespace Ced\Barcode\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
   
	protected $_scopeConfig;
	

	/* protected $_assetRepo; */
	protected $_storeId = 0;
	
	
	public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig) 
	{
			$this->_scopeConfig = $scopeConfig;
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
	
    public function getImgName()
    {//echo $config_path; die("jk");
    $img = $this->_scopeConfig->getValue('barcode/active11/banner', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    return $img;
    }
    
   public function getBarcodeOption()
   {
   	$barcodevalue = $this->_scopeConfig->getValue('barcode/active11/baroption', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
   	return $barcodevalue;
   }
   
   public function getDescriptionOption()
   {
   	$Descriptionvalue = $this->_scopeConfig->getValue('barcode/active11/desoption', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
   	return $Descriptionvalue;
   }
   
   public function getEncodeOption()
   {
   	$Encodevalue = $this->_scopeConfig->getValue('barcode/active11/encodeoption', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
   	return $Encodevalue;
   }
   public function isEnable()
   {
   	$Enable = $this->_scopeConfig->getValue('barcode/active/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  
   	return $Enable;
   }
   public function getConfig($value){
   
   	$configvalue =  $this->_scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
 	return $configvalue;
   }
}
