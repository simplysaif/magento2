<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Popup;

class Login extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonhelperData;
    /**
     *
     * @var \Magento\Customer\Api\AccountManagementInterface
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
        $this->_jsonhelperData = $jsonhelperData;
        $this->_accountManagement = $accountManagement;
        parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
    }

    public function execute()
    {

        try {

            $this->_login();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _login()
    {
        //$sessionId = session_id();
        $username = $this->getRequest()->getPost('socialogin_email', false);
        $password = $this->getRequest()->getPost('socialogin_password', false);

        $result = ['success' => false];

        if ($username && $password) {
            try {
                // $this->_getSession()->login($username, $password);
                //$login = $this->_accountManagement->create();
                $customer = $this->_accountManagement->authenticate(
                    $username,
                    $password
                );
                $this->_getSession()->setCustomerDataAsLoggedIn($customer);
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (!isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = __(
                'Please enter a username and password.');
        }
        //$jsonEncode = $this->_jsonhelperData->create();
        $this->getResponse()->setBody($this->_jsonhelperData->jsonEncode($result));
    }

}