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

use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\Controller\ResultFactory;


class postmassStatus extends \Magento\Backend\App\Action
{

    /**
     * @var _isAllowed
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::post');

    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

    public function execute()
    {    

        $data = $this->getRequest()->getParams();
        $status=$data['status'];
        if ($data) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $productDeleted = 0;
            foreach($data['attribute_id'] as $val)
            {
                $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($val);
                $model->setBlogStatus($status)->save();
                $productDeleted++;
            }
            $this->_redirect('blog/post/index');
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been saved.', $productDeleted));
        }
    }
}

