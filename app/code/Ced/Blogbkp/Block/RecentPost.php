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
class RecentPost extends \Magento\Framework\View\Element\Template
{
    /**
     * @var $objectManager
     */
    
    public  $objectManager;
    
    /**
     * @var scopeConfig
     */
    public $scopeConfig;

    /**
     * RecentPost constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ced\Blog\Model\BlogPostFactory $gridFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Session\Generic $session
     * @param \Ced\Blog\Model\Url $url
     * @param array $data
     */
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ced\Blog\Model\BlogPostFactory $gridFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Session\Generic $session,
        \Ced\Blog\Model\Url $url,
        array $data = []
    ) {
        $this->_url = $url;
        $this->objectManager = $objectManager;
        $this->_gridFactory = $gridFactory;
        $this->_session = $session;
        parent::__construct($context, $data);
        $collection = $this->_gridFactory->create()->getCollection();
        $this->setCollection($collection);
        $this->pageConfig->getTitle()->set(__('Blog'));
    }

    protected function _prepareLayout()
    {
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
            

               $title = $this->_gridFactory->create()->getCollection();
            
            return parent::_prepareLayout();
        }

    }
}
?>
