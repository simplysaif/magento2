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
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
Class AfterPlaceOrder implements ObserverInterface
{
    
    protected $_objectManager;
    protected $_quoteFactory;
    protected $_advanceFactory;
    protected $_object;
    protected $_coreRegistry = null;
    protected $frontController;
    protected $request;
    
    public function __construct(        
            \Magento\Framework\ObjectManagerInterface $objectManager, 
            \Magento\Framework\App\RequestInterface $request, 
            \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->request = $request;
        $this->_getSession = $customerSession;
		$this->_objectManager = $objectManager;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $prices = $this->_objectManager->create('Magento\Customer\Model\Session')->getRfqPrice();
        		
        foreach($prices as $key => $value){
            $this->_objectManager->create('Magento\Catalog\Model\Product')->load($key)->setPrice($value['new_price'])->save();
        }  
	        
	}
}