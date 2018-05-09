<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Livelogin extends \Magestore\Sociallogin\Controller\Sociallogin
{

    /**
     * @var \Magestore\Sociallogin\Model\LiveloginFactory
     */
    protected  $_liveloginFactory;
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
        \Magestore\Sociallogin\Model\LiveloginFactory $liveloginFactory,
        \Magento\Framework\App\Action\Context $context)
    {
        $this->_liveloginFactory = $liveloginFactory;
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
        $isAuth = $this->getRequest()->getParam('auth');
        $code = $this->getRequest()->getParam('code');
        $live = $this->_liveloginFactory->create()->newLive();
        try {
            $json = $live->authenticate($code);
            $user = $live->get("me", $live->param);
        } catch (\Exception $e) {
            $this->messageManager->addError('Login failed as you have not granted access.');
            die("<script type=\"text/javascript\">document.cookie = 'private_content_version' + \"=\" + (Math.random()*10000000000000000) + \"; path=/\";try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $email = $user->emails->account;
        //get website_id and sote_id of each stores
        $store_id = $this->_storeManager->getStore()->getStoreId(); //add
        $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

        if ($isAuth) {
            $data = array('firstname' => $first_name, 'lastname' => $last_name, 'email' => $email);
            $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id); //add edtition
            if (!$customer || !$customer->getId()) {
                //Login multisite
                $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                if ($this->_helperData->getConfig('livelogin/is_send_password_to_customer')) {
                    $customer->sendPasswordReminderEmail();
                }
            }
            if ($customer->getConfirmation()) {
                try {
                    $customer->setConfirmation(null);
                    $customer->save();
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
            $this->_getSession()->setCustomerAsLoggedIn($customer);
            die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_loginPostRedirect() . "\";}else{try{window.opener.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
        }
    }

}