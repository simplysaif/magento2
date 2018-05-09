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

namespace Ced\Blog\Block\Adminhtml\BlogComment;

 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;
    
    /**
     * @var objectManager
     */
    protected $_objectManager;

    /**
     * @var status
     */
    protected $_status;
    
    /**
     * @param Magento\Backend\Block\Template\Context
     * @param Magento\Backend\Helper\Data
     * @param Magento\Framework\Module\Manager
     * @param Ced\Blog\Model\Status
     */
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Ced\Blog\Model\Status $status,
        array $data = []
    ) {
        $this->_objectManager = $objectInterface;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
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
        $collection = $this->_objectManager->create('Ced\Blog\Model\BlogComment')->getCollection();
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
            'status',
            [
                'header' => __('Status'),
                'type' => 'options',
                'index' => 'status',
                'class' => 'xxx',
                'options' => $this->_status->getOptionArray(),
                ]
        );
        
        $this->addColumn(
            'user',
            [
                'header' => __('User'),
                'type' => 'text',
                'index' => 'user',
                'class' => 'xxx'
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email Id'),
                'index' => 'email',
                'class' => 'xxx'
                ]
        );
       
        $this->addColumn(
            'user_type',
            [
                'header' => __(' User Type'),
                'type' => 'select',
                'index' => 'user_type',
                'filter' => 'Ced\Blog\Block\Adminhtml\BlogComment\Grid\Filter\Type',
                'renderer' => 'Ced\Blog\Block\Adminhtml\BlogComment\Grid\Renderer\Type'
                ]
        );
        
         $this->addColumn(
             'approve', [
                'header'        => __('Approve'),
                'align'         => 'left',
                'index'         => 'approve',
                'filter'            => false,
                'type'          => 'text',
                'renderer' => 'Ced\Blog\Block\Adminhtml\BlogComment\Grid\Renderer\Approve'
                ]
         ); 
        
         $this->addColumn(
             'View',
             [
                 'header' => __('View'),
                 'type' => 'action',
                 'getter' => 'getId',
                 'actions' => [
                 [
                 'caption' => __('View'),
                 'url' => [
                 'base' => 'blog/*/edit'
                 
                 
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
     * @param prepareMassaction
     */
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
    
        $this->getMassactionBlock()->addItem(
            'delete',
            [
            'label' => __('Delete'),
            'url' => $this->getUrl('blog/*/massDelete'),
            'confirm' => __('Are you sure?')
            ]
        );
        
        $statuses = $this->_status->toOptionArray();
        
        $this->getMassactionBlock()->addItem(
            'status',
            [
            'label'=> __('Change Comment(s) Status'),
            'url'  => $this->getUrl('*/*/massStatus/', ['_current'=>true]),
            'additional' => [
            'visibility' => [
            'name' => 'status',
            'type' => 'select',
            'class' => 'required-entry',
            'label' => ('Status'),
            'default'=>'-1',
            'values' =>$statuses,
            ]
            ]
            ]
        );
    
    }
    
    /**
     * @param getGridUrl
     */
    public function getGridUrl()
    {
        return $this->getUrl('blog/*/grid', ['_current' => true]);
    }
 
    /**
     * @param getRowUrl
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'blog/*/edit',
            ['id' => $row->getId()]
        );
    }
}
