<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Popup;

use Magento\Customer\Model\AccountManagement;

class SendPass extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     *
     * @var \Magento\Framework\Json\Helper\Data
     */

    protected $_accountManagement;
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magestore\Sociallogin\Helper\Data $helperData,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magestore\Sociallogin\Model\ResourceModel\Customer\CollectionFactory $customerSocialCollectionFactory,
        \Magestore\Sociallogin\Model\ResourceModel\Vklogin\CollectionFactory $vkloginCollectionFactory,
        \Magestore\Sociallogin\Model\CustomerFactory $customerSocialFactory,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Framework\Json\Helper\Data $jsonhelperData,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_accountManagement = $accountManagement;
        parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
    }

    public function execute()
    {

        try {

            $this->_sendPass();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _sendPass()
    {
        $email = $this->getRequest()->getPost('socialogin_email_forgot', false);
        $model = $this->_customerFactory->create();
        $customer = $model->setWebsiteId($this->_storeManager->getStore()->getWebsiteId())
            ->loadByEmail($email);

        if ($customer->getId()) {
            try {
                $newPass = $this->_accountManagement->create();
                $newPass->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
                // $newPassword = $customer->generatePassword();
                // $customer->changePassword($newPassword, false);
                // $customer->sendPasswordReminderEmail();
                $result = ['success' => true];
            } catch (\Exception $e) {
                $result = ['success' => false, 'error' => $e->getMessage()];
            }
        } else {
            $result = ['success' => false, 'error' => 'Not found!'];
        }
        // $this->messageManager->addSuccess(__('We\'ll email you a link to reset your password.'));
        $jsonEncode = $this->_objectManager->create();
        $this->getResponse()->setBody($jsonEncode->jsonEncode($result));
    }

}