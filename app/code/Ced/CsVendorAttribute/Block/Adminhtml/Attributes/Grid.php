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
  * @package   Ced_CsVendorAttribute
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsVendorAttribute\Block\Adminhtml\Attributes;
 
class Grid extends \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $_gridFactory;
 
    protected $_objectManager;

    protected $_status;
 
    protected $backendHelper;
    
    protected $_resource;
    
    const VAR_NAME_FILTER = 'vendor_attribute';
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param \Magento\Framework\Module\Manager       $moduleManager
     * @param array                                   $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        
      
        $this->_objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resource;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
          parent::_construct();
        $this->setId('attributeGrid');
        $this->setDefaultSort('Asc');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        // $this->setUseAjax(true);
        $this->setVarNameFilter('vendor_attribute');
    }
 
    /**
     * @return $this
     */
    
    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $manager=$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        return $manager->getStore($storeId);
    } 
    protected function _prepareCollection()
    {

        $store = $this->_getStore();
        $this->setStoreId($store->getId());

        $collection = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')->getCollection();//Mage::getModel('eav/entity_attribute')->getCollection();
        $typeId = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->getEntityTypeId();//Mage::getModel('csmarketplace/vendor')->getEntityTypeId();
        $collection = $collection->addFieldToFilter('entity_type_id', array('eq'=>$typeId));
    
        $tableName = $this->_resource->getTableName('ced_csmarketplace_vendor_form_attribute');//Mage::getSingleton('core/resource')->getTableName('csmarketplace/vendor_form');
        $collection->getSelect()->joinLeft(array('vform'=>$tableName), 'main_table.attribute_id = vform.attribute_id && vform.store_id ='.$this->getStoreId(), array('is_visible'=> 'vform.is_visible', 'use_in_registration'=>'vform.use_in_registration', 'use_in_left_profile'=>'vform.use_in_left_profile','use_in_invoice'=> 'vform.use_in_invoice'));
      
        $this->setCollection($collection);
      
        parent::_prepareCollection();
       
        return $this;
    }
 
    protected function _prepareColumns() 
    {
        
        parent::_prepareColumns();
        $this->getColumn('attribute_code')->setFilterIndex('main_table.attribute_code');
        $this->addColumnAfter(
            'is_visible', array(
            'header'=>__('Use in Edit Form'),
            'sortable'=>true,
            'index'=>'is_visible',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'align' => 'center',
            /* 'filter_index' => 'vform.is_visible', */
            ), 'frontend_label'
        );

        $this->addColumnAfter(
            'use_in_registration', array(
            'header'=>__('Use in Registration Form'),
            'sortable'=>true,
            'index'=>'use_in_registration',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ), 
            'align' => 'center',
            /* 'filter_index' => 'vform.is_visible', */
            ), 'is_visible'
        );

        $this->addColumnAfter(
            'use_in_invoice', array(
            'header'=>__('Use in Seller Invoice'),
            'sortable'=>true,
            'index'=>'use_in_invoice',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ), 
            'align' => 'center',
            /* 'filter_index' => 'vform.is_visible', */
            ), 'use_in_invoice'
        );
        
        $this->addColumnAfter(
            'use_in_left_profile', array(
            'header'=>__('Use in Left Profile'),
            'sortable'=>true,
            'index'=>'use_in_left_profile',
            'type' => 'options',
            'options' => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
            'align' => 'center',
            /* 'filter_index' => 'vform.is_visible', */
            ), 'use_in_registration'
        );
        
        return $this;
    }
    
    public function getGridUrl() 
    {
        return $this->getUrl('*/*/grid', array('_secure'=>true, '_current'=>true));
    } 
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['attribute_id' => $row->getAttributeId()]);
    }
}