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
 * @package     Ced_Vbadges
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Ui\Renderer;
 
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
 
class Thumbnail extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * object of store manger class
     * @var storemanager
     */
    protected $_storeManager;
 
    /**
     * @param ContextInterface      $context
     * @param UiComponentFactory    $uiComponentFactory
     * @param StoreManagerInterface $storemanager
     * @param array                 $components
     * @param array                 $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storemanager,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_storeManager = $storemanager;
    }
 
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
   
    public function prepareDataSource(array $dataSource)
    {
    	$mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    	
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as & $item) {
              	$badgeimage = $item['badge_image'];
              	if($badgeimage == null){
              		$imageHelper = $mediaDirectory.'badge/default.png';
              	}
              	else{
                	$imageHelper = $mediaDirectory.'badge/'.$badgeimage;
              	}
                $item[$fieldName . '_src'] = $imageHelper;
                $item[$fieldName . '_alt'] = $badgeimage;
                $item[$fieldName . '_orig_src'] = $imageHelper;
            }
        }
        return $dataSource;
    }
}