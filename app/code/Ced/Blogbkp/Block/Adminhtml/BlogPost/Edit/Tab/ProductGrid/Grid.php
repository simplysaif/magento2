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

 namespace Ced\Blog\Block\Adminhtml\BlogPost\Edit\Tab\ProductGrid;

 class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
 {

    /**
     * @var \Magento\Framework\Module\Manager
     */

    protected $moduleManager;

    /**
     * @var gridFactory
     */

    protected $_gridFactory;

    /**
     * @var status
     */

    protected $_status;

    /**
     * @var _productFactory
     */
    protected $_productFactory;

    /**
     * @param Magento\Backend\Block\Template\Context
     * @param Magento\Backend\Block\Template\Context
     * @param Magento\Backend\Helper\Data
     * @param Magento\Framework\Module\Manager
     * @param Magento\Catalog\Model\ProductFactory
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
        )
    {
        $this->moduleManager = $moduleManager;
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;    
        parent::__construct($context, $backendHelper, $data);

    }
    /**
     * @return void
     */

    protected function _construct()
    {    

        parent::_construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('attribute_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }

    /**
     * @return $this
     */

    protected function _prepareCollection()
    {          

        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'sku'
            )->addAttributeToSelect(
            'name'
            )->addAttributeToSelect(
            'attribute_set_id'
            )->addAttributeToSelect(
            'type_id'
            );
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left');
            $this->setCollection($collection);
            parent::_prepareCollection();
            return $this;

        }

    /**
     * Retrieve currently edited product model
     * @return array|null
     */

    public function getProduct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $selected = $objectManager->create('Magento\Catalog\Model\Product')->load(3);
        return $selected;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    protected function _prepareColumns()
    {

        $this->addColumn(
            'product_id', array (
             'header_css_class' => 'a-center',
             'type' => 'checkbox',
             'field_name' => 'product_id[]',
             'values' => $this->_getSelectedProducts(),
             'align' => 'center',
             'index' => 'entity_id'
             ) 
            );

        $this->addColumn(
            'entity_id',
            [
            'header' => __('ID'),
            'index' => 'entity_id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
            ); 

        $this->addColumn(
            'name',
            [
            'header' => __('Name'),
            'index' => 'name',
            ]
            );

        $this->addColumn(
            'type_id',
            [
            'header' => __('Type'),
            'index' => 'type_id',
            ]
            );

        $this->addColumn(
            'Featured_image',
            [
            'header' => __('sku'),
            'index' => 'sku',
            ]
            );

        $this->addColumn(
            'price',
            [
            'header' => __('Price'),
            'index' => 'price',
            ]
            );

        $this->addColumn(
            'position',
            [
            'header' => __('Position'),
            'name' => 'position',
            'type' => 'number',
            'validate_class' => 'validate-number',
            'index' => 'entity_id',
            'editable' => true,
            'edit_only' => true,
            'header_css_class' => 'col-position',
            'column_css_class' => 'col-position'
            ]
            );
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }    

    /**
     * Retrieve selected related products
     * @return array
     */

    protected function _getSelectedProducts()
    {

        $products = $this->getProductsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }

    /**
     * Retrieve related products
     *
     * @return array
     */

    public function getSelectedRelatedProducts()
    {
        $products = [];
        $ids = explode("&", $this->_coreRegistry->registry('current_post')->getProductId());
        foreach ($ids as $product) {
            $data = explode("=", $product);
            $position = 0;
            if(isset($data[1])) {
                $position = substr(base64_decode($data[1]), 9, 15); 
            }
            $products[$data[0]] = ['position' => $position];  
        }
        return $products;
    }

    /**
     * Add filter
     * @param Column $column
     * @return $this
     */

    protected function _addColumnFilterToCollection($column)
    {

        // Set custom filter for in product flag

        if ($column->getId() == 'product_id') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return string
     */

    public function getGridUrl()
    {
        return $this->getUrl('blog/*/relatedproduct', ['_current' => true]);
    }
}
