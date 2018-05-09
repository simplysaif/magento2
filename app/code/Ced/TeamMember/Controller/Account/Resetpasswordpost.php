<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\TeamMember\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Ced\TeamMember\Model\AccountManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\InputException;

class Resetpasswordpost extends \Magento\Framework\App\Action\Action
{
    /** @var AccountManagementInterface */
    protected $accountManagement;

    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $accountManagement
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagement $accountManagement
        //CustomerRepositoryInterface $customerRepository
    ) {
        $this->session = $customerSession;
        $this->accountManagement = $accountManagement;
        //$this->customerRepository = $customerRepository;
        parent::__construct($context);
    }

    /**
     * Reset forgotten password
     *
     * Used to handle data received from reset forgotten password form
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
       // $resetPasswordToken = (string)$this->getRequest()->getQuery('token');
        $memberId = (int)$this->getRequest()->getParam('id');
        $password = (string)$this->getRequest()->getPost('password');
        $passwordConfirmation = (string)$this->getRequest()->getPost('password_confirmation');

        if ($password !== $passwordConfirmation) {
            $this->messageManager->addError(__("New Password and Confirm New Password values didn't match."));
            $resultRedirect->setPath('*/*/createPassword', ['id' => $memberId]);
            return $resultRedirect;
        }
        if (iconv_strlen($password) <= 0) {
            $this->messageManager->addError(__('Please enter a new password.'));
            $resultRedirect->setPath('*/*/createPassword', ['id' => $memberId]);
            return $resultRedirect;
        }

        try {
            $teammemberEmail = $this->_objectManager->create('Ced\TeamMember\Model\TeamMember')->load($memberId)->getEmail();
            $this->accountManagement->resetPassword($teammemberEmail,$password,$memberId);
            $this->messageManager->addSuccess(__('You updated your password.'));
            $resultRedirect->setPath('*/*/login');
            return $resultRedirect;
        } catch (\Exception $exception) {
            echo $exception->getMessage();die('-------');
            $this->messageManager->addError(__('Something went wrong while saving the new password.'));
        }
        $resultRedirect->setPath('*/*/createPassword', ['id' => $memberId]);
        return $resultRedirect;
    }
}
