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
 * @package     Ced_CsDeal
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Controller\Adminhtml\Vdeals;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
 
class ChangeStatus extends \Magento\Backend\App\Action
{
   
    protected $resultPageFactory;
 
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
 
    public function execute()
    {
       $checkstatus=$this->getRequest()->getParam('status');
		$dealId=$this->getRequest()->getParam('deal_id');	
		if( $dealId > 0 && $checkstatus!='') {
			try{
				$errors=$this->_objectManager->get('Ced\CsDeal\Model\Deal')->changeVdealStatus($dealId,$checkstatus);
				
				if($errors['success'])
					$this->messageManager->addSuccessMessage(__("Status changed Successfully"));
				else if($errors['error'])
					$this->messageManager->addError(__("Can't process approval/disapproval for the Product.The Product's vendor is disapproved or not exist."));
			}
			catch(Exception $e){
				$this->messageManager->addError(__('%s',$e->getMessage()));
			}
		}		
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		return $resultRedirect->setPath('*/*/');
    }
}