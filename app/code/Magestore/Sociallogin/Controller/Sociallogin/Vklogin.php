<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Vklogin extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     * @var \Magestore\Sociallogin\Model\VkloginFactory
     */
    protected  $_vkloginFactory;
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
        \Magestore\Sociallogin\Model\VkloginFactory $vkloginFactory,
        \Magento\Framework\App\Action\Context $context)
    {
        $this->_vkloginFactory = $vkloginFactory;
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
        if (!$this->getAuthorizedToken()) {

            $token = $this->getAuthorization();

        } else {

            $token = $this->getAuthorizedToken();
        }

        return $token;
    }

    public function getVkModel()
    {
        return $this->_vkloginFactory->create();
    }

    // if exit access token
    public function getAuthorizedToken()
    {
        $token = false;
        if (!is_null($this->_getSingtone()->getAccessToken())) {
            $token = unserialize($this->_getSingtone()->getAccessToken());
        }
        return $token;
    }

    // if not exit access token
    public function getAuthorization()
    {

        $redirectUrl = $this->_storeManager->getStore()->getBaseUrl() . 'sociallogin/vklogin/user';
        // $scope = 130;

        $scope = 'offline,wall,friends,email';

        $callBackUrl = $this->_loginPostRedirect();

        $vklogin = $this->getVkModel()->getVk();
        $url = $vklogin->getAuthorizeUrl($scope, $redirectUrl);
        header('Location: ' . $url);
        die();
    }

}