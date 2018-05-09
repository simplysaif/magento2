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

namespace Ced\Blog\Block\Adminhtml\BlogPost\Image\Grid\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     *  return image
     */
    public function render(\Magento\Framework\DataObject $row)
    {   $html='';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if(!empty($row->getFeaturedImage())){
            $image = $objectManager->create('\Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$row->getFeaturedImage();
            $_assetRepo = $objectManager->create('\Magento\Framework\View\Asset\Repository');
            $html = '<img id="' . $this->getColumn()->getId() . '" src="'.$image . '"height="' . '50px' . '"width="' . '50px' .'"';
            $html .= '/>';
        }else{
            $_assetRepo = $objectManager->create('\Magento\Framework\View\Asset\Repository');
            $image = $_assetRepo->getUrl('Magento_Catalog::images/product/placeholder/image.jpg');

            $html = '<img id="' . $this->getColumn()->getId() . '" src="'.$image . '"height="' . '50px' . '"width="' . '50px' .'"';
            $html .= '/>';
        }
        return $html;

    }
}