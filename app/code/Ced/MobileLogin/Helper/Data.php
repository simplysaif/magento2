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
  * @package     Ced_MobileLogin
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
  
namespace Ced\MobileLogin\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_objectManager;
	protected $_customerFactoery;
  protected $_scopeConfigManager;
  protected $_storeManager;

	public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Customer\Model\CustomerFactory $customerFactory      
    ) {
        $this->_objectManager = $objectManager;
        $this->_customerFactory = $customerFactory;      
        parent::__construct($context); 
        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');      
    }

    public function duplicate($mobile)
    {
        
        if($mobile){
        	$customer = $this->_customerFactory->create()->getCollection()
                            ->addAttributeToFilter('mobile',$mobile);            
        	if(count($customer->getData()))
                return false;
            return true;
        }
        return false;
    }

    public function validate($mobile)
    {
        $number = $this->_scopeConfigManager->getValue('ced_mobilelogin/mobile_login/number');
        if(strlen(trim($mobile))==$number){
            return true;
        }
        else{
            return false;
        }
    }

    public function editMobile($mobile)
    {
       
        if($mobile){
            $flag = true;
            $allcustomers = $this->_objectManager->create('Magento\Customer\Model\Customer')->getCollection()
                            ->addAttributeToFilter('mobile',$mobile);
            $cId = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomerId();
            if(count($allcustomers)){
                foreach($allcustomers as $allcustomer){
                    if($cId != $allcustomer->getEntityId())
                        $flag = false;
                }  
            }     
            return $flag;
        }
        return false;
    }

    /**
     * Set a specified store ID value
     *
     * @param  int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }
    
    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore() 
    {
        if ($this->_storeId) { $storeId = (int)$this->_storeId; 
        }
        else { $storeId =  isset($_REQUEST['store'])?(int) $_REQUEST['store']:null; 
        }
        return $this->_storeManager->getStore($storeId);
    }
    
    public function getCustomCSS()
    {
        return $this->_scopeConfigManager->getValue('ced_csmarketplace/vendor/theme_css', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }
    
    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsDashboard()
    {
        return $this->getVendorUrl() == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'/index' == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'/index/' == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'index' == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'index/' == $this->_getUrl('*/*/*');
    }

    public function setLogo($logo_src, $logo_alt)
    {
        $this->setLogoSrc($logo_src);
        $this->setLogoAlt($logo_alt);
        return $this;
    }
    
    public function getMarketplaceVersion()
    {
        return trim((string)$this->getReleaseVersion('Ced_CsMarketplace'));
    }
    
    public function getReleaseVersion($module)
    {
        $modulePath = $this->moduleRegistry->getPath(self::XML_PATH_INSTALLATED_MODULES, $module);
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, "$modulePath/etc/module.xml");
        $source = new \Magento\Framework\Simplexml\Config($filePath);
        if($source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version) {
            return $source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version->__toString(); 
        }
        return false; 
    }
   
    
    /**
     * Url encode the parameters
     *
     * @param  string | array
     * @return string | array | boolean
     */
    public function prepareParams($data)
    {
        if(!is_array($data) && strlen($data)) {
            return urlencode($data);
        }
        if($data && is_array($data) && count($data)>0) {
            foreach($data as $key=>$value){
                $data[$key] = urlencode($value);
            }
            return $data;
        }
        return false;
    }
    
    /**
     * Url decode the parameters
     *
     * @param  string | array
     * @return string | array | boolean
     */
    public function extractParams($data)
    {
        if(!is_array($data) && strlen($data)) {
            return urldecode($data);
        }
        if($data && is_array($data) && count($data)>0) {
            foreach($data as $key=>$value){
                $data[$key] = urldecode($value);
            }
            return $data;
        }
        return false;
    }
    
    /**
     * Add params into url string
     *
     * @param  string  $url       (default '')
     * @param  array   $params    (default array())
     * @param  boolean $urlencode (default true)
     * @return string | array
     */
    public function addParams($url = '', $params = array(), $urlencode = true) 
    {
        if(count($params)>0) {
            foreach($params as $key=>$value){
                if(parse_url($url, PHP_URL_QUERY)) {
                    if($urlencode) {
                        $url .= '&'.$key.'='.$this->prepareParams($value); 
                    }
                    else {
                        $url .= '&'.$key.'='.$value; 
                    }
                } else {
                    if($urlencode) {
                        $url .= '?'.$key.'='.$this->prepareParams($value); 
                    }
                    else {
                        $url .= '?'.$key.'='.$value; 
                    }
                }
            }
        }
        return $url;
    }
    
    /**
     * Retrieve all the extensions name and version developed by CedCommerce
     *
     * @param  boolean $asString (default false)
     * @return array|string
     */
    public function getCedCommerceExtensions($asString = false) 
    {
        if($asString) {
            $cedCommerceModules = '';
        } else {
            $cedCommerceModules = array();
        }
        $allModules = $this->_context->getScopeConfig()->getValue(\Ced\SecurePay\Model\Feed::XML_PATH_INSTALLATED_MODULES);
        $allModules = json_decode(json_encode($allModules), true);
        foreach($allModules as $name=>$module) {
            $name = trim($name);
            if(preg_match('/ced_/i', $name) && isset($module['release_version'])) {
                if($asString) {
                    $cedCommerceModules .= $name.':'.trim($module['release_version']).'~';
                } else {
                    $cedCommerceModules[$name] = trim($module['release_version']);
                }
            }
        }
        if($asString) { trim($cedCommerceModules, '~'); 
        }
        return $cedCommerceModules;
    }
    
    /**
     * Retrieve environment information of magento
     * And installed extensions provided by CedCommerce
     *
     * @return array
     */
    public function getEnvironmentInformation() 
    {
        $info = array();
        $info['domain_name'] = $this->_productMetadata->getBaseUrl();
        $info['magento_edition'] = 'default';
        if(method_exists('Mage', 'getEdition')) { $info['magento_edition'] = $this->_productMetadata->getEdition(); 
        }
        $info['magento_version'] = $this->_productMetadata->getVersion();
        $info['php_version'] = phpversion();
        $info['feed_types'] = $this->getStoreConfig(\Ced\SecurePay\Model\Feed::XML_FEED_TYPES);
        $info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);
        
        return $info;
    }
    
    /**
     * Retrieve admin interest in current feed type
     *
     * @param  SimpleXMLElement $item
     * @return boolean $isAllowed
     */
    public function isAllowedFeedType(SimpleXMLElement $item) 
    {
        $isAllowed = false;
        if(is_array($this->_allowedFeedType) && count($this->_allowedFeedType) >0) {
            $cedModules = $this->getCedCommerceExtensions();
            switch(trim((string)$item->update_type)) {
            case \Ced\SecurePay\Model\Source\Updates\Type::TYPE_NEW_RELEASE :
            case \Ced\SecurePay\Model\Source\Updates\Type::TYPE_INSTALLED_UPDATE :
                if (in_array(\Ced\SecurePay\Model\Source\Updates\Type::TYPE_INSTALLED_UPDATE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0 && isset($cedModules[trim($item->module)]) && version_compare($cedModules[trim($item->module)], trim($item->release_version), '<')===true) {
                    $isAllowed = true;
                    break;
                }
            case \Ced\SecurePay\Model\Source\Updates\Type::TYPE_UPDATE_RELEASE :
                if(in_array(\Ced\SecurePay\Model\Source\Updates\Type::TYPE_UPDATE_RELEASE, $this->_allowedFeedType) && strlen(trim($item->module)) > 0) {
                    $isAllowed = true;
                    break;
                }
                if(in_array(\Ced\SecurePay\Model\Source\Updates\Type::TYPE_NEW_RELEASE, $this->_allowedFeedType)) {
                    $isAllowed = true;
                }
                break;
            case \Ced\SecurePay\Model\Source\Updates\Type::TYPE_PROMO :
                if(in_array(\Ced\SecurePay\Model\Source\Updates\Type::TYPE_PROMO, $this->_allowedFeedType)) {
                    $isAllowed = true;
                }
                break;
            case \Ced\SecurePay\Model\Source\Updates\Type::TYPE_INFO :
                if(in_array(\Ced\SecurePay\Model\Source\Updates\Type::TYPE_INFO, $this->_allowedFeedType)) {
                    $isAllowed = true;
                }
                break;
            }
        }
        return $isAllowed;
    }
  
    
    
    /**
     * Function for setting Config value of current store
     *
     * @param string $path,
     * @param string $value,
     */
    public function setStoreConfig($path, $value, $storeId=null)
    {
        $store=$this->_storeManager->getStore($storeId);
        $data = [
                    'path' => $path,
                    'scope' =>  'stores',
                    'scope_id' => $storeId,
                    'scope_code' => $store->getCode(),
                    'value' => $value,
                ];
        $this->_configValueManager->addData($data);
        $this->_transaction->addObject($this->_configValueManager);
        $this->_transaction->save();
    }

    /**
     * Function for getting Config value of current store
     *
     * @param string $path,
     */
    public function getStoreConfig($path,$storeId=null)
    {
    
        $store=$this->_storeManager->getStore($storeId);
        return $this->_scopeConfigManager->getValue($path, 'store', $store->getCode());
    }
}