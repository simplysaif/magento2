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
namespace Ced\CsMultistepreg\Controller\Multistep;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
Use Magento\Framework\View\Result\PageFactory; 
use Magento\Framework\UrlFactory;
class Index extends \Ced\CsMultistepreg\Controller\Vendor{

	protected $_resultPageFactory;
 	protected $_session;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        Session $customerSession,
        PageFactory $resultPageFactory,
         UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->_session = $customerSession;
    }
 
    public function execute()
    {
    	$request = $this->_request;
    	if(!$request->getParam('id')){
    		$this->_redirect('marketplace/vendor/index');
    		return ;
    	}

        $vendorModel = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor');
        $vendorModel->load($request->getParam('id'));
        $vEmail = $vendorModel->getEmail();
        //$customerEmail =  $this->_session->getCustomer()->getEmail();
        $customerID = $this->_objectManager->create('Ced\CsMarketplace\Model\Session')->getCustomerSession()->getCustomerId();
        $customerEmail =  $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerID)->getEmail();
        if($vEmail != $customerEmail){
            return $this->_redirect('csmarketplace/vendor/index');
        }
    	$resultRedirect = $this->resultRedirectFactory->create();
    	$resultPage = $this->_resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->set('Multi Step Registration');
        return $resultPage;
    }
}