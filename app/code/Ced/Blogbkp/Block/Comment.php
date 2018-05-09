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
  
namespace Ced\Blog\Block;
class Comment extends \Magento\Framework\View\Element\Template
{
    /**
     * @var objectManager
     */
    public  $objectManager;

    /**
     * @var scopeConfig
     */
    public $_scopeConfig;

    /**
     * Comment constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\Blog\Model\BlogCommentFactory $BlogFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\Blog\Model\BlogCommentFactory $BlogFactory,
        array $data = []
    ) {
        $this->objectManager = $objectManager;
        $this->_blogFactory = $BlogFactory;
        parent::__construct($context, $data);

        $id = $this->getRequest()->getParam('id');
        $model = $objectManager->create('Ced\Blog\Model\BlogComment')->getCollection()
            ->addFieldToFilter('post_id', $id);
        $this->setCollection($model);
    }
    
    /**
     * @var getAction
     */
    public function getAction()
    {
        return $this->getUrl(
            'blog/index/save',
            [
            '_secure' => $this->getRequest()->isSecure(),
            'id' => $this->getRequest()->getParam('id'),
            ]
        );
    } 
    
    /**
     * @var return post model
     */
    public function getPostItemInfo()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        return $model;
    }
    
    /**
     * @var getCommentInfo
     */
    public function getCommentInfo()
    {  
        $id = $this->getRequest()->getParam('id');
        $model = $this->objectManager->create('Ced\Blog\Model\BlogComment')
            ->getCollection()
            ->addFieldToFilter('post_id', $id)
             ->addFieldToFilter('status', 1);
        return $model;
    }
    
    /**
     * @var _prepareLayout
     */
    protected function _prepareLayout()
    {
        $data=$this->getPostItemInfo()->getData();
        if(isset($data['title'])) {
            $this->pageConfig->getTitle()->set($data['title']); 
        }
        if(isset($data['meta_description'])) {
            $this->pageConfig->setKeywords($data['meta_description']); 
        }
        if(isset($data['meta_content'])) {
            $this->pageConfig->setDescription($data['meta_content']); 
        }
        parent::_prepareLayout();
        
        if ($this->getCollection()) {
            $collect = $this->getCollection()->addFieldToFilter('status', 1);
            $config_value=count($collect->getData());
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'ced.grid.comment.pager'
            )->setLimit(2)->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }
    
    /**
     * @var getPagerHtml
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
}
