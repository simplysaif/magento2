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
 * @category    Ced
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Profilesave extends \Ced\CsMarketplace\Controller\Vendor
{

    protected $resultPageFactory;

    public $_customerSession;

    protected $_scopeConfig;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        CustomerRepositoryInterface $customerRepository,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }
        
    
    /**
     * Promo quote edit action
     *
     * @return                                  void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId())
            return;
        if ($this->getRequest()->getPost()) {
            $subvendor = $this->_getSession()->getSubVendorData();
            $model = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount');
            if($id = $subvendor['id']) {
                try{
                    $model->load($id);
                    $post = $this->getRequest()->getParams();
                    $data = array();
                    $data['first_name'] = $post['first_name'];
                    $data['last_name'] = $post['last_name'];
                    if(isset($post['image_delete'])){
                        $data['profile_image'] = '';
                    }
                    
                    /*
                    *upload image
                    */
                    if(!empty($_FILES['image']['name']) && strlen($_FILES['image']['name'])>0) {
                        try 
                        {
                            $fieldName = $_FILES['image']['name'];
                            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                            $path = $mediaDirectory->getAbsolutePath('ced/cssubaccount/images/'.$id);
                            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => "image"));
                            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png')); 
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(false);
                            $fileData = $uploader->validateFile(); 
                            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                            $fileName = $fieldName;
                            $flag = $uploader->save($path, $fileName);
                            $data['profile_image'] ='ced/cssubaccount/images/'.$id.'/'.$_FILES['image']['name'];
                            if(strlen($data['profile_image']) == 0){
                                $data['profile_image']='noimage';
                            }
                        }
                catch(\Exception $e) 
                {
                    $this->messageManager->addError("Image Not Uploaded");
                    $this->_redirect("*/*/profile");
                    return;
                }       
            }
                    $model->addData($data);
                    $model->save();
                    $this->messageManager->addSuccess(__('The profile information has been saved.'));
                    $this->_redirect('*/*/profile');
                    return;
                }
                catch(\Exception $e){
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/profile');
                    return;
                }
            }

        }
        $this->messageManager->addError(__('Unable to find sub-vendor to save'));
        $this->_redirect('*/*/profile');

    }
    
}
