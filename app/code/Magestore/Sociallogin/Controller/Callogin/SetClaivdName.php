<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Controller\Callogin;
class SetClaivdName extends \Magestore\Sociallogin\Controller\Sociallogin
{
    /**
     * @var \Magestore\Sociallogin\Model\CalloginFactory
     */
    protected $_calloginFactory;

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
        \Magestore\Sociallogin\Model\CalloginFactory $calloginFactory,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_calloginFactory = $calloginFactory;
        parent::__construct($customerSession, $storeManager, $scopeConfig, $helperData, $session, $customerFactory, $customerSocialCollectionFactory, $vkloginCollectionFactory, $customerSocialFactory, $context);
    }
    public function execute()
    {

        try {
            $this->_setClaivdName();

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

    public function _setClaivdName()
    {
        $data = $this->getRequest()->getPost();

        if ($data) {
            $name = $data['name'];
            $url = $this->getCalModel()->getCalLoginUrl($name);
            $this->getResponse()->setRedirect($url);
        } else {
            $this->_getSingtone()->addError('Please enter Blog name!');
            die("<script type=\"text/javascript\">try{window.opener.location.reload(true);}catch(e){window.opener.location.href=\"" . $this->_storeManager->getStore()->getBaseUrl() . "\"} window.close();</script>");
        }

    }

    public function getCalModel()
    {
        return $this->_calloginFactory->create();
    }

}