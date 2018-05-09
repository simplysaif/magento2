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

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{

    /**
     * @var execute
     */

    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogRelation')->getCollection()->addFieldToFilter('cat_id', $id);
        $ar = $model->addFieldToSelect('id');
        foreach($ar as $ids) {
            $ids->delete();

        }

        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->getCollection()
            ->addFieldToFilter('category_id', $id);

        foreach($model->getData() as $val)
        {

            $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($val['id']);
            $model->setId($val['id'])->delete();
        }

        $model = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->load($id);
        $model->setId($id)->delete();
        $this->_redirect('blog/category/index');
    }
}
