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

namespace Ced\Blog\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Test extends \Magento\Framework\App\Action\Action
{

    /**
     * @param resultPageFactory
     */

    protected $resultPageFactory;

    /**
     * @param resultRedirectFactory
     */

    protected $resultRedirectFactory;


    /**
     * @param resultForwardFactory
     */

    protected $resultForwardFactory;


    /**
     * @param resultRedirect
     */

    protected $resultRedirect;

    /**
     * @param Magento\Framework\App\Action\Context
     * @param Magento\Framework\Controller\Result\ForwardFactory
     * @param Magento\Framework\View\Result\PageFactory
     */

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {

        $this->resultForwardFactory= $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

    }
    /**
     * @param execute
     */

    public function execute()
    {

        $resultRedirect = $this->resultRedirectFactory->create();
        $store = $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $config_value = $store->getValue('ced_blog/general/activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$config_value) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('defaultIndex');
            return $resultForward;
        }else  {
            $id = $this->getRequest()->getParam('id');
            $model = $this->_objectManager->create('Ced\Blog\Model\BlogPost')->load($id);
            return $this->resultPageFactory->create();
        }
    }
}

