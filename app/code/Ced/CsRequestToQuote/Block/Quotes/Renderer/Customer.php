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
 * @package     Ced_CsRequestToQuote
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsRequestToQuote\Block\Quotes\Renderer;

class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $_vproduct;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Customer\Model\Customer $customerData,
    		\Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_customerData = $customerData;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\DataObject $row)
    {
    	$customer = $this->_customerData->load($row->getCustomerId());
    	$customerName = $customer->getName();       
       return $customerName;
    	
    }
}