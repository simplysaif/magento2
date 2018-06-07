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

namespace Ced\Blog\Controller\Adminhtml\User;
use Magento\Framework\Exception\AuthenticationException;
class Save extends \Magento\User\Controller\Adminhtml\User\Save
{

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
       if(array_key_exists('user_id',$data)){
           $userId = (int)$this->getRequest()->getParam('user_id');
       }
        $data = $this->getRequest()->getPostValue();
        $blog_category = implode(',', $data['blog_category']);

        if (!$data) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $model = $this->_userFactory->create();
        if(array_key_exists('user_id',$data)){
            $model = $this->_userFactory->create()->load($userId);
            if ($userId && $model->isObjectNew()) {
                $this->messageManager->addError(__('This user no longer exists.'));
                $this->_redirect('adminhtml/*/');
                return;
            }
        }
        $model->setData($this->_getAdminUserData($data));
        $uRoles = $this->getRequest()->getParam('roles', []);
        if (count($uRoles)) {
            $model->setRoleId($uRoles[0]);
        }
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        if(array_key_exists('user_id',$data)){
            if ($userId == $currentUser->getId() && $this->_objectManager->get(
                    'Magento\Framework\Validator\Locale')->isValid($data['interface_locale']))
            {
                $this->_objectManager->get(
                    'Magento\Backend\Model\Locale\Manager')->switchBackendInterfaceLocale($data['interface_locale']);
            }
        }
        /**
         *
         * Before updating admin user data, ensure that password of current admin user is entered and is correct
         */

        $currentUserPasswordField = \Magento\User\Block\User\Edit\Tab\Main::CURRENT_USER_PASSWORD_FIELD;

        $isCurrentUserPasswordValid = isset($data[$currentUserPasswordField]) && !empty($data[$currentUserPasswordField]) &&
            is_string($data[$currentUserPasswordField]);
        try {
            if (!($isCurrentUserPasswordValid && $currentUser->verifyIdentity($data[$currentUserPasswordField]))) {
                throw new AuthenticationException(__('You have entered an invalid password for current user.'));
            }
            $model->save();

            /* saving data in blog user */

            if (array_key_exists('user_id', $data)){
                $collection = $this->_objectManager->create('Ced\Blog\Model\User')->load($data['user_id'],'user_id');
                $blogUserId = $collection->getId();
                $newcollection = $this->_objectManager->create('Ced\Blog\Model\User')->load($blogUserId);
                if($_FILES['profile']['name'] !="") {
                    try
                    {
                        $mediaDirectory = $this->_objectManager->get('\Magento\Framework\Filesystem')
                            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        $path = $mediaDirectory->getAbsolutePath('ced/blog/user');
                        $uploader = $this->_objectManager->create('\Magento\MediaStorage\Model\File\Uploader', array('fileId' =>'profile'));
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $extension = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
                        $fileName = $_FILES['profile']['name'].time().'.'.$extension;
                        $flag = $uploader->save($path, $fileName);
                        chmod($path."/".$fileName, 0777);

                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }

                    $fileName = 'ced/blog/user/'.$fileName;
                    $newcollection->setData('profile', $fileName);
                } else {
                    $user_model = $newcollection->load($model->getId());
                    $img = $user_model['profile'];
                    $newcollection->setData('profile', $img);
                }

                $newcollection->setData('user_id', $model->getId())
                    ->setData('blog_category', $blog_category)
                    ->setData('about', $data['about'])
                    ->save();
            }
            else{
                $collectionNew = $this->_objectManager->create('Ced\Blog\Model\User');
                if($_FILES['profile']['name'] !="") {
                    try
                    {
                        $mediaDirectory = $this->_objectManager->get('\Magento\Framework\Filesystem')
                            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                        $path = $mediaDirectory->getAbsolutePath('ced/blog/user');
                        $uploader = $this->_objectManager->create('\Magento\MediaStorage\Model\File\Uploader', array('fileId' =>'profile'));
                        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); // or pdf or anything
                        $uploader->setAllowRenameFiles(false);
                        $uploader->setFilesDispersion(false);
                        $extension = pathinfo($_FILES['profile']['name'], PATHINFO_EXTENSION);
                        $fileName = $_FILES['profile']['name'].time().'.'.$extension;
                        $flag = $uploader->save($path, $fileName);
                        chmod($path."/".$fileName, 0777);

                    } catch (\Exception $e) {
                        echo $e->getMessage();
                    }

                    $fileName = 'ced/blog/user/'.$fileName;
                    $collectionNew->setData('profile', $fileName);
                } else {
                    $user_model = $collectionNew->load($model->getId());
                    $img = $user_model['profile'];
                    $collectionNew->setData('profile', $img);
                }

                $collectionNew->setData('user_id', $model->getId())
                    ->setData('blog_category', $blog_category)
                    ->setData('about', $data['about'])
                    ->save();
            }
            /* end */

            $this->messageManager->addSuccess(__('You saved the user.'));
            $this->_getSession()->setUserData(false);
            $this->_redirect('adminhtml/*/');
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($model, $data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($model, $data);
        }
    }

    /**
     * @param \Magento\User\Model\User $model
     * @param array $data
     * @return void
     */

    protected function redirectToEdit(\Magento\User\Model\User $model, array $data)
    {
        $this->_getSession()->setUserData($data);
        $arguments = $model->getId() ? ['user_id' => $model->getId()] : [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        $this->_redirect('adminhtml/*/edit', $arguments);
    }
}

