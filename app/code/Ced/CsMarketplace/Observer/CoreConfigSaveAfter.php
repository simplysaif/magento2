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
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
Class CoreConfigSaveAfter implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        
        $this->_objectManager = $objectManager;
    }
    /**
     *Notify Customer Account share Change 
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $groups = $observer->getEvent()->getDataObject()->getGroups();
        $customer_share=isset($groups['account_share']['fields']['scope']['value'])?$groups['account_share']['fields']['scope']['value']:$this->_objectManager->get('\Ced\CsMarketplace\Helper\Data')->getStoreConfig(\Magento\Customer\Model\Config\Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE);
        $config = new \Magento\Core\Model\Config();
        if($customer_share!=''&&$customer_share!=$this->_objectManager->get('\Ced\CsMarketplace\Helper\Data')->getStoreConfig(\Magento\Customer\Model\Config\Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE)) {
            $config->saveConfig(\Ced\CsMarketplace\Model\Vendor::XML_PATH_VENDOR_WEBSITE_SHARE, 1); 
        }
    }
}    
     
    ?>
