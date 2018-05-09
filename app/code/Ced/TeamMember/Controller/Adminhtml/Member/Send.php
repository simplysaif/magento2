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
 
namespace Ced\TeamMember\Controller\Adminhtml\Member;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Send extends \Magento\Backend\App\Action
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
protected $resultPageFactory;
        public function __construct(
           Context $context,
        	\Magento\Framework\App\Config\ScopeConfigInterface $_storeManager,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
        {
            parent::__construct($context);
            $this->resultPageFactory = $resultPageFactory;
            $this->_scopeConfig = $_storeManager;
        }
	
    public function execute()
    {  
    	$model = $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage');
    	$model->setSender('admin');
    	$model->setSenderEmail($this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    	$model->setReceiver('teammember');
    	$model->setReceiverEmail($this->getRequest()->getParam('memberemail'));
    	$model->setMessage($this->getRequest()->getParam('message'));
    	$model->save();
      $this->messageManager->addSuccess(__('Message Sent Successfully'));
    }
}
