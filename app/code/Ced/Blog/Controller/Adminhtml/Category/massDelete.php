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

/**
 * massDelete category Controller
 *
 * @author CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */

class massDelete extends \Magento\Backend\App\Action
{
    /**
     * @param execute
     */

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $productDeleted = 0;
            foreach($id as $val)
            {
                $model = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->load($val);
                $model->setId($val)->delete();
                $productDeleted++;
            }
            $this->_redirect('blog/category/index');
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $productDeleted));
        }
    }
}  

