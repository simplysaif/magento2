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

class NewTicket extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
    /*
     * @var Registry
     * */
	public $registry;
    /*
     * @var ObjectManager
     * */
	public $objectManager;
	/**
	 * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\View\Result\PageFactory
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
			) {
				$this->resultPageFactory = $resultPageFactory;
				$this->registry = $registry;
				$this->objectManager = $context->getObjectManager();
				parent::__construct($context);
	}
	/*
	 * Crate Page
	 * */
	public function execute()
	{
		$role = $this->getUserData();
		if($role == 'Agent'){
			return $this->_redirect('helpdesk/tickets/ticketsinfo');
		}else{
			$id = $this->getRequest()->getParam('id');
			if(isset($id) && $id != null){
				$data = $this->objectManager->create('Ced\HelpDesk\Model\Ticket')
										 ->load($id)
										 ->getData();
				$this->registry->register('ced_ticket',$data);
			}
			/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
			return $this->resultPageFactory->create();
		}
		
	}
    /*
     * Get User Role
     * @return string
     * */
	public function getUserData()
    {
        $user_id = $this->objectManager
        				->create('Magento\Backend\Model\Auth\Session')
        				->getUser()
        				->getData('user_id');
        $data = $this->objectManager
        			 ->create('Ced\HelpDesk\Model\Agent')
                     ->getCollection()
                     ->addFieldToFilter('user_id',$user_id)
                     ->getFirstItem()
                     ->getRoleName();
        return $data;
    }
}