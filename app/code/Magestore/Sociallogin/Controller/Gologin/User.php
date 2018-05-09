<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Gologin;

class User extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     * @var \Magestore\Sociallogin\Model\Gologin
     */
    protected $_gologinFactory;

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
        \Magestore\Sociallogin\Model\GologinFactory $gologinFactory,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_gologinFactory = $gologinFactory;
        parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
    }

    public function execute()
    {

        try {

            $this->_user();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _user()
    {

        $gologin = $this->_gologinFactory->create()->Gonew();

        $oauth2 = new \Google_Oauth2Service($gologin);
        $code = $this->getRequest()->getParam('code');
        if (!$code) {
            $this->messageManager->addError('Login failed as you have not granted access.');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
        $accessToken = $gologin->authenticate($code);
        $client = $oauth2->userinfo->get();

        $user = array();
        $email = $client['email'];
        $name = $client['name'];
        $arrName = explode(' ', $name, 2);
        $user['firstname'] = $arrName[0];
        $user['lastname'] = $arrName[1];
        $user['email'] = $email;

        //get website_id and sote_id of each stores
        $store_id = $this->_storeManager->getStore()->getStoreId(); //add
        $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

        $customer = $this->_helperData->getCustomerByEmail($user['email'], $website_id); //add edition
        if (!$customer || !$customer->getId()) {
            //Login multisite
            $customer = $this->_helperData->createCustomerMultiWebsite($user, $website_id, $store_id);
            if ($this->_helperData->getConfig('gologin/is_send_password_to_customer')) {
                $customer->sendPasswordReminderEmail();
            }
        }
        // fix confirmation
        if ($customer->getConfirmation()) {
            try {
                $customer->setConfirmation(null);
                $customer->save();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_getSession()->setCustomerAsLoggedIn($customer);
        die("<script type=\"text/javascript\">document.cookie = 'private_content_version' + \"=\" + (Math.random()*10000000000000000) + \"; path=/\";if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_loginPostRedirect() . "\";}else{try{window.opener.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
    }

}