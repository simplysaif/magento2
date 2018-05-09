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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\TeamMember\Controller\Admin;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Ced\TeamMember\Model\Session;

class Send extends \Magento\Framework\App\Action\Action
{
 /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
	protected $session;
	protected $_resultPageFactory;
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
			Session $teammemberSession
	) {
       parent::__construct($context);
		$this->_resultPageFactory  = $resultPageFactory;
		$this->session = $teammemberSession;
		$this->scopeConfig = $scopeConfig;
	}
    public function execute()
    {    
    if (!$this->session->getLoggedIn()) {
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('teammember/account/login');
    		return $resultRedirect;
    	}
    	

    	$model = $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage');
    	$model->setSender('teammember');
    	$model->setSenderEmail($this->session->getTeamMemberDataAsLoggedIn()->getEmail());
    	$model->setReceiver('admin');
    	$model->setReceiverEmail($this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    	$model->setMessage($this->getRequest()->getParam('message'));
    	$model->save();
      $this->messageManager->addSuccess(__('Message Sent Successfully'));
      return $this->_redirect('*/*/chat');
    }
}
