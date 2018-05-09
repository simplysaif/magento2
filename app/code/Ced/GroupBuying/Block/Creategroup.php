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
 * @package     Ced_Groupgift
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\GroupBuying\Block;

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;

class Creategroup extends \Magento\Framework\View\Element\Template
{

	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;

	protected $session;

    public $_scopeConfig;

    public $_coreRegistry = null;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    ){
		$this->session = $customerSession;
		$this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Check If the product is grouped or not
     *
     * @return boolean
     */

    public function isgrouped(){
        $id = $this->_coreRegistry->registry('current_product')->getId();
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($id);
        if ($product->getTypeId() == 'grouped') {
            return true;                    
        }
        else{
            return false;
        }
    }
    
    /**
     * Check if the customer is logged-in or not
     *
     * @return boolean
     */
    
    public function islogin(){
        if ($this->_objectManager->get('Magento\Customer\Model\Session')->isLoggedIn()) {
            return true;
        }
        else{
            return false;
        }
    }
	
}
