<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;

class Fblogin extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     * @var \Magento\Framework\App\PageCache\Version
     */
    protected $_version;
    /**
     * @var \Magestore\Sociallogin\Model\FbloginFactory
     */
    protected  $_fbloginFactory;
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
        \Magestore\Sociallogin\Model\FbloginFactory $fbloginFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\PageCache\Version $version
    )
    {
        $this->_fbloginFactory = $fbloginFactory;
        $this->_version = $version;
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
        $facebook = $this->_fbloginFactory->create()->newFacebook();
        $userId = $facebook->getUser();

        if ($isAuth && !$userId && $this->getRequest()->getParam('error_reason') == 'user_denied') {
            echo("<script>window.close()</script>");
        } elseif ($isAuth && !$userId) {
            $loginUrl = $facebook->getLoginUrl(array('scope' => 'email'));

            echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
            exit;
        }

        $user = $this->_fbloginFactory->create()->getFbUser();

        if ($isAuth && $user) {
            $store_id = $this->_storeManager->getStore()->getStoreId(); //add
            $website_id = $this->_storeManager->getStore()->getWebsiteId(); //add
            $data = array('firstname' => $user['first_name'], 'lastname' => $user['last_name'], 'email' => $user['email']);

            if ($data['email']) {

                $customer = $this->_helperData->getCustomerByEmail($data['email'], $website_id);

                if (!$customer || !$customer->getData('entity_id')) {

                    //Login multisite
                    $customer = $this->_helperData->createCustomerMultiWebsite($data, $website_id, $store_id);
                    if ($this->_helperData->getConfig('fblogin/is_send_password_to_customer')) {

                        $customer->sendPasswordReminderEmail();

                    }
                }

                //fix confirmation
                if ($customer->getData('confirmation') != NULL) {
                    try {
                        $customer->setConfirmation(NULL);
                        $customer->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
                $this->_getSession()->setCustomerAsLoggedIn($customer);
//                $this->_version->process();
                die("<script type=\"text/javascript\">document.cookie = 'private_content_version' + \"=\" + (Math.random()*10000000000000000) + \"; path=/\";if(navigator.userAgent.match('CriOS')){window.location.href=\"" . $this->_loginPostRedirect() . "\";}else{try{window.opener.location.href=\"" . $this->_loginPostRedirect() . "\";}catch(e){window.opener.location.reload(true);} window.close();}</script>");
            } else {
                $this->messageManager->addError('You provided a email invalid!');
                die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
            }
        }
    }

}