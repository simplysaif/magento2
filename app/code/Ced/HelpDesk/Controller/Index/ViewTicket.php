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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class ViewTicket extends \Magento\Framework\App\Action\Action
{
    /*
     * @param Context
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param PageFactory
     * */
    public function __construct(
            Context $context,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            PageFactory $resultPageFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /*
     * Create Page
     * */
    public function execute()
    {

        if(!$this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable')){
            $this->_redirect('cms/index/index');
            return ;
        }
        $email = $this->getRequest()->getParam('email');
        $id =  $this->getRequest()->getParam('id');
        
        if(isset($id) && isset($email))
        {
            $ticket = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()
            ->addFieldtoFilter('ticket_id',$id)->addFieldtoFilter('customer_email',$email);
        }
        $count = count($ticket); 
        if($count == 0)
        {
            $message = __('Please Check your Details. No Tickets matching with given Email id and Ticket id');
            $this->messageManager->addError($message);
            $this->_redirect('helpdesk/index/view');
        }
        else
        {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->set(__('View Your Ticket'));
            return $resultPage;
        }
        
    }
}