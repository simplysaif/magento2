<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Linkedlogin extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     * @var \Magestore\Sociallogin\Model\LinkedloginFactory
     */
    protected  $_linkedloginFactory;
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
        \Magestore\Sociallogin\Model\LinkedloginFactory $linkedloginFactory,
        \Magento\Framework\App\Action\Context $context)
    {
        $this->_linkedloginFactory = $linkedloginFactory;
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
            try {
                $token = $this->getAuthorization();
            } catch (\Exception $e) {
                $this->messageManager->addError('Htpp not request.Please input api key on config again');
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
            }
        } else {
            $token = $this->getAuthorizedToken();
        }
        return $token;
    }

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
        $scope = 'r_emailaddress';
        $olinked = $this->_linkedloginFactory->create();
        $olinked->setCallbackUrl($this->_storeManager->getStore()->getUrl('sociallogin/linkedlogin/user'));
        if (!is_null($this->getRequest()->getParam('oauth_token')) && !is_null($this->getRequest()->getParam('oauth_verifier'))) {
            $oauth_data = array(
                'oauth_token' => $this->_getRequest()->getParam('oauth_token'),
                'oauth_verifier' => $this->_getRequest()->getParam('oauth_verifier'),
            );
            $token = $olinked->getAccessToken($oauth_data, unserialize($this->_getSingtone()->getRequestToken()));
            $this->_getSingtone()->setAccessToken(serialize($token));
            $olinked->redirect();
        } else {
            $token = $olinked->getRequestToken(array('scope' => $scope));
            $this->_getSingtone()->setRequestToken(serialize($token));
            $olinked->redirect();
        }
        return $token;
    }

}