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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Controller\Adminhtml\Tickets;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class TicketsInfo extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	/**
	 * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\View\Result\PageFactory
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
			) {
				$this->resultPageFactory = $resultPageFactory;
				parent::__construct($context);
	}
	/*
	 * Create Page
	 * */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		$user = $this->_objectManager->create('Magento\Backend\Model\Auth\Session')->getUser()->getData('user_id');
		$agent = $this->_objectManager->create('Ced\HelpDesk\Model\Agent')->load($user,'user_id');
		if($agent->getRoleName() == "Agent")
		{
			$this->_redirect('*/*/agentticket');
		}
		return $this->resultPageFactory->create();
	}
}
