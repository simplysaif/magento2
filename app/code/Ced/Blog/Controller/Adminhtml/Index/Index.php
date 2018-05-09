<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced
 * @package     Ced_Blog
 * @author   	CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace Ced\Blog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action{
	/**
	 * Index Action for Index
	 * @return Void
	 * */
	protected $resultPageFactory;
	
	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}
	
	/**
	 * Index action
	 *
	 * @return void
	 */
	public function execute()
	{
		
		$resultPage = $this->resultPageFactory->create();
		$resultPage->setActiveMenu('Ced_Blog::blog_attribute');
		$resultPage->addBreadcrumb(__('Add Attribute'), __('Manage Blog Attributes'));
		$resultPage->getConfig()->getTitle()->prepend(__('Manage Blog Attributes'));
		return $resultPage;
	}
}