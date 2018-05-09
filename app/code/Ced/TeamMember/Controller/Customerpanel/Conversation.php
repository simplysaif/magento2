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
 
namespace Ced\TeamMember\Controller\Customerpanel;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;

class Conversation extends \Magento\Framework\App\Action\Action
{
    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */
	protected $session;
	protected $_resultPageFactory;
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory,
			Session $customerSession
	) {
       parent::__construct($context);
		$this->_resultPageFactory  = $resultPageFactory;
		$this->session = $customerSession;
	}
    public function execute()
    {    
    	if (!$this->session->isLoggedIn()) {
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('customer/account/login');
    		return $resultRedirect;
    	}
    	
    	 $resultPage = $this->_resultPageFactory->create();   
        $resultPage->getConfig()->getTitle()->set(__('Chat Section'));
        return $resultPage;

    }
}
