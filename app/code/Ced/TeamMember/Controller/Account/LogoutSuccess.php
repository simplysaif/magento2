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
 
namespace Ced\TeamMember\Controller\Account;

use Magento\Framework\App\Action\Context;
use Ced\TeamMember\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class LogoutSuccess extends \Ced\TeamMember\Controller\TeamMember
{
    /**
     * Logout success page
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
		$this->session = $teammemberSession;
	}
    public function execute()
    {
    	/* if (!$this->session->getLoggedIn()) {
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('teammember/account/login');
    		return $resultRedirect;
    	} */
    	$this->session->unsTeamMemberDataAsLoggedIn();
    	$this->session->unsLoggedIn();
    	$this->session->unsTeamMemberId();
    	$this->session->destroy();
       $resultPage = $this->resultPageFactory->create();
       $resultPage->getConfig()->getTitle()->set(__('Logged Out'));
       return $resultPage;
    }    
}
