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

class Edit extends \Ced\CsMarketplace\Controller\Vendor
{

    public function execute()
    {
        //die("lkkb");
        /* if(!$this->_objectManager->get('Ced\Productfaq\Model\Productfaq')->isEnabled()){
            return ;
        } */
        if(!$this->_getSession()->getVendorId()) 
            return; 
        
        
        if($this->getRequest()->getParam('id')>0){
            $id = $this->getRequest()->getParam('id');
            $FaqCollection = $this->_objectManager->create('Ced\Productfaq\Model\Productfaq')->load($id);
            if(sizeof($FaqCollection->getData())>0){
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('Edit Faq'));
        return $resultPage;
            }else{
                $this->_redirect('*/*/');
            }
        }else{
            $this->_redirect('*/*/');
        }
        
        
        
    
    }
}
