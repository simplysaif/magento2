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

namespace Ced\Blog\Block\Adminhtml\Blogcat\Category\Grid\Renderer;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * Renderer
     *
     * @return string
     */

    public function render(\Magento\Framework\DataObject $row)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $image = $objectManager->create('\Magento\Store\Model\StoreManagerInterface')
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$row->getProfile();
        $html = '<img id="' . $this->getColumn()->getId() . '" src="'.$image . '"height="' . '50px' . '"';
        $html .= '/>';
        return $html;
    }
}
