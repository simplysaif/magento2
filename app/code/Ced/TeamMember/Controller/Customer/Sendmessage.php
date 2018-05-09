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
 
namespace Ced\TeamMember\Controller\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Ced\TeamMember\Model\Session;

class Sendmessage extends \Ced\TeamMember\Controller\TeamMember
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
	protected $session;
	
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory,
			Session $teammemberSession
	) {
		parent::__construct($context, $teammemberSession, $resultPageFactory);
		$this->_resultPageFactory  = $resultPageFactory;
		$this->session = $teammemberSession;
	}
    public function execute()
    {    
    if (!$this->session->getLoggedIn()) {
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('teammember/account/login');
    		return $resultRedirect;
    	}
    	
    	//echo $this->getRequest()->getParam('vendorid');

    	$model = $this->_objectManager->create('Ced\TeamMember\Model\TeamMessage');
    	$model->setSender('teammember');
    	$model->setSenderEmail($this->session->getTeamMemberDataAsLoggedIn()->getEmail());
    	$model->setReceiver('customer');
    	$model->setReceiverEmail($this->getRequest()->getParam('customeremail'));
    	$model->setMessage($this->getRequest()->getParam('message'));
    	$model->save();
      $this->messageManager->addSuccess(__('Message Sent Successfully'));
      return;
    }
}
