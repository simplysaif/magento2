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
namespace Ced\CsImportExport\Block\Export;

use Magento\Framework\Exception\FileSystemException;
class Image extends \Magento\Backend\Block\Widget\Container
{
    
    /**
     * Internal constructor
     *
     * @return void
     */
    protected $path;
    
    /**
     * File factory
     *
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    protected $fileFactory;
    
    /**
     * Filesystem driver
     *
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    protected $driver;
    
    protected $_customerSesssion;
    public $_objectManager;
    
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context, 
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem\File\ReadFactory $fileFactory,
        array $data = []
    ) {
    
        $this->buttonList = $context->getButtonList();
        $this->toolbar = $context->getButtonToolbar();
        $this->_customerSession=$customerSession;
        $this->_objectManager = $objectManager;
        $this->fileFactory = $fileFactory;
        parent::__construct($context, $data);
    }
    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Image Uploader');
    }
    
     
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Ced_CsImportExport::export/form/view.phtml');
    }
    
    public function VendorId()
    {
        
        return $this->_customerSession->getVendorId();
    }
     
    
    public function read()
    {
        $vendor = $this->_customerSession->getVendorId();
        $path = $this->_objectManager->get('\Magento\Framework\Filesystem')
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $path->getAbsolutePath('import/'.$vendor.'/');
        $dir    = $path;
        $directories = glob($path .'*', GLOB_ONLYDIR);
        
        if(empty($directories)) {
            return;
        }
        foreach($directories as $file)
        {
            if(is_dir($file)) {
                
                $direc=glob($file .'/*', GLOB_ONLYDIR);
                
                foreach($direc as $fl)
                {
                    
                    $allFileFolder[] = glob($fl . '/*');
                }
                
            }
        }
        if(empty($allFileFolder)) {
            return;
        }
        $array_meged=array();
        $mediapath =[];
        foreach($allFileFolder as $key=> $images)
        {
            if(empty($images)) {
                continue;
            }
            foreach($images as $img){
                if(empty($img)) {
                    continue;
                }
                $temp = $img;
                $mediapath[]=explode('media/', $temp);
            }           
            
        }
        if(empty($mediapath)) {
            return;
        }
        foreach($mediapath as $temp)
        {
            $pathofimage=$temp[1];
            $image[]=$this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$pathofimage;
            
        }
        return $image;
    }
}
