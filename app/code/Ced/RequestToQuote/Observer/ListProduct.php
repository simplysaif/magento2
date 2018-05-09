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
 * @package     Ced_RequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\RequestToQuote\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
 
class ListProduct implements ObserverInterface
{
	protected $_objectManager;
	public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager, 
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession
        ) {

		$this->request = $request;
        $this->_getSession = $customerSession;
		$this->_objectManager = $objectManager;
	}
    public function execute(\Magento\Framework\Event\Observer $observer) { 
        $item = $observer->getEvent()->getCollection();

        $login = $this->_getSession->isLoggedIn();
        $hideGroups = $this->getAddtoCartCustomers();
        if(empty($login)){
            $groupId =  '0';
        }
        else{
            $groupId =  $this->_getSession->getCustomer()->getGroupId();
            
        }
        if (in_array($groupId, $hideGroups)){ 
            $hideto = "yes";
        }
        else{
            $hideto = "no";
        }
        
        if($hideto == "yes"){

            foreach ($item->getData() as $value) {
                $productId = $value['entity_id'];
                print_r($productId);

            }
            die("dsfhgjdsf");
        }



    }

    public function getAddtoCartCustomers(){
        $configvalue = $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $configvalue->getValue('requesttoquote_configuration/active/hidecart');
        $customergroups = explode(',',$value);

        return $customergroups;

    }
    
    public function getPriceHideCustomers(){
        $configvalue = $this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $configvalue->getValue('requesttoquote_configuration/active/hideprice');
        $groups = explode(',',$value);

        return $groups;

    }
}