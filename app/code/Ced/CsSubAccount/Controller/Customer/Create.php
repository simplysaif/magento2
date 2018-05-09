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

namespace Ced\CsSubAccount\Controller\Customer; 

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
 
class Create extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * 
     *
     * @var \Magento\Framework\View\Result\Page 
     */
    protected $resultPageFactory;

    protected $_scopeConfig;

    public function __construct(
        Context $context, 
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        
    }

    public function execute()
    {  

        if(!$this->_scopeConfig->getValue('ced_cssubaccount/general/cssubaccount_active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $this->messageManager->addError(__('Sub-vendor Account System is disabled by admin.'));
            $this->_redirect('csmarketplace/account/login');
            return;
        }
        if(!$this->_customerSession->getSubVendorData()){ 
            $this->_objectManager->create('Magento\Customer\Model\Session')->logout();
            
        }
        
        if ($this->_customerSession->isLoggedIn() && $this->_customerSession->getSubVendorData()){
            if($this->_customerSession->getSubVendorData('email') != $this->_customerSession->getCustomer('email'))
                $this->_objectManager->create('Magento\Customer\Model\Session')->logout();
            else{
                $this->_redirect('csmarketplace/vendor/index');
                return;
            }
        }
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Sub-vendor account'));
        return $resultPage;
        
    }
}
 
