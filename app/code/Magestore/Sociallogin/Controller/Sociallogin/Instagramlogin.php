<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Sociallogin;
class Instagramlogin extends \Magestore\Sociallogin\Controller\Sociallogin {

	/**
	 * @var \Magestore\Sociallogin\Model\InstagramloginFactory
	 */
	protected  $_instagramloginFactory;
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
		\Magestore\Sociallogin\Model\InstagramloginFactory $instagramloginFactory,
		\Magento\Framework\App\Action\Context $context)
	{
		$this->_instagramloginFactory = $instagramloginFactory;
		parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
	}
	public function execute() {

		try {

			$this->_login();
		} catch (\Exception $e) {
			$this->messageManager->addError($e->getMessage());
		}

	}

	public function _login() {
		$code = $this->getRequest()->getParam('code');
		$instagram = $this->_instagramloginFactory->create()->newInstagram();
		if (!$code) {
			$loginUrl = $instagram->getLoginUrl();
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
		$data = $instagram->getOAuthToken($code);
		if ($code && !$data->user->username) {
			$loginUrl = $instagram->getLoginUrl();
			echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
			exit;
		}
		$token = $data->user;
		$instaframId = $token->id;
		$customerId = $this->getCustomerId($instaframId);

		if ($customerId) {
			$customer = $this->_customerFactory->create()->load($customerId);
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

		} else {
			// redirect to login page
			$name = (string) $token->username;
			$email = $name . '@instagram.com';
			$user['firstname'] = $name;
			$user['lastname'] = $name;
			$user['email'] = $email;
			//get website_id and sote_id of each stores

			$store_id = $this->_storeManager->getStore()->getStoreId();
			$website_id = $this->_storeManager->getStore()->getWebsiteId();
			$customer = $this->_helperData->getCustomerByEmail($user['email'], $website_id); //add edtition

			if (!$customer || !$customer->getId()) {
				//Login multisite
				$customer = $this->_helperData->createCustomerMultiWebsite($user, $website_id, $store_id);
			}
			if ($customer->getConfirmation()) {
				try {
					$customer->setConfirmation(null);
					$customer->save();
				} catch (\Exception $e) {
					$this->messageManager->addNotice($e->getMessage());
				}
			}

			$this->_getSession()->setCustomerAsLoggedIn($customer);
			$this->setAuthorCustomer($instaframId, $customer->getId());
			$this->_getSingtone()->setCustomerIdSocialLogin($instaframId);
			$nextUrl = $this->_helperData->getEditUrl();

			$this->messageManager->addNotice('Please enter your contact detail.');

			die("<script>window.close();window.opener.location = '$nextUrl';</script>");
		}
	}

	public function getCustomerId($instaframId) {
		$customer = $this->_customerSocialCollectionFactory->create();
		$user = $customer->addFieldToFilter('instagram_id', $instaframId)
			->getFirstItem();
		if ($user) {
			return $user->getData('customer_id');
		} else {
			return NULL;
		}

	}

	public function setAuthorCustomer($inId, $customerId) {
		$mod = $this->_customerSocialFactory->create();
		$mod->setData('instagram_id', $inId);
		$mod->setData('customer_id', $customerId);
		$mod->save();
		return;
	}

}