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

class NewPriority extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
    /*
     * @var Registry
     * */
	public $registry;
    /*
     * @var ObjectManager
     * */
	public $objectManager;
	/**
	 * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\View\Result\PageFactory
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\Registry $registry,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
			) {
				$this->resultPageFactory = $resultPageFactory;
				$this->registry = $registry;
				$this->objectManager = $context->getObjectManager();
				parent::__construct($context);
	}
	/*
	 * create page
	 * */
	public function execute()
	{	
		$id = $this->getRequest()->getParam('id');
		if(isset($id) && $id != null){
			$data = $this->objectManager->create('Ced\HelpDesk\Model\Priority')->load($id)->getData();
			$this->registry->register('ced_priority',$data);
			$title = $data['title'];
			$resultRedirect = $this->resultPageFactory->create();
			$resultRedirect->getConfig()->getTitle()->set($title);
			/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
			return $resultRedirect;
		}
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		return $this->resultPageFactory->create();
	}
}