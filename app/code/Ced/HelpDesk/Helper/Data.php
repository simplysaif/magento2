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
namespace Ced\HelpDesk\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/*
	*@var \Magento\Framework\ObjectManagerInterface
	*/
	protected $_scopeConfig;
	/*
	*@var \Magento\Framework\ObjectManagerInterface
	*/
	protected $_objectManager;
	/*
	*@param \Magento\Framework\App\Helper\Context
	*@param \Magento\Framework\ObjectManagerInterface
	*/
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager
	)
	{
		$this->_scopeConfig = $context->getScopeConfig();
		$this->_objectManager = $objectManager;
		parent::__construct($context);
		
	}
	
	public function getStoreConfig($value)
	{
		$ConfigValue = $this->_scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	    return $ConfigValue;
	}
	/*send mail to admin for ticket creation */
	public function mailAdmin($ticket_id,$customer_name)  
	{
		if (!$this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_admin')) {
            return ;
        }
   		$senderName = "Support System";
   		$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
   		$id = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->load($ticket_id,'ticket_id')->getId();
  		$key = $this->_objectManager->create('Magento\Backend\Helper\Data')->getUrl("helpdesk/tickets/manage/",array("id"=>$id));
		if(!empty($ticket_id) && !empty($customer_name)){
			$adminIds = $this->_objectManager
						 ->create('Magento\Authorization\Model\Role')
						 ->load('Administrators','role_name')
						 ->getRoleUsers();
			foreach ($adminIds as $id) {
				$userCollection = $this->_objectManager
									   ->create('Magento\User\Model\User')
									   ->load($id);
				$email = $userCollection->getEmail();
	   			$name = $userCollection->getUsername();
				if($email){
					$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			        try {
			            $error = false;
			            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			            $sender = [
			            'name' => $senderName,
			            'email' =>$senderEmail,
			            ];
			            $transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender')
			                ->setTemplateIdentifier('send_admin_email_template') // this code we have mentioned in the email_templates.xml
			                ->setTemplateOptions(
			                    [
			                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
			                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
			                    ]
			                )
			              
			                ->setTemplateVars(['myvar1' => $ticket_id ,'myvar2' => $key,'myvar3' => $customer_name,'myvar4' => $name])
			                
			                ->setFrom($sender)
			                ->addTo($email)
			                ->getTransport();
			      
			            $transport->sendMessage(); 
			            $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
			            return;
			        } catch (\Exception $e) {
			        	echo $e; 
			            return;
			        }
				}
			}
		}
	}
	
	/* New Ticket by customer, notification for customer */
	public function mailCustomer($ticket_id,$customer_name,$customer_email) 
	{
			if(!empty($customer_email) && !empty($customer_name) && !empty($ticket_id)){
				$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl('helpdesk/tickets/form',array('id'=>$ticket_id));
				$adminUserModel = $this->_objectManager->create('Magento\User\Model\User');
				$userCollection = $adminUserModel->load(1);
				$name = $userCollection->getUsername();				
				$senderName = "Support System";
				$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
				$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
				
				try {
					$error = false;
					$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
					$sender = [
					'name' => $senderName,
					'email' =>$senderEmail,
					];
				
					$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender')
					->setTemplateIdentifier('send_customer_email_template') // this code we have mentioned in the email_templates.xml
					->setTemplateOptions(
							[
							'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
							'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
							]
					)
					->setTemplateVars(['myvar1' => $ticket_id ,'myvar2' => $url,'myvar3' => $customer_name])
					
					->setFrom($sender)
					->addTo($customer_email)
					->getTransport();
				
					$transport->sendMessage();
					$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
					return;
				} catch (\Exception $e) {
				//	echo $e; die;
				//	return;
				}
		}
	}
	/* New Ticket by support, notification for customer */
	public function ticketByAdmin($ticket_id,$customer_name,$customer_email)
	{
		if(!empty($customer_email) && !empty($ticket_id) && !empty($customer_name)){
			$url=Mage::getUrl('helpdesk/helpdesk/form',array('id'=>$ticket_id));
			$template=Mage::helper('helpdesk')->newTicketStaff();
			$emailTemplate  = Mage::getModel('core/email_template')
								->loadDefault($template);
			$emailTemplateVariables = array();
			$emailTemplateVariables['myvar1'] = $ticket_id;
			$emailTemplateVariables['myvar2'] = $url;
			$emailTemplateVariables['myvar3'] = $customer_name;
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			$admin = Mage::getSingleton('admin/session')->getUser();
			$fromName= $admin->getUsername();
			$fromEmail = $admin->getEmail();
			$mail1 = Mage::getModel('core/email')
						->setToName($customer_name)
						->setToEmail($customer_email)
						->setBody($processedTemplate)
						->setSubject('Subject :Ticket Created')
						->setFromEmail($fromEmail)
						->setFromName($fromName)
						->setType('html');
			try{
				$mail1->send();
			}catch(Exception $e){}
		}
	}
	/* New Reply by customer, notification for support */
	public function messageStaff($ticket_id,$customer_name,$customer_email,$agent_id)
	{
		$getStaff=Mage::getModel('admin/user')->load($agent_id);
		$staffMail=$getStaff->getEmail();
		$staffName = $getStaff->getUsername();
		if(!empty($staffMail) && !empty($ticket_id) && !empty($customer_name)){
			$url=Mage::getUrl('adminhtml_helpdesk/edit',array('ticket_id'=>$ticket_id));
			$template=Mage::helper('helpdesk')->newReplyCust();
			$emailTemplate  = Mage::getModel('core/email_template')
			->loadDefault($template);
			$emailTemplateVariables = array();
			$emailTemplateVariables['myvar1'] = $ticket_id;
			$emailTemplateVariables['myvar2'] = $url;
			$emailTemplateVariables['myvar3'] = $customer_name;
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			$mail1 = Mage::getModel('core/email')
						->setToName($staffName)
						->setToEmail($staffMail)
						->setBody($processedTemplate)
						->setSubject('Subject :Reply From '.$customer_name)
						->setFromEmail($customer_email)
						->setFromName($customer_name)
						->setType('html');
			try{
				$mail1->send();
			}catch(Exception $e){}
		}
	}
	
	/* New Reply by support, notification for customer */
	public function messageCustomer($ticket_id,$customer_name,$customer_mail)
	{
		if(!empty($customer_mail) && !empty($customer_name) && !empty($ticket_id)){
			$url=Mage::getUrl('helpdesk/helpdesk/form',array('id'=>$ticket_id));
			$template=Mage::helper('helpdesk')->newReplySup();
			$emailTemplate  = Mage::getModel('core/email_template')
			->loadDefault($template);
			$emailTemplateVariables = array();
			$emailTemplateVariables['myvar1'] = $ticket_id;
			$emailTemplateVariables['myvar2'] = $url;
			$emailTemplateVariables['myvar3'] = $customer_name;
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			$admin = Mage::getSingleton('admin/session')->getUser();
			$fromName= $admin->getUsername();
			$fromEmail = $admin->getEmail();
			$mail1 = Mage::getModel('core/email')
								->setToName($customer_name)
								->setToEmail($customer_mail)
								->setBody($processedTemplate)
								->setSubject('Subject :Reply Of Your Ticket '.$ticket_id)
								->setFromEmail($fromEmail)
								->setFromName($fromName)
								->setType('html');
			try{
				$mail1->send();
			}catch(Exception $e){}
		}
	}
	
	
	
	/* Reply to guest customer */
    public function mailguestCustomer($ticket_id,$customer_name,$customer_email)
	{
			if(!empty($customer_email) && !empty($customer_name) && !empty($ticket_id)){
				$senderName = "Support System";
				$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
				$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
				
				try {
					$error = false;
					$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
					$sender = [
					'name' => $senderName,
					'email' =>$senderEmail,
					];
					$key = $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl("helpdesk/index/viewticket/email/".$customer_email."/id/".$ticket_id);
					$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender')
					->setTemplateIdentifier('send_guest_customer_email_template') // this code we have mentioned in the email_templates.xml
					->setTemplateOptions(
							[
							'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
							'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
							]
					)
					->setTemplateVars(['ticket_id' => $ticket_id ,'customer_name' => $customer_name,'url' => $key])
					->setFrom($sender)
					->addTo($customer_email)
					->getTransport();
				
					$transport->sendMessage();
					$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
					return;
				} catch (\Exception $e) {
					echo $e; 
					return;
				}
		}
	}
	
	/*Ticket re-assignation, notification for primary agent  */
	public function mailPrimaryAgent($ticket_id,$agent_id,$customer_name) 
	{
		$getStaff=Mage::getModel('admin/user')->load($agent_id);
		$staffMail=$getStaff->getEmail();
		$staffName=$getStaff->getUsername();
		if(!empty($staffMail) && !empty($ticket_id) && !empty($staffName)){
			$url=Mage::getUrl('adminhtml/helpdesk/edit',array('ticket_id'=>$ticket_id));
			$template=Mage::helper('helpdesk')->ticketAssign1();
			$emailTemplate  = Mage::getModel('core/email_template')
			->loadDefault($template);
			$emailTemplateVariables = array();
			$emailTemplateVariables['myvar1'] = $ticket_id;
			$emailTemplateVariables['myvar3'] = $staffName;
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			$admin = Mage::getSingleton('admin/session')->getUser();
			$fromName= $admin->getUsername();
			$fromEmail = $admin->getEmail();
			$mail1 = Mage::getModel('core/email')
							->setToName($staffName)
							->setToEmail($staffMail)
							->setBody($processedTemplate)
							->setSubject('Subject :'.$ticket_id.' has been assigned to someone else.')
							->setFromEmail($fromEmail)
							->setFromName($fromName)
							->setType('html');
			try{
				$mail1->send();
			}catch(Exception $e){}
		}
	}
	/* Ticket assignation, notification for new assignee */
	public function mailStaff($ticket_id,$staff_id,$customer_name) 
	{
		$getStaff=Mage::getModel('admin/user')->load($staff_id);
		$staffMail=$getStaff->getEmail();
		$staffName=$getStaff->getUsername();
			if(!empty($staffMail) && !empty($ticket_id) && !empty($customer_name)){
				$url=Mage::getUrl('adminhtml/helpdesk/edit',array('ticket_id'=>$ticket_id));
				$template=Mage::helper('helpdesk')->ticketAssign2();
				$emailTemplate  = Mage::getModel('core/email_template')
								->loadDefault($template);
				$emailTemplateVariables = array();
				$emailTemplateVariables['myvar1'] = $ticket_id;
				$emailTemplateVariables['myvar2'] = $url;
				$emailTemplateVariables['myvar3'] = $customer_name;
				$emailTemplateVariables['myvar4'] = $staffName;
				$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
				$admin = Mage::getSingleton('admin/session')->getUser();
				$fromName= $admin->getUsername();
				$fromEmail = $admin->getEmail();
				$mail1 = Mage::getModel('core/email')
							->setToName($staffName)
							->setToEmail($staffMail)
							->setBody($processedTemplate)
							->setSubject('Subject :'.$ticket_id.' has been assigned to you.')
							->setFromEmail($fromEmail)
							->setFromName($fromName)
							->setType('html');
			try{
				$mail1->send();
			}catch(Exception $e){}
		}
	}
	/* Mail Staff that about missed tickets  */
	public function delayReply($staffId,$tic_id)
	{
		$getStaff=Mage::getModel('admin/user')->load($staff_id);
		$staffMail = $getStaff->getEmail();
		$staffName = $getStaff->getUsername(); 
		if(!empty($staffMail) && !empty($tic_id)){
			$url=Mage::getUrl('adminhtml/helpdesk/edit',array('ticket_id'=>$tic_id));
			$template=Mage::helper('helpdesk')->notifyTemplate();
			$emailTemplate  = Mage::getModel('core/email_template')
			->loadDefault($template);
			$emailTemplateVariables = array();
			$emailTemplateVariables['myvar1'] = $tic_id;
			$emailTemplateVariables['myvar2'] = $url;
			$processedTemplate = $emailTemplate->getProcessedTemplate($emailTemplateVariables);
			$adminUserModel = Mage::getModel('admin/user');
			$userCollection = $adminUserModel->load(1);
			$mail = $userCollection->getEmail();
			$name =$userCollection->getUsername();
			$mail1 = Mage::getModel('core/email')
					->setToName($staffName)
					->setToEmail($staffMail)
					->setBody($processedTemplate)
					->setSubject('Subject :Missed Tickets')
					->setFromEmail($mail)
					->setFromName($name)
					->setType('html');
			try{
				$mail1->send();
			}catch(Exception $e){}
		}
	}
	/* Delete expire Ticket, Mail to Admin */
	public function expireTicketDelete($ticketIds,$adminMail,$adminName,$type)
	{
		if(!empty($adminMail) && !empty($ticketIds)){
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			$senderName = "Support System";
			$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
			
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('deleted_ticket_mail') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketIds' => $ticketIds,'admin' => $adminName,'type' =>$type ])
				->setFrom($sender)
				
				->addTo($senderEmail);
				
				$a = $transport->getTransport();
				$a->sendMessage();
				$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
				return;
			} catch (\Exception $e) {
				echo $e->getMessage();
				return;
			}
		}
	}
	/* Get Admin Id */
	public function adminId()
	{
		$getadmin = Mage::getModel('admin/user')->load(1);
		$adminId = $getadmin->getUserId();
		return $adminId;
	}
    /*send mail to support for reply*/
	public function mailSupportFromCustomer($ticket_id,$customer_name,$customer_email,$attach,$message)
	{
		if(!empty($customer_email) && !empty($customer_name) && !empty($ticket_id)){
			$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl('helpdesk/tickets/form',array('id'=>$ticket_id));
			$receiverName = "Support System";
			$receiverEmail = $this->getStoreConfig('helpdesk/general/support_email');
			$id = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->load($ticket_id,'ticket_id')->getId();
			$key= $this->_objectManager->create('Magento\Backend\Helper\Data')->getUrl("helpdesk/tickets/manage/",array("id"=>$id));
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $customer_name,
				'email' =>$customer_email,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_support_email_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketId' => $ticket_id,'support' => $receiverName ,'link' => $key,'customer' => $customer_name ,'message' =>$message ])
				->setFrom($sender)
				->addTo($receiverEmail);
				/*if (isset($attach) && !empty($attach) && is_array($attach)) {
					foreach ($attach as $key => $value) {
							$mimeType = $this->_objectManager
															 ->get('Ced\HelpDesk\Helper\Data')
															 ->getMimeType($attach[$key]['filepath'].$attach[$key]['filename']);
						if($mimeType == 'notFound'){
							$this->messageManager->addError(__('File not uploaded '));
						}else{
							$transport->addAttachment(file_get_contents($attach[$key]['filepath'].$attach[$key]['filename']),$mimeType,$attach[$key]['filename']);
						}
					}
				}*/
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
    /*Send mail to assignee agent form customer*/
	public function mailAgentFromCustomer($ticket_id,$agentName,$agentEmail,$attach,$message,$customer_name)
	{	
		if(!empty($agentEmail) && !empty($agentName) && !empty($ticket_id)){
			$senderName = "Support System";
			$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
			$id = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->load($ticket_id,'ticket_id')->getId();
			$key= $this->_objectManager->create('Magento\Backend\Helper\Data')->getUrl("helpdesk/tickets/manage/",array("id"=>$id));
	
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');

				$transport->setTemplateIdentifier('send_support_email_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketId' => $ticket_id,'support' => $agentName ,'link' => $key,'customer' => $customer_name ,'message' =>$message ])
				->setFrom($sender)
				->addTo($agentEmail);
				/*if (isset($attach) && !empty($attach) && is_array($attach)) {
					foreach ($attach as $key => $value) {
						$mimeType = $this->_objectManager
															 ->get('Ced\HelpDesk\Helper\Data')
															 ->getMimeType($attach[$key]['filepath'].$attach[$key]['filename']);
						if($mimeType == 'notFound'){
							$this->messageManager->addError(__('File not uploaded '));
						}else{
							$transport->addAttachment(file_get_contents($attach[$key]['filepath'].$attach[$key]['filename']),$mimeType,$attach[$key]['filename']);
						}	
					}
				}*/
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
    /*Send email to customer about status*/
	public function mailCustomerStatus($customer_name,$customer_email,$ticket_id,$status)
	{
		if(!empty($customer_email) && !empty($customer_name) && !empty($ticket_id)){
			$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl('helpdesk/tickets/form',['id'=>$ticket_id]);
			$senderName = "Support System";
			$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');

			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_customer_status_closed_resolved_email_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketId' => $ticket_id,'link' => $url,'customer_name' => $customer_name,'status' =>$status])
				->setFrom($sender)
				->addTo($customer_email);
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
	
	public function mailAgentStatus($agent_name,$agnentEmail,$customer_name,$ticket_id,$status,$comments)
	{
		if(!empty($agnentEmail) && !empty($agent_name) && !empty($ticket_id)){
			$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl('helpdesk/tickets/form',['id'=>$ticket_id]);
			$senderName = "Support System";
			$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');

			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_agent_status_closed_resolved_email_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketId' => $ticket_id,'agent_name' => $agent_name,'status' =>$status,'customer_name' => $customer_name,'message' => $comments])
				->setFrom($sender)
				->addTo($agnentEmail);
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
	/*Send email to agent to notify ticket*/
    public function notifyAgentMail($ticket_id,$agentName,$agentEmail)
    {
        if(!empty($agentEmail) && !empty($agentName) && !empty($ticket_id)){
            $senderName = "Support System";
            $senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
            $id = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->load($ticket_id,'ticket_id')->getId();
            $key= $this->_objectManager->create('Magento\Backend\Helper\Data')->getUrl("helpdesk/tickets/manage/",array("id"=>$id));
            $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
            try {
                $error = false;
                $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                $sender = [
                    'name' => $senderName,
                    'email' =>$senderEmail,
                ];
                $transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
                $transport->setTemplateIdentifier('notify_agent_mail') // this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                    ->setTemplateVars(['ticket_id' => $ticket_id,'agent_name' => $agentName ,'url' => $key ])
                    ->setFrom($sender)
                    ->addTo($agentEmail);
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
    /* Get Mime Type of uploaded file */
    public function getMimeType($file)
    {
    	$mimeType = [
    		'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            //ms office
            'doc' => 'application/msword',
			'dot' => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
			'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
			'xls' => 'application/vnd.ms-excel',
			'xlt' => 'application/vnd.ms-excel',
			'xla' => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',	
			'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb'=> 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'ppt'=> 'application/vnd.ms-powerpoint',
		    'pot'=> 'application/vnd.ms-powerpoint',
			'pps'=> 'application/vnd.ms-powerpoint',
			'ppa'=> 'application/vnd.ms-powerpoint',		
			'pptx'=> 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx' =>	'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
			'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
    		];
    	$arr = [];
    	$arr = explode('.', $file);
    	$length = sizeof($arr);
    	if (function_exists('mime_content_type') ){
    		return mime_content_type($file);
    	}else{
    		if(isset($mimeType[$arr[$length-1]])){
    			return $mimeType[$arr[$length-1]];
    		}else{
    			return 'notFound';
    		}
    	}
    }
	/* Current Date */
	public function currentDate()
	{
		return Mage::getModel('core/date')->date('Y-m-d H:i:s');
	}
	/*
     * Send Email to Head of Department
     * @param $agentName
     * @param $headEmail
     * @param $headName
     * @param $customer_name
     * @param $message
     * @param $status
     * @param $attach
     * @param $ticketId
     * */
	public function sendDepartmentHeadEmail($agentName,$headEmail,$headName,$customer_name,$message,$status,$attach,$ticketId,$signature)
	{
		if(!empty($headEmail) && !empty($headName)){		
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
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_department_head_email_reply_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
					);
				if ($status == 'Closed' || $status == 'Resolved') {
					$transport->setTemplateVars([
									   'agent_name' => $agentName,
									   'name' => $headName ,
									   'customer_name' => $customer_name,
									   'message' => strip_tags($message),
									   'status' => $status,
									   'ticketId' => $ticketId,
									   'signature' => $signature 
									   ]);
				}else{
					$transport->setTemplateVars([
											'name' => $headName ,
											'agent_name' => $agentName,
									   		'message' => strip_tags($message),
									   		'ticketId' => $ticketId,
									   		'signature' => $signature 
									   ]);
				}
				$transport->setFrom($sender);
				$transport->addTo($headEmail);
				if (isset($attach) && !empty($attach)) {
					$fileName = [];
					$fileName = explode('/', $attach['filename']);
					$mimeType = $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->getMimeType($attach['filepath']);
					if($mimeType == 'notFound'){
						$this->messageManager->addError(__('File not uploaded '));
					}else{
						$transport->addAttachment(file_get_contents($attach['filepath']),$mimeType,$fileName[1]);
					}
				}
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

	/* Mail to Department head when ticket has been created by customer and customer select department*/
	public function mailHeadTicketCreate($head_name,$head_email,$ticket_id,$customer_name) 
	{
		if(!empty($head_name) && !empty($head_email)){	
	   		$senderName = "Support System";
	   		$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
	   		$id = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->load($ticket_id,'ticket_id')->getId();
	  		$key= $this->_objectManager->create('Magento\Backend\Helper\Data')->getUrl("helpdesk/tickets/manage/",array("id"=>$id));
			if(!empty($ticket_id) && !empty($customer_name)){
				
			          $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			        try {
			            $error = false;
			            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
			            $sender = [
			            'name' => $senderName,
			            'email' =>$senderEmail,
			            ];
			            
			           
			            $transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender')
			                ->setTemplateIdentifier('send_admin_email_template') // this code we have mentioned in the email_templates.xml
			                ->setTemplateOptions(
			                    [
			                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
			                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
			                    ]
			                )
			              
			                ->setTemplateVars(['myvar1' => $ticket_id ,'myvar2' => $key,'myvar3' => $customer_name,'myvar4' => $head_name])
			                ->setFrom($sender)
			                ->addTo($head_email)
			                ->getTransport();
			      
			            $transport->sendMessage(); 
			            $this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
			            return;
			        } catch (\Exception $e) {
			        //	echo $e; 
			            //return;
			        }
			}
		}
	}

	public function mailAdminStatus($admin_name,$adminEmail,$customer_name,$ticket_id,$status,$comments)
	{
		if(!empty($adminEmail) && !empty($admin_name) && !empty($ticket_id)){
			$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl('helpdesk/tickets/form',['id'=>$ticket_id]);
			$senderName = "Support System";
			$senderEmail = $this->getStoreConfig('helpdesk/general/support_email');
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_agent_status_closed_resolved_email_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketId' => $ticket_id,'agent_name' => $admin_name,'status' =>$status,'customer_name' => $customer_name,'message' => $comments])
				->setFrom($sender)
				->addTo($adminEmail);
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

	/*
     * Send Email to customer From Support
     * @param $ticketId
     * @param $customer_name
     * @param $customer_email
     * */
	public function sendCustomerEmail($ticketId,$customer_name ,$customer_email)
	{
		if(!empty($customer_email) && !empty($customer_name)){
			$senderName = "Support System";
			$senderEmail = $this->_objectManager
								->create('Ced\HelpDesk\Helper\Data')
								->getStoreConfig('helpdesk/general/support_email');
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getBaseUrl();
				$url = $url.'helpdesk/tickets/form/id/'.$ticketId;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('\Magento\Framework\Mail\Template\TransportBuilder');
				$transport->setTemplateIdentifier('send_customer_email_ticket_create_by_admin_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
					)
				->setTemplateVars([
								   'ticket_id' => $ticketId,
								   'customer_name' => $customer_name,
								   'url' => $url
								   ])
				->setFrom($sender)
				->addTo($customer_email);
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
	/*send mail to Agent and Department head when admin create ticket for customer*/
	public function mailAgentCreateByAdmin($ticket_id,$customer_name,$agentEmail,$agent_name)
	{
		if(!empty($agentEmail) && !empty($agent_name) && !empty($ticket_id)){
			$url=  $this->_objectManager->create('\Magento\Framework\UrlInterface')->getUrl('helpdesk/tickets/form',array('id'=>$ticket_id));
			$senderName = "Support System";
			$senderEmail = $this->_objectManager
								->create('Ced\HelpDesk\Helper\Data')
								->getStoreConfig('helpdesk/general/support_email');
			$id = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->load($ticket_id,'ticket_id')->getId();
			$key= $this->_objectManager->create('Magento\Backend\Helper\Data')->getUrl("helpdesk/tickets/manage/",array("id"=>$id));
			$this->_objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->_objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_agent_email_ticket_create_by_admin_template') // this code we have mentioned in the email_templates.xml
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
				)
				->setTemplateVars(['ticketId' => $ticket_id,'agent_name' => $agent_name ,'link' => $key,'customer_name' => $customer_name])
				->setFrom($sender)
				->addTo($agentEmail);
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
