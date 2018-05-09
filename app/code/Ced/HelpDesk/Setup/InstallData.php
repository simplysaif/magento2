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
namespace Ced\HelpDesk\Setup;
 
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;
 
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * RoleFactory
     *
     * @var roleModel
     */
    private $roleModel;
 
     /**
     * RulesFactory
     *
     * @var rulesModel
     */
    private $rulesModel;
     /**
     * ObjectManagerInterface
     *
     * @var _objectManager
     */
    public $_objectManager;
    /**
     * Init
     *
     * @param \Magento\Authorization\Model\RoleFactory $roleModel
     * @param \Magento\Framework\ObjectManagerInterface $om
     * @param \Magento\Authorization\Model\RulesFactory $rulesModel
     */
    public function __construct(
        \Magento\Authorization\Model\RoleFactory $roleModel, 
    	\Magento\Framework\ObjectManagerInterface $om,
        \Magento\Authorization\Model\RulesFactory $rulesModel
        )
    {
        $this->_objectManager = $om;
        $this->roleModel = $roleModel;
        $this->rulesModel = $rulesModel;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
        * Create role Agent
        */
        $role=$this->roleModel->create();
        $role->setName('Agent') //Set Role Name Which you want to create 
                ->setPid(0) //set parent role id of your role
                ->setRoleType(RoleGroup::ROLE_TYPE) 
                ->setUserType(UserContextInterface::USER_TYPE_ADMIN);
        $role->save();
        $resource=['Magento_Backend::admin',
                    'Magento_Backend::dashboard',
                    'Ced_HelpDesk::helpdesk',
                    'Ced_HelpDesk::helpdesk_menu',
                    'Ced_HelpDesk::tickets_info'
                  ];
        $this->rulesModel->create()->setRoleId($role->getId())->setResources($resource)->saveRel();
        $priority = $this->_objectManager->create('Ced\HelpDesk\Model\Priority');
        $priority->setData('status','1');
        $priority->setData('title','Normal');
        $priority->setData('code','Normal');
        $priority->setData('bgcolor','#CDF7FC');
        $priority->save();
        $priority = $this->_objectManager->create('Ced\HelpDesk\Model\Priority');
        $priority->setData('status','1');
        $priority->setData('title','Urgent');
        $priority->setData('code','Urgent');
        $priority->setData('bgcolor','#FF0C09');
        $priority->save();
        $priority = $this->_objectManager->create('Ced\HelpDesk\Model\Priority');
        $priority->setData('status','1');
        $priority->setData('title','ASAP');
        $priority->setData('code','Asap');
        $priority->setData('bgcolor','#FCFF09');
        $priority->save();
        $status = $this->_objectManager->create('Ced\HelpDesk\Model\Status');
        $status->setData('status','1');
        $status->setData('title','New');
        $status->setData('code','New');
        $status->setData('bgcolor','#7658FF');
        $status->save();
         $status = $this->_objectManager->create('Ced\HelpDesk\Model\Status');
        $status->setData('status','1');
        $status->setData('title','Open');
        $status->setData('code','Open');
        $status->setData('bgcolor','#B0E0E6');
        $status->save();
        $status = $this->_objectManager->create('Ced\HelpDesk\Model\Status');
        $status->setData('status','1');
        $status->setData('title','Closed');
        $status->setData('code','Closed');
        $status->setData('bgcolor','#F71F2A');
        $status->save();
        $status = $this->_objectManager->create('Ced\HelpDesk\Model\Status');
        $status->setData('status','1');
        $status->setData('title','Waiting for customer');
        $status->setData('code','Waiting for customer');
        $status->setData('bgcolor','#FFA19D');
        $status->save();
        $status = $this->_objectManager->create('Ced\HelpDesk\Model\Status');
        $status->setData('status','1');
        $status->setData('title','Resolved');
        $status->setData('code','Resolved');
        $status->setData('bgcolor','#9FFF11');
        $status->save();
        $adminModel = $this->_objectManager->create('Magento\User\Model\User')->load(1);
        $adminId = $adminModel->getUserId();
        $dept = $this->_objectManager->create('Ced\HelpDesk\Model\Department');
        $dept->setName('Admin');
        $dept->setCode('admin');
        $dept->setSortOrder(1);
        $dept->setActive(1);
        $dept->setDepartmentHead($adminId);
        $dept->setAgent($adminId);
        $dept->setDeptSignature(null);
        $dept->save();
        $agentModel = $this->_objectManager->create('Ced\HelpDesk\Model\Agent');
        $agentModel->setUsername($adminModel->getUsername());
        $agentModel->setEmail($adminModel->getEmail());
        $agentModel->setUserId($adminId);
        $agentModel->setActive(1);
        $agentModel->setRoleName('Administrators');
        $agentModel->save();
    }
}