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

class Registry extends \Magento\Framework\View\Element\Template
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	protected $urlModel;

	protected $session;

    public $_helper;

    public $_options;

    public $allowed_guest;

    protected $scopeConfig;

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
		UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry
    ){   
		$this->session = $customerSession;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->allowed_guest = 5;
        parent::__construct($context);
    }


     public function getcustomer()
    {
        $id=$this->session->getCustomerId();
        $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($id);
        return $customer['email'];
    }
	
}
