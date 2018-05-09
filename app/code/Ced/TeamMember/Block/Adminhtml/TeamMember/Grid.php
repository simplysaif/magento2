<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Academic Free License (AFL 3.0)
  * You can check the licence at this URL: http://cedcommerce.com/license-agreement.txt
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/afl-3.0.php
  *
  * @category    Ced
  * @package     Ced_CsGroup
  * @author       CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */
namespace Ced\TeamMember\Block\Adminhtml\TeamMember;
 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $moduleManager;
 
   
    protected $_gridFactory;
 
    protected $_objectManager;
    
 
    protected $backendHelper;
    
    protected $_resource;
   
    protected $_status;
   /**
    * 
    * @param \Magento\Backend\Block\Template\Context $context
    * @param \Magento\Backend\Helper\Data $backendHelper
    * @param \Magento\Framework\ObjectManagerInterface $objectManager
    * @param \Magento\Framework\App\ResourceConnection $resource
    * @param \Magento\Framework\Module\Manager $moduleManager
    * @param array $data
    */
    public function __construct(
    		
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Framework\App\ResourceConnection $resource,
    	\Ced\TeamMember\Model\System\Config\Source\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
    	
      
        $this->_objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
        $this->_resource = $resource;
        $this->_status = $status;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * @return void
     */
    protected function _construct()
    {
          parent::_construct();
        $this->setId('teammemberGrid');
        $this->setDefaultSort('Asc');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        
    }
 
    /**
     * @return $this
     */
	protected function _prepareCollection()
    {
        $collection =  $this->_objectManager->create("Ced\TeamMember\Model\TeamMember")->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns() {
    	
       
		 $this->addColumn('id', array(
            'header'    =>__('ID#'),
            'index'     =>'id',
            'align'     => 'left',
            'width'    => '50px'
        ));
		
		$this->addColumn('first_name', array(
            'header'    =>__('First Name'),
            'index'     =>'first_name',
            'align'     => 'left',
        ));

        $this->addColumn('last_name', array(
            'header'    =>__('Last Name'),
            'index'     =>'last_name'
        ));
        
        $this->addColumn('email', array(
        		'header'    =>__('Email'),
        		'index'     =>'email',
        		'align'    =>'left'
        ));
         $this->addColumn('status', [
				'header'        => __('Member Status'),
				'align'     	=> 'left',
				'index'         => 'status',
				'type'          => 'options',
				'options'		=> $this->_status->toOptionArray(true),
				'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
			]
        );
		
		$this->addColumn('approve', [
				'header'        => __('approve'),
				'align'     	=> 'left',
				'filter'   	 	=> false,
				'type'          => 'text',
				'renderer' => 'Ced\TeamMember\Block\Adminhtml\TeamMember\Renderer\Approve'
			]
		);
        return parent::_prepareColumns();
    }
	
	  /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
 
    
    
}