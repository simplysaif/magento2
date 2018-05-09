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

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var objectManager
     */
    protected $_objectManager;

    protected $urlBuilder;
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultRedirectFactory;

    /**
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Backend\Model\View\Result\Redirect
     * @param Magento\Backend\App\Action\Context
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        array $data = []
    ) {
        parent::__construct($context);
    }

    /**
     * @var _isAllowed
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Blog::post');
    }


    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPost();
        $lastSpace = strrpos($data['url'], " ");
        if($lastSpace==0) {
            $data['url']=ltrim($data['url'], ' ');
        }
        $data['url']=str_replace(' ', '-', $data['url']);
        $data['url']= preg_replace("![^a-z0-9]+!i", "-", $data['url']);

        if($data['id']) {

            try
            {
                $mediaDirectory =$this->_objectManager->get('\Magento\Framework\Filesystem')
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                $path = $mediaDirectory->getAbsolutePath('ced/blog/post');
                if($_FILES['featured_image']['name']!=''){
                    $uploader = $this->_objectManager->create('\Magento\MediaStorage\Model\File\Uploader', array('fileId' =>'featured_image'));
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $fileName = $_FILES['featured_image']['name'].time().'.'.$extension;
                    $flag = $uploader->save($path, $fileName);
                    $fileName ='ced/blog/post/'.$fileName;
                }
            }
            catch (\Exception $e)
            {
                echo $e->getMessage();
            }
            /* For editing saved post
            * 
            * */
            $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($data['id']);

            /* code end */

            if(!empty($data['featured_image']['delete'])) {
                $media = $this->_objectManager->get('\Magento\Framework\Filesystem')
                    ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)
                    ->getAbsolutePath();
                unlink($media.$data['featured_image']['value']);
                $model->setData('featured_image', 'ced/blog/post/thumbnail.jpg');
            }

            if(isset($_FILES['featured_image'])) {
                if($_FILES['featured_image']['name']!="") {
                    $model->setData('featured_image', $fileName);
                }
            }

            try
            {


                $model->setData('blog_status', $data['blog_status']);
                $model->setData('blog_category',json_encode($data['blog_category']));
                $model->setData('post_text', $data['post_text']);
                $model->setData('product_id', json_encode($data['product_id']));
                $model->setData('publish_date', $data['created_at']);
                $model->setData('tags', $data['tags']);
                $model->setData('title', $data['title']);
                $model->setData('url', $data['url']);
                $model->setData('meta_content', $data['meta_content']);
                $model->setData('meta_title', $data['meta_title']);
                $model->setData('author', $data['author']);
                $model->setData('meta_description', $data['meta_description']);
                $model->setData('update_at', $data['created_at']);
                $model->setData('update_at', time());

                $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
                $user = $currentUser->getData();
                $model->setData('author', $user['firstname']);
                $model->save();

            }catch(\Exception $e){ echo $e->getMessage();}

            /* saving data in category_post relation table */

            $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
            $category_data = $collection->getCollection()->addFieldToFilter('post_id', $data['id']);
            $existingIds = $category_data->getColumnValues('cat_id');
            if($data['blog_category'])
                $ids = $data['blog_category'];
            else
                $ids=array();

            $todelete = array_diff($existingIds, $ids);

            foreach ($todelete as $deletekey)
            {
                $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                $datadelete = $collection->getCollection()->addFieldToFilter('cat_id', $deletekey)
                    ->addFieldToFilter('post_id', $data['id']);
                foreach ($datadelete as $deleteid)
                {
                    $deleteid->delete();
                }

            }
            $toSave = array_diff($ids, $existingIds);

            foreach ($toSave as $savekey)
            {
                if($savekey!='') {
                    $categoryModel = $this->_objectManager->create('Ced\Blog\Model\Blogcat')
                        ->getCollection()
                        ->addFieldToFilter('id',$savekey);

                    $catIds = $categoryModel->getColumnValues('id');
                    foreach ($catIds as $_catIds) {
                        $collection_save = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                        $collection_save->setData('post_id', $data['id'])
                            ->setData('cat_id', $_catIds)
                            ->save();
                    }
                }
            }

            /* code end */
            $this->messageManager->addSuccess(__('Post Updated Successfully'));

        } else {
            /*codes for validating if url already exist in database for other post*/
            $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($data['url'],'url');

            if(empty($model->getData())) {
                $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost');
                try {
                    $mediaDirectory = $this->_objectManager->get('\Magento\Framework\Filesystem')
                        ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

                    $path = $mediaDirectory->getAbsolutePath('ced/blog/post');
                    $uploader = $this->_objectManager->create('\Magento\MediaStorage\Model\File\Uploader', array('fileId' => 'featured_image'));
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); // or pdf or anything
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
                    $fileName = $_FILES['featured_image']['name'] . time() . '.' . $extension;
                    $flag = $uploader->save($path, $fileName);
                    $fileName = 'ced/blog/post/' . $fileName;
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                if (isset($_FILES['featured_image']['name'])) {
                    if ($_FILES['featured_image']['name'] != "") {
                        $model->setData('featured_image', $fileName);
                    } else {
                        $model->setData('featured_image', 'ced/blog/post/thumbnail.jpg');
                    }
                }
                try {
                    $model->setData('blog_status', $data['blog_status']);
                    $model->setData('blog_category', json_encode($data['blog_category']));
                    $model->setData('product_id', $data['links']['related']);
                    $model->setData('post_text', $data['post_text']);
                    $model->setData('publish_date', $data['created_at']);
                    $model->setData('title', $data['title']);
                    $model->setData('url', $data['url']);
                    $model->setData('tags', $data['tags']);
                    $model->setData('meta_content', $data['meta_content']);
                    $model->setData('meta_title', $data['meta_title']);
                    $model->setData('author', $data['author']);
                    $model->setData('meta_description', $data['meta_description']);
                    $model->setData('created_at', $data['created_at']);
                    $model->setData('created_at', time());

                    $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
                    $model->setData('author', $currentUser->getUsername());
                    $model->setData('user_id', $currentUser->getId());

                    $model->save();

                    /* saving data in category_post realtion table */
                    $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                    if (is_array($data['blog_category'])) {
                        foreach ($data['blog_category'] as $post) {
                            if (!empty($post)) {
                                /*done by developer */
                                $catCollection = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->getCollection()
                                    ->addFieldToFilter('id', $post);
                                $catIds = $catCollection->getColumnValues('id');
                                foreach ($catIds as $_catIds) {
                                    $collection = $this->_objectManager->create('Ced\Blog\Model\BlogRelation');
                                    $collection->setData('cat_id', $_catIds)
                                        ->setData('post_id', $model['id'])
                                        ->save();
                                }
                                /*end */
                            }
                        }
                    }
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
                /* code end */
                $this->messageManager->addSuccess(__('Post Added Successfully'));
            }
            else{
                $this->messageManager->addErrorMessage('Post url already exist');
                return  $resultRedirect->setPath('blog/post/edit');
            }
        }
        if($this->getRequest()->getParam('back')) {
            return  $resultRedirect->setPath('*/*/edit', ['post_id' => $model['id'], '_current' => true]);
        }
        else
        {
            $this->_redirect('blog/post/index');
        }
    }

}
