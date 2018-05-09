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

namespace Ced\Blog\Block\Adminhtml\Blogcat\Edit\Tab\PostGrid;

 
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    
    protected $moduleManager;

    protected $_gridFactory;
    
    protected $_coreRegistry = null;
    
    protected $status;

    protected $objectManager;
    
    protected $_productFactory;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Ced\Blog\Model\PostStatus $status,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\Blog\Model\BlogPostFactory $productFactory, 
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->status = $status;
        $this->_coreRegistry = $registry;
        $this->objectManager = $objectManager;
        $this->_productFactory = $productFactory;
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
        $collection = $this->_productFactory->create()->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }
        
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'post_id', array (
            'header_css_class' => 'a-center',
            'field_name' => 'post_id[]',
            'type' => 'checkbox',
            'values' => $this->_getSelectedPost(),
            'align' => 'center',
            'index' => 'id'
             ) 
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
            'renderer' => 'Ced\Blog\Block\Adminhtml\BlogPost\Image\Grid\Renderer\Image'
            ]
        );
    
        $this->addColumn(
            'Status',
            [
            'header' => __('Status'),
            'type' => 'options',
            'index' => 'blog_status',
            'class' => 'xxx',
            'options' => $this->status->getOptionArray(),
            ]
        );
        
        
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'id',
                'editable' => true,
                'edit_only' => true,
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position'
            ]
        );
        
        return parent::_prepareColumns();
    }
    
    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getData('gridGrid') ? $this->getData('gridGrid') : $this->getUrl('blog/*/postGrid', ['_current' => true]);
    }
    
    
    
    /**
     * Retrieve related products
     *
     * @return array
     */
    public function getSelectedRelatedPosts()
    {
        $ids = [];
        $registry = $this->_coreRegistry->registry('current_category');
        foreach ($registry as $relation) 
        {
            $ids[$relation->getPostId()]  =  ['position' => $relation->getPostId()]; 
            
        }
        return $ids;
    }
    
    
    /**
     * Add filter
     *
     * @param  Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'post_id') {
            $postIds = $this->_getSelectedPost();
            if (empty($postIds)) {
                $postIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('id', ['in' => $postIds]);
            } else {
                if ($postIds) {
                    $this->getCollection()->addFieldToFilter('id', ['nin' => $postIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    
    /**
     * Retrieve selected related products
     *
     * @return array
     */
    protected function _getSelectedPost()
    {
        $products = $this->getPostsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedRelatedPosts());
        }
        return $products;
    }
}
