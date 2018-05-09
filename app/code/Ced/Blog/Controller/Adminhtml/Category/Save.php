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

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $_objectManager;
    
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultRedirectFactory;
    
    /**
     * @param Magento\Framework\App\Action\Context
     * @param Magento\Backend\Model\View\Result\Redirect
     * @param Magento\Framework\Controller\Result\ForwardFactory
     * @param Magento\Framework\View\Result\PageFactory
     */
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirectFactory ,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->resultForwardFactory= $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPost();
        $ids = array();
        $lastSpace = strrpos($data['url_key'], " ");
        if($lastSpace==0) {
            $data['url_key']=ltrim($data['url_key'], ' ');
        }    
        $data['url_key']=str_replace(' ', '-', $data['url_key']);
        $data['url_key']= preg_replace("![^a-z0-9]+!i", "-", $data['url_key']);
        if($data['id']) {
            $model = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->load($data['id']);
            if(!empty($data['profile']['delete'])) {
                 $media = $this->_objectManager->get('\Magento\Framework\Filesystem')
                     ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                     ->getAbsolutePath();
                unlink($media.$data['profile']['value']);
                $data['profile']= '';
                $model->setData('profile', $data['profile']);

            }
            if($_FILES['profile']['name']!="") {
                try
                {
                    $mediaDirectory =$this->_objectManager->get('\Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $path = $mediaDirectory->getAbsolutePath('ced/blog/category');
                    $uploader = $this->_objectManager->create('\Magento\MediaStorage\Model\File\Uploader', array('fileId' =>'profile'));
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $extension = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
                    $fileName = $_FILES['profile']['name'].time().'.'.$extension;
                    $flag = $uploader->save($path, $fileName);
                    chmod($path."/".$fileName, 0777);
                    $fileName ='ced/blog/category/'.$fileName;

                }catch (\Exception $e) {
                    echo $e->getMessage();
                }

                $data['profile'] = $fileName;    
            }else {
                $img = $model['profile'];
                $data['profile'] = $img;
            }
            if(isset($data['links']['related'])) {
                $related = $data['links']['related'];
                $related = explode("&", $related);
                foreach($related as $id){
                    $id = explode("=", $id);
                    if(isset($id[0])) {
                        $ids[] =$id[0]; 
                    }
                }
            }
                
            $model->setData('profile', $data['profile'])
                ->setData('sortOrder', $data['sortOrder'])
                ->setData('title', $data['title'])
                ->setData('url_key', $data['url_key'])
                ->setData('meta_title', $data['meta_title'])
                ->setData('keywords', $data['keywords'])
                ->setData('about', $data['about'])
                ->save();

            /* saving data in category_post relation table */

            $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
            $post_data = $collection->getCollection()->addFieldToFilter('cat_id', $data['id']);
            $existingIds = $post_data->getColumnValues('post_id');
            $todelete = array_diff($existingIds, $ids);
            foreach ($todelete as $deletekey) 
            {
                $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                $datadelete = $collection->getCollection()->addFieldToFilter('post_id', $deletekey);
                foreach ($datadelete as $deleteid) 
                {
                    $deleteid->delete();
                    /*$postdelete = $collection->load($deleteid->getId())
                    ->setId($deleteid->getId())
                    ->delete();*/
                }
            }
            $toSave = array_diff($ids, $existingIds);
            foreach ($toSave as $savekey)
            {
                if($savekey!= 0) {
                    $collection_save = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                    $collection_save->setData('post_id', $savekey)
                        ->setData('cat_id', $data['id'])
                        ->save();
                }
            }

            /* code end */
            $this->messageManager->addSuccess(__('Category  Updated Successfully'));

            if ($this->getRequest()->getParam('back')) {
                return  $resultRedirect->setPath('*/*/edit', ['id' => $model['id'], '_current' => true]);
            }else{
                $this->_redirect('blog/category/index');
            }
      
        } else { 
            $model = $this->_objectManager->create('Ced\Blog\Model\Blogcat');
            if($_FILES['profile']['name']!="") {
                try{
                    $mediaDirectory =$this->_objectManager->get('\Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $path = $mediaDirectory->getAbsolutePath('ced/blog/category');
                    $uploader = $this->_objectManager->create('\Magento\MediaStorage\Model\File\Uploader', array('fileId' =>'profile'));
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $extension = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
                    $fileName = $_FILES['profile']['name'].time().'.'.$extension;
                    $flag = $uploader->save($path, $fileName);
                    chmod($path."/".$fileName, 0777);
                    $fileName = 'ced/blog/category/'.$fileName;
                    $data['profile'] = $fileName;
                }catch (\Exception $e) {
                    echo $e->getMessage();
                }
                $model->setData('profile', $data['profile']);
            } else {
                $model->setData('profile', 'ced/blog/post/thumbnail.jpg');
            }

            if(isset($data['links']['related'])) {
                $related = $data['links']['related'];
                $related = explode("&", $related);
                foreach($related as $id){
                    $id = explode("=", $id);
                    $ids[] =$id[0];
                }
            }
             $model->setData('sortOrder', $data['sortOrder'])
                 ->setData('title', $data['title'])
                 ->setData('meta_title', $data['meta_title'])
                 ->setData('url_key', $data['url_key'])
                 ->setData('keywords', $data['keywords'])
                 ->setData('about', $data['about'])
                 ->save();
        
              /* saving data in category_post realtion table */
        
            $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
            if(is_array($ids)) {
                foreach ($ids as $post )
                {
                    if(!empty($post)) {
                        $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                        $collection->setData('cat_id', $model['id'])
                            ->setData('post_id', $post)
                            ->save();
                    }
                }
            }
            /* code end */

            $this->messageManager->addSuccess(__('Category Added Successfully'));
        }
        if ($this->getRequest()->getParam('back')) {
            return  $resultRedirect->setPath('*/*/edit', ['id' => $model['id'], '_current' => true]);
        }else{
            $this->_redirect('blog/category/index');
        }
      
    }
}

