<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Popup;

class CreateAcc extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonhelperData;
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
        \Magento\Framework\Json\Helper\Data $jsonhelperData,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_jsonhelperData = $jsonhelperData;
        parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
    }
    public function execute()
    {

        try {

            $this->_create();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

    }

    public function _create()
    {

        if ($this->_getSession()->isLoggedIn()) {
            $result = ['success' => false, 'Can Not Login!'];
        } else {
            $firstName = $this->getRequest()->getPost('firstname', false);
            $lastName = $this->getRequest()->getPost('lastname', false);
            $pass = $this->getRequest()->getPost('pass', false);
            $passConfirm = $this->getRequest()->getPost('passConfirm', false);
            $email = $this->getRequest()->getPost('email', false);
            $model = $this->_customerFactory->create();
            $customer = $model->setFirstname($firstName)
                ->setLastname($lastName)
                ->setEmail($email)
                ->setPassword($pass)
                ->setConfirmation($passConfirm);

            try {
                $customer->save();

                $this->_eventManager->dispatch('customer_register_success',
                    ['customer' => $customer]
                );
                $result = ['success' => true];
                try {
                    $customer->sendNewAccountEmail(
                        'confirmation',
                        $this->_getSession()->getBeforeAuthUrl()
                    );
                } catch (\Exception $e) {
                    \Magento\Framework\App\ObjectManager::getInstance()->create(
                        'Psr\Log\LoggerInterface'
                    )->debug($e->getMessage());
                }
                $this->_getSession()->setCustomerAsLoggedIn($customer);

            } catch (\Exception $e) {
                $result = ['success' => false, 'error' => $e->getMessage()];
            }
        }
        $this->getResponse()->setBody($this->_jsonhelperData->jsonEncode($result));
    }

}