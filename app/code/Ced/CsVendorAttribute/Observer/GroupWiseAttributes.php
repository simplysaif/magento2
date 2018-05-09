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
  * @package   Ced_CsVendorAttribute
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsVendorAttribute\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
Class GroupWiseAttributes implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_quoteFactory;
    protected $_scopeConfig;
    
    public function __construct(        
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
    
        
        $this->_objectManager = $objectManager;
        $this->_quoteFactory = $quoteFactory;
        $this->_scopeConfig=$scopeConfig;
    }
    
    /**
     * Adds catalog categories to top menu
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        
        
        $vendorAttributes = $observer->getvendorattributes();
        if(! $this->_scopeConfig->getValue('ced_csmarketplace/general/activation_csvendorattribute', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            $vendorAttributes=$vendorAttributes->addFieldToFilter('is_user_defined', array('eq'=>0));
        }
        return $vendorAttributes;
    }

     /**
     * Adds catalog categories to top menu
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */    
}