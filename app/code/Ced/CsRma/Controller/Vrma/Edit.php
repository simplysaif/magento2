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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Controller\Vrma;

class Edit extends \Ced\CsMarketplace\Controller\Vendor
{
   
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        if(!$this->_getSession()->getVendorId()) { 
            return; 
        }

        $id = $this->getRequest()->getParam('rma_id');
        $vendorRma = $this->_objectManager->get('Ced\CsRma\Model\Request')->load($id);
        
        $resultPage->getConfig()->getTitle()->prepend(__('Return Request'));
        $resultPage->getConfig()->getTitle()->prepend(__('#'.$vendorRma->getRmaId()));
        return $resultPage;
    }
}
