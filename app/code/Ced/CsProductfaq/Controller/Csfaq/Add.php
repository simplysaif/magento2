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
 * @category Ced
 * @package Ced_CsProductfaq
 * @author CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProductfaq\Controller\Csfaq;

class Add extends \Ced\CsMarketplace\Controller\Vendor
{

    public function execute()
    {
        /* if(!$this->_objectManager->get('Ced\CsCmsPage\Helper\Data')->isEnabled()){
            return ;
        } */
        if(!$this->_getSession()->getVendorId()) 
            return; 
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Add New Faq'));
        //return $resultForward->forward('edit');
        return $resultPage;
    }
}


