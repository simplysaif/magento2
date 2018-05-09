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

namespace Ced\RequestToQuote\Block\Quotes;

use Magento\Framework\View\Element\Template;

class AddToQuote extends \Magento\Framework\View\Element\Template {

	public function __construct(
                \Magento\Framework\View\Element\Template\Context $context, 
                \Magento\Framework\Registry $registry, 
                \Magento\Framework\App\Request\Http $request,
                \Magento\Catalog\Model\ProductFactory $productloader,
                array $data = []
                ) 
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configvalue = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $enablevalue = $configvalue->getValue('requesttoquote_configuration/active/enable');

        if($enablevalue == 1)
        {
            $this->setTemplate('quotes/addtoquote.phtml');
        }
		parent::__construct ( $context, $data );
        $this->_productloader = $productloader;
		$this->_coreRegistry = $registry;
        $this->_request = $request;
        self::getProduct();
	}
	
	protected function _prepareLayout() {

		return parent::_prepareLayout ();
	}

    public function getProductId()
    {
        $id = $this->_request->getParam('id');
        return $id;
    }

    public function getProduct()
    {
        $id = $this->_request->getParam('id');
        if ($id) {
            $product = $this->_productloader->create()->load($id);
            }   

        return $product;
    }

    public function getVendor($productId){
        return "0";
    }

    public function getProductType()
    {
        $id = $this->_request->getParam('id');
        if ($id) {
            $producttype = $this->_productloader->create()->load($id)->getTypeId();;
            }   
                 
        return $producttype;
    }

    public function getProductName()
    {
        $id = $this->_request->getParam('id');
        if ($id) {
            $productName = $this->_productloader->create()->load($id)->getName();
            }   
                
        return $productName;
    }

    public function getAllowedCustomerGroups(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configvalue = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $configvalue->getValue('requesttoquote_configuration/active/custgroups');
        $custgroups = explode(',',$value);

        return $custgroups;

    }
    
    public function getAddtoCartCustomers(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $configvalue = $objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $configvalue->getValue('requesttoquote_configuration/active/hidecart');
        $customergroups = explode(',',$value);

        return $customergroups;

    }


}