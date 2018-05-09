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
  * @package     Ced_Barcode
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */ 
namespace Ced\Barcode\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Cron\Model\Config\Backend\Product\Alert;

/**
 * Class ProductActions
 */
class ProductActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
//	protected $objectmanager;
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
      //  Magento\Framework\App\ObjectManager $objectmanager,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
    //	$this->objectmanager = $objectmanager;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
   
    	public function prepareDataSource(array $dataSource)
  {
  	
  	$model =  \Magento\Framework\App\ObjectManager::getInstance();
  	$enable = $model->create('Ced\Barcode\Helper\Data')->isEnable();
  	if($enable != 0)
  	{
  	
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
            	
            	
            	
            	if($item['type_id'] =="configurable" || $item['type_id'] =="bundle" || $item['type_id'] =="grouped")
            	{
            		$item[$this->getData('name')]['edit'] = [
            		
            		'label' => __('N/A'),
            		 
            		];
            	}
            	
            
            	else{
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl('barcode/barcode/barcode',['id' => $item['entity_id'], 'store' => $storeId]),
                   'label' => __('Generate Barcode'),
               
                    
                ];
            }
            }
        }
  	}
        return $dataSource;
    }
}
