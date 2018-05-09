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
namespace Ced\HelpDesk\Cron;
 
class Cron
{
	/*
	 * @var \Magento\Framework\ObjectManagerInterface
	 * */
	public $_objectManager;
    /*
	 * @var \Magento\Framework\Filesystem\Driver\File
	 * */
    public $_file;
	/*
	 * @param \Magento\Framework\ObjectManagerInterface
	 * @param \Magento\Framework\Filesystem\Driver\File
	 * */
	public function __construct(\Magento\Framework\ObjectManagerInterface $ob,
                                \Magento\Framework\Filesystem\Driver\File $file)
	{
        $this->_file = $file;
		$this->_objectManager = $ob;
	}
    public function execute()
    {
       	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
					$logger = new \Zend\Log\Logger();
					$logger->addWriter($writer);
					$logger->info(print_r('shikha', true));
 
        return "hello";
    }
    /*
     * Delete Tickets
     * */
    public function deleteTicket()
    {
    	
    	$enable = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable');
		if(!$enable)
    	{
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
                $customerId = $tickets->getCustomerId();
    			$date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
			
					$date = date_create($date);
    			$difference = date_diff($date, $created_time);
    			$daysdiff = $difference->d;
    			if($daysdiff >= $timeDelete){
    
    			$deteledTickets[] = $ticket_id;
    			$tickets->delete();
                $this->unlinkUrl($ticket_id,$customerId);
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
				  $mail = $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->expireTicketDelete($message,$adminMail,$adminName);
    			   
    			}
    		  }
    	    }
         }
    /*
     *
     * Close Tickets
     * */
         public function closeTicket()
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
    /*
     * Notify Staff to reply at a particular time Interval
     * */
    public function notifyStaff()
    {
        $enable = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable');
        if(!$enable)
        {
            return;
        }
        $notify_staff = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/notify_staff');
        $notify_time = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/notify_time');
        if($notify_staff){
            if($notify_time){
                $ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection();
                $count = $ticketModel->count();
                if($count>0){
                    $agentNotifyData = [];
                    foreach($ticketModel as $tickets){
                        $created_time = $tickets->getCreatedTime();
                        $created_time = date_create($created_time);
                        $ticket_id = $tickets->getTicketId();
                        $date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
                        $a = $date;
                        $date = date_create($date);
                        $difference = date_diff($date, $created_time);
                        $dayDiff = $difference->d;
                        $hoursdiff = $difference->h;
                        if(($hoursdiff >= $notify_time || $dayDiff > 0) && ($tickets->status() != 'Closed' || $tickets->status() != 'Resolved')){
                            $agentEmail = $this->_objectManager->create('Ced\HelpDesk\Model\Agent')->load($tickets->getAgent())->getEmail();
                            $agentName = $tickets->getAgentName();
                            $agentNotifyData[] = ['ticket_id' =>$ticket_id,'agent_name'=>$agentName,'agent_email'=>$agentEmail];
                        }
                    }
                    if (!empty($agentNotifyData)){
                        $helperObject = $this->_objectManager->create('Ced\HelpDesk\Helper\Data');
                        foreach($agentNotifyData as $value){
                            $helperObject->notifyAgentMail($value['ticket_id'],$value['agent_name'],$value['agent_email']);
                        }

                    }

                }
            }
        }
    }
    /*
     * Unlink Url
     * */
    public function unlinkUrl($ticketId,$customerId){
        $path = $this->_objectManager
            ->create('Magento\Framework\Filesystem')
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $abs_path = $path->getAbsolutePath('images/helpdesk/'.$customerId.'/'.$ticketId.'/');
        $mesModel = $this->_objectManager->create('Ced\HelpDesk\Model\Message');
        $mesCollection = $mesModel->getCollection()->addFieldToFilter('ticket_id',$ticketId);
        foreach ($mesCollection->getItems() as $message){
            $attach = $message->getAttachment();
            $allAttach = explode(',',$attach);
            if(!empty($allAttach) && is_array($allAttach)){
                foreach ($allAttach as $value){
                    if ($this->_file->isExists($abs_path.$value))  {
                        $this->_file->deleteFile($abs_path.$value);
                    }
                }

            }
        }
    }
    
}