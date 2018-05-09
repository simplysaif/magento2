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
namespace Ced\HelpDesk\Controller\Adminhtml\Priority;

class SavePriority extends \Magento\Backend\App\Action
{
    /*
     * @var Object Manager
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
	 * save priority Data
	 *
	 * */
	public function execute()
	{
		$data = $this->getRequest()->getPostValue();
		$back = $this->getRequest()->getParam('back');
		$priorityModel = $this->objectManager->create('Ced\HelpDesk\Model\Priority');
		if (!empty($data['id'])) {
            $priorityModel->load($data['id']);
            $priorityModel->setTitle($data['title']);
            $priorityModel->setStatus($data['status']);
            $priorityModel->setBgcolor('#'.$data['bgcolor']);
            $priorityModel->setCode($data['code']);
            $priorityModel->save();
		}else{
            $priorityModel->setTitle($data['title']);
            $priorityModel->setStatus($data['status']);
            $priorityModel->setBgcolor('#'.$data['bgcolor']);
            $priorityModel->setCode($data['code']);
            $priorityModel->save();
			$data['id'] = $priorityModel->getId();
		}
		$this->messageManager->addSuccess(
            __('Save Priority Successfully...')
        );
		(isset($back) && $back == 'edit') ? $this->_redirect('*/*/editpriority/id/'.$data['id']) : $this->_redirect('*/*/priorityinfo');
	}
}