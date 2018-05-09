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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
Class Aclcheck implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_quoteFactory;
    protected $_advanceFactory;
    protected $_object;
    protected $session;
    protected $_request;
    protected $urlBuilder;
    protected $_scopeConfig;
    
    public function __construct(        
            Session $customerSession,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\App\Request\Http $request,
            UrlInterface $urlBuilder,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeinterface
    )
    {
        $this->_objectManager = $objectManager;
        $this->session=$customerSession;
        $this->_request=$request;
        $this->urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeinterface;
    }
    
     /**
     * Adds catalog categories to top menu
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $parameters=$this->_request->getParams();
        $moduleName = $this->_request->getModuleName();
        $controllerName = $this->_request->getControllerName();
        $actionName = $this->_request->getActionName();
        $urlredirect="csmarketplace/vendor/index";
        $this->_context = $this->_objectManager->get('Magento\Framework\App\Helper\Context');
        $aclresources = $this->_context->getScopeConfig()->getValue('vendor_acl');
        $marketplaceurls = array();
        if(empty($aclresources)){
            
            return $this;
        }
        foreach($aclresources['resources']['vendor']['children'] as $key => $value){
            if(isset($value['paths']))
            {
                $value['path']= $value['paths'];
            }
            if(isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'] ,\Magento\Store\Model\ScopeInterface::SCOPE_STORE) && !isset($value['dependsonparent']))
                continue;
            elseif(isset($value['ifconfig']) && !$this->_scopeConfig->getValue($value['ifconfig'] ,\Magento\Store\Model\ScopeInterface::SCOPE_STORE))
            {
                $value = $value['dependsonparent'][$key];
            }
            
            if(isset($value['children']) && is_array($value['children']) && !empty($value['children']))
            {
                foreach($value['children'] as $key2=>$value2)
                {
                    if(isset($value2['ifconfig']) && !$this->_scopeConfig->getValue($value2['ifconfig'] ,\Magento\Store\Model\ScopeInterface::SCOPE_STORE))
                        continue;
                    $marketplaceurls[] = $value2['path'];   
                }
                if(isset($value['path']))
                    $marketplaceurls[] = $value['path'];
                
            }
            else
            {
                $marketplaceurls[] = $value['path'];    
            }
        }
        $marketplaceurls = array_unique($marketplaceurls);
        if(($key = array_search("#", $marketplaceurls)) !== false) {
            unset($marketplaceurls[$key]);
        }
        $vendor=$this->_getSession()->getVendor();
        if(!$vendor)
        {
            return $this;
        }
        $group=$vendor['group'];
        $subVendor = $this->session->getSubVendorData();
        if(!empty($subVendor)){
            $sub_vendordata = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($subVendor['id'])->getRole();

            if($sub_vendordata=='all')
            {
                return $this;
            }
            if(strpos($sub_vendordata, $moduleName.'/'.$controllerName.'/'.$actionName) === true){
                return $this;
            }
            elseif(strpos($sub_vendordata, $moduleName.'/'.$controllerName.'/'.$actionName) != -1){
                return $this;
            }
            elseif(($moduleName.'/'.$controllerName.'/'.$actionName === 'csmarketplace/vendor/index') || ($moduleName.'/'.$controllerName.'/'.$actionName === 'csmarketplace/vendor/map') || ($moduleName.'/'.$controllerName.'/'.$actionName === 'csmarketplace/vendor/chart')){
                return $this;
            }
            else{
                $url = $this->urlBuilder->getUrl($urlredirect,array('_secure'=>true));
                $observer->getEvent()->getAction()->getResponse()->setRedirect($url);
                $observer->getEvent()->getCurrent()->_allowedResource = false;
                return $this;
            }
        }
        else
            return $this;
        
    }   
    protected function _getSession()
    {
        return $this->session;
    }
    
    protected function checkurl($string ,$arr)
    {
        
        foreach($arr as $_arr)
        {
        
            if (strpos($_arr, $string) !== false) {
                return true;
            }
      }
      
      return false;
        
    }
    
    protected function isallDashboardallow($urlpath)
    {
         if (strpos('csmarketplace/vendor/chart',$urlpath) !== false || strpos('csmarketplace/vendor/map',$urlpath)!==false) {
                return true;
            
         }
         return false;
    }

}
