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
  * @category  Ced
  * @package   Ced_CsImportExport
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
 
namespace Ced\CsImportExport\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_helper;
    
    protected $_objectManager;
    
    protected $_transportBuilder;
    

    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->_objectManager = $objectManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        parent::__construct($context);
        $this->_helper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
    }

    protected function _getSession() 
    {
        return $this->_objectManager->create('Magento\Customer\Model\Session');
    }
    
    /**
     * Get vendor ID
     *
     * @return int
     */
    public function getVendorId() 
    {
        return $this->_getSession()->getVendorId();
    }
    
    /**
     * Get vendor
     *
     * @return Ced_CsMarketplace_Model_Vendor
     */
    public function getVendor() 
    {
        return $this->_getSession()->getVendor();
    }

    public function isAdmin()
    {
        $state = $this->_objectManager->get('Magento\Framework\App\State');
        $url = $this->_objectManager->create('Magento\Framework\UrlInterface')->getCurrentUrl();
        
        if($state->getAreaCode()== 'adminhtml') {
            return true;
        }
        if (strpos($url, 'admin') !== false) {
            return true;
        }
        return false;
    }
    
    
    public function isAllowedToImport()
    {
        $venod_ids = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorProductIds($this->getVendorId());
        $storeId = $this->_helper->getStore()->getId();
        $prod_limit = $this->_helper->getStoreConfig('ced_vproducts/general/limit', $storeId);
        if(count($venod_ids) < $prod_limit) {
            return true;
        }
        return false;
    }
    
    public function remainLimitToImport()
    {
        $venod_ids = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorProductIds($this->getVendorId());
        $storeId = $this->_helper->getStore()->getId();
        $prod_limit = $this->_helper->getStoreConfig('ced_vproducts/general/limit', $storeId);
        if(count($venod_ids) < $prod_limit) {
            return $prod_limit - count($venod_ids);
        }else{
            return false;
        }
    }
    
    
    /**
     * function sendNotificationMail
     *
     * for sending notification mail to admin for mass import
     *
     * @return Boolean
     */
    
    public function sendNotificationMail()
    {

        $data1=array();
        $vendor = $this->getVendor();
        $msg = 'This is notification mail that the vendor '.$vendor['name'];
        $msg .= ' had run the mass import process for the product. Please review it.';
        $data1['msg'] = $msg;
        $data1['vendor'] = $vendor['name'];
        $data1['sender_email'] = $vendor['name'];
        $data1['sender_name'] =  $vendor['email'];
        $mail_recevier =  $this->scopeConfig->getValue('ced_csmarketplace/csimportexport/allownotification', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);    
        $data1['receiver_name'] =  $this->scopeConfig->getValue('trans_email/ident_'.$mail_recevier.'/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $data1['receiver_email'] = $this->scopeConfig->getValue('trans_email/ident_'.$mail_recevier.'/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        try {
        	$storename = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_helper->getStore()->getId());
        	if(!$storename)
        		$storename = "Default Store";
        	
        	$adminname = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_helper->getStore()->getId());
        	if(!$adminname)
        		$adminname = "Admin User";
        	
        	$adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        	if(!$adminemail)
        		$adminemail = "owner@example.com";
    
        	$this->_template  ='ced_csimportexport_notify_admin';
        	$this->_inlineTranslation->suspend();
        	$this->_transportBuilder->setTemplateIdentifier($this->_template)
        	->setTemplateOptions(
        			[
        			'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
        			'store' => $this->_helper->getStore()->getId(),
        			]
        	)
        	->setTemplateVars($data1)
        	->setFrom([
        			'name' =>$vendor['name'],
        			'email' => $vendor['email'],
        			])
        			->addTo($adminemail, $adminname);
        	       
        	$transport = $this->_transportBuilder->getTransport();
        	$transport->sendMessage();
        
        	$this->_inlineTranslation->resume();
        } catch (\Exception $e) {
        	
        }
        
    }
    
   
}
