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
namespace Ced\CsMultistepreg\Controller\Rewrite\Account;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
class Approval extends \Ced\CsMarketplace\Controller\Account\Approval{

	/**
	 *
	 *
	 * @var CustomerRepositoryInterface
	 */
	protected $customerRepository;
	
	/**
	 *
	 *
	 * @var DataObjectHelper
	 */
	protected $dataObjectHelper;
	
	/**
	 * @param Context                     $context
	 * @param Session                     $customerSession
	 * @param PageFactory                 $resultPageFactory
	 * @param CustomerRepositoryInterface $customerRepository
	 * @param DataObjectHelper            $dataObjectHelper
	 */
	public function __construct(
			Context $context,
			Session $customerSession,
			PageFactory $resultPageFactory,
			CustomerRepositoryInterface $customerRepository,
			DataObjectHelper $dataObjectHelper,
			\Magento\Framework\UrlFactory $urlFactory,
			\Magento\Framework\Module\Manager $moduleManager,
			\Ced\CsMarketplace\Model\Vendor $vendor,
			\Ced\CsMarketplace\Helper\Data $helper
	) {
		
		$this->customerRepository = $customerRepository;
		$this->dataObjectHelper = $dataObjectHelper;
		parent::__construct(
	        $context,
	        $customerSession,
	        $resultPageFactory,
	        $customerRepository,
	        $dataObjectHelper,
	        $urlFactory,
	        $moduleManager,
	        $vendor,
	        $helper
    	);
	}
	
	/**
	 * Forgot customer account information page
	 *
	 * @return \Magento\Framework\View\Result\Page
	 */
	public function execute()
	{	
		try{

			$resultRedirect = $this->resultRedirectFactory->create();
			if ($this->_getSession()->isLoggedIn() && $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->authenticate($this->session->getCustomerId())) {
				$resultRedirect->setPath('*/vendor/');
				return $resultRedirect;
			}
			
			/* redirect to multistep registration if has not filled all the pages */
			$vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($this->session->getCustomerId());
			if($vendor  && !$vendor->getMultistepDone()){
				$enabled = $this->_objectManager->create('Ced\CsMultistepreg\Helper\Data')->isEnabled();
	    		if($enabled == '1'){
	    		
	    			$resultRedirect->setPath('csmultistep/multistep/index',array('id'=>$vendor->getId()));
					return $resultRedirect;
				}
				
			}
			
			
			/* upto here */
			if (!$this->authenticate($this)) { $this->_actionFlag->set('', 'no-dispatch', true);
			}
			$this->session->unsVendorId();
			$this->session->unsVendor();
			if($this->session->isLoggedIn()) {
		
				$vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($this->session->getCustomerId());
				if($vendor && $vendor->getId()) {
					$this->session->setData('vendor_id', $vendor->getId());
					$this->session->setData('vendor', $vendor->getData());
				}
			}
			$resultPage = $this->resultPageFactory->create();
		
			$resultPage->getConfig()->getTitle()->set(__('Account Approval'));
			//   $resultPage->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
			return $resultPage;
		}catch(Exception $e){
			echo $e->getMessage();
		}
	}
	
}
