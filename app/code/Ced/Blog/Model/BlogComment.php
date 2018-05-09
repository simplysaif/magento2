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

namespace Ced\Blog\Model;

use Ced\Blog\Model\IFrontendRoute;

class BlogComment extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var ROUTE_ACTION_NAME
     */

    const ROUTE_ACTION_NAME = 'comment';

    /**
     * @var ROUTE_CONTROLLER_NAME
     */
    const ROUTE_CONTROLLER_NAME = 'index';

    /**
     * @var Magento\Framework\Registry
     */

    protected $_coreRegistry;

    /**
     * @var Magento\Framework\Model\Context
     */

    protected $_context;
    /**
     * @var Magento\Framework\UrlInterface
     */

    protected $urlBuilder;
    /**
     * @var Magento\Framework\ObjectManagerInterface
     */

    protected $objectManager;

    /**
     * @param Magento\Framework\ObjectManagerInterface
     * @param Magento\Framework\Model\Context
     * @param Magento\Framework\UrlInterface
     * @param Magento\Framework\Registry
     * @param Magento\Framework\Model\ResourceModel\AbstractResource
     */

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\UrlInterface  $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {

        $this->objectManager = $objectManager;
        $this->_context = $context;
        $this->urlBuilder = $urlBuilder;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */

    protected function _construct()
    {
        $this->_init('Ced\Blog\Model\ResourceModel\BlogComment');

    }

    /**
     * validate comment
     * @return void
     */

    public function validate()
    {
        $errors = [];
        if (!\Zend_Validate::is($this->getUser(), 'NotEmpty')) {
            $errors[] = __('Please enter a user.');
        }
        if (!\Zend_Validate::is($this->getEmail(), 'NotEmpty')) {
            $errors[] = __('Please enter an email id.');

        }
        if (!\Zend_Validate::is($this->getDescription(), 'NotEmpty')) {
            $errors[] = __('Please enter a comment.');

        }
        if (empty($errors)) {
            return true;
        }

        return $errors;
    }
}
