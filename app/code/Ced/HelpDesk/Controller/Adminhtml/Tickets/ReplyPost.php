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

class ReplyPost extends \Magento\Backend\App\Action
{
    /*
     * @var ObjectManager
     * */
	public $objectManager;
    /*
     * @var ForwardFactory
     * */
	protected $resultForwordFactory;
    /*
     * @var ScopeConfigInterface
     * */
	public $_scopeConfig;
    /*
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwordFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
			) {
				$this->_scopeConfig = $scopeConfig;
				$this->objectManager = $context->getObjectManager();
				$this->resultForwordFactory = $resultForwordFactory;
				parent::__construct($context);
	}
	/*
	 * Save Reply Post Data
	 * */
	public function execute()
	{
		$admin = [];
		$data = $this->getRequest()->getPostValue();
		$admin = $this->objectManager->create('Magento\Authorization\Model\Role')->load('Administrators','role_name')->getRoleUsers();
		$userModel = $this->objectManager
						  ->create('Magento\User\Model\User');
		$userId = $this->objectManager->create('Magento\Backend\Model\Auth\Session')->getUser()->getData('user_id');
		$agentModel = $this->objectManager
						   ->create('Ced\HelpDesk\Model\Agent');
		$roleCollection = $agentModel->getCollection()->addFieldToFilter('user_id',$userId)->getFirstItem();
		$role = $roleCollection->getRoleName();
		$agentId = $roleCollection->getId();
		if (isset($data['ticket_id']) && !empty($data['ticket_id'])) {
			$departmentCollection = $this->objectManager
							   ->create('Ced\HelpDesk\Model\Ticket')
							   ->getCollection()
							   ->addFieldToFilter('ticket_id',$data['ticket_id'])
							   ->getFirstItem();
			$departmentCode	= $departmentCollection->getDepartment();
			$numOfMsg = $departmentCollection->getNumMsg();
			$departmentCollection->setNumMsg($numOfMsg+1)->save();
			$deptCollection =  $this->objectManager
								   ->create('Ced\HelpDesk\Model\Department')
								   ->getCollection()
								   ->addFieldToFilter('code',$departmentCode)
								   ->getFirstItem();
			$departmentHeadId = $deptCollection->getDepartmentHead();

		}
		$back = $this->getRequest()->getParam('back');
		$messageModel = $this->objectManager->create('Ced\HelpDesk\Model\Message');
		$date = $this->objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
		$ticketModel = $this->objectManager->create('Ced\HelpDesk\Model\Ticket');
		if (isset($data['id']) && !empty($data['id'])) {
			$ticketModel->load($data['id'])->setStatus($data['status'])->save();
			$customer_email = $ticketModel->getCustomerEmail();
			$customer_name = $ticketModel->getCustomerName();
		}
		if (!empty($data['ticket_id'])) {
			$attach= [];
			try{
				$messageModel->setMessage(strip_tags($data['reply_description']));
				$messageModel->setFrom($data['from']);
				$messageModel->setTo($data['to']);
				$messageModel->setTicketId($data['ticket_id']);
				$messageModel->setCreated($date);
				$attach = $this->uploadFile($data['ticket_id'],$data['to']);
				if (isset($attach) && !empty($attach)) {
					$messageModel->setAttachment($attach['filename']);
				}
				$messageModel->setType('reply');
				$messageModel->save();
				if (isset($data['signature']) && $data['signature']) {
					$signature = $deptCollection->getDeptSignature();	 
				}else{
					$signature = null;
				}
				if ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_customer')) {
					$this->sendCustomerEmail($customer_email,
								$customer_name,
								$data['reply_description'],
								$data['status'],
								$attach,
								$data['ticket_id'],
								$signature);
				}
				if (isset($userId) && isset($role) && $role == 'Agent') {
					if($departmentHeadId == $agentId){
						if ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head') && $this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) {
							foreach ($admin as $adminId) {
								$adminData = $userModel->load($adminId);
								$adminEmail = $adminData->getEmail();
								$adminName = $adminData->getUsername();
								$this->objectManager->create('Ced\HelpDesk\Helper\Data')->sendDepartmentHeadEmail(
													$data['from'],
													$adminEmail,
													$adminName,
													$customer_name,
													$data['reply_description'],
													$data['status'],
													$attach,
													$data['ticket_id'],
													$signature
													);
							}
						}
					}else{
						if ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head')) {
							$headData = $agentModel->load($departmentHeadId);
							$headEmail = $headData->getEmail();
							$headName = $headData->getUsername();
							$this->objectManager->create('Ced\HelpDesk\Helper\Data')->sendDepartmentHeadEmail(
													$data['from'],
													$headEmail,
													$headName,
													$customer_name,
													$data['reply_description'],
													$data['status'],
													$attach,
													$data['ticket_id'],
													$signature
													);
						}
						if ($this->objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_admin')) {
							foreach ($admin as $adminId) {
								$adminData = $userModel->load($adminId);
								$adminEmail = $adminData->getEmail();
								$adminName = $adminData->getUsername();
								$this->objectManager->get('Ced\HelpDesk\Helper\Data')->sendDepartmentHeadEmail(
													$data['from'],
													$adminEmail,
													$adminName,
													$customer_name,
													$data['reply_description'],
													$data['status'],
													$attach,
													$data['ticket_id'],
													$signature
													);
							}
						}
					}
				}
			}catch(\Exception $e){
				echo $e->getMessage();
			}
		}
		if(!isset($back) && $back != 'edit' && isset($data['id'])){
			$this->messageManager->addSuccess(
		            __('Replied Successfully...')
		        );
			return $this->_redirect('*/*/ticketsinfo');
		}else{
			return $this->_redirect('*/*/manage/id/'.$data['id']);
		}
	}
    
    /*
     * Upload Files
     * */
	public function uploadFile($id,$email)
	{	 
		$extension = []; 
		try{
			$ext = $this->objectManager
						->create('Ced\HelpDesk\Helper\Data')
						->getStoreConfig('helpdesk/frontend/allow_extensions');
	    	$extension = explode(',',$ext);
            $date =$this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
            $customer_Id = $this->objectManager
	    						->create('Magento\Customer\Model\Customer')
	    						->getCollection()
	    						->addFieldToFilter('email',$email)
	    						->getFirstItem()
	    						->getId();
	    	if (isset($customer_Id) && !empty($customer_Id)) {
	    		$customer_Id = $customer_Id;
	    	}else{
	    		$customer_Id = 'guest';
	    	}
	    	$fileUploaderFactory = $this->objectManager->create('\Magento\MediaStorage\Model\File\UploaderFactory');

	    	$filesystem = $this->objectManager
	    						->create('\Magento\Framework\Filesystem');
	    	$uploader = $fileUploaderFactory->create(['fileId' => 'attachment']);
	    	$uploader->setAllowedExtensions($extension);
	    	$uploader->setAllowRenameFiles(false);
	    	$uploader->setFilesDispersion(false);
	    	$uploader->setAllowCreateFolders(true);
	    	$path = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
	    	$abs_path = $path->getAbsolutePath('images/helpdesk/'.$customer_Id.'/'.$id.'/'.$date.'/');
	    	$uploader->save($abs_path);
	    	$string = $uploader->getUploadedFileName();

	    	$filepath = $abs_path.$string;
	    	return ['filename' => $date.'/'.$string, 'filepath' => $filepath];
		 }catch(\Exception $e){
		 	return null;
		 }
	}
    /*
     * Send Email to customer
     * */
	public function sendCustomerEmail($customer_email,$customer_name,$message,$status,$attach , $ticketId,$signature)
	{	
		if(!empty($customer_email) && !empty($customer_name)){
			$senderName = "Support Sustem";
			$senderEmail = $this->objectManager
								->create('Ced\HelpDesk\Helper\Data')
								->getStoreConfig('helpdesk/general/support_email');
			$this->objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
			try {
				$error = false;
				$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
				$sender = [
				'name' => $senderName,
				'email' =>$senderEmail,
				];
				$transport = $this->objectManager->create('Ced\HelpDesk\Model\EmailSender');
				$transport->setTemplateIdentifier('send_customer_email_reply_template')
				->setTemplateOptions(
						[
						'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
						'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
						]
					);
				if ($status == 'Closed' || $status == 'Resolved') {
					$transport->setTemplateVars(['customer_name' => $customer_name ,
									   'message' => strip_tags($message),
									   'status' => $status,
									   'ticketId' => $ticketId,
									   'signature' => $signature 
									   ]);
				}else{
					$transport->setTemplateVars(['customer_name' => $customer_name ,
									   'message' => strip_tags($message),
									   'ticketId' => $ticketId,
									   'signature' => $signature
									   ]);
				}
				$transport->setFrom($sender)
						  ->addTo($customer_email);
				if (isset($attach) && !empty($attach)) {
					$fileName = [];
					$fileName = explode('/', $attach['filename']);
					$mimeType = $this->objectManager->get('Ced\HelpDesk\Helper\Data')->getMimeType($attach['filepath']);
					if($mimeType == 'notFound'){
						$this->messageManager->addError(__('File not uploaded '));
					}else{
						$transport->addAttachment(file_get_contents($attach['filepath']),$mimeType,$fileName[1]);
					}
				}
				$a = $transport->getTransport();
				$a->sendMessage();
				$this->objectManager->create('\Magento\Framework\Translate\Inline\StateInterface')->resume();
				return;
				} catch (\Exception $e) {
					echo $e; 
					return;
				}
		}
	}
}