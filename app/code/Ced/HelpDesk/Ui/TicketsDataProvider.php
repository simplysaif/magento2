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
namespace Ced\HelpDesk\Ui;

use Ced\HelpDesk\Model\ResourceModel\Ticket\CollectionFactory;

class TicketsDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Tickets collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Ui\DataProvider\AddFieldToCollectionInterface[]
     */
    protected $addFieldStrategies;

    /**
     * @var \Magento\Ui\DataProvider\AddFilterToCollectionInterface[]
     */
    protected $addFilterStrategies;
    public $_objectManager;
    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Ui\DataProvider\AddFieldToCollectionInterface[] $addFieldStrategies
     * @param \Magento\Ui\DataProvider\AddFilterToCollectionInterface[] $addFilterStrategies
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->addFieldStrategies = $addFieldStrategies;
        $this->addFilterStrategies = $addFilterStrategies;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {   
        $collection = $this->collection->getData();
        $user = $this->_objectManager->create('Magento\Backend\Model\Auth\Session')->getUser()->getData('user_id');
        $adminUserRoleId = $this->_objectManager->create('Magento\Authorization\Model\Role')->load('Administrators','role_name')->getRoleId();
       
        $agentId = $this->_objectManager->create('Ced\HelpDesk\Model\Agent')->load($user,'user_id')->getId();
	    $agent = $this->_objectManager->create('Magento\Authorization\Model\Role')->load($user,'user_id')->getData();
        if($agent['parent_id'] == $adminUserRoleId)
		{
			return [
			'totalRecords' => $this->collection->getSize(),
			'items' => array_values($collection),
			 
			];
		}

		else{
			$deptData = $this->_objectManager->create('Ced\HelpDesk\Model\Department')->getCollection()->addFieldToFilter('department_head',$agentId);
	        $dept = [];
			foreach ($deptData as $key=>$val)
			{
				$dept[] = $val['code'];
			}
			
			
			if(count($dept)>0 )
			{
		       $newCollection = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()->addFieldToFilter('department',['in'=>$dept])->getData();
	       // print_r($newCollection->getData()); die("hj");
	        return [
	            'totalRecords' => count($newCollection),
	            'items' => array_values($newCollection),
	        ];
				
			}
	       else {
	       	
	       	$AgentTickets = $this->_objectManager->create('Ced\HelpDesk\Model\Ticket')->getCollection()->addFieldToFilter('agent',$agentId)->getData();
	       	
	       	
	       	return [
	       	'totalRecords' => count($AgentTickets),
	       	'items' => array_values($AgentTickets),
	       	
	       	];
	       }
	
		}
		
		
        
    }
    
}
