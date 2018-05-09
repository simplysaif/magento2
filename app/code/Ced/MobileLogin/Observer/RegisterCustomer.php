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
 * @package     Ced_MobileLogin
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\MobileLogin\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Message\ManagerInterface;

Class RegisterCustomer implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $request;
    private $messageManager;
    protected $_storeManager;
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager,
        RequestInterface $request,
        ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_objectManager = $objectManager;
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->_storeManager = $storeManager;
    }
    /**
     *Vendor registration 
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    { //die('in Register customer observer');
        if($email = $this->request->getPost('email') && $mobile = $this->request->getPost('mobile')){
            try {
                if($this->_objectManager->get('Ced\MobileLogin\Helper\Data')->validate($mobile) &&
                  $this->_objectManager->get('Ced\MobileLogin\Helper\Data')->duplicate($mobile)){
                    $customer = $observer->getEvent()->getCustomer();       
                    $customer->setMobile($mobile);              
                }
            }catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
    }
}    
