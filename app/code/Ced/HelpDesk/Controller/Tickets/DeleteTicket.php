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
namespace Ced\HelpDesk\Controller\Tickets;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class DeleteTicket extends \Magento\Framework\App\Action\Action
{
    /*
     * @var PageFactory
     * */
	protected $resultPageFactory;
    /*
     * @var StoreManagerInterface
     * */
	protected $_storeManager;
	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
			Context $context,
			\Magento\Store\Model\StoreManagerInterface $storeManager,
			PageFactory $resultPageFactory
	) {
		$this->_storeManager = $storeManager;
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct($context);
	}
	
	public function executefff()
	{
		$enable = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable');
		if(!$enable)
		{
			$this->_redirect('cms/index/index');
			return;
		}
		$detete_ticket = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/delete_ticket');
		$timeDelete = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/auto_delete');
		if($detete_ticket){
			if($timeDelete){
				$ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection();
				$count = $ticketModel->count();
				if($count>0){
					$deteledTickets = [];
					foreach($ticketModel as $tickets){
					$created_time = $tickets->getCreatedTime();
					
					$created_time = date_create($created_time);
					$ticket_id = $tickets->getTicketId();
					$date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
					
					$date = date_create($date);
					$difference = date_diff($date, $created_time);
					$daysdiff = $difference->d;
					$timeDelete = 2;
					if($daysdiff >=$timeDelete){
						$deteledTickets[] = $ticket_id;
						$msgModel = $this->_objectManager->create('Ced\HelpDesk\Model\Message')->getCollection()->addFieldToFilter('ticket_id',$ticket_id);
						foreach($msgModel as $msg)
						{
							echo $msg->getId();echo "<br>";
							$msg->delete();
						}
					
					}
				  }
				  $message = '';
				  for($i=0;$i<count($deteledTickets); $i++)
				  {
				  	$message .= $deteledTickets[$i].',';
				  }
				  
				  $message =substr($message, 0, -1); 
				  $admin_data = $this->_objectManager->create('Magento\User\Model\User')->load(1);
				  $adminMail = $admin_data->getEmail();
				  $adminName = $admin_data->getUsername();
				  $type = 'deleted';
				  $mail = $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->expireTicketDelete($message,$adminMail,$adminName,$type);
				}
			}			
		}
	
	}

	public function execute()
	{
		
		$enable = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable');
		if(!$enable)
		{
			return;
		}
		$close_ticket = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/close_ticket');
		$timeClose = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/auto_close');
		if($timeClose){
			$ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection();
			$count = $ticketModel->count();
			if($count>0){
				$closedTickets = [];
				foreach($ticketModel as $tickets){
					$created_time = $tickets->getCreatedTime();
					$created_time = date_create($created_time);
					$ticket_id = $tickets->getTicketId();
					$date = $date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
					$date = date_create($date);
					$difference = date_diff($date, $created_time);
					$daysdiff = $difference->d;
					$timeClose = 2;
					if($daysdiff>=$timeClose){
						$closedTickets[] = $tickets->getTicketId();
						$tickets->setData('status','Closed')->save();
					}
				}
				
				$message = '';
				for($i=0;$i<count($closedTickets); $i++)
				{
				$message .= $closedTickets[$i].',';
				}
				
				$message =substr($message, 0, -1);
				$admin_data = $this->_objectManager->create('Magento\User\Model\User')->load(1);
				$adminMail = $admin_data->getEmail();
				$adminName = $admin_data->getUsername();
				$type = 'closed';
				$mail = $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->expireTicketDelete($message,$adminMail,$adminName,$type);
			}
		}
	}

}