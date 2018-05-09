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

// use Magento\Framework\Event\Observer;
use Magento\Customer\Model\Session;
use Magento\Framework\Message\Manager;
// use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

Class RegisterVendor implements ObserverInterface
{
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    
    protected $_request;
    
    protected $_session;
    
    //	protected $messageManager;
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $customerSession,
        Manager $manager                                
    ) {
    
        $this->messageManager = $manager;
        //	$this->_request = $request;
        $this->_session = $customerSession;
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
        $controll_obj = $observer->getAccountController();
        if($controll_obj->getRequest()->getParam('is_vendor') == 1) {
            $venderData = $controll_obj->getRequest()->getParam('vendor');
            $customerData = $observer->getCustomer();
            try {
                $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')
                    ->setCustomer($customerData)
                    ->register($venderData);
                           
                if(!$vendor->getErrors()) {
                    $vendor->save();
                    if($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS) {
                        $this->messageManager->addSuccess(__('Your vendor application has been Pending.'));
                    } else if ($vendor->getStatus() == \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS) {
                        $this->messageManager->addSuccess(__('Your vendor application has been Approved.'));
                    }
                } elseif ($vendor->getErrors()) {
                    foreach ($vendor->getErrors() as $error) {
                        $this->_session->addError($error);
                    }
                    $this->_session->setFormData($venderData);
                } else {
                    $this->_session->addError(__('Your vendor application has been denied'));
                }
            } catch (\Exception $e) {
                $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->logException($e);
            }
        }
    }    
}
