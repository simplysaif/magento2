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
namespace Ced\HelpDesk\Block;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;

class Ticket extends \Magento\Framework\View\Element\Template
{
    /*
     * @var \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory
     * */
	protected $_attributeCollection;
    /*
     * @var \Magento\Eav\Model\Entity
     * */
	protected $_eavEntity;
    /*
     * @var \Magento\Customer\Model\Session
     * */
	public $_session;
    /*
     * @var \Magento\Framework\ObjectManagerInterface
     * */
	public $_objectManager;
    /*
     * @var \Magento\Customer\Model\AttributeFactory
     * */
	protected $_attributeFactory;
    /*
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\ObjectManagerInterface
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory
     * @param \Magento\Eav\Model\Entity
     * @param \Magento\Customer\Model\AttributeFactory
     * @param \Magento\Customer\Model\Session
     * */
	
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager,
			\Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection,
			\Magento\Eav\Model\Entity $eavEntity,
			\Magento\Customer\Model\AttributeFactory $attributeFactory,
			\Magento\Customer\Model\Session $customerSession,
			array $data = []
	) {
			
		parent::__construct($context, $data);
		$this->_objectManager = $objectManager;
		$this->_attributeCollection = $attributeCollection;
		$this->_eavEntity = $eavEntity;
		$this->_attributeFactory = $attributeFactory;
		$this->_session = $customerSession;
	}
	

	/**
	 * Retrieve ticket data
	 * @return array
	 */
 	public function ticketModel()
 	{
 		$id = $this->getRequest()->getParam('id');
 		if($id){
 			$ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()
 									->addFieldtoFilter('ticket_id',$id);
			$count = $ticketModel->count();
 			if($count>0){
 				$model = $ticketModel->getData();
 				if(isset($model) && is_array($model)){
 					return $model;
 				}
 			} 
 			else {
 				return false;
 			}			
 		}
 	}
 	
 	/**
 	 * Retrieve status color
 	 * @return color
 	 */
 	public function statusColor($status)
 	{
 		$Model= $this->_objectManager->create('Ced\HelpDesk\Model\Status')->load($status,'code')->getBgcolor();
 		if(!empty($Model)){
 			return $Model;
 		}
 			else{
 				return false;
 			}
 	}
 	/**
 	 * Retrieve priority color
 	 * @return color
 	 */
 	public function priorityColor($priority)
 	{
 		$Model= $this->_objectManager->create('Ced\HelpDesk\Model\Priority')->getCollection()
 								->addFieldtoFilter('title',array('title'=>$priority));
 		$count = $Model->count();
 		if($count>0){
 			$priorityModel = $Model->getData();
 			if(isset($priorityModel) && is_array($priorityModel)){
 				foreach($priorityModel as $color){
 					$bg = $color['bgcolor'];
 				}
 				return $bg;
 			}
 			else{
 				return false;
 			}
 		}
 	}
 	/**
 	 * @return customer email
 	 */
 	public function customerEmail()
 	{
 	 	$customer = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomer();
 	 	$customer_email = $customer->getEmail(); 
 	 	return $customer_email;
 	}
 	
 	public function customerId()
 	{
 		$customer = $this->_objectManager->create('Magento\Customer\Model\Session')->getCustomer();
 		$customer_id = $customer->getId();
 		return $customer_id;
 	}
 	
 	/**
 	 * Retrieve message data
 	 * @return array
 	 */
 	public function messageModel()
 	{
 		$id = $this->getRequest()->getParam('id');
 		$ticketMessage= $this->_objectManager->create('Ced\HelpDesk\Model\Message')->getCollection()->addFieldtoFilter('ticket_id',$id)->getData();
 		if(isset($ticketMessage) && is_array($ticketMessage)){
 			return $ticketMessage;
 		}
 		else{
 			return false;
 		}
 	}
 	
 	/**
 	 * @return message from details
 	 */
 	public function fromDetails($value)
 	{
 		$id = $this->getRequest()->getParam('id');
 		if(is_numeric($value['from'])){
 				$getadmin = $this->_objectManager->create('Magento\Authorization\Model\Role')->load($value['from']);
 				$adminName = $getadmin->getUsername();
 				return $adminName;
 		}
 		else{
 				$ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()
 							->addFieldtoFilter('ticket_id',$value['ticket_id'])->getData();
 				if(isset($ticketModel) && is_array($ticketModel)){
 					foreach($ticketModel as $value){
 						$from = $value['customer_name'];
 					}
 					return $from;
 			}
 		} 			
 	}

 	public function departmentName($deptCode)
	{
		if (isset($deptCode) && !empty($deptCode)) {
			return $this->_objectManager->create('Ced\HelpDesk\Model\Department')->load($deptCode,'code')->getName();
		}else{
			return $deptCode;
		}
	}
	
}