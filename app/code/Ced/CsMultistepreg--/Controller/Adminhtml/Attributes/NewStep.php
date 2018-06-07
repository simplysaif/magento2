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
 * @category    Ced
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Controller\Adminhtml\Attributes;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class NewStep extends \Magento\Backend\App\Action{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	protected $modelFactory;
	protected $_scopeConfig;
	protected $_storeManager;

	/**
	 * @param Context     $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
			Context $context,
			PageFactory $resultPageFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
		$this->resultPageFactory = $resultPageFactory;
		$this->resultRedirectFactory = $context->getResultRedirectFactory();
	}

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function execute(){
		$resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_CsMarketplace::csmarketplace');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Steps'));
 
        return $resultPage;
	}
}

