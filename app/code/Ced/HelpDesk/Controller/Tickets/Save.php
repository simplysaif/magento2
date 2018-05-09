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

class Save extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /*
     * @var \Magento\Store\Model\StoreManagerInterface
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

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
    	if(!$this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable')){
            $this->_redirect('cms/index/index');
    		return ;
    	}
    	if(!$this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) 
    	{
    		$this->messageManager->addError(__('Please Login First.'));
    		$this->_redirect('customer/account/login');
    		return;
    	}
	
    	if(!empty($this->getRequest()->getPostValue()))
    	{		
    		$dept= $this->getRequest()->getParam('dept');
    		$image_limit =  $this->getRequest()->getParam('image_count');
            $upload_image =  $this->getRequest()->getParam('upload_image');
            $unupload_image =  $this->getRequest()->getParam('unupload_image');
            $finalUploadedImage = array_diff(explode(',', $upload_image), explode(',', $unupload_image));
    		$subject=$this->getRequest()->getParam('subject');
    		$message=$this->getRequest()->getParam('message');
    		$message = strip_tags($message);
    		$attachment=$this->getRequest()->getParam('attachment');
    		$priority=$this->getRequest()->getParam('priority');
    		$order=$this->getRequest()->getParam('order');
    		
    		$priorityValue=$this->_objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/allow_priority');
    		$deptValue=$this->_objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/select_dept');
    		if(!empty($subject) && !empty($message)){
    			if($priorityValue){
    				if(!empty($priority)){
    					$priority_value=$priority;
    				}
    			}
    			else{
    				$priority_value='Normal';
    			}
    			if($deptValue){
    				if(!empty($dept)){
    					$dept_value=$dept;
    				}
    			}
    			else{
    				$defaultDept=$this->_objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/default_dept');
    				$dept_value = 'admin';
    			}
    			if(isset($order) && !empty($order)){
    				$order = $order;
    			}else{
    				$order = 'N/A';
    			}
    			$getadmin= $this->_objectManager->create('Magento\User\Model\User')->load(1);
    			$staffName = $getadmin->getUserName();
    			
    			$adminname = $getadmin->getUserId();
    			$staff= $adminname;
    			$customer = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer();
    			$customer_name=$customer->getName();
    			
    			$customer_Id=$customer->getId();
    			
    			$customer_email=$customer->getEmail();
    			$date = $date = $this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
    			$store_id = $this->_storeManager->getStore()->getId();
    			$ticket = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection();
    			$tic= $ticket->count();
    			
    			if($tic>0){
    				$ticketModel=$this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()->getLastItem()->getData();
    				$id=$ticketModel['ticket_id']+1;
    			}
    			else {
    				$id=10000001;
    			}
    			$ext= $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/allow_extensions');
    			$extension=explode(',',$ext);
    			$string = array();
    			
                foreach ($finalUploadedImage as $value) 
    			{ 
    				if(!empty($_FILES)){
    					try{
    						
    					    $fileIndex = "file".$value;
    					  
    						$fileUploaderFactory = $this->_objectManager->create('\Magento\MediaStorage\Model\File\UploaderFactory');
    						$filesystem = $this->_objectManager->create('\Magento\Framework\Filesystem');
    						$uploader = $fileUploaderFactory->create(['fileId' => $fileIndex]);
    						$uploader->setAllowedExtensions($extension);
    						$uploader->setAllowRenameFiles(false);
    						$uploader->setFilesDispersion(false);
    						$uploader->setAllowCreateFolders(true);
    						$path = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    						$abs_path = $path->getAbsolutePath('images/helpdesk/'.$customer_Id.'/'.$id.'/'.$date.'/');
                            $uploader->save($abs_path);
                            $uploadedFileName = $uploader->getUploadedFileName();
                            $string[] = $date.'/'.$uploadedFileName;
    						$mediaUrl = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    						
    				}
    				catch (\Exception $e){
    					$this->messageManager->addError($e->getMessage());
    					$this->_redirect('helpdesk/tickets/index');
    					return;
    				
    				}
    				}
    			
    			} 
    			$string = implode(',',$string);
    			
    			if(!isset($string))
    			{
    				$string = '';
    			}
    			$num_msg=1;
    			
    			
    				try {
    					
    					$ticketMessage= $this->_objectManager->create('Ced\HelpDesk\Model\Message');
    					$ticketMessage->setData('ticket_id',$id)
    					->setData('message',$message)
    					->setData('type','reply')
    					->setData('attachment',$string)
    					->setData('created',$date)
    					->setData('from',$customer_name)->save();
    					$ticketModel=$this->_objectManager->create('Ced\HelpDesk\Model\Ticket');
    					$ticketModel->setData('message',$message)
    					->setData('ticket_id',$id)
    					->setData('department',$dept_value)
    					->setData('subject',$subject)
    					->setData('status','New')
    					->setData('num_msg',$num_msg)
    					->setData('order',$order)
    					->setData('customer_name',$customer_name)
    					->setData('customer_email',$customer_email)
    					->setData('customer_id',$customer_Id)
    					->setData('agent',$staff)
    					->setData('agent_name',$staffName)
    					->setData('lock',0)
    					->setData('store_view',$store_id)
    					->setData('created_time',$date)
    					->setData('priority',$priority_value)->save();
    				}
    				catch (\Exception $e){
    					echo ($e->getMessage());
    				}	
                    if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_customer')) {
                        $mailCustomer = $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->mailCustomer($id,$customer_name,$customer_email);
                    }
                    if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head')) {
                        $headId = $this->_objectManager
                                       ->create('Ced\HelpDesk\Model\Department')
                                       ->load($dept_value,'code')
                                       ->getDepartmentHead();
                        $agentModel = $this->_objectManager
                                           ->create('Ced\HelpDesk\Model\Agent')
                                           ->load($headId);
                        $head_email = $agentModel->getEmail();
                        $head_name = $agentModel->getUsername();
                        $mailDepartmentHead = $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->mailHeadTicketCreate($head_name,$head_email,$id,$customer_name);
                    }
    				$message = __('Ticket Submitted');
    				$this->messageManager->addSuccess($message);
    				$this->_redirect('helpdesk/tickets/index');
    				return;
    		}
    		else{
    			$message = __('Please Fill All The Fields');
    			$this->messageManager->addError($message);
    			$this->_redirect('helpdesk/tickets/index');
    			return;
    		}
    	}
    }
}
