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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Vendor; 

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Ced\CsMarketplace\Model\Vendor;
use Ced\CsMarketplace\Helper\Data;
class Approval extends \Ced\CsMarketplace\Controller\Account\Approval
{
    /**
     * 
     *
     * @var \Magento\Framework\View\Result\Page 
     */
    protected $resultPageFactory;

    public $_customerSession;

    protected $_scopeConfig;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        CustomerRepositoryInterface $customerRepository,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Vendor $vendordata,
        Data $helperdata
    ) {
        $this->customerRepository = $customerRepository;
        $this->_customerSession = $customerSession;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $customerSession, $resultPageFactory, $customerRepository, $dataObjectHelper, $urlFactory, $moduleManager,$vendordata,$helperdata);
    }

    public function execute()
    {  

        if(!$this->_customerSession->getParentVendor())
            return parent::execute();
        if(!$this->_scopeConfig->getValue('ced_cssubaccount/general/cssubaccount_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->_redirect('customer/account/');
            return;
        }
        
        $collection = $this->_objectManager->create('Ced\CsSubAccount\Model\CsSubAccount')->load($this->_customerSession->getCustomerEmail(),'email');
        if(!count($collection)){
            $this->messageManager->addError(__('Sub-vendor does not exist'));
            $this->_redirect('csmarketplace/vendor/login/');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Sub-Vendor Approval Status'));
        return $resultPage;
        
    }
}
 
