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
  * @package   Ced_CsImportExport
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsImportExport\Controller\Import;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;

class Index extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected $_scopeConfig;

    public function __construct(       
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
       
    }
     
    public function execute()
    {
      if(!$this->_scopeConfig->getValue('ced_csmarketplace/general/activation_csimportexport', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return $this->_redirect('csmarketplace/vendor/index');
        }
        $this->messageManager->addNotice(
            $this->_objectManager->get('Magento\ImportExport\Helper\Data')->getMaxUploadSizeMessage()
        );
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        //    $resultPage->setActiveMenu('Magento_ImportExport::system_convert_import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import/Export'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        return $resultPage;
    }
}
