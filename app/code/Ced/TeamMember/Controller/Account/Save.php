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
 * @package     Ced_TeamMember
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 

namespace Ced\TeamMember\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Ced\TeamMember\Model\Session;

class Save extends \Ced\TeamMember\Controller\TeamMember
{
	protected $session;
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
			Session $teammemberSession
	) {
		parent::__construct($context, $teammemberSession, $resultPageFactory);
		$this->_resultPageFactory  = $resultPageFactory;
		$this->_scopeConfig = $scopeInterface;
		$this->session = $teammemberSession;
	}
    public function execute()
    {      
    	if (!$this->session->getLoggedIn()) {
    		$resultRedirect = $this->resultRedirectFactory->create();
    		$resultRedirect->setPath('teammember/account/login');
    		return $resultRedirect;
    	}
    	$data = $this->getRequest()->getPostValue();
    	//print_r($this->getRequest()->getPostValue());die;
    	$teamId = $this->getRequest()->getParam('id');
    	 
    	$path =$this->_objectManager->get('\Magento\Framework\Filesystem')
    	->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    	$path = $path->getAbsolutePath('teammember/'.$teamId);
    	$msg=""; 
    	if($teamId) {
    		if(!empty($_FILES['logo']['name'])) 
                    {

    					try {
    						
    						$uploader = $this->_objectManager->create(
    								'\Magento\MediaStorage\Model\File\Uploader', array(
    										'fileId' =>  "logo",
    								)
    						);
    						$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png'));
    						$uploader->setAllowRenameFiles(false);
    						$uploader->setFilesDispersion(false);
    						$uploader->save($path);
    					}
    					catch (\Exception $e)
    					{
    					    $msg.=$e->getMessage();
                           $this->messageManager->addError($msg);       
                           $this->_redirect('*/member/index');
                           return;
    				    }
    				
    				 }
    				
    				}
    				else
    				{
    				$msg.="Error Occured While Uploading Image.";
                    $this->messageManager->addNotice($msg);       
                    $this->_redirect('*/member/index');
                    return;
    			}

    			if(isset($data['profile_delete']))
    			{
    				unlink($data['profile_delete_logo']);
    			}
    			
    	
    	$data['email'] =  $data['email1'];
    	
    	$modeladata = $this->_objectManager->create('Ced\TeamMember\Model\TeamMemberData')->load($this->getRequest()->getParam('id'),'teammember_id');
    	$modeladata->addData($data);
    	$modeladata->setTeammemberId($this->getRequest()->getParam('id'));
    	if(isset($_FILES['logo']['name']) && $_FILES['logo']['name']!='')
    	$modeladata->setLogo($_FILES['logo']['name']);
    	else{
    		$modeladata->setLogo('');
    	}
    	$modeladata->save();
    	$this->messageManager->addSuccess(__('You Saved Your Information'));
    	
    	$this->_redirect('*/member/index');
    	return;
    }
}
