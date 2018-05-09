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
use Magento\Framework\App\RequestInterface;
Class MassDeleteVendor implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request
    ) {
    
        
        $this->_objectManager = $objectManager;
        $this->request = $request;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { 
        $customerids = $this->request()->getParam('customer');
        foreach ($customerids as $customerId){
            $vendor= $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($customerId);
            if($vendor && $vendor->getId()) {
                $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->deleteVendorProducts($vendor->getId());
                $this->_objectManager->get('Ced\CsMarketplace\Helper\Mail')->sendAccountEmail(\Ced\CsMarketplace\Model\Vendor::VENDOR_DELETED_STATUS, '', $vendor);
                $vendor->delete();
            }
        }    
    }
}
?>
