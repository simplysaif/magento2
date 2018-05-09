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

/**
 * @var Magento\Backend\App\Action
 */

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{


    /**
     * @var objectManager
     */

    protected $_objectManager;

    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */

    public function __construct(
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context,
        array $data = []
    ) {
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);

    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::comment');

    }

    /**
     * @var execute
     */

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $collection = $this->_objectManager->create('Ced\Blog\Model\BlogComment')->getCollection();
            $id=$this->getRequest()->getParams('id');

            if($id) {    
                $model = $this->_objectManager->create('Ced\Blog\Model\BlogComment')->load($data['id']);
                $model->setData($data);
                $model->save();
                if ($this->getRequest()->getParam('back')) {
                    return  $resultRedirect->setPath('*/*/edit', ['id' => $data['id'], '_current' => true]);
                }
                   $this->_redirect('blog/comment/index');
                   $this->messageManager->addSuccess(__('Updated Successfully'));
            } 
        }
    }
}



