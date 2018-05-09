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

use Magento\Backend\App\Action;

class massDelete extends \Magento\Backend\App\Action
{

    /**
     * @var _isAllowed
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::post');
    }


    public function execute()
    {
        $data = $this->getRequest()->getParams();
        if ($data) {
            $id = $this->getRequest()->getParam('attribute_id');
            $count = 0;
            foreach($id as $val)
            {   try{
                $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($val);
                $model->setId($val)->delete();
                $modelcategory = $this->_objectManager->create('Ced\Blog\Model\BlogRelation')
                    ->getCollection()->addFieldToFilter('post_id',$val);
                if(!empty($modelcategory->getData())){
                    $dataPostCategory = $modelcategory->getData();
                    foreach ($dataPostCategory as $data){
                        $id = $data['id'];
                        $newmodel = $this->_objectManager->create('Ced\Blog\Model\BlogRelation')->load($id);
                        $newmodel->setId($id)->delete();
                        $count++;
                    }
                }
            }
            catch (\Exception $e){
                $e->getMessage();
            }

            }
            $this->_redirect('blog/post/index');
            $this->messageManager->addSuccess(__('Deleted Successfully'));
        }
    }

}
