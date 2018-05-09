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
  * @category  Ced
  * @package   Ced_Blog
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\Blog\Controller\Adminhtml\Comment;

use Magento\Framework\View\Result\PageFactory;

use Magento\Backend\App\Action;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    protected $resultPageFactory;

    /**
     * $_objectManager
     * @var \Magento\Framework\App\ObjectManager $objectManager
     */

    protected $_objectManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {

        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     * @return void
     */

    public function execute()
    {

        $helper = $this->_objectManager->create('Ced\Blog\Helper\Data')->enableModule();
        if($helper==1) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->setActiveMenu('Ced_Blog::blog_comment');
            $resultPage->getConfig()->getTitle()->prepend(__('Manage Blog Comment'));
            return $resultPage;
        } else {
            $this->messageManager->addError('module is disable');
            $this->_redirect('adminhtml/dashboard/index');
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::comment');

    }

}
