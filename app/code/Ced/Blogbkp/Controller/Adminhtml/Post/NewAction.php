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

 namespace Ced\Blog\Controller\Adminhtml\Post;

 class NewAction extends \Magento\Backend\App\Action
 {
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */

    protected $resultForwardFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
        ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);

    }
    /**
     * Forward to edit
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }

    /**
     * @var _isAllowed
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::blog_post');

    }
}
