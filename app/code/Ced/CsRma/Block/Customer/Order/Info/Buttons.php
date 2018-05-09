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
 * @package     Ced_CsRma
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 
namespace Ced\CsRma\Block\Customer\Order\Info;

use Magento\Customer\Model\Context;

class Buttons extends \Magento\Sales\Block\Order\Info\Buttons
{
    /**
     * @var string
     */
    protected $_template = 'order/info/buttons.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Ced\CsRma\Helper\OrderDetail
     */
    public $rmaOrderHelper;
    
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Ced\CsRma\Helper\OrderDetail $rmaOrderHelper,
    	\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct($context,$registry,$httpContext, $data);
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->_isScopePrivate = true;
        $this->rmaOrderHelper = $rmaOrderHelper;
        $this->_objectManager = $objectManager;
    }

    public function getRmaUrl($order)
    {
        /*if (!$this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('csrma/guest/reorder', ['order_id' => $order->getId()]);
        }*/
        return $this->getUrl('csrma/customerrma/new/', ['order_id' => $order->getIncrementId()]);
    }
    
    /**
     *
     * @param int $orderId
     * returns whether orders can be cancel or not
     */
    public function cancelOrder($orderId)
    {
    	$order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
    	$store = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
    	if(!$store->getValue('ced_csmarketplace/rma_general_group/cancel_order'))
    	{
    		return false;
    	}
    	else{
    		if ($order && $order->canCancel()) {
    			return true;
    		}
    		else{
    			return false;
    		}
    	}
    	 
    	 
    }
}
