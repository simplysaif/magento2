<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Twlogin extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     * @var \Magestore\Sociallogin\Model\TwloginFactory
     */
    protected  $_twloginFactory;
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
        \Magestore\Sociallogin\Model\TwloginFactory $twloginFactory,
        \Magento\Framework\App\Action\Context $context)
    {
        $this->_twloginFactory = $twloginFactory;
        parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
    }
    /**
     *
     * @return void
     */
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
        $otwitter = $this->_twloginFactory->create();
        /* @var $otwitter Twitter_Model_Consumer */
        $otwitter->setCallbackUrl($this->_storeManager->getStore()->getUrl('sociallogin/twlogin/user', array('_secure' => true)));
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier'),
            );
            $token = $otwitter->getAccessToken($oauth_data, unserialize($this->_getSingtone()->getRequestToken()));
            $this->_getSingtone()->setAccessToken(serialize($token));
            $otwitter->redirect();
        } else {
            $token = $otwitter->getRequestToken();
            $this->_getSingtone()->setRequestToken(serialize($token));
            $otwitter->redirect();
        }
        return $token;
    }

}