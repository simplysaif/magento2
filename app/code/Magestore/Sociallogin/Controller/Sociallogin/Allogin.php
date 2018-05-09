<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Allogin extends \Magestore\Sociallogin\Controller\Sociallogin
{

    /**
     * @var \Magestore\Sociallogin\Model\AlloginFactory
     */
    protected $_alloginFactory;
    
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
       \Magestore\Sociallogin\Model\AlloginFactory $alloginFactory,
       \Magento\Framework\App\Action\Context $context
   )
   {
       $this->_alloginFactory = $alloginFactory;
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

    /**
     * getToken and call profile user aol
     **/
    public function _login()
    {
        $aol = $this->_alloginFactory->create()->newAol();
        $userId = $aol->mode;

        if (!$userId) {
            $aol_session = $this->_alloginFactory->create()->setAolIdlogin($aol);
            $url = $aol_session->authUrl();
            echo "<script type='text/javascript'>top.location.href = '$url';</script>";
            exit;
        } else {

            if (!$aol->validate()) {
                $aol_session = $this->_alloginFactory->create()->setAolIdlogin($aol, $name = '');

                $url = $aol_session->authUrl();

                echo "<script type='text/javascript'>top.location.href = '$url';</script>";
                exit;
            } else {
                $user_info = $aol->getAttributes();
                if (count($user_info)) {
                    $frist_name = $user_info['namePerson/friendly'];
                    $last_name = $user_info['namePerson/friendly'];
                    $email = $user_info['contact/email'];
                    if (!$frist_name) {
                        if ($user_info['namePerson/friendly']) {
                            $frist_name = $user_info['namePerson/friendly'];
                        } else {
                            $email = explode("@", $email);
                            $frist_name = $email['0'];
                        }
                    }

                    if (!$last_name) {
                        $last_name = '_aol';
                    }

                    //get website_id and sote_id of each stores
                    $store_id = $this->_storeManager->getStore()->getStoreId(); //add
                    $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add

                    $data = array('firstname' => $frist_name, 'lastname' => $last_name, 'email' => $user_info['contact/email']);
                    $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id);
                    if (!$customer || !$customer->getId()) {
                        //login multisite
                        $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                        if ($this->_helperData->getConfig('aollogin/is_send_password_to_customer')) {
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
                    die("<script type=\"text/javascript\">if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\";}else{try{window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
                } else {
                    $this->messageManager->addError('Login failed as you have not granted access.');
                    die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
                }
            }
        }
    }

}