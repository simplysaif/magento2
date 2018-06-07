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
namespace Magestore\Affiliateplus\Controller\Account;

/**
 * Class CheckReferredEmail
 * @package Magestore\Affiliateplus\Controller\Account
 */
class Checkkeyshop extends \Magestore\Affiliateplus\Controller\AbstractAction
{
    /**
     * Execute action
     */
    public function execute(){
        $keyshop = $this->getRequest()->getParam('key_shop');
        $this->xlog($keyshop);
        $isvalid = true;
        $html='';
        if ($isvalid) {
            $error = true;

            $affiliate = $this->getAffiliateAccountModel()->load($keyshop, 'key_shop');
            if ($affiliate && $affiliate->getId()) {
                $error = false;
            }
            if (!$error) {
                $html = "<div class='error-msg'>" . __('The key shop %1 belongs to a customer. Please try a different one', $keyshop) . "</div>";
                $html .= '<input type="hidden" id="is_valid_key_shop" value="0"/>';
            } else {
                $html = "<div class='success-msg'>" . __('You can use <b>%1</b> as key shop', $keyshop) . "</div>";
                $html .= '<input type="hidden" id="is_valid_key_shop" value="1"/>';
            }
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * get affiliate account model
     */
    protected function getAffiliateAccountModel(){
        return $this->_objectManager->create('Magestore\Affiliateplus\Model\Account');
    }
    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }
}
