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

use Magento\Framework\Exception\LocalizedException;

use Magento\Framework\Controller\ResultFactory;

class massStatus extends \Magento\Backend\App\Action
{

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $status = $data['status'];
        if ($data) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();            
            $productDeleted = 0;
            foreach($data['massaction'] as $val)
            {
                $model = $objectManager->create('Ced\Blog\Model\BlogComment')->load($val);
                $model->setStatus($status)->save();
                $productDeleted++;
            }
            $this->_redirect('blog/comment/index');
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been saved.', $productDeleted));
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

