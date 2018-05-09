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
 * @package     Ced_TeamMember
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 

namespace Ced\TeamMember\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Ced\TeamMember\Model\Session;

class Login extends \Ced\TeamMember\Controller\TeamMember
{
	protected $session;
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
			Session $teammemberSession
	) {
		parent::__construct($context, $teammemberSession, $resultPageFactory);
		$this->_resultPageFactory  = $resultPageFactory;
		$this->_scopeConfig = $scopeInterface;
		$this->session = $teammemberSession;
	}
    public function execute()
    {      
    	if ($this->session->getLoggedIn()) {
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('teammember/member/index');
    		return $resultRedirect;
    	}
    	
    	
    	
    	$resultPage = $this->_resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->prepend(__('TeamMember Login'));
    	$resultPage->getConfig()->getTitle()->prepend(__('TeamMember Login'));
    	return $resultPage;
    }
}
