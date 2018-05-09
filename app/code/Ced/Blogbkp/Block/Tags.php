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

class Tags extends \Magento\Framework\View\Element\Template
{
    /**
     * @var objectManager
     */
    public $objectManager;
    
    /**
     * @var scopeConfig
     */
    public  $_scopeConfig;
    
    /**
     * @param Magento\Framework\View\Element\Template\Context
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Store\Model\StoreManagerInterface
     * @param Ced\Blog\Model\BlogPostFactory
     * @param Magento\Framework\Data\Form\FormKey
     * @param Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function __construct(
        \Ced\Blog\Model\Url $url,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Ced\Blog\Model\BlogPostFactory $gridFactory,
        \Magento\Framework\Session\Generic $session,
        array $data = []
    ) {
        $this->_url = $url;
        $this->objectManager = $objectInterface;
        $this->_gridFactory = $gridFactory;
        $this->_session = $session;
        parent::__construct($context, $data);
    }
    
    /**
     * @var return tags
     */
    public function getTagCollection()
    {
        $data = $this->getRequest()->getParams();
        $model = $this->_gridFactory->create()->getCollection();
        return $model->addFieldToFilter('tags', array('like'=>'%'.$data['search'].'%'));
    }
    
    /**
     * @var prepareLayout
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
                    'title' => __('Blog'),
                    'link' => $this->_url->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'post',
                [
                    'label' => __('Tag search'),
                    'title' => __('Tag search'),
                ]
            );

            $this->pageConfig->getTitle()->set(__('Tag Search Result'));
            return parent::_prepareLayout();
        }
        if ($this->getCollection()) {
            $config_value=$this->_scopeConfig->getValue('ced_blog/general/post_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'ced.grid.view.pager'
            )->setLimit($config_value)->setCollection(
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
    
    /**
     * @var getCommentInfo
     */
    
    public function getCommentInfo($id)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Ced\Blog\Model\BlogComment')
            ->getCollection()
            ->addFieldToFilter('post_id', $id)
            ->addFieldToFilter('status', 1);
        $data=count($model);
        return  $data;
    }
    
    /**
     * @var setSession
     */
    protected function setSession()
    {
        $id= $this->getRequest()->getParam('id');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        $data=$model->getData();
        return $data;
    }

    
}
?>
