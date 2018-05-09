<?php
 
namespace Ced\TeamMember\Controller\Adminhtml\Member;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Conversation extends \Magento\Backend\App\Action
{   
        protected $resultPageFactory;
        public function __construct(
            Context $context,
            \Magento\Framework\View\Result\PageFactory $resultPageFactory
        )
        {
            parent::__construct($context);
            $this->resultPageFactory = $resultPageFactory;
        }
  
    public function execute()
    {
    	
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Conversation'), __('Conversation'));
        $resultPage->addBreadcrumb(__('Conversation'), __('Conversation'));
        $resultPage->getConfig()->getTitle()->prepend(__('Conversation'));
 
        return $resultPage;
    }
}
