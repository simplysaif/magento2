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
 * @package     Ced_CsMultistepregistration
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMultistepreg\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_scopeConfigManager;
	
	public function __construct(
			\Magento\Framework\App\Helper\Context $context,
			\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		$this->_objectManager = $objectManager;
		$this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
		parent::__construct($context);
	}

	
	
	public function isEnabled(){
		$enabled = $this->_scopeConfigManager->getValue('ced_csmarketplace/multistepregistration/activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		return $enabled;
	}
}