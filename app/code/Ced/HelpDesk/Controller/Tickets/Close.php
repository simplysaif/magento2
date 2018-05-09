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

  class Close extends \Magento\Customer\Controller\AbstractAccount
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
      $ids = [];
    	if(!$this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/general/enable')){
            $this->_redirect('cms/index/index');
            return ;
      }
      $ticket_id=$this->getRequest()->getParam('id');
      $status = $this->getRequest()->getParam('status_id');
      $comments = $this->getRequest()->getParam('comments');
      $comments = strip_tags($comments);
      if(!empty($ticket_id)){
      	$ticketModel = $this->_objectManager
                            ->get('Ced\HelpDesk\Model\Ticket')
                            ->getCollection()
                            ->addFieldToFilter('ticket_id',$ticket_id)
                            ->getFirstItem();
      	$ticketModel->setData('status',$status);
      	$ticketModel->setData('closing_message',$comments);
      	$ticketModel->save();
        $customer_name = $ticketModel->getCustomerName();
        $customer_email = $ticketModel->getCustomerEmail();
        $agent_id = $ticketModel->getAgent();
        $departmentCode = $ticketModel->getDepartment();
        $agent_name = $ticketModel->getAgentName();
        $departmentHeadId = $this->_objectManager
                                ->create('Ced\HelpDesk\Model\Department')
                                ->getCollection()
                                ->addFieldToFilter('code',$departmentCode)
                                ->getFirstItem()
                                ->getDepartmentHead();
        $agentModel = $this->_objectManager
                           ->create('Ced\HelpDesk\Model\Agent');
        $agentRole = $agentModel->load($agent_id)->getRoleName();
        if (!empty($departmentHeadId) && !empty($agent_id)) {
          if ($departmentHeadId == $agent_id) {
            if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head') && $this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) {
                $agentLoad = $agentModel->load($agent_id);
                $agnentEmail = $agentLoad->getEmail();
                $this->_objectManager
                     ->create('Ced\HelpDesk\Helper\Data')
                     ->mailAgentStatus($agent_name,$agnentEmail,$customer_name,$ticket_id,$status,$comments);
            }
          }elseif($agentRole != 'Administrators'){
            if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_agent')) {
                      $agentModel = $this->_objectManager
                                         ->create('Ced\HelpDesk\Model\Agent');
                      $agentLoad = $agentModel->load($agent_id);
                      $agnentEmail = $agentLoad->getEmail();
                      $this->_objectManager
                           ->create('Ced\HelpDesk\Helper\Data')
                           ->mailAgentStatus($agent_name,$agnentEmail,$customer_name,$ticket_id,$status,$comments);
            }
            if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_head')) {
              $agentModel = $this->_objectManager
                                 ->create('Ced\HelpDesk\Model\Agent');
              $departmentHeadLoad = $agentModel->load($departmentHeadId);
              $headName = $departmentHeadLoad->getUsername();
              $headEmail = $departmentHeadLoad->getEmail();
              $this->_objectManager
                   ->create('Ced\HelpDesk\Helper\Data')
                   ->mailAgentStatus($headName,$headEmail,$customer_name,$ticket_id,$status,$comments);
              }
           }
         }
        if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_customer')) {
          $this->_objectManager
               ->create('Ced\HelpDesk\Helper\Data')
               ->mailCustomerStatus($customer_name,$customer_email,$ticket_id,$status);
        }
        $ids = $this->_objectManager->create('Magento\Authorization\Model\Role')->load('Administrators','role_name')->getRoleUsers();
        $adminData = $this->_objectManager
                          ->create('Magento\User\Model\User')
                          ->getCollection()
                          ->addFieldToFilter('user_id',['in'=>$ids])
                          ->addFieldToSelect('*')
                          ->getData();
        if ($this->_objectManager->get('Ced\HelpDesk\Helper\Data')->getStoreConfig('helpdesk/email/mail_admin')) {
          if (is_array($adminData) && !empty($adminData)) {
            foreach ($adminData as $value) {
                $this->_objectManager
               ->create('Ced\HelpDesk\Helper\Data')
               ->mailAdminStatus($value['username'],$value['email'],$customer_name,$ticket_id,$status,$comments);
            } 
          }
        }
      	$this->_redirect('helpdesk/tickets/index');
      	return;
      }
    }   	
 }