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
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Blog\Block\Post\PostList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract blog post list block
 */
abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_post;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ced\Blog\Model\ResourceModel\BlogPost\CollectionFactory
     */
    protected $postCollectionFactory;

    /**
     * @var $_postVariable
     */

    protected $_postVariable;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Ced\Blog\Model\ResourceModel\BlogPost\CollectionFactory $postCollectionFactory
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Ced\Blog\Model\ResourceModel\BlogPost\CollectionFactory $postCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->postCollectionFactory = $postCollectionFactory;

    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _getPostCollection()
    {
        $this->_postVariable = $this->postCollectionFactory->create();
        if ($this->getPageSize()) {
            $this->_postVariable->setPageSize($this->getPageSize());
        }
    }

    /**
     * get Post Collection
     */

    public function getPostCollection()
    {
        if (is_null($this->_postVariable)) {
            $this->_getPostCollection();
        }

        return $this->_postVariable;
    }

}
