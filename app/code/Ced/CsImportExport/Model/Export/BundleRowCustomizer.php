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
namespace Ced\CsImportExport\Model\Export;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Ced\CsImportExport\Model\Export\RowCustomizerInterface;
use Magento\CatalogImportExport\Model\Import\Product as ImportProductModel;
use Magento\Bundle\Model\ResourceModel\Selection\Collection as SelectionCollection;
use Magento\ImportExport\Controller\Adminhtml\Import;
use Magento\ImportExport\Model\Import as ImportModel;
use \Magento\Catalog\Model\Product\Type\AbstractType;

/**
 * Class RowCustomizer
 */
class BundleRowCustomizer implements RowCustomizerInterface
{
    const BUNDLE_PRICE_TYPE_COL = 'bundle_price_type';

    const BUNDLE_SKU_TYPE_COL = 'bundle_sku_type';

    const BUNDLE_PRICE_VIEW_COL = 'bundle_price_view';

    const BUNDLE_WEIGHT_TYPE_COL = 'bundle_weight_type';

    const BUNDLE_VALUES_COL = 'bundle_values';

    const VALUE_FIXED = 'fixed';

    const VALUE_DYNAMIC = 'dynamic';

    const VALUE_PERCENT = 'percent';

    const VALUE_PRICE_RANGE = 'Price range';

    const VALUE_AS_LOW_AS = 'As low as';

    /**
     * Mapping for bundle types
     *
     * @var array
     */
    protected $typeMapping = [
        '0' => self::VALUE_DYNAMIC,
        '1' => self::VALUE_FIXED
    ];

    /**
     * Mapping for price views
     *
     * @var array
     */
    protected $priceViewMapping = [
        '0' => self::VALUE_PRICE_RANGE,
        '1' => self::VALUE_AS_LOW_AS
    ];

    /**
     * Mapping for price types
     *
     * @var array
     */
    protected $priceTypeMapping = [
        '0' => self::VALUE_FIXED,
        '1' => self::VALUE_PERCENT
    ];

    /**
     * Bundle product columns
     *
     * @var array
     */
    protected $bundleColumns = [
        self::BUNDLE_PRICE_TYPE_COL,
        self::BUNDLE_SKU_TYPE_COL,
        self::BUNDLE_PRICE_VIEW_COL,
        self::BUNDLE_WEIGHT_TYPE_COL,
        self::BUNDLE_VALUES_COL
    ];

    /**
     * Product's bundle data
     *
     * @var array
     */
    protected $bundleData = [];
    protected $_objectManager;
    /**
     * Column name for shipment_type attribute
     *
     * @var string
     */
    private $shipmentTypeColumn = 'bundle_shipment_type';

    /**
     * Mapping for shipment type
     *
     * @var array
     */
    private $shipmentTypeMapping = [
        AbstractType::SHIPMENT_TOGETHER => 'together',
        AbstractType::SHIPMENT_SEPARATELY => 'separately',
    ];

    /**
     * Retrieve list of bundle specific columns
     * @return array
     */
    public function __construct(
    		\Magento\Framework\ObjectManagerInterface $objectInterface	
    ){
    	$this->_objectManager=$objectInterface;
    }
    
    
    private function getBundleColumns()
    {
        return array_merge($this->bundleColumns, [$this->shipmentTypeColumn]);
    }

    /**
     * Prepare data for export
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param int[] $productIds
     * @return $this
     */
    public function prepareData($collection, $productIds)
    {
    	
        $productCollection = clone $collection;
        $productCollection->addAttributeToFilter(
            'entity_id',
            ['in' => $productIds]
        )->addAttributeToFilter(
            'type_id',
            ['eq' => \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE]
        );

        return $this->populateBundleData($productCollection);
    }

    /**
     * Set headers columns
     *
     * @param array $columns
     * @return array
     */
    public function addHeaderColumns($columns)
    {
        $columns = array_merge($columns, $this->getBundleColumns());

        return $columns;
    }

    /**
     * Add data for export
     *
     * @param array $dataRow
     * @param int $productId
     * @return array
     */
    public function addData($dataRow, $productId)
    {
    	
        if (!empty($this->bundleData[$productId])) {
            $dataRow = array_merge($this->cleanNotBundleAdditionalAttributes($dataRow), $this->bundleData[$productId]);
        }

        return $dataRow;
    }

    /**
     * Calculate the largest links block
     *
     * @param array $additionalRowsCount
     * @param int $productId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }

    /**
     * Populate bundle product data
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    protected function populateBundleData($collection)
    {
    	//print_r($collection->getData());die;
        foreach ($collection->getData() as $product) {
        	$products = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($product['entity_id']);
            $id = $product['entity_id'];
            $this->bundleData[$id][self::BUNDLE_PRICE_TYPE_COL] = $this->getTypeValue($products->getPriceType());
            $this->bundleData[$id][$this->shipmentTypeColumn] = $this->getShipmentTypeValue(
                $products->getShipmentType()
            );
            $this->bundleData[$id][self::BUNDLE_SKU_TYPE_COL] = $this->getTypeValue($products->getSkuType());
            $this->bundleData[$id][self::BUNDLE_PRICE_VIEW_COL] = $this->getPriceViewValue($products->getPriceView());
            $this->bundleData[$id][self::BUNDLE_WEIGHT_TYPE_COL] = $this->getTypeValue($products->getWeightType());
            $this->bundleData[$id][self::BUNDLE_VALUES_COL] = $this->getFormattedBundleOptionValues($products);
        }
        return $this;
    }

    /**
     * Retrieve formatted bundle options
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function getFormattedBundleOptionValues($product)
    {
        /** @var \Magento\Bundle\Model\ResourceModel\Option\Collection $optionsCollection */
        $optionsCollection = $product->getTypeInstance()
            ->getOptionsCollection($product)
            ->setOrder('position', Collection::SORT_ORDER_ASC);

