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
Class SetVendorPanel implements ObserverInterface
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
     * Adds catalog categories to top menu
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $theme = $this->_objectManager->get('Magento\Framework\View\Design\Theme\FlyweightFactory')->create('Ced/ced', 'frontend');
        $this->_objectManager->get('Magento\Framework\View\DesignInterface')->setDesignTheme($theme); 
    }
        
}
