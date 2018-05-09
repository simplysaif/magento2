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
namespace Ced\HelpDesk\Controller\Adminhtml\Agents;

class Save extends \Magento\Backend\App\Action
{
    /*
     * @var ObjectManager
     * */
	public $objectManager;
    /*
     * @param \Magento\Backend\App\Action\Context
     * */
	public function __construct(
			\Magento\Backend\App\Action\Context $context
			) {
				$this->objectManager = $context->getObjectManager();
				parent::__construct($context);
	}
	/*
	 * save agent data
	 * */
	public function execute()
	{
		$data = $this->getRequest()->getPostValue();
		$back = $this->getRequest()->getParam('back');
		$agentModel = $this->objectManager->create('Ced\HelpDesk\Model\Agent');
		$userModel = $this->objectManager->create('Magento\User\Model\User');
		if (!empty($data['id'])) {
			$agentModel->load($data['id']);
			$userId = $agentModel->getUserId();
			$agentModel->setData($data);
			$agentModel->save();
			$userModel->load($userId);
			$userModel->setFirstname($data['firstname']);
			$userModel->setLastname($data['lastname']);
			$userModel->setUsername($data['username']);
			$userModel->setEmail($data['email']);
			$userModel->setIsActive($data['active']);
			if (isset($data['change_password']) && !empty($data['change_password'])) {
				$userModel->setPassword($data['change_password']);
			}
			$userModel->save();
		}else{
			$roleModel = $this->objectManager->create('Magento\Authorization\Model\Role');
			$roleId = $roleModel->getCollection()->addFieldToFilter('role_name','Agent')->getFirstItem()->getRoleId();
			$userModel->setFirstname($data['firstname']);
			$userModel->setLastname($data['lastname']);
			$userModel->setUsername($data['username']);
			$userModel->setEmail($data['email']);
			$userModel->setIsActive($data['active']);
			$userModel->setPassword($data['password']);
			$userModel->save();

			$userId = $userModel->getUserId();

			$roleModel->setRoleName($data['username']);
			$roleModel->setUserId($userId);
			$roleModel->setRoleType('U');
			$roleModel->setParentId($roleId);
			$roleModel->setUserType('2');
			$roleModel->setTreeLevel('2');
			$roleModel->save();

			$agentModel->setUsername($data['username']);
			$agentModel->setEmail($data['email']);
			$agentModel->setUserId($userId);
			$agentModel->setActive($data['active']);
			$agentModel->setRoleName($data['role_name']);
			$agentModel->save();

			$data['id'] = $agentModel->getId();
		}
		$this->messageManager->addSuccess(
            __('Save Agent Successfully...')
        );
		(isset($back) && $back == 'edit' && isset($data['id'])) ? $this->_redirect('*/*/editagent/id/'.$data['id']) : $this->_redirect('*/*/agentsinfo');
	}
}