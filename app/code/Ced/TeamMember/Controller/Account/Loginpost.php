<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\TeamMember\Controller\Account;

use Magento\Framework\App\Action\Context;
use Ced\TeamMember\Model\Session;
use Ced\TeamMember\Model\AccountManagement;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Loginpost extends \Magento\Framework\App\Action\Action
{
    /** @var AccountManagementInterface */
    protected $AccountManagement;

    /** @var Validator */
    protected $formKeyValidator;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     * @param Validator $formKeyValidator
     * @param AccountRedirect $accountRedirect
     */
    public function __construct(
        Context $context,
        Session $teammemberSession,
       	AccountManagement $accountManagement,
        CustomerUrl $customerHelperData,
    		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        Validator $formKeyValidator
    ) {
        $this->session = $teammemberSession;
        $this->AccountManagement = $accountManagement;
        $this->customerUrl = $customerHelperData;
        $this->formKeyValidator = $formKeyValidator;
        $this->_scopeConfig = $scopeInterface;
        parent::__construct($context);
    }


    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
    	$resultRedirect = $this->resultRedirectFactory->create();
    	
    	if ($this->session->getLoggedIn()) {
    		/** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
    		$resultRedirect->setPath('teammember/member/index');
    		return $resultRedirect;
    	}
    	
       //print_r($this->getRequest()->getPostValue());die('dfdf');
        if ($this->getRequest()->isPost()) {
        	
            $login = $this->getRequest()->getPost('login');
            //print_r($login);die;
            if (!empty($login['username']) && !empty($login['password'])) {
            	
            	$model = $this->_objectManager->create('Ced\TeamMember\Model\TeamMember')->load($login['username'],'email');
            	if($model->getStatus() != \Ced\TeamMember\Model\TeamMember::TEAMMEMBER_APPROVED_STATUS) {
            		$this->messageManager->addError(__('Your Account Is Not Approved Yet'));
            		return $this->_redirect('*/*/login');
            	}
            	
                try {
                $teammember = $this->AccountManagement->authenticate($login['username'], $login['password']);
                $this->session->setTeamMemberDataAsLoggedIn($teammember);
                $this->session->setLoggedIn(1);
                $this->session->setTeamMemberId($teammember->getId());
                //echo $this->session->getLoggedIn();
                } catch (\Exception $e) {
                	
                    // PA DSS violation: throwing or logging an exception here can disclose customer password
                    $this->messageManager->addError(
                        __($e->getMessage())
                    		
                    );
                    return $this->_redirect('*/*/login');
                }
            } else {
                $this->messageManager->addError(__('A login and a password are required.'));
                return $this->_redirect('*/*/login');
            }
        }
//die('logged in');
        return $resultRedirect->setPath('*/member/index');
    }
}
