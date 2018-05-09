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

class SaveAssign extends \Magento\Backend\App\Action
{
	/*
	 * Save Assign Data
	 * */
	public function execute()
	{
		$agentData = [];
		$data = $this->getRequest()->getPostValue();
		$ticketModel = $this->_objectManager
							->create('Ced\HelpDesk\Model\Ticket');
		$agentModel = $this->_objectManager
							->create('Ced\HelpDesk\Model\Agent');
		$messageModel = $this->_objectManager
							 ->create('Ced\HelpDesk\Model\Message');
		$date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
		if (isset($data['agent'])) {
			$agentData = explode('-',$data['agent']);
			$data['agent_id'] = $agentData[0];
			$data['agent_name'] = $agentData[1];
		}
		$roleModel = $this->_objectManager
							 ->create('Magento\User\Model\User')->getRoles();
		if (!empty($data['id'])) {
			$ticketModel->load($data['id']);
			$ticketModel->setAgent($data['agent_id']);
			$ticketModel->setAgentName($data['agent_name']);
			if (isset($data['priority']) && !empty($data['priority'])) {
				$ticketModel->setPriority($data['priority']);
			}
			$ticketModel->save();
		}
		if (!empty($data['ticket_id'])) {
			try{
				$messageModel->setMessage(strip_tags($data['reassign_description']));
				$messageModel->setFrom($data['from']);
				$messageModel->setTo($data['agent_name']);
				$messageModel->setTicketId($data['ticket_id']);
				$messageModel->setCreated($date);
				$messageModel->setType('re_assign');
				$messageModel->save();
				$agentEmail = $agentModel->load($data['agent_id'])
										 ->getEmail();
				if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) {
					$this->mailAgentAssign($agentEmail,$data['agent_name'],$data['ticket_id'],$data['from'],$data['reassign_description']);
				}
			}catch(\Exception $e){
				echo $e->getMessage();
			}
		}
		$this->messageManager->addSuccess(
            __('Ticket Assign Successfully...')
        );
        $this->_redirect('helpdesk/tickets/ticketsinfo');
	}
    /*
     * Send email to Assignee agent
     * @param $agent_email
     * @param $agent_name
     * @param $ticketId
     * @param $assigner
     * @param $description
     *
     * */
	public function mailAgentAssign($agent_email,$agent_name,$ticketId,$assigner,$description)
	{
		if(!empty($agent_email) && !empty($agent_name)){
			$senderName = "Support System";
			$senderEmail = $this->_objectManager
								->create('Ced\HelpDesk\Helper\Data')
								->getStoreConfig('helpdesk/general/support_email');
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder');
				$transport->setTemplateIdentifier('send_agent_email_assign_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						])
				->setTemplateVars(['agent_name' => $agent_name ,
								   'ticket_description' => strip_tags($description),
								   'assigner' => $assigner,
								   'ticket_id' => $ticketId 
								   ])
				->setFrom($sender)
				->addTo($agent_email);
				$a = $transport->getTransport();
				$a->sendMessage();
				$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
				return;
			} catch (\Exception $e) {
				echo $e; 
				return;
			}
			
		}
	}
}