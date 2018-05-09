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
/**
* 
*/
class Manage extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	public $registry;
	public $objectManager;
	/**
	 * DeptInfo action
	 *
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
	public function execute()
	{
		$id = $this->getRequest()->getParam('id');
		if(isset($id) && $id != null){
			$ticketModel = $this->objectManager
								->create('Ced\HelpDesk\Model\Ticket')
								->load($id);
			$ticketId = $ticketModel->getTicketId();
			$data =	$ticketModel->getData();
			$message = $this->objectManager
							->create('Ced\HelpDesk\Model\Message')
							->getCollection()
							->addFieldToFilter('ticket_id',$ticketId)
							->getData();
			$this->registry->register('ced_ticket_data',$data);
			$this->registry->register('ced_message',$message);
			$title = $data['customer_name'].'-'.$data['ticket_id'];
		}
		$resultRedirect = $this->resultPageFactory->create();
		$resultRedirect->getConfig()->getTitle()->set($title);
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		return $resultRedirect;
	}
}