<?php 
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsProduct
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace Ced\TeamMember\Helper;
use Ced\TeamMember\Model\Session; 

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_session;
	protected $_inlineTranslation;
	protected $_transportBuilder;
	protected $_storeManager;
	public function __construct(
					\Magento\Framework\App\Helper\Context $context,
					\Magento\Framework\ObjectManagerInterface $objectManager,
			        \Magento\Store\Model\StoreManagerInterface $storeManager,
			        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
			        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
					Session $teammemberSession
							)
    {
		$this->_objectManager = $objectManager;
		parent::__construct($context);
		$this->_session = $teammemberSession;
		$this->_storeManager = $storeManager;
		$this->_inlineTranslation = $inlineTranslation;
		$this->_transportBuilder = $transportBuilder;
    }
	
	
	public function getLabel()
	{
		if($this->_session->getLoggedIn())
			return 'TeamMember SignOut';
		else 
			return "TeamMemberLogin"; 
				
	}
	
	public function getLoginLogOutUrl()
	{
		$url = $this->_objectManager->create('\Magento\Framework\UrlInterface');
		if($this->_session->getLoggedIn()){
		return $url->getUrl('teammember/account/logout');
		}
		else{
			return $url->getUrl('teammember/account/login');
		}
	}
	
	public function sendEmail($memberid,$status){
		
		

		$modeldata = $this->_objectManager->create('Ced\TeamMember\Model\TeamMember')->load($memberid);
		
		$emailvariables['membername'] = $modeldata->getFirstName();
		$emailvariables['storename'] = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		//$emailvariables['status'] = $subcription->getPlanName();
		
          if($status =='approved'){
		$this->_template  ='ced_teammember_approval_email';
		}
		else{
			$this->_template  ='ced_teammember_disapproval_email';
		}
		$this->_inlineTranslation->suspend();

		$this->_transportBuilder->setTemplateIdentifier($this->_template)
		->setTemplateOptions(
				[
				'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
				'store' => $this->_storeManager->getStore()->getId(),
				]
		)
		->setTemplateVars($emailvariables)
		->setFrom([
				'name' => $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
				'email' => $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
				])
				//->addSubject('Account Status')
				->addTo($modeldata->getEmail(), $modeldata->getFirstName());  
		
		
		try {
			$transport = $this->_transportBuilder->getTransport();
			$transport->sendMessage();
			$this->_inlineTranslation->resume();
		} catch (\Exception $e) {
			echo $e->getMessage();die;
		}
		
	}
	
	public function sendResetPasswordEmail($teammember,$email){
		 
		$url = $this->_objectManager->create('\Magento\Framework\UrlInterface');
		 
		$emailvariables['membername'] = $teammember->getFirstName();
		$emailvariables['storename'] = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$emailvariables['url'] = $url->getUrl('teammember/account/createPassword',array('id'=>$teammember->getId()));
		$this->_template  ='ced_temmember_resetpassword_email';
	
		$storename = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		$storeemail = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	
		if(!$storename){
			$storename = 'Customer Support';
		}
	
		if(!$storeemail){
			$storeemail = 'support@example.com';
		}
	
		$this->_inlineTranslation->suspend();
		$this->_transportBuilder->setTemplateIdentifier($this->_template)
		->setTemplateOptions(
				[
				'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
				'store' => $this->_storeManager->getStore()->getId(),
				]
		)
		->setTemplateVars($emailvariables)
		->setFrom([
				'name' => $storename,
				'email' => $storeemail,
				])
				// ->addSubject('Password Reset Request')
				->addTo($email, $teammember->getFirstName());
		 
		try {
			$transport = $this->_transportBuilder->getTransport();
			$transport->sendMessage();
			$this->_inlineTranslation->resume();
		} catch (\Exception $e) {
			echo $e->getMessage();die;
		}
		 
		 
	}
	
	
}
