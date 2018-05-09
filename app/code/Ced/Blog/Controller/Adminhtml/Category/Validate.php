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

namespace Ced\Blog\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;

use Magento\Framework\View\Result\PageFactory;

use Magento\Backend\App\Action;


/**
 *  category profile  Controller
 *
 * @author CedCommerce Magento Core Team <Ced_MagentoCoreTeam@cedcommerce.com>
 */

class Validate extends \Magento\Backend\App\AbstractAction
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */

    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */

    protected $layoutFactory;

    /**
     * @var objectManager
     */

    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Validator
     */

    protected $productValidator;

    /**
     * @var objectManager
     */

    protected $_objectManager;

    /**
     * @var PageFactory
     */

    protected $resultPageFactory;


    /**
     * Validate constructor.
     * @param Context $context
     * @param \Ced\Blog\Model\Validator $productValidator
     * @param \Ced\Blog\Model\BlogcatFactory $productFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\Blog\Model\Validator $productValidator,
        \Ced\Blog\Model\BlogcatFactory $productFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {

        $this->resultJsonFactory = $resultJsonFactory;
        $this->productValidator = $productValidator;
        $this->layoutFactory = $layoutFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);

    }

    /**
     * Validate product
     * @return \Magento\Framework\Controller\Result\Json
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */

    public function execute()
    {

        $response = new \Magento\Framework\DataObject();
        $response->setError(false);
        try {
            $productData = $this->getRequest()->getPost('url_key');

            /* @var $product \Magento\Catalog\Model\Product */

            $product = $this->productFactory->create();
            $productId = $this->getRequest()->getParam('id');
            if ($productId) {
                $product->load($productId);
            }
            $result =  $this->productValidator->validate($product, $this->getRequest(), $response);

        }  catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $layout = $this->layoutFactory->create();
            $layout->initMessages();
            $response->setError(true);
            $response->setHtmlMessage($layout->getMessagesBlock()->getGroupedHtml());
        }
        return $this->resultJsonFactory->create()->setJsonData($response->toJson());
    }
}
