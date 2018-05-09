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

namespace Ced\CsDeal\Block;

use Magento\Store\Model\Store;
use Magento\Customer\Model\Session;
use Ced\CsMarketplace\Block\Vendor\AbstractBlock;

class Dealgrid extends \Magento\Backend\Block\Widget\Grid\Extended {
	
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
    	$this->setId('dealgrid');
    	$this->setDefaultSort('post_id');
    	$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('post_filter');
    }

	protected function _prepareCollection()
	{
		$vendorId=$this->getVendorId();
		$collection = $this->_objectManager->get('Ced\CsDeal\Model\ResourceModel\Deal\Collection')->addFieldToFilter('vendor_id',$vendorId);
		$this->setCollection($collection);
		parent::_prepareCollection();
		return $this;
	}
	
	protected function _prepareMassaction()
	{
        $this->setMassactionIdField('deal_id');
        $this->getMassactionBlock()->setTemplate('Ced_CsDeal::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('deal_id');
	
		$this->getMassactionBlock()->addItem('delete', array(
				'label'=> __('Delete'),
				'url'  => $this->getUrl('*/*/massDelete',array(
						'confirm' => __('Are you sure?')
				))
		));
		$this->getMassactionBlock()->addItem('enable', array(
				'label'=> __('Enable'),
				'url'  => $this->getUrl('*/*/massEnable',array(
						'confirm' => __('Are you sure?')
				))
		));
		$this->getMassactionBlock()->addItem('disable', array(
				'label'=> __('Disable'),
				'url'  => $this->getUrl('*/*/massDisable',array(
						'confirm' => __('Are you sure?')
				))
		));
	
	
		return $this;
	}
    
    protected function _prepareColumns()
    {
    	$store = $this->_getStore();
        $this->addColumn('deal_id',
        		array(
        				'header'=> __('Deal Id'),
        				'width' => '5px',
        				'type'  => 'text',
        				'align'     => 'left',
        				'index' => 'deal_id',
        		));
        
        $this->addColumn('product_name',
        		array(
        				'header'=> __('Product Name'),
        				'width' => '150px',
        				'type'  => 'text',
        				'align'     => 'left',
        				'index' => 'product_name',
        		));
        
        $this->addColumn('deal_price',
				array(
						'header'=> __('Deal Price'),
						'type'  => 'price',
						'width' => '5px',
						'currency_code' => $store->getBaseCurrency()->getCode(),
						'index' => 'deal_price',
						));
        
        $this->addColumn('end_date',
				array(
						'header'=> __('Deal End'),
						'type' => 'datetime',
						'index' => 'end_date',
				));
        
        $this->addColumn('status',
				array(
						'header'=> __('Deal Status'),
						'width' => '5px',
						'type'  => 'options',
						'align'     => 'left',
						'index' => 'status',
						'options' =>$this->_objectManager->get('Ced\CsDeal\Model\Status')->toOptionArray()

						));
        
        $this->addColumn('admin_status',
				array(
						'header'=> __('Admin Status'),
						'width' => '5px',
						'type'  => 'options',
						'align'     => 'left',
						'index' => 'admin_status',
						'options' => $this->_objectManager->get('Ced\CsDeal\Model\Deal')->getMassActionArray(),
						'renderer'=>'Ced\CsDeal\Block\Edit\Tab\Renderer\AdminStatus',
						));

        $this->addColumn('edit_deal',
        		array(
        				'header'=> __('Edit'),
        				'width' => '70px',
        				'index' => 'edit_deal',
        				'sortable' => false,
        				'filter'   => false,
        				'renderer'=>'Ced\CsDeal\Block\Edit\Tab\Renderer\Deal',
        		));
        
            return parent::_prepareColumns();
    }
    
    public function getVendorId() {
    	return $this->session->getVendorId();
    }

    protected function _productStatusFilter($collection, $column){
        if(!strlen($column->getFilter()->getValue())) {
            return $this;
        }
        if($column->getFilter()->getValue()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS.\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED){
            $this->getCollection()
            ->addAttributeToFilter('check_status', array('eq' =>\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS))
            ->addAttributeToFilter('status', array('eq' =>\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED));
        }
        else if($column->getFilter()->getValue()==\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS.\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED){
            $this->getCollection()
            ->addAttributeToFilter('check_status', array('eq' =>\Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS))
            ->addAttributeToFilter('status', array('eq' =>\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED));
        }
        else 
            $this->getCollection()->addAttributeToFilter('check_status', array('eq' =>$column->getFilter()->getValue()));
        return $this;
    }
	
	 /**
     * Filter store condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Framework\DataObject $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
   
    
    protected function _getUrlModelClass()
    {
    	return 'core/url';
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
    /*protected function _prepareFilterButtons()
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
    */
    public function getGridUrl()
    {
    	return $this->getUrl('*/*/listdeal');
    }
    
}
