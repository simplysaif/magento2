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

class Index extends \Magento\Framework\View\Element\Template
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
	*@var \Ced\HelpDesk\Model\Department
	*
	**/
	public $departmentModel;
    /*
     * @var \Magento\Customer\Model\AttributeFactory
     * */
	protected $_attributeFactory;
	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection
     * @param \Magento\Eav\Model\Entity $eavEntity
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param \Magento\Customer\Model\Session $customerSession
	 * @param array $data
	 */
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
	 * Set Ticket Model
	 */
	public function _construct()
	{
       $this->_objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
       $customerSession = $this->_objectManager->create('Magento\Customer\Model\Session');
       $customer_Id = $customerSession->getCustomer()->getId();
       $ticketModel = $this->_objectManager
                           ->create('Ced\HelpDesk\Model\Ticket')
                           ->getCollection()
                           ->addFieldtoFilter('customer_id',array('customer_id'=>$customer_Id))
                           ->setOrder('id','DESC');
		$this->setTicket($ticketModel);
	}
	
	/**
	 * Prepare Pager Layout
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		/** @var \Magento\Theme\Block\Html\Pager */
		$pager = $this->getLayout()->createBlock(
				'Magento\Theme\Block\Html\Pager',
				'helpdesk.ticcket.list.pager'
		)->setAvailableLimit(array(5=>5))
		->setCollection($this->getTicket());
 		$this->setChild('helpdesk.ticcket.list.pager', $pager);
 		$this->getTicket()->load();
 		return $this;
	}
	
	/*
	 * get pager html
	 * */
	public function getPagerHtml()
	{
		return $this->getChildHtml('helpdesk.ticcket.list.pager');
	}
	
	/**
	 * Retrieve department data
	 * @return array
	 */
	public function deptModel()
	{
		$this->departmentModel = $this->_objectManager
									  ->create('Ced\HelpDesk\Model\Department');
		$deptModel = $this->departmentModel->getCollection()->addFieldToFilter('active',1)->getData();
		if(is_array($deptModel) && isset($deptModel)){
			return $deptModel;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Retrieve priority data
	 * @return array
	 */
	public function priorityModel()
	{
		$priorityModel = $this->_objectManager->create('Ced\HelpDesk\Model\Priority')->getCollection()->addFieldToFilter('status',1)->getData();
		if(is_array($priorityModel) && isset($priorityModel)){
			return $priorityModel;
		}
		else{
			return false;
		}
	}
	
	/**
	 * Retrieve ticket data
	 * @return collection
	 */
	public function ticketModel()
	{
		$customer = $this->_session->getCustomer();
		$customer_email = $customer->getEmail();
		$ticketModel = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()
		->addFieldtoFilter('customer_email',array('customer_email'=>$customer_email))->setOrder('id','DESC')->setPageSize(10);
		$count = $ticketModel->count();
		if($count>0){
			return $ticketModel;
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
			$pri = $Model->getData();
			if(isset($pri) && is_array($pri)){
				foreach($pri as $color){
					$bg1=$color['bgcolor'];
				}
				return $bg1;
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
		$Model=$this->_objectManager->create('Ced\HelpDesk\Model\Status')->getCollection()
		->addFieldtoFilter('title',array('title'=>$status));
		$count = $Model->count();
		if($count>0){
			$status_model = $Model->getData();
			if(isset($status_model) && is_array($status_model)){
				foreach($status_model as $color){
					$bg=$color['bgcolor'];
				}
				return $bg;
			}
			else {
				return false;
			}
		}
	}
	
	/**
	 * Retrieve sales data
	 * @return collection
	 */
	public function salesModel()
	{
		$customer = $this->_session->getCustomer();
		$customer_Id = $customer->getId();
		$collection =$this->_objectManager->create('Magento\Sales\Model\Order')->getCollection()
		->addAttributeToSelect('*')
		->addFieldToFilter('customer_id', $customer_Id);
		$count = $collection->count();
		if($count>0){
			return $collection;
		}
		else{
			return false;
		}
	}

	public function departmentName($deptCode)
	{
		if (isset($deptCode) && !empty($deptCode)) {
			return $this->departmentModel->load($deptCode,'code')->getName();
		}else{
			return $deptCode;
		}
	}
}