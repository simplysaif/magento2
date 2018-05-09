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

class Save extends \Magento\Backend\App\Action
{
    /*
     * @var ObjectManager
     * */
	public $objectManager;
    /*
     *@var ForwardFactory
     * */
	protected $resultForwordFactory;

	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwordFactory
			) {
				$this->resultForwordFactory = $resultForwordFactory;
				$this->objectManager = $context->getObjectManager();
				parent::__construct($context);
	}
	/*
	 * Save Ticket Information
	 * */
	public function execute()
	{
		$resultForward = $this->resultForwordFactory->create();
		$data = $this->getRequest()->getPostValue();
		$back = $this->getRequest()->getParam('back');
		$date = $this->objectManager
					 ->get('\Magento\Framework\Stdlib\DateTime\DateTime')
					 ->gmtDate();
		if (isset($data['agent'])) {
			$a = explode('-', $data['agent']);
			$data['agent'] = $a[0];
			$data['agent_name'] = $a[1];
		}
		$ticketModel = $this->objectManager
							->create('Ced\HelpDesk\Model\Ticket');
		if (!empty($data['id'])) {
			$ticketModel->load($data['id']);
			$ticketModel->setData($data);
			$ticketModel->save();
			$this->messageManager->addSuccess(
            __('Save Ticket Successfully...')
        	);
		}else{
			$customerId = $this->getCustomerId($data['customer_email']);
			if (!$customerId) {
				$customerId = 'guest';
			}
		}
		if (isset($customerId) && !empty($customerId)) {
            $ticketModel = $this->objectManager->create('Ced\HelpDesk\Model\Ticket');
            $ticketCount = $ticketModel->getCollection()->count();
            if ($ticketCount > 0){
                $ticketId = $ticketModel->getCollection()->getLastItem()->getTicketId();
                $ticketId = $ticketId+1;
            }else{
                $ticketId = 100000001;
            }
			$ticketModel->setTicketId($ticketId);
			$ticketModel->setCustomerId($customerId);
			$ticketModel->setCustomerName($data['customer_name']);
			$ticketModel->setCustomerEmail($data['customer_email']);
			$ticketModel->setSubject($data['subject']);
			$ticketModel->setOrder($data['order']);
			$ticketModel->setDepartment($data['department']);
			$ticketModel->setAgent($data['agent']);
			$ticketModel->setAgentName($data['agent_name']);
			$ticketModel->setStatus($data['status']);
			$ticketModel->setPriority($data['priority']);
			if (!empty($data['order'] && isset($data['order']))) {
				$ticketModel->setOrder($data['order']);
			}else{
				$ticketModel->setOrder('N/A');
			}
			$ticketModel->setStoreView('1');
			$ticketModel->setNumMsg('1');
			$ticketModel->setLock('0');
			$ticketModel->setCreatedTime($date);
			$ticketModel->setMessage(strip_tags($data['message']));
			$ticketModel->save();
			$messageModel = $this->_objectManager->create('Ced\HelpDesk\Model\Message');
            $messageModel->setMessage(strip_tags($data['message']));
            $username = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')
            				->getUser()
            				->getUsername();
            $messageModel->setFrom($username);
            $messageModel->setTo($ticketModel->getCustomerEmail());
            $messageModel->setTicketId($ticketModel->getTicketId());
            $messageModel->setCreated($date);
            $messageModel->setType('reply');
            $messageModel->save();
			$data['id'] = $ticketModel->getId();
			if ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_customer')) {
				$this->objectManager->create('Ced\HelpDesk\Helper\Data')->sendCustomerEmail($ticketModel->getTicketId(),$data['customer_name'],$data['customer_email']);
			}
			$departmentHeadId = $this->objectManager->create('Ced\HelpDesk\Model\Department')->load($data['department'],'code')->getDepartmentHead();
			$agentId = $data['agent'];
			if ($departmentHeadId == $agentId) {
				if (($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) || ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head'))){
					$agentModel = $this->objectManager->create('Ced\HelpDesk\Model\Agent')->load($agentId);
					$agentEmail = $agentModel->getEmail();
					$agent_name = $agentModel->getUsername();
					$a = $this->objectManager->create('Ced\HelpDesk\Helper\Data')->mailAgentCreateByAdmin($ticketId,$data['customer_name'],$agentEmail,$agent_name);
				}
			}else{
				if($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')){
					$agentModel = $this->objectManager->create('Ced\HelpDesk\Model\Agent')->load($agentId);
					$agentEmail = $agentModel->getEmail();
					$agent_name = $agentModel->getUsername();
					$b = $this->objectManager->create('Ced\HelpDesk\Helper\Data')->mailAgentCreateByAdmin($ticketId,$data['customer_name'],$agentEmail,$agent_name);
				}
				if ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head')) {
					$agentModel = $this->objectManager->create('Ced\HelpDesk\Model\Agent')->load($departmentHeadId);
					$headEmail = $agentModel->getEmail();
					$head_name = $agentModel->getUsername();
					$c = $this->objectManager->create('Ced\HelpDesk\Helper\Data')->mailAgentCreateByAdmin($ticketId,$data['customer_name'],$headEmail,$head_name);
				}
			}
			$this->messageManager->addSuccess(
            __('Save Ticket Successfully...')
        	);
		}else{
			$this->messageManager->addError(
            __('Customer with this email does not exist...')
        	);
        	return $this->_redirect('*/*/newticket');
		}
		(isset($back) && $back == 'edit' && isset($data['id'])) ? $this->_redirect('newticket/id/'.$data['id'])
			: $this->_redirect('helpdesk/tickets/ticketsinfo');
	}
    /*
     * Get Customer Id
     * @param $customerEmail
     * @return int
     * */
	public function getCustomerId($customerEmail)
	{
		$customerId = $this->objectManager
						   ->create('Magento\Customer\Model\Customer')
						   ->getCollection()
						   ->addFieldToFilter('email',$customerEmail)
						   ->getFirstItem()
						   ->getId();
		return $customerId;
	}
    
}