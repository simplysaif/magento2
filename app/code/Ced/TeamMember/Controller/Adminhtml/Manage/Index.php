<?php
 
namespace Ced\TeamMember\Controller\Adminhtml\Manage;
 
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
 
class Index extends \Magento\Backend\App\Action
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
        $resultPage->addBreadcrumb(__('Manage TeamMember'), __('Manage TeamMember'));
        $resultPage->addBreadcrumb(__('Manage TeamMember'), __('Manage TeamMember'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage TeamMember'));
 
        return $resultPage;
    }
}
