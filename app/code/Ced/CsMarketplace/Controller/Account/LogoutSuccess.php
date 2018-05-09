<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Controller\Account;

class LogoutSuccess extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Logout success page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
      $resultPage = $this->resultPageFactory->create();
      $resultPage->getConfig()->getTitle()->set(__('Logged Out'));
       return $resultPage;
    }    
}
