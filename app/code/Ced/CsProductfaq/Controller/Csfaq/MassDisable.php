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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ced\Productfaq\Model\ResourceModel\Productfaq\CollectionFactory;

/**
 * Class MassDelete
 */
class MassDisable extends \Ced\CsMarketplace\Controller\Vendor
{
   
  /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
      
      $id = $this->getRequest()->getParam('id');
      $ids = explode(',', $id);
        
      
      /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
      $resultRedirect = $this->resultRedirectFactory->create();
      if ($ids) 
      {
        try {
              foreach ($ids as $id)
              {
                $faqmodel = $this->_objectManager->create('Ced\Productfaq\Model\Productfaq');
                $faqmodel->load($id);
                $faqmodel->setIsActive(0);
                $faqmodel->save();
              }
              
                $this->messageManager->addSuccess(__('The faqs has been disabled.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
               
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
      
        $this->messageManager->addError(__('We can\'t find a faq to disable.'));
        return $resultRedirect->setPath('*/*/');
    }
}
