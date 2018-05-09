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
namespace Ced\HelpDesk\Controller\Adminhtml\Status;

class SaveStatus extends \Magento\Backend\App\Action
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
	 * Save Status Information
	 * */
	public function execute()
	{
		$data = $this->getRequest()->getPostValue();
		$back = $this->getRequest()->getParam('back');
		$statusModel = $this->objectManager->create('Ced\HelpDesk\Model\Status');
		if (!empty($data['id'])) {
			$statusModel->load($data['id']);
            $statusModel->setTitle($data['title']);
            $statusModel->setStatus($data['status']);
            $statusModel->setBgcolor('#'.$data['bgcolor']);
            $statusModel->setCode($data['code']);
			$statusModel->save();
		}else{
			$statusModel->setTitle($data['title']);
			$statusModel->setStatus($data['status']);
			$statusModel->setBgcolor('#'.$data['bgcolor']);
			$statusModel->setCode($data['code']);
			$statusModel->save();
			$data['id'] = $statusModel->getId();
		}
		$this->messageManager->addSuccess(
            __('Save Status Successfully...')
        );
		(isset($back) && $back == 'edit') ? $this->_redirect('*/*/editstatus/id/'.$data['id']) : $this->_redirect('*/*/statusinfo');
	}
}