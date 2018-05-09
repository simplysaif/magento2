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
  * @license   http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var _isAllowed
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::post');
    }

    /**
     * @var execute
     */

    public function execute()
    {    
        $data = $this->getRequest()->getParams();
        if ($data) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $postData = $this->getRequest()->getPost();
            $id = $this->getRequest()->getParam('post_id');
            $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
            $model->setId($id)->delete();
            $this->_redirect('blog/post/index');
            $this->messageManager->addSuccess(__('Deleted Successfully'));
        }
    }
}
