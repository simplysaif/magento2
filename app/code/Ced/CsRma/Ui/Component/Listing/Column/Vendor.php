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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Vendor extends Column
{

    /**
    * @var   \Magento\Framework\ObjectManagerInterface $objectManager
    */

    protected $objectManager;

    /** @var UrlInterface */

    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->objectManager = $objectManager;
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['vendor_id'])) {
                    $vendorModel = $this->objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($item['vendor_id']);
                    $vendorName = $vendorModel->getName();
                    $url = $this->urlBuilder->getUrl('csmarketplace/vendor/edit', ['vendor_id' => $item['vendor_id']]);
                    $item[$name.'_html'] = '<a href="'.$url.'" target="_blank">'.$vendorName.'</a>';
                }
            }
        }
        return $dataSource;
    }
}