        $bundleData = '';
        foreach ($optionsCollection as $option) {
            $bundleData .= $this->getFormattedBundleSelections(
                $this->getFormattedOptionValues($option),
                $product->getTypeInstance()
                    ->getSelectionsCollection([$option->getId()], $product)
                    ->setOrder('position', Collection::SORT_ORDER_ASC)
            );
        }
//print_r(rtrim($bundleData, ImportProductModel::PSEUDO_MULTI_LINE_SEPARATOR));die('cssss');
        return rtrim($bundleData, ImportProductModel::PSEUDO_MULTI_LINE_SEPARATOR);
    }

    /**
     * Retrieve formatted bundle selections
     *
     * @param string $optionValues
     * @param SelectionCollection $selections
     * @return string
     */
    protected function getFormattedBundleSelections($optionValues, SelectionCollection $selections)
    {
        $bundleData = '';
        $selections->addAttributeToSort('position');
        foreach ($selections as $selection) {
            $selectionData = [
                'sku' => $selection->getSku(),
                'price' => $selection->getSelectionPriceValue(),
                'default' => $selection->getIsDefault(),
                'default_qty' => $selection->getSelectionQty(),
                'price_type' => $this->getPriceTypeValue($selection->getSelectionPriceType())
            ];
            $bundleData .= $optionValues
                . ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR
                . implode(
                    ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                    array_map(
                        function ($value, $key) {
                            return $key . ImportProductModel::PAIR_NAME_VALUE_SEPARATOR . $value;
                        },
                        $selectionData,
                        array_keys($selectionData)
                    )
                )
                . ImportProductModel::PSEUDO_MULTI_LINE_SEPARATOR;
        }

        return $bundleData;
    }

    /**
     * Retrieve option value of bundle product
     *
     * @param \Magento\Bundle\Model\Option $option
     * @return string
     */
    protected function getFormattedOptionValues($option)
    {
        return 'name' . ImportProductModel::PAIR_NAME_VALUE_SEPARATOR
        . $option->getTitle() . ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR
        . 'type' . ImportProductModel::PAIR_NAME_VALUE_SEPARATOR
        . $option->getType() . ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR
        . 'required' . ImportProductModel::PAIR_NAME_VALUE_SEPARATOR
        . $option->getRequired();
    }

    /**
     * Retrieve bundle type value by code
     *
     * @param string $type
     * @return string
     */
    protected function getTypeValue($type)
    {
        return isset($this->typeMapping[$type]) ? $this->typeMapping[$type] : self::VALUE_DYNAMIC;
    }

    /**
     * Retrieve bundle price view value by code
     *
     * @param string $type
     * @return string
     */
    protected function getPriceViewValue($type)
    {
        return isset($this->priceViewMapping[$type]) ? $this->priceViewMapping[$type] : self::VALUE_PRICE_RANGE;
    }

    /**
     * Retrieve bundle price type value by code
     *
     * @param string $type
     * @return string
     */
    protected function getPriceTypeValue($type)
    {
        return isset($this->priceTypeMapping[$type]) ? $this->priceTypeMapping[$type] : null;
    }

    /**
     * Retrieve bundle shipment type value by code
     *
     * @param string $type
     * @return string
     */
    private function getShipmentTypeValue($type)
    {
        return isset($this->shipmentTypeMapping[$type]) ? $this->shipmentTypeMapping[$type] : null;
    }

    /**
     * Remove bundle specified additional attributes as now they are stored in specified columns
     *
     * @param array $dataRow
     * @return array
     */
    protected function cleanNotBundleAdditionalAttributes($dataRow)
    {
        if (!empty($dataRow['additional_attributes'])) {
            $additionalAttributes = explode(
                ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR,
                $dataRow['additional_attributes']
            );
            $dataRow['additional_attributes'] = $this->getNotBundleAttributes($additionalAttributes);
        }

        return $dataRow;
    }

    /**
     * Retrieve not bundle additional attributes
     *
     * @param array $additionalAttributes
     * @return string
     */
    protected function getNotBundleAttributes($additionalAttributes)
    {
        $cleanedAdditionalAttributes = '';
        foreach ($additionalAttributes as $attribute) {
            list($attributeCode, $attributeValue) = explode(ImportProductModel::PAIR_NAME_VALUE_SEPARATOR, $attribute);
            if (!in_array('bundle_' . $attributeCode, $this->getBundleColumns())) {
                $cleanedAdditionalAttributes .= $attributeCode
                    . ImportProductModel::PAIR_NAME_VALUE_SEPARATOR
                    . $attributeValue
                    . ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR;
            }
        }

        return rtrim($cleanedAdditionalAttributes, ImportModel::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR);
    }
}
