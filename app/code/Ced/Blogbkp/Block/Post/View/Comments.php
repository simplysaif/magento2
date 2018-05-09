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

namespace Ced\Blog\Block\Post\View;

use Magento\Store\Model\ScopeInterface;

class Comments extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context  $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Page  $post
     * @param \Magento\Framework\Registry  $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Cms\Model\PageFactory $postFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Block template file
     *
     * @var string
     */
    protected $_template = 'post/view/comments.phtml';

    /**
     * Retrieve comments type
     *
     * @return bool
     */
    public function commentsType()
    {
        return $this->_scopeConfig->getValue(
            'ced_blog/comment/type', ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * totalNuOfComments
     *
     * @return int
     */
    public function totalNuOfComments()
    {
        return (int)$this->_scopeConfig->getValue(
            'ced_blog/comment/number_of_comments', ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve facebook app id
     *
     * @return string
     */
    public function retrieveFacebookAppId()
    {
        return $this->_scopeConfig->getValue(
            'ced_blog/comment/fb_app_id', ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getDisqusForumShortname
     *
     * @return string
     */
    public function getDisqusForumShortname()
    {
        return $this->_scopeConfig->getValue(
            'ced_blog/comment/disqus_forum_shortname', ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve locale code
     *
     * @return string
     */
    public function getLocaleCode()
    {
        return $this->_localeResolver->getLocale();
    }

    /**
     * Retrieve posts instance
     */
    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData(
                'post',
                $this->_coreRegistry->registry('current_blog_post')
            );
        }
        return $this->getData('post');
    }
}
