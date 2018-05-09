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
  * @license   http://cedcommerce.com/license-agreement.txt
  */
 
 namespace Ced\Blog\Block\Adminhtml\Blogcat;

 class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
 {
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */

    protected $_objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        array $data = []
        ) {
        parent::__construct($context, $backendHelper, $data);
        $this->_objectManager = $objectInterface;
        $this->moduleManager = $moduleManager;
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {    
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('id');
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
        $collection = $this->_objectManager->create('Ced\Blog\Model\Blogcat')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
    
    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
            'header' => __('Id'),
            'type' => 'number',
            'index' => 'id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
            );
        
        $this->addColumn(
            'profile',
            [
            'header' => __('Featured Image'),
            'type' => 'image',
            'index' => 'profile',
            'filter' => false,
            'renderer' => 'Ced\Blog\Block\Adminhtml\Blogcat\Category\Grid\Renderer\Image'
            ]
            );
        
        $this->addColumn(
            'title',
            [
            'header' => __('Name'),
            'type' => 'text',
            'index' => 'title',
            ]
            );
        $this->addColumn(
            'url_key',
            [
            'header' => __('Url Key'),
            'index' => 'url_key',
            'type' => 'text'
            ]
            );
        $this->addColumn(
            'Edit',
            [
            'header' => __('Edit'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
            [
            'caption' => __('Edit'),
            'url' => [
            'base' => '*/*/edit'
            ],
            'field' => 'id'
            ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action'
            ]
            );
        
        return parent::_prepareColumns();
    }
    
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        
        $this->getMassactionBlock()->addItem(
            'delete',
            [
            'label' => __('Delete'),
            'url' => $this->getUrl('blog/*/massDelete'),
            'confirm' => __('Are you sure?')
            ]    
            );
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('blog/*/grid', ['_current' => true]);
    }
    
    
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'blog/*/edit',
            ['id' => $row->getId()]
            );
    }
}
