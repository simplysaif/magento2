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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Controller\Guestrma;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Form extends \Magento\Framework\App\Action\Action
{

	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

	/**
	 * @param resultPageFactory
	 */

	protected $resultPageFactory;
   /**
	 * @param resultForwardFactory
	 */

	protected $resultForwardFactory;
    /**
	 * @param Magento\Framework\App\Action\Context $context
	 * @param Magento\Framework\View\Result\PageFactory
	 * @param Magento\Framework\Controller\Result\ForwardFactory
	 */

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
		)
	{
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
    } 

    /**
     * @param execute
     */

	public function execute()
	{
		if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/');
        }
        $resultPage = $this->resultPageFactory->create();
		$resultPage->getConfig()->getTitle()->set(__('Returns Request'));
        $this->_objectManager->get('Ced\CsRma\Helper\Guest')->getBreadcrumbs($resultPage);
        return $resultPage;
	}
}



