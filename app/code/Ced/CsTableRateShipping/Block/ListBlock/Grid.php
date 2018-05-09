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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsTableRateShipping\Block\ListBlock;

use Magento\Store\Model\Store;
use Magento\Customer\Model\Session;
use Ced\CsMarketplace\Block\Vendor\AbstractBlock;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
  
    protected $_collectionFactory;
    protected $_productFactory;    
    protected $_vproduct;    
    protected $_type;
    protected $pageLayoutBuilder;
    public $_objectManager;
    protected $session;
   

    public function __construct(
    \Magento\Backend\Block\Template\Context $context,
    \Magento\Backend\Helper\Data $backendHelper,
    \Magento\Store\Model\WebsiteFactory $websiteFactory,
    \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
    \Magento\Catalog\Model\ProductFactory $productFactory,
    \Magento\Catalog\Model\Product\Type $type,
    \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
    \Magento\Catalog\Model\Product\Visibility $visibility,
    \Magento\Framework\Module\Manager $moduleManager,
    \Ced\CsMarketplace\Model\Vproducts $vproduct,
    \Magento\Framework\ObjectManagerInterface $objectManager,
    Session $customerSession,
    array $data = []
    ) {
      $this->_websiteFactory = $websiteFactory;
      $this->_collectionFactory = $setsFactory;
      $this->_productFactory = $productFactory;
      $this->_type = $type;
      $this->_status = $status;
      $this->_visibility = $visibility;
      $this->moduleManager = $moduleManager;
      $this->_vproduct = $vproduct;
      $this->_objectManager = $objectManager;
      $this->session = $customerSession;
      parent::__construct($context, $backendHelper, $data);
      $this->setData('area','adminhtml');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('tablerate_grid');
        $this->setDefaultSort('pk');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('pk');
    }

  protected function _prepareCollection()
  {
    
    $vendorId=$this->getVendorId();
    $collection = $this->_objectManager->create('Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Collection')->addFieldToFilter('vendor_id',$vendorId);
    if($this->getRequest()->getParam('sort')){
        $collection->getSelect()->order($this->getRequest()->getParam('sort').' '.$this->getRequest()->getParam('dir'));
    }
    $this->setCollection($collection);
    parent::_prepareCollection();
    return $this;
  }

    
    protected function _prepareColumns()
    {
      $store = $this->_getStore();
        $this->addColumn('pk',
            array(
                'header'=> __('Id'),
                'width' => '80px',
                'type'  => 'text',
                'index' => 'pk',
            ));
        $this->addColumn('dest_country_id',
            array(
                'header'=> __('Destination Country Id'),
                'width' => '80px',
                'type'  => 'text',
                'index' => 'dest_country_id',
            ));
        $this->addColumn('dest_region_id', array(
            'header'=> __('Destination Region Id'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'dest_region_id',
        ));
        $this->addColumn('dest_zip', array(
            'header'=> __('Destination Zip Code'),
            'width' => '80px',
            'type'  => 'text',
            'sortable' => false,
            'index' => 'dest_zip',
        ));
        $this->addColumn('condition_name', array(
            'header'=> __('Condition Name'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'condition_name',
        ));
        $this->addColumn('condition_value', array(
            'header'=> __('Condition Value'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'condition_value',
        ));
        $this->addColumn('price', array(
            'header'=> __('Price'),
            'width' => '80px',
            'type'  => 'text',
            'index' => 'price',
        ));
        return parent::_prepareColumns();
    }
    
    public function getVendorId() {
      return $this->session->getVendorId();
    }
    
    protected function _getStore()
    {
      $storeId = (int)$this->getRequest()->getParam('store', 0);
      return $this->_storeManager->getStore($storeId);
    }
    /**
     * Prepare grid filter buttons
     *
     * @return void
     */
    protected function _prepareFilterButtons()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'action-secondary',
                    'area' => 'adminhtml'
                ]
            )->setDataAttribute(['action' => 'grid-filter-apply'])
        );
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('cstablerateshipping/rates/view', array('id' => $row->getPk()));
    }
    
}