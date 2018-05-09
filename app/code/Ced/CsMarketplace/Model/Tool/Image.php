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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Model\Tool;
 
class Image extends \Magento\Catalog\Model\Product\Image
{

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Media\Config $catalogProductMediaConfig,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context, $registry, $storeManager, $catalogProductMediaConfig, $coreFileStorageDatabase, $filesystem,  $imageFactory, $assetRepo, $viewFileSystem, $scopeConfig, null, null, $data);
        $this->_objectManager = $objectManager;
    }


    /**
     * Set filenames for base file and new file
     *
     * @param  string $file
     * @return Mage_Catalog_Model_Product_Image
     */
    public function setBaseFile($file)
    {
        $this->_isBaseFilePlaceholder = false;
        
        if (($file) && (0 !== strpos($file, '/', 0))) {
            $file = '/' . $file;
        }       
        
        if ('/no_selection' == $file) {
            $file = null;
        }
        $baseFile = $this->_mediaDirectory->getAbsolutePath($file);
        if ($file) {            
            if ((!$this->_fileExists($file)) || !$this->_checkMemory($file)) {                
                $file = null;
            }
        }
        if (!$file) {
            $attr = $this->getDestinationSubdir();
            
            if($attr == "logo" && $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder', $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId())) {
                $imgpath = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vshops/general/vshoppage_vendor_placeholder', $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());            
                $skinPlaceholder = "/ced/csmarketplace/".$imgpath;
                $file = $skinPlaceholder;
                $this->_isBaseFilePlaceholder = true;
            }
            else if($attr == "banner" && $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder', $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId())) {
                $imgpath = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vshops/general/vshoppage_banner_placeholder', $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());
                $skinPlaceholder = "/ced/csmarketplace/".$imgpath;
                $file = $skinPlaceholder;
                $this->_isBaseFilePlaceholder = true;
            }
            else{
                // check if placeholder defined in config
                $isConfigPlaceholder = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig("catalog/placeholder/{$this->getDestinationSubdir()}_placeholder");
                $configPlaceholder   = '/placeholder/' . $isConfigPlaceholder;                
                if (0 && $isConfigPlaceholder && $this->_fileExists($configPlaceholder)) {
                    $file = $configPlaceholder;
                }
                else {                
                    $this->_newFile = true;
                    $this->_isBaseFilePlaceholder = true;
                    return $this;
                }
            }
        }
        
        if ((!$file) || (!$this->_mediaDirectory->isFile($file))) {
            throw new \Exception(__('Image file was not found.'));
        }
        $this->_baseFile = $file;
        // build new filename (most important params)
        $path = array(
            $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config')->getBaseMediaPath(),
            'cache',
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId(),
            $path[] = $this->getDestinationSubdir()
        );
        if((!empty($this->_width)) || (!empty($this->_height))) {
            $path[] = "{$this->_width}x{$this->_height}"; 
        }
        
        // add misk params as a hash
        $miscParams = array(
                ($this->_keepAspectRatio  ? '' : 'non') . 'proportional',
                ($this->_keepFrame        ? '' : 'no')  . 'frame',
                ($this->_keepTransparency ? '' : 'no')  . 'transparency',
                ($this->_constrainOnly ? 'do' : 'not')  . 'constrainonly',
                $this->_rgbToString($this->_backgroundColor),
                'angle' . $this->_angle,
                'quality' . $this->_quality
        );

        // if has watermark add watermark params to hash
        if ($this->getWatermarkFile()) {
            $miscParams[] = $this->getWatermarkFile();
            $miscParams[] = $this->getWatermarkImageOpacity();
            $miscParams[] = $this->getWatermarkPosition();
            $miscParams[] = $this->getWatermarkWidth();
            $miscParams[] = $this->getWatermarkHeigth();
        }

        $path[] = md5(implode('_', $miscParams));
        
        // append prepared filename
        $this->_newFile = implode('/', $path) . $file;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->_newFile === true) {    
            $url = $this->_assetRepo->getUrl(
                "Ced_CsMarketplace::images/ced/csmarketplace/vendor/placeholder/{$this->getDestinationSubdir()}.jpg"
            );
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ) . $this->_newFile;
        }
        return $url;
    }
    
    
}
