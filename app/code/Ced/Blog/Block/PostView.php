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

class PostView extends \Magento\Framework\View\Element\Template
{
    /**
     * @var $_objectManager;
     */
    public  $_objectManager;

    /**
     * @var $scopeConfig;
     */
    public $_scopeConfig;

    /**
     * @param Magento\Framework\View\Element\Template\Context
     * @param Magento\Framework\ObjectManagerInterface
     * @param Ced\Blog\Model\BlogPostFactory
     * @param Magento\Framework\Session\Generic
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ced\Blog\Model\BlogCommentFactory $gridFactory,
        \Ced\Blog\Model\Url $url,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Framework\Session\Generic $session,
        array $data = []
    ) {
        $this->_url = $url;
        $this->customerSession = $customerSession;
        $this->_objectManager = $objectInterface;
        $this->_gridFactory = $gridFactory;
        $this->_session = $session;
        $this->scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->getCollection();
        $this->setCollection($model);


    }

    /**
     * @var _prepareLayout;
     */
    protected function _prepareLayout()
    {
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->getCollection();
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
                    'label' => __('post'),
                    'title' => __('post'),
                ]
            );
        }

        $data=$this->getItem();
        if(!empty($data['title'])) {
            $this->pageConfig->getTitle()->set($data['title']);
        }else{
            foreach ($model as $title )
            {
                $this->pageConfig->getTitle()->set($title->getTitle());
            }
        }
        if(isset($data['meta_description'])) {
            $this->pageConfig->setKeywords($data['meta_description']);
        }
        if(isset($data['meta_content'])) {
            $this->pageConfig->setDescription($data['meta_content']);
        }

        parent::_prepareLayout();

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
     * @var getPagerHtml;
     */

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @var return url;
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
     * @var setSession;
     */
    protected function setSession()
    {
        $id= $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        $data=$model->getData();
        return $data;
    }
    public function getItem()
    {
        $id= $this->getRequest()->getParam('id');

        $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        $data=$model->getData();
        /* $this->getLayout()->getBlock('head')->setTitle('blog');
        $this->getLayout()->getBlock('head')->setDescription('desc');
        $this->getLayout()->getBlock('head')->setKeywords('keywords'); */
        return $data;
    }

    /**
     * @var getCommentInfo
     */
    public function getCommentInfo()
    {
        $id= $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Ced\Blog\Model\BlogComment')
            ->getCollection()
            ->addFieldToFilter('post_id', $id)
            ->addFieldToFilter('status', 1);
        return $model;
    }
}

?>
