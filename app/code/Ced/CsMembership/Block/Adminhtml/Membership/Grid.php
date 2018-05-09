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
 * @package     Ced_CsMembership
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMembership\Block\Adminhtml\Membership;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
 
    /**
     * @var \\Ced\CsMarketplace\Model\VendorFactory
     */
    protected $_vendorFactory;
	
	protected $_websiteFactory;
    /**
     * @var \SR\Weblog\Model\Status
     */
    protected $_group;
	
	protected $_status;
 
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMembership\Model\MembershipFactory $membershipFactory,
		\Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Framework\Module\Manager $moduleManager,
		\Ced\CsMarketplace\Model\System\Config\Source\Group $group,
		\Ced\CsMarketplace\Model\System\Config\Source\Status $status,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        array $data = []
    ){
        $this->_membershipFactory = $membershipFactory;
		$this->_websiteFactory = $websiteFactory;
        $this->_group = $group;
		$this->_status = $status;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('membershipGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('vendor_filter');
    }
 
    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		    $collection = $this->_membershipFactory->create()->getCollection();
        $filter=$this->getRequest()->getParams();
                if(isset($filter['id']) && $filter['id']!=''){
                    $collection->addFieldToFilter('id',$this->getRequest()->getParam('id'));
                }
						
		    $this->setCollection($collection);
        return  parent::_prepareCollection();
    }
 
	protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }
 
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
		$this->addColumn('id', [
				'header'    => __('ID'),
				'align'     =>'right',
				'index'     => 'id',
        "width" => "50px",
				'type'	  => 'number'
			]
		);

        $this->addColumn("name", [
                "header" => __("Title"),
                "index" => "name",
                ]
        );

        $this->addColumn("sku", [
                "header" => __("Product_Id"),
                "index" => "product_id",
                ]
        );
                
        $this->addColumn("duration", [
                "header" => __("Duration (In month(s))"),
                "index" => "duration",
                ]
        );
        $this->addColumn("product_limit", [
                "header" => __("No of Product"),
                "index" => "product_limit",
                ]
        );
        $this->addColumn("category_ids", [
                "header" => __("Allowed Category"),
                'renderer' => 'Ced\CsMembership\Block\Adminhtml\Membership\Renderer\Category',
                "index" => "category_ids",
                ]
        );

        $store = $this->_getStore();
		$this->addColumn(
            'price',
            [
                'header' => __('Package Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        $this->addColumn(
            'special_price',
            [
                'header' => __('Package Special Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'special_price',
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [0 => 'Yes', 1 => 'No']
            ]
        );
		
		
 
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
 
        return parent::_prepareColumns();
    }
 

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
 
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
 
    /**
     * @param \SR\Weblog\Model\BlogPosts|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit',
            ['id' => $row->getId()]
        );
    }

    static public function getOptionArray3()
    {
            $data_array=array(); 
            $data_array[0]='Yes';
            $data_array[1]='No';
            return($data_array);
    }
    static public function getValueArray3()
    {
            $data_array=array();
            foreach(\Ced\CsMembership\Block\Adminhtml\Membership\Grid::getOptionArray3() as $k=>$v){
                     $data_array[]=array('value'=>$k,'label'=>$v);    
            }
            return($data_array);

    }
}
