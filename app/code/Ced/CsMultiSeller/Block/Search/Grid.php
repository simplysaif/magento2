<?php 
/**
  * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * https://cedcommerce.com/license-agreement.txt
  *
  * @category    Ced
  * @package     Ced_CsMultiSeller
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
  * @license      https://cedcommerce.com/license-agreement.txt
  */

/**
 * Producty Edit block
 *
 * @category   Ced
 * @package    Ced_CsMultiSeller
 * @author 	   CedCommerce Core Team <connect@cedcommerce.com>
 */
namespace Ced\CsMultiSeller\Block\Search;
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	//protected $_template = 'Magento_Backend::widget/grid/extended.phtml';

	/**
	 * Massaction block name
	 *
	 * @var string
	 */
	//protected $_massactionBlockName = 'csmarketplace/widget_grid_massaction';
	protected $_count=0;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var  \Ced\CsMarketplace\Model\VproductsFactory
     */
    protected $vproductsFactory;
	
	/**
     * @var  \Ced\CsMarketplace\Model\Vproducts
     */
    protected $_vproducts;
	
	/**
     * @var  \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
 
    /**
     * @var \SR\Weblog\Model\Status
     */
   // protected $_status;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SR\Weblog\Model\BlogPostsFactory $blogPostFactory
     * @param \SR\Weblog\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMarketplace\Model\VproductsFactory $vproductsFactory,
        \Ced\CsMarketplace\Model\Vproducts $vproducts,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ) {
        $this->_vproductsFactory = $vproductsFactory;
		$this->_vproducts = $vproducts;
		$this->_objectManager = $objectManager;
        //$this->_status = $status;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area',"adminhtml");
    }
    
    /**
     * Set Grid Parameters
     */
    
	public function _construct()
	{
		parent::_construct();
		$this->setId('searchGrid');
		$this->setDefaultSort('entity_id');
		//$this->setTemplate('csmarketplace/widget/grid.phtml');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
		$this->setVarNameFilter('search_filter');
		
	}
	
	/**
	 *Get Store Id
	 */
	
	protected function _getStore()
	{
		$storeId = (int) $this->getRequest()->getParam('store', 0);
		return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($storeId);
	}
	
	/**
	 *Column Filter Collection
	 */
	protected function _addColumnFilterToCollection($column)
	{
		if ($this->getCollection()) {
			if ($column->getId() == 'websites') {
				$this->getCollection()->joinField(
						'websites',
						'catalog_product_website',
						'website_id',
						'product_id=entity_id',
						null,
						'left'
				);
			}
		}
		return parent::_addColumnFilterToCollection($column);
	}
	
	/**
	 * Prepares Collection For Grid
	 */
	protected function _prepareCollection()
	{
		$product_collection = array();
		$store = $this->_getStore();
		$types= $this->_objectManager->get('Ced\CsMultiSeller\Model\System\Config\Source\Type')->toFilterOptionArray(true,false,$store->getId());
		$types = array_keys($types);
		$this->_count=count($types);
		$vendorId = $this->_objectManager->get('Ced\CsMarketplace\Model\Session')->getVendorId();
		$tablename = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getTableName('ced_csmarketplace_vendor_products');
		$storeId = 0;
		if($this->getRequest()->getParam('store')){
			$websiteId = $this->_objectManager->get('Magento\Store\Model\Store')->load($this->getRequest()->getParam('store'))->getWebsiteId();
			if($websiteId){
				if(in_array($websiteId,$this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getAllowedWebsiteIds())){
					$storeId=$this->getRequest()->getParam('store');
				}
			}
		}
		$product_collection = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection')
				->addAttributeToSelect('*')
				->addAttributeToFilter('type_id', array('in'=>$types))
				->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
				->addAttributeToFilter('visibility',array('neq'=>\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE));
		$product_collection->getSelect()
		->joinleft(array('vproducts'=>$tablename),'vproducts.product_id=e.entity_id',array('vproducts.check_status','vproducts.is_multiseller','vproducts.vendor_id'));
		$product_collection->getSelect()
		->where(new \Zend_Db_Expr ('CASE `vproducts`.`check_status` WHEN 1 THEN 1 WHEN 0 THEN 0 ELSE 1 END')."='1'")
		->where(new \Zend_Db_Expr ('CASE `vproducts`.`is_multiseller` WHEN 1 THEN 1 WHEN 0 THEN 0 ELSE 0 END')." ='0'")
		->where(new \Zend_Db_Expr ('CASE `vproducts`.`vendor_id` WHEN '.$vendorId.' THEN '.$vendorId.' WHEN 0 THEN 0 ELSE 0 END')." <> '".$vendorId."'");
		if($storeId)
			$product_collection->addStoreFilter($storeId);
		//->where(new Zend_Db_Expr ('CASE `vproducts`.`vendor_id` WHEN '.$vendorId.' THEN '.$vendorId.' WHEN 0 THEN 0 ELSE 0 END')." <> '".$vendorId."'");
		//->joinField('check_status','csmarketplace/vproducts', 'check_status','product_id=entity_id',null,'left')
		//->joinField('is_multiseller','csmarketplace/vproducts', 'is_multiseller','product_id=entity_id',null,'left');
		//$product_collection->getSelect()->columns(array('is_approved' => new Zend_Db_Expr ('CASE `vproducts`.`check_status` WHEN 1 THEN 1 WHEN 0 THEN 0 ELSE 1 END')));
		//$product_collection->getSelect()->columns(array('is_multisell'=> new Zend_Db_Expr ('CASE `vproducts`.`is_multiseller` WHEN 1 THEN 1 WHEN 0 THEN 0 ELSE 0 END')));
		
		//     						->addAttributeToFilter('check_status',array('eq'=>Ced_CsMultiSeller_Model_Multisell::APPROVED_STATUS))
		//$product_collection->addFieldToFilter('is_approved',array('eq'=>'1'))
		//->addFieldToFilter('is_multisell',array('eq'=>'0'))
		$product_collection->addWebsiteNamesToResult();
		$this->setCollection($product_collection);
		//print_r($product_collection->getData());die;
		parent::_prepareCollection();
		//$this->getCollection()->addWebsiteNamesToResult();
		
	//	$this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product\Collection')->addAttributeToSelect('*');
	//	print_r($data->getData());die;
		return $this;
	}


	/**
	 * Prepares Column For Grid
	 */
	protected function _prepareColumns()
	{//die('fhjgfukjrd');
		$store = $this->_getStore();
	
		 $this->addColumn(
            'entity_id',
            [
                'header'=> __('ID'),
				'width' => '5px',
				'type'  => 'number',
				'align'     => 'left',
				'index' => 'entity_id',
            ]
        );
		
		 
		$this->addColumn('name',
				[
						'header'=> __('Name'),
						'index' => 'name',
						'type'  => 'text',
						//'renderer' => 'Ced\CsMultiSeller\Block\Search\Renderer\Productimage',
						//'filter_condition_callback' => array($this, '_productNameFilter')
				]);
		if($this->_count>1){
			$this->addColumn('type_id',
					[
							'header'=> __('Type'),
							'width' => '20px',
							'index' => 'type_id',
							'type'  => 'options',
							'options' => $this->_objectManager->get('Ced\CsMultiSeller\Model\System\Config\Source\Type')->toFilterOptionArray(true,false,$store->getId()),
					]);
		}
		$this->addColumn('sku',
				[
						'header'=> __('SKU'),
						'type'  => 'text',
						'width' => '20px',
						'index' => 'sku',
				]);
	
		$this->addColumn('price',
				[
						'header'=> __('Price'),
						'type'  => 'price',
						'currency_code' => $store->getBaseCurrency()->getCode(),
						'index' => 'price',
				]);
		
		if (!$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->isSingleStoreMode()) {
		//$this->_storeManager->isSingleStoreMode()
			$this->addColumn('websites',
					[
							'header'=> __('Websites'),
							'width' => '100px',
							'sortable'  => false,
							'index'     => 'websites',
							'type'      => 'options',
							'options'   => $this->_objectManager->get('Magento\Store\Model\WebsiteFactory')->create()->getCollection()->toOptionHash(),
					]);

			
	}
		$this->addColumn('sell',
				[
						'header'    =>  __('Sell Product'),
						'width'     => '100',
						'type'      => 'text',
						'filter'    => false,
						'sortable'  => false,
						'index'     => 'sell',
						'renderer' => 'Ced\CsMultiSeller\Block\Search\Renderer\Sell',
				]);
		 return parent::_prepareColumns();
	}
	
	
	/**
	 * Prepares Filter Buttons
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
    /**Return Grid Url
     */
    public function getGridUrl()
    {
    	return $this->getUrl('*/*/gridsearch', ['_current' => true]);
    }


     /*   FILTER FOR PRODUCT NAME   */
    protected function _productNameFilter($collection, \Magento\Framework\DataObject $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        //echo $value;die;
        $vendors = //$this->_objectManager->create('Magento\Catalog\Model\Product')->getCollection()->addAttributeToSelect('*');
                   $collection->addFieldToFilter('name',array('like'=>'% '.$value.' %')); 
        //print_r($vendors->getName());die;                                 
        $vendor_id = array();
        foreach ($vendors as $_vendor){
        	//echo $_vendor->getName();
            $vendor_id[] = $_vendor->getSku();
        }  
               
         $this->getCollection()->addAttributeToFilter('sku', array('in'=>$vendor_id)); 
        return $this;
    }
}
