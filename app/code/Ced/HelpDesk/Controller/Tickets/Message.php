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

class Message extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /*
     * @var  \Magento\Store\Model\StoreManagerInterface
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
        $attach = [];
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
    	$ticket_id = $this->getRequest()->getParam('id');
        $ticketModel = $this->_objectManager
                            ->create('Ced\HelpDesk\Model\Ticket')
                            ->getCollection()
                            ->addFieldToFilter('ticket_id',$ticket_id)
                            ->getFirstItem();
        $departmentCode = $ticketModel->getDepartment();
        $agentId = $ticketModel->getAgent();
        $departmentModel = $this->_objectManager
                                ->create('Ced\HelpDesk\Model\Department')
                                ->getCollection()
                                ->addFieldToFilter('code',$departmentCode)
                                ->getFirstItem();
        $departmentHeadId = $departmentModel->getDepartmentHead();
        $agentModel = $this->_objectManager
                           ->create('Ced\HelpDesk\Model\Agent');
        $agentRole = $agentModel->load($agentId)->getRoleName();
    	$image_limit =  $this->getRequest()->getParam('image_count');
        $upload_image =  $this->getRequest()->getParam('upload_image');
        $unupload_image =  $this->getRequest()->getParam('unupload_image');
        $finalUploadedImage = array_diff(explode(',', $upload_image), explode(',', $unupload_image));
    	$message=$this->getRequest()->getParam('message');
    	$message = strip_tags($message);
    	$date =$this->_objectManager->get('\Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate();
    	$customer = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomer();
    	$customer_name=$customer->getName();
    	$customer_Id=$customer->getId();
    	$customer_email = $customer->getEmail();
    	
    	if($this->getRequest()->isPost()){
    		if(!empty($message) && !empty($ticket_id)){
    			
    			$ext =  $this->_objectManager->create('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/frontend/allow_extensions');
    			$extension = explode(',',$ext);
    			$string = '';
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
    						$abs_path = $path->getAbsolutePath('images/helpdesk/'.$customer_Id.'/'.$ticket_id.'/'.$date.'/');
    						$uploader->save($abs_path);
                            $uploadedFileName = $uploader->getUploadedFileName();
    						$string[] = $date.'/'.$uploadedFileName;
    						$attach[] = ['filename' =>$uploadedFileName,
                                        'filepath' =>$abs_path];
    						$mediaUrl = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    					}
    				catch (\Exception $e){
    					$this->messageManager->addError($e->getMessage());
    						
    					$this->_redirect('helpdesk/tickets/index');
    					return;
    				}
    				}
    			
    			} 
    			
    			if(!isset($string))
    			{
    				$string = '';
    			}
    			else{
    				if(!empty($string))
    				{
    					$string = implode(',',$string);
    				}
    				
    			}
    			$ticketMessage = $this->_objectManager->get('Ced\HelpDesk\Model\Message');
    			$ticketMessage->setData('ticket_id',$ticket_id)
    			->setData('message',$message)
			    ->setData('type','reply')
    			->setData('attachment',$string)
    			->setData('created',$date)
    			->setData('from',$customer_name)
                ->save();
    			
    			$ticketModel = $this->_objectManager->get('Ced\HelpDesk\Model\Ticket')->getCollection()
    			->addFieldToFilter('ticket_id',$ticket_id)->getData();
    			if(isset($ticketModel) && is_array($ticketModel)){
    				foreach($ticketModel as $value){
    					$count = $value['num_msg']+1;
    					$ticketModel = $this->_objectManager->get('Ced\HelpDesk\Model\Ticket')->getCollection()
    					->addFieldToFilter('ticket_id',$ticket_id)->getFirstItem();
    					$ticketModel->setData('num_msg',$count)
    					->setData('status','Open')->save();
    				}
    				$agent_id=$value['agent'];
    				if(!isset($attach))
    				{
    					$attach ='';
    				}
    				if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_customer')) {
                        $mailtoSupport = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->mailSupportFromCustomer($ticket_id,$customer_name,$customer_email,$attach,$message);
                    }
                    if (isset($agentId) && isset($departmentHeadId) && !empty($agentId) && !empty($departmentHeadId)) {
                        if (($agentId == $departmentHeadId) && $agentRole == 'Agent') {
                           if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head') && $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) {
                               $agentLoad = $agentModel->load($agentId);
                               $agentName = $agentLoad->getUsername();
                               $agentEmail = $agentLoad->getEmail();
                               $mailtoHead = $this->_objectManager
                                                     ->create('Ced\HelpDesk\Helper\Data')
                                                     ->mailAgentFromCustomer($ticket_id,$agentName,$agentEmail,$attach,$message,$customer_name);
                           }
                        }elseif($agentRole == 'Administrators'){
                            if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_admin')) {
                                $adminLoad = $agentModel->load($agentId);
                                $adminName = $adminLoad->getUsername();
                                $adminEmail = $adminLoad->getEmail();
                                $mailtoAdmin = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->mailAgentFromCustomer($ticket_id,$adminName,$adminEmail,$attach,$message,$customer_name);
                            }
                            
                        }else{
                            $agentLoad = $agentModel->load($agentId);
                            $agentName = $agentLoad->getUsername();
                            $agentEmail = $agentLoad->getEmail();
                            $departmentHeadLoad = $agentModel->load($departmentHeadId);
                            $headName = $departmentHeadLoad->getUsername();
                            $headEmail = $departmentHeadLoad->getEmail();
                            if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) {
                                $mailtoAgent = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->mailAgentFromCustomer($ticket_id,$agentName,$agentEmail,$attach,$message,$customer_name);
                            }
                            if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head')) {
                                $mailtoHead = $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->mailAgentFromCustomer($ticket_id,$agentName,$agentEmail,$attach,$message,$customer_name);
                            }  
                        }
                    }
    					
    			}
    			if(!empty($message)){
    				$ticketModel->setData('message',$message)->save();
    			}
    				
    			$this->_redirect('helpdesk/tickets/form',array('id'=>$ticket_id));
    			return;
    		}
    		else {
    			$message = __('Please Fill The Message');
    			$this->messageManager->addError($message);
    			$this->_redirect('helpdesk/tickets/form',array('id'=>$ticket_id));
    			return;
    		}
    	}
    	else {
    		$message = __('Please Fill The form');
    		$this->messageManager->addError($message);
    		$this->_redirect('helpdesk/tickets/form',array('id'=>$ticket_id));
    		return;
    	}
    	
    }
    	
}	
