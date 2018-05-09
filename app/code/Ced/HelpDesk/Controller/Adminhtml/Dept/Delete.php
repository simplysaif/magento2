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

class Delete extends \Magento\Backend\App\Action
{
    /*
     * delete department
     * */
	public function execute()
	{
		$id = $this->getRequest()->getParam('id');
		if (isset($id) && !empty($id)) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$model = $objectManager->create('Ced\HelpDesk\Model\Department');
			$model->load($id)->delete();
			$this->messageManager->addSuccess(
	            __('Department Deleted Successfully...')
	      		);
		}else{
			$this->messageManager->addSuccess(
	            __('Something wents Wrong...')
	      		);
		}
		$this->_redirect('*/*/deptinfo');
	}
}