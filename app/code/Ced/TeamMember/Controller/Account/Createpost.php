<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\TeamMember\Controller\Account;

use Magento\Customer\Model\Registration;
use Ced\TeamMember\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Ced\TeamMember\Model\AccountManagement;
use Magento\Framework\Exception\InputException;

class Createpost extends \Magento\Framework\App\Action\Action
{
    /** @var Registration */
    protected $registration;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param Registration $registration
     */
    
    
    public function __construct(
    		Context $context,
    		Session $teammemberSession,
    		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
    		AccountManagement $accountManagement
    ) {
    	$this->session = $teammemberSession;
    	$this->_scopeConfig = $scopeInterface;
    	$this->accountManagement = $accountManagement;

    	parent::__construct($context);
    }

    
    public function execute()
    {
    	
    
    	if (!$this->getRequest()->isPost()) {
    		$url = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
    		$resultRedirect->setUrl($this->_redirect->error($url));
    		return $resultRedirect;
    	}
    		try{
    		$password = $this->getRequest()->getParam('password');
    		$confirmation = $this->getRequest()->getParam('password_confirmation');
    		
    
    		$this->checkPasswordConfirmation($password, $confirmation);
    
    		$teammember = $this->accountManagement
    		->createAccount($this->getRequest()->getPostValue());
    		}catch(\Exception $e){
    			$this->messageManager->addError($e->getMessage());
    			return $this->_redirect('*/*/create');
    			
    		}
    		
    		if($this->_scopeConfig->getValue('teammember_section/teammember/admin_approval',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)){
    			$this->messageManager->addSuccess(__('Thanky For Registering.You Acount Under Admin Approval.On Approval You will get Email Confirmation'));
    			return $this->_redirect('*/*/login');
    		}

    		$this->session->setTeamMemberDataAsLoggedIn($teammember);
    		$this->session->setLoggedIn(1);
    		$this->session->setTeamMemberId($teammember->getId());
    		
    		$resultRedirect = $this->resultRedirectFactory->create();
    		return $resultRedirect->setPath('*/member/index');
    		
    }
    
    protected function checkPasswordConfirmation($password, $confirmation)
    {
    	if ($password != $confirmation) {
    		throw new InputException(__('Please make sure your passwords match.'));
    	}
    }
}
