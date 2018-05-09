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
  * @package     Ced_Blog
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

 namespace Ced\Blog\Block\Adminhtml\BlogPost;

 
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
     * @var _gridFactory
     */
    
    protected $_gridFactory;
    
    /**
     * @var status
     */
    protected $status;
    
    /**
     * @param Magento\Backend\Block\Template\Context
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Backend\Helper\Data 
     *
     */

    public function __construct(

        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Ced\Blog\Model\PostStatus $status,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
        ) {
    	$this->status = $status;
        $this->_objectManager = $objectInterface;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }
    
    /**
     * @return void
     */
    protected function _construct()
    {	
        //die('grid_construct');
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('attribute_id');
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

    	$currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
    	$user=$currentUser->getData();
    	if($user['user_id']==1)
    	{
    		$collection = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->getCollection();
    	}else { 
            $collection = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->getCollection()->addFieldToFilter('user_id',$user['user_id']);
        }
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
            'Id',
            [
            'header' => __('ID'),
            'type' => 'number',
            'index' => 'id',
            'header_css_class' => 'col-id',
            'column_css_class' => 'col-id'
            ]
            ); 
         $this->addColumn(
            'Title',
            [
            'header' => __('Title'),
            'index' => 'title',
            'class' => 'xxx'
            ]
            );
         $this->addColumn(
          'url',
          [
          'header' => __('Url'),
          'index' => 'url',
          'class' => 'xxx'
          ]
          );
         $this->addColumn(
          'Featured_image',
          [
          'header' => __('Featured Image'),
          'index' => 'featured_image',
          'class' => 'xxx',
          'filter' => false,
          'sortable' => false,
          'renderer' => 'Ced\Blog\Block\Adminhtml\BlogPost\Image\Grid\Renderer\Image'
          ]
          );
         $this->addColumn(
            'created_at',
            [
            'header' => __('Created'),
            'index' => 'created_at',
            'type' => 'datetime',
            'align' => 'center',
            'default' => __('N/A'),
            'html_decorators' => ['nobr'],
            'header_css_class' => 'col-period',
            'column_css_class' => 'col-period'
            ]
            );
         $this->addColumn(
          'Status',
          [
          'type' => 'options',
          'header' => __('Status'),
          'index' => 'blog_status',
          'class' => 'xxx',
          'options' => $this->status->getOptionArray(),
          ]
          );
         $this->addColumn(
            'edit',
            [
            'header' => __('Edit'),
            'type' => 'action',
            'getter' => 'getId',
            'actions' => [
            [
            'caption' => __('Edit'),
            'url' => [
            'base' => 'blog/post/edit'
            ],
            'field' => 'post_id'
            ]
            ],
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'header_css_class' => 'col-action',
            'column_css_class' => 'col-action'
            ]
            );

         $block = $this->getLayout()->getBlock('grid.bottom.links');
         if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('attribute_id');
        
        $this->getMassactionBlock()->addItem(
            'delete',
            [
            'label' => __('Delete'),
            'url' => $this->getUrl('blog/*/massDelete'),
            'confirm' => __('Are you sure?')
            ],
            'delete',
            [
            'label' => __('Delete'),
            'url' => $this->getUrl('blog/*/massDelete'),
            'confirm' => __('Are you sure?')
            ]
            
            );
        
        
        $arr=array('publish'=>'publish','unpublish'=>'unpublish','draft'=>'draft');
        $this->getMassactionBlock()->addItem('status',
           [
           'label'=> __('Change Post(s) Status'),
           'url'  => $this->getUrl('blog/post/postmassStatus/', ['_current'=>true]),
           'additional' => [
           'visibility' => [
           'name' => 'status',
           'type' => 'select',
           'class' => 'required-entry',
           'label' => ('Status'),
           'default'=>'-1',
           'values' =>$arr,
           ]
           ]
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
            ['post_id' => $row->getId()]
            );
    }
}