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
namespace Ced\HelpDesk\Controller\Adminhtml\Dept;

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
	 *Save Department Information
	 * */
	public function execute()
	{
		$data = $this->getRequest()->getPostValue();
		$back = $this->getRequest()->getParam('back');
		$deptModel = $this->objectManager->create('Ced\HelpDesk\Model\Department');
		if(isset($data['agent']) && is_array($data['agent']) && !empty($data['agent'])){
			if(!in_array($data['department_head'], $data['agent'])){
				$data['agent'][] = $data['department_head'];
			}
			$data['agent'] = implode(',', $data['agent']);
		}
		if (!empty($data['id'])) {
			$deptModel->load($data['id']);
			$deptModel->setName($data['name']);
			$deptModel->setCode($data['code']);
			$deptModel->setAgent($data['agent']);
			$deptModel->setActive($data['active']);
			$deptModel->setDepartmentHead($data['department_head']);
			$deptModel->setDeptSignature($data['dept_signature']);
			$deptModel->save();
		}else{
			$deptModel->setName($data['name']);
			$deptModel->setCode($data['code']);
			$deptModel->setAgent($data['agent']);
			$deptModel->setActive($data['active']);
			$deptModel->setDepartmentHead($data['department_head']);
			$deptModel->setDeptSignature($data['dept_signature']);
			$deptModel->save();
			$data['id'] = $deptModel->getId();
		}
		$this->messageManager->addSuccess(
            __('Save Department Successfully...')
        );
		(isset($back) && $back == 'edit' && isset($data['id'])) ? $this->_redirect('*/*/editdept/id/'.$data['id']) : $this->_redirect('*/*/deptinfo');
	}
}