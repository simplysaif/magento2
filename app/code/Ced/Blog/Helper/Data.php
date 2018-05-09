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


namespace Ced\Blog\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @const FRONTEND_NAME
     */

    const FRONTEND_NAME = 'blog';

    /**
     * @var frontendName
     */

    protected $_frontendName;

    /**
     * @var scopeConfig
     */

    protected $_scopeConfig;


    /**

     * @param Magento\Framework\App\Helper\Context
     * @param Magento\Framework\App\Config\ScopeConfigInterface
     */

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {

        $this->_scopeConfig = $context->getScopeConfig();

        parent::__construct($context);

    }

    /**
     * return config value
     */

    public function enableModule()
    {

        $_frontendName=$this->_scopeConfig->getValue('ced_blog/general/activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);

        return $_frontendName;

    }

    /**
     * return FrontendName
     */

    public function getFrontendName($ucFirst = false)
    {
        /*$_frontendName=$this->_scopeConfig->getValue('ced_blog/general/route_activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 0);*/
        $_frontendName = 'blog';
        if (!$_frontendName) {
            $_frontendName = self::FRONTEND_NAME;
        }
        if ($ucFirst) {
            $_frontendName = ucfirst($_frontendName);
        }
        return $_frontendName;
    }

}
