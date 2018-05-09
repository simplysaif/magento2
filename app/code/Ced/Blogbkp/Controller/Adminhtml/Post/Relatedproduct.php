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

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Relatedproduct extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */

    protected $resultPageFactory;

    protected $_coreRegistry;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;

    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('post_id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        $this->_coreRegistry->register('current_post', $model);
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('blog.product.edit.tab.related')->setProductsRelated($this->getRequest()->getPost('products_related', null));
        return $resultLayout;

    }

    /**
     * @var _isAllowed
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::post');

    }
}

