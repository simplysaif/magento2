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
class UserPost extends \Magento\Framework\View\Element\Template
{
    /**
     * @var objectManager
     */
    public $objectManager;
    
    /**
     * @var $formKey
     */
    protected $formKey;
    
    /**
     * @var blogFactory
     */
    protected $_blogFactory;
    
    /**
     * @var blogFactory
     */
    protected $_catFactory;

    /**
     * @var scopeconfig
     */
    protected $_scopeConfig;

    /**
     * UserPost constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Ced\Blog\Model\BlogPostFactory $blogpostfactory
     * @param \Ced\Blog\Model\BlogCatFactory $blogcatfactory
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Ced\Blog\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\Blog\Model\BlogPostFactory $blogpostfactory,
        \Ced\Blog\Model\BlogCatFactory $blogcatfactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Ced\Blog\Model\Url $url,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $data);
        $this->objectManager = $objectManager;
        $this->formKey = $formKey;
        $this->_blogFactory = $blogpostfactory;
        $this->_catFactory = $blogcatfactory;

    }
    
    /**
     * function _prepareLayout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'blog',
                [
                    'label' => __('Blog'),
                    'title' => __(' Blog'),
                    'link' => $this->_url->getBaseUrl()
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'category',
                [
                    'label' => __('Category'),
                    'title' => __(' Category'),
                ]
            );
        }

        $data=$this->getMeta();
        if(!empty($data[0]['meta_title'])) {
            $this->pageConfig->getTitle()->set($data[0]['meta_title']);
        }else{
            $collect = $this->_catFactory->create()->getCollection();
            foreach ($collect as $title) 
            {
                $this->pageConfig->getTitle()->set($title->getTitle());
            }
        }
        if(isset($data[0]['keywords'])) {
            $this->pageConfig->setKeywords($data[0]['keywords']); 
        }
        if(isset($data[0]['about'])) {
            $this->pageConfig->setDescription($data[0]['about']); 
        }
        
        if ($this->getCollection()) {
            $config_value=$this->_scopeConfig->getValue('ced_blog/general/post_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'ced.grid.userpost.pager'
            )->setLimit($config_value)->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $pager->setAvailableLimit(array(5=>5,10=>10,20=>20,'all'=>'all'));
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }

    /**
    * @return string
    */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
    * @return collection
    */

    public function getCategoryPost()
    {
        $data = $this->getRequest()->getParams();
        $model = $this->objectManager->create('Ced\Blog\Model\BlogRelation')->getCollection();
        
        return $model->addFieldToFilter('cat_id', array('eq'=>$data['id']));
    }

    /**
    * @return collection
    */
    public function getMeta()
    {
        $data = $this->getRequest()->getParams();
        $model= $this->_catFactory->create()->getCollection();
        return $model->addFieldToFilter('id', array('like'=>'%'.$data['id'].'%'))->getData();
    }
}
