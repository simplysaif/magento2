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

namespace Ced\CsTableRateShipping\Helper;

/**
 * Configuration data of carrier
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    

    protected $_helper;
    protected $_objectManager;
    protected $_vsettingsFactory;
    
    public function __construct(
      \Magento\Framework\App\Helper\Context $context,
      \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
        $this->_helper  = $this->_objectManager->get('Ced\CsMarketplace\Helper\Data');
    }
    
    public function isEnabled($storeId=0)
    {
        
        if($storeId == 0) {
            $storeId = $this->_helper->getStore()->getId(); 
        }
        return $this->_helper->getStoreConfig('ced_cstablerateshipping/general/active', $storeId);
        //return $this->_helper->getStoreConfig('csfreeshipping/general/active', $storeId);
    }
    
    
}
