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



class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var execute
     */

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogComment')->load($id);
        $model->setId($id)->delete();
        $this->_redirect('blog/comment/index');
    }

    /**
     * @return string
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::comment');

    }

}
