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
 * @package     Ced_CsDeal
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeal\Block\Adminhtml\Vdeals;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $moduleManager;
    protected $_messagingFactory;
    protected $_status;
    protected $backendHelper;
    public $_objectManager;
    
    public function __construct(
            
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsDeal\Model\DealFactory $messagingFactory,
        //\Cedcoss\Grid\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
         $this->_messagingFactory = $messagingFactory;
       // $this->_status = $status;
        $this->moduleManager = $moduleManager;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('chat_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }
 
    protected function _prepareCollection()
    {
        
        $collection = $this->_messagingFactory->create()->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
 
    protected function _prepareColumns()
    {
          $this->addColumn('deal_id',
            array(
                'header'=> __('Deal ID'),
                'width' => '10px',
                'type'  => 'number',
                  'align'     => 'left',
                'index' => 'deal_id',
        ));
        $this->addColumn('product_id',
            array(
                'header'=> __('Product ID'),
                'width' => '10px',
                'type'  => 'number',
                'align'     => 'left',
                'index' => 'product_id',
        ));
         $this->addColumn('product_name',
            array(
                'header'=> __('Product Name'),
                'width' => '200px',
                'align'     => 'left',
                'index' => 'product_name',
                /*'renderer' => 'Ced\CsDeal\Block\Adminhtml\Vdeals\Renderer\Productname',*/
        ));
        $this->addColumn('vendor_id',
            array(
            'header'    => __('Vendor Id'),
            'align'     => 'left',
            'width' => '10px',
            'index'     => 'vendor_id',
        ));
        $this->addColumn('vendor_id',
                array(
              'header'    => __('Vendor Name'),
                'align'     => 'left',
              'width' => '300px',
                'index'     => 'vendor_id',
                  'renderer' => 'Ced\CsDeal\Block\Adminhtml\Vdeals\Renderer\Vendorname',
              'filter_condition_callback' => array($this, '_vendornameFilter'),
        ));
        $this->addColumn('status',
            array(
            'header'    => __('Status'),
            'align'     => 'left',
            'width' => '80px',
            'index'     => 'status',
           ));
        $store=$this->_getStore();
        $this->addColumn('product_price',
            array(
                'header'=> __('Product Price'),
                'width' => '80px',
                'type'      =>'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'align'     => 'left',
                'index' => 'product_id',
                'renderer' => 'Ced\CsDeal\Block\Adminhtml\Vdeals\Renderer\Productprice',
        ));
        $this->addColumn('deal_price', array(
          'header' => __('Deal Price'),
          'width' => '80px',
          'index' => 'deal_price',
          'type'  => 'currency',
          'type'      =>'price',
          'currency_code' => $store->getBaseCurrency()->getCode(), 
        ));
        $this->addColumn('action',
                array(
                        'header'    => __('Action'),
                        'type'      => 'text',
                        'width' => '120px',
                        'align'     => 'center',
                        'filter'    => false,
                        'sortable'  => false,
                        'renderer'=>'Ced\CsDeal\Block\Adminhtml\Vdeals\Renderer\Action',
                        'index'     => 'action',
                        )); 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }
 
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
    
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    protected function _vendornameFilter($collection, $column){
    if (!$value = $column->getFilter()->getValue()) {
        return $this;
    }
    $vendorIds = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->getCollection()
    ->addAttributeToFilter('name', array('like' => '%'.$column->getFilter()->getValue().'%'))
    ->getAllIds();
  
    if(count($vendorIds)>0)
        $this->getCollection()->addFieldToFilter('vendor_id', array('in', $vendorIds));
    else{
        $this->getCollection()->addFieldToFilter('vendor_id');
    }
    return $this;
  }
 
    
   
}