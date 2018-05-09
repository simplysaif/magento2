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

class Profile extends \Magento\Framework\View\Element\Template
{
    /**
     * @var resultPageFactory
     */

    public $objectManager;

    /**
     * @param Magento\Framework\View\Element\Template\Context
     * @param Magento\Framework\ObjectManagerInterface
     * @param Ced\Blog\Model\BlogPostFactory
     * @param Magento\Framework\Session\Generic
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Ced\Blog\Model\BlogPostFactory $gridFactory,
        \Ced\Blog\Model\Url $url,
        \Magento\Framework\Session\Generic $session,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_gridFactory = $gridFactory;
        $this->objectManager = $objectInterface;
        $this->_session = $session;
        parent::__construct($context, $data);
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
            foreach ($title as $author)
            {
                $breadcrumbsBlock->addCrumb(
                    $author->getAuthor(),
                    [
                        'label'=>$author->getAuthor(),
                        'title'=>$author->getAuthor()
                    ]
                );
                $this->pageConfig->getTitle()->set($author->getAuthor());
            }
            return parent::_prepareLayout();
        }

    }
    /**
     * @var getProfile
     */

    public function getProfile()
    {
        $model = $this->objectManager->create('Ced\Blog\Model\User')->getCollection();
        return $model;
    }

    public function getProfileData()
    {
        $id= $this->getRequest()->getParam('id');
        $model = $this->objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
        $user_collection = $this->objectManager->create('Ced\Blog\Model\User')->getCollection();
        return $user_collection->addFieldToFilter('user_id', $model->getUserId());
    }
}
?>
