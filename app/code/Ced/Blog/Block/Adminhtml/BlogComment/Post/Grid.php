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

namespace Ced\Blog\Block\Adminhtml\BlogComment\Post;
/**
 * Adminhtml product grid block
 *
 * @author                                     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     *@param Magento\Backend\Block\Template\Context
     *@param Magento\Backend\Helper\Data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $data
        );
    }
    
    /**
     *@param construct
     */
    protected function _construct()
    {
        $this->setId('grid_id');
        $this->setRowClickCallback('review.gridRowClick');
        $this->setUseAjax(true);
    }
     
    /**
     *@param prepareCollection
     */
    protected function _prepareCollection()
    {
      
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $currentUser = $objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();
        $user=$currentUser->getData();
        if($user['user_id']==1) {
            $collection = $objectManager->create('Ced\Blog\Model\BlogPost')->getCollection();
        }
        else
        { 
            $collection = $objectManager->create('Ced\Blog\Model\BlogPost')->getCollection()->addFieldToFilter('user_id', $user['user_id']);
        }
        $this->setCollection($collection);
        
        parent::_prepareCollection();
        return $this;
    }
    
    /**
     *@param _prepareColumns
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
            'renderer' => 'Ced\Blog\Block\Adminhtml\BlogPost\Image\Grid\Renderer\Image'
            ]
        );
        $this->addColumn(
            'Status',
            [
            'header' => __('Status'),
            'index' => 'blog_status',
            'class' => 'xxx'
            ]
        );
    }
    
    /**
     * getGridUrl
     *
     * @return $this
     */
    public function getGridUrl()
    {
        return $this->getUrl('blog/comment/postGrid', ['_current' => true]);
    }
    
    /**
     * getRowUrl
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('blog/comment/jsonInfo', ['id' => $row->getId()]);
    } 
    /**
     * Prepare mass action
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }
}
