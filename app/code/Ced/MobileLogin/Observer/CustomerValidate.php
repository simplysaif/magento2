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

Class CustomerValidate implements ObserverInterface
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
    { //die('in customer validate observer');
        $customer = $this->request->getPost('customer');
        //echo $this->request->getPost('customer');
        $msg ='';
        if(is_numeric($customer['mobile'])){
            $digit = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('ced_mobilelogin/mobile_login/number');
            if(!$this->_objectManager->get('Ced\MobileLogin\Helper\Data')->validate($customer['mobile']))
                $msg = 'Mobile number must contains '.$digit.' digits.';
            $allcustomers = $this->_objectManager->create('Magento\Customer\Model\Customer')->getCollection()
                            ->addAttributeToFilter('mobile',$customer['mobile']);

            if(count($allcustomers)){
                foreach($allcustomers as $allcustomer){
                    if($customer['email'] != $allcustomer->getEmail()){
                        $msg = 'Customer with this Mobile Number already exists.';
                        break;
                    }
                }  
            }                      
        }else{
            $msg = 'Mobile Number must be numeric type.';
        }
        
        if($msg){           
            $this->messageManager->addError(__($msg));
            throw new \Exception($msg, 1);
        }          
    }
}    
?>
