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

class Admin extends \Magento\Backend\App\Action
{

    /**
     * @var execute
     * return save comment for admin
     */
    
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $model = $this->_objectManager->create('Ced\Blog\Model\BlogComment');
            $model->setData('user', $data['user'])
                ->setData('email', $data['email'])    
                ->setData('user_type', $data['user_type'])
                ->setData('status', $data['status'])    
                ->setData('created_at', $data['created_at'])
                ->setData('description', $data['description'])
                ->setData('post_id', $data['post_id'])
                ->setData('post_title', $data['post_title'])
                ->save();
            $this->_redirect('blog/comment/index');
            $this->messageManager->addSuccess(__('Comment Added Successfully By Admin'));
        }
    }

    /**
     * @return string
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::comment');

    }

}



