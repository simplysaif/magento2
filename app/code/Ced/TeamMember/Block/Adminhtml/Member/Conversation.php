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
 * @package     Ced_CsMessaging
 * @author       CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\TeamMember\Block\Adminhtml\Member;
class Conversation extends \Magento\Backend\Block\Template
{
	
	public $formKey;
	public $_objectManager;
	public function __construct(
			\Magento\Framework\Data\Form\FormKey $formKey,
			\Magento\Backend\Block\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectInterface,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			array $data = []
	) 
	{   
		parent::__construct($context, $data);
		$this->formKey = $formKey;
		$this->scopeConfig = $scopeConfig;
		$this->_objectManager = $objectInterface;
	}
	public function getFormKey()
	{
		return $this->formKey->getFormKey();
	}
	
	public function getConversation(){
		
		return $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage')->getCollection();
		
	}

    public function getSenderToken()
	  {  
	    $token_s=$this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)."-".$this->getRequest()->getParam('memberemail');
	    return $token_s;
	  }

	public function getRecevierToken()
	  {  
	    $token_r =$this->getRequest()->getParam('memberemail')."-".$this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    return $token_r;
	  }
	
}