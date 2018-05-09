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
  * @package   Ced_CsImportExport
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsImportExport\Controller\Import;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\Controller\ResultFactory;

class Uploadimage extends \Ced\CsMarketplace\Controller\Vendor
{
    
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }

    /**
     * Image Uploading
     *
     * @return null
     */
    public function execute()
    {
        $vendorId = $this->session->getVendorId();
       
    $path =$this->_objectManager->get('\Magento\Framework\Filesystem')
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $path->getAbsolutePath('import/'.$vendorId);
       
        if($vendorId) {    
            if(!empty($_FILES['file_upload']['name'])) {
                foreach($_FILES['file_upload']['name']  as $key =>$image)
                {
                    if (!empty($image)) {
                        try {
                            $broken = str_split($image, 1);
                             $uploader = $this->_objectManager->create(
                                 '\Magento\MediaStorage\Model\File\Uploader', array(
                                 'fileId' =>  "file_upload[{$key}]",    
                                 )
                             );
                             $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'png','csv','pdf','zip','ods','docx','rar','mp3','mp4','xlsx'));
                             $uploader->setAllowRenameFiles(false);
                             $uploader->setFilesDispersion(true);
                             $uploader->save($path);
                        } 
                        catch (\Exception $e)
                        {
                           echo __($e->getMessage());
                        }
                    }
                }
            }
           echo __('No Images To Upload.');
        }
        else
        {
           echo __('Error Occured While Uploading Image.');  
        }     
    }    
}
