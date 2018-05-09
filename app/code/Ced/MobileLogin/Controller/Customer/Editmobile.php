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
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\MobileLogin\Controller\Customer;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Editmobile extends \Magento\Framework\App\Action\Action
{
  /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    protected $_scopeConfigManager;
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry

    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
    }
   
  public function execute()
  {
    //print_r($this->getRequest()->getPost('mobile'));die;
    $duplicate = false; 
    $validate = false;
    $digit = $this->_scopeConfigManager->getValue('ced_mobilelogin/mobile_login/number');
    if($mobile = $this->getRequest()->getPost('mobile')){ 
      if($this->_objectManager->create('Ced\MobileLogin\Helper\Data')->editMobile($mobile))
        $duplicate = true;
      if($this->_objectManager->create('Ced\MobileLogin\Helper\Data')->validate($mobile))
        $validate = true;

    }
    if($duplicate && $validate)
      echo 'true';die;
    if(!$validate)
      echo 'Mobile number must contains '.$digit.' digits.';die;
    if(!$duplicate)
      echo 'Customer with this Mobile Number already exists.';die;
    //return;
  }
}