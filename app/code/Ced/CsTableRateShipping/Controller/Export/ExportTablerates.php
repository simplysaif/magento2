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
 * @package     Ced_CsTableRateShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsTableRateShipping\Controller\Export;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportTablerates extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $_allowedResource = true;
    /**
 * @var Session 
*/
    protected $session;
    protected $_messagingFactory;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlModel;
    
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_resultPageFactory;
     
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;
    protected $_objectManager;
    protected $_fileFactory;
    

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultPageFactory  = $resultPageFactory;
        $this->_moduleManager = $moduleManager;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        //     $this->resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
    }

    /**
     * Export shipping table rates in csv format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        if(!$this->_getSession()->getVendorId()) {
            return; 
        }
        if(!$this->_objectManager->get('Ced\CsMultiShipping\Helper\Data')->isEnabled() || !$this->_objectManager->get('Ced\CsTableRateShipping\Helper\Data')->isEnabled()) {
            $this->_redirect('csmarketplace/vendor/index');
            return;
        }
        $fileName   = 'tablerates.csv';
        $gridBlock = $this->_view->getLayout()->createBlock( 
            //'Magento\OfflineShipping\Block\Adminhtml\Carrier\Tablerate\Grid'
                  'Ced\CsTableRateShipping\Block\Adminhtml\Carrier\Tablerate\Grid'
        );
         
        $websiteId  = $this->getRequest()->getParam('website');
        $vsetting_model =$this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings');
        $conditionName = $vsetting_model->getCollection()->addFieldToFilter('vendor_id', $this->getVendorId())->getData();
        foreach($conditionName as $key => $value){
                
            if($value['key'] == 'shipping/tablerate/condition') {
                $conditionName = $value['value'];
            }        
                
        }
        //print_r($conditionName);die;
        $gridBlock->setWebsiteId($websiteId)->setConditionName($conditionName);
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
    public function _getSession()
    {
        return $this->session;
    }
    public function getVendorId()
    {
        return $this->_objectManager->create('Magento\Customer\Model\Session')->getVendorId();
    }
}

