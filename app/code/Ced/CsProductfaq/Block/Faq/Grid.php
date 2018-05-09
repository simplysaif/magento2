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
 * @category Ced
 * @package Ced_CsProductfaq
 * @author CedCommerce Core Team
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProductfaq\Block\Faq;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;



class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $_collectionFactory;
    protected $session;
    protected $_cmsPage;
    protected $_type;
    protected $pageLayoutBuilder;
    protected $_massactionBlockName = 'Ced\CsProductfaq\Block\Widget\Grid\Massaction';
    
    
    public $_objectManager;
    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Backend\Helper\Data $backendHelper,
            \Magento\Cms\Model\Page $cmsPage,
             Session $customerSession,
            \Magento\Store\Model\WebsiteFactory $websiteFactory,
            \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory,
            \Magento\Framework\ObjectManagerInterface $objectManager,
            \Magento\Framework\Module\Manager $moduleManager,
            \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
            array $data = []
    ) {
        
        $this->_websiteFactory = $websiteFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_objectManager = $objectManager;
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->moduleManager = $moduleManager;
        $this->_cmsPage = $cmsPage;
        $this->session = $customerSession;

        parent::__construct($context, $backendHelper, $data);
        
    }
    
    public function _construct()
    { 
        $VendorId = $this->session->getVendorId();

        parent::_construct();
        $this->setId('csfaqGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setData('area','adminhtml');
    } 
  
    protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }
    
  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('id');
    $this->getMassactionBlock()->setFormFieldName('id');
  
    $this->getMassactionBlock()->addItem(
        'delete',
        [
        'label' => __('Delete'),
        'url' => $this->getUrl('*/*/delete'),
       
        ]
    );
    
    $this->getMassactionBlock()->addItem(
            'enable',
            [
            'label' => __('Enable'),
            'url' => $this->getUrl('*/*/massenable'),
            
            ]
    );
    
    $this->getMassactionBlock()->addItem(
            'disable',
            [
            'label' => __('Disable'),
            'url' => $this->getUrl('*/*/massdisable'),
        
            ]
    );
    
  }
    protected function _prepareCollection()
    {
    
        $VendorId = $this->session->getVendorId();
        $vendorsFaq = array();
	    $collection = $this->_objectManager->create('Ced\Productfaq\Model\Productfaq')->getCollection()->getData();
	    foreach ($collection as $Products)
	    {
	        $pId = $Products['product_id'];
	        $Vendorcollection = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->load($pId,'product_id')->getData();
	        if(count($Vendorcollection) > 0)
	        {
	            $vendorsFaq[] = $pId;
	        }
	    }
        $vendorsFaq = array_unique($vendorsFaq);
        
        $FaqCollection = $this->_objectManager->create('Ced\Productfaq\Model\Productfaq')->getCollection()->addFieldToFilter('product_id',array('in'=>$vendorsFaq));
       
        
        $this->setCollection($FaqCollection);
        
        return parent::_prepareCollection();
        
        
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('id', 
                [
            'header'    => ('Id'),
            'align'     => 'left',
            'index'     => 'id',
        ]
    );
        
       

        $this->addColumn('product_id', 
                [
            'header'    => ('Product Id'),
            'align'     => 'left',
            'index'     => 'product_id',
           
        ]
    );

        
        $this->addColumn('title',
                [
                'header'    => ('Title'),
                'align'     => 'left',
                'index'     => 'title'
                ]
        );
        
        
        $this->addColumn('email_id',
                [
                'header'    => ('Customer Email'),
                'align'     => 'left',
                'index'     => 'email_id'
                ]
        );
        
        $this->addColumn('publish_date',
                [
                'header' => __('Created At'),
                'index' => 'publish_date',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );
        
        $this->addColumn('is_active',
                [
                'header'    => ('Status'),
                'align'     => 'left',
        		'type'      => 'options',
                'index'     => 'is_active',
        		'options'   =>['1'=>'Enabled','2'=>'Disabled']
                ]
        );
        
    
   $this->addColumn('action',
        [
            'header'    => __('Action'),
            'width'     => '50px',
            'type'      => 'action',
            'getter'     => 'getId',
            'actions'   => array(
                array(
                    'caption' =>__('Edit'),
                    'url'     => array(
                        'base'=>'*/*/edit',
                        'params'=>array('store'=>$this->getRequest()->getParam('id'))
                    ),
                    'field'   => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
    ]);

       
        return parent::_prepareColumns();
    }
    
  


    /**
     * Row click url
     *
     * @return string
     */
   public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
