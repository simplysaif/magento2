<?php

/**
 * Magestore.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Affiliateplus
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Affiliateplus\Controller\Index;


class View extends \Magestore\Affiliateplus\Controller\AbstractAction
{


    public function execute()
    {

        if (!$this->_dataHelper->isAffiliateModuleEnabled()) {
            return $this->_redirect($this->getBaseUrl());
        }

        if ($this->isRegistered() && $this->accountNotLogin()) {
            if ($this->getAccountHelper()->getAccount()->getApproved() == 1) {
                $this->messageManager->addError(__('Your affiliate account is currently disabled. Please contact us to resolve this issue.'));
            }
            elseif (!$this->getCoreSession()->getData('has_been_signup')) {
                $this->messageManager->addNotice(__('Your affiliate registration is awaiting for approval. Please be patient.'));
            }
        }

        $resultPage = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_PAGE);

        $page = $this->_objectManager->create('Magento\Cms\Model\Page');

        if ($page->getId()) {
            $resultPage->getConfig()->getTitle()->set($page->getContentHeading());
        } else {

            $resultPage->getConfig()->getTitle()->set(__('Affiliate Page Shop'));
        }
        return $resultPage;
    }
    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }
}
