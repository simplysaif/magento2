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
namespace Magestore\Affiliateplus\Helper;

/**
 * Helper Refer Friend
 */
class SubStore extends HelperAbstract
{
    /**
     * @return bool
     */
    public function disableSubStore() {

        if ($this->getConfig('affiliateplus/general/show_sub_store') && !$this->_objectManager->create('Magestore\Affiliateplus\Helper\Account')->isNotAvailableAccount() ) {
            return false;
        }
        return true;
    }
    public function isShowSubstore(){
        return $this->getConfig('affiliateplus/general/show_sub_store');
    }
    public function xlog($message = 'null')
    {
        $log = print_r($message, true);
        \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Psr\Log\LoggerInterface')
            ->debug($log);
    }
}
