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

namespace Ced\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;

use Magento\Framework\View\Result\PageFactory;

class PostGrid extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */

    protected $_resultLayoutFactory;

    protected $_coreRegistry;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */

    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->_resultLayoutFactory = $resultLayoutFactory;

    }

    /**
     * Index action
     * @return void
     */

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogRelation')->getCollection()
            ->addFieldToFilter('cat_id', $id);
        $this->_coreRegistry->register('current_category', $model);
        $resultLayout = $this->_resultLayoutFactory->create();

        $resultLayout->getLayout()->getBlock('blog.post.edit.tab.related')
            ->setPostsRelated($this->getRequest()->getPost('posts_related', null));
        return $resultLayout;

    }

}

