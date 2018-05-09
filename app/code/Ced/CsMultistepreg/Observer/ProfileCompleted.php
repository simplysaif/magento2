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
namespace Ced\CsMultistepreg\Observer;

use \Psr\Log\LoggerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ProfileCompleted implements ObserverInterface
{
	protected $logger;
	protected $request;
	protected $scopeConfig;
	protected $_objectManager;
	public function __construct(LoggerInterface $logger,\Magento\Framework\App\Request\Http $request,
    ScopeConfigInterface $scopeConfig,\Magento\Framework\ObjectManagerInterface $objectInterface){
		$this->logger = $logger;
		$this->scopeConfig = $scopeConfig;
		$this->request = $request;
		$this->_objectManager = $objectInterface;
	}

	public function execute(Observer $observer){ 
		$vendor = $observer->getVendor();
		$receiverInfo = [
		    'name' => $vendor->getPublicName(),
		    'email' => $vendor->getEmail()
		];
	
		$adminEmail = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);
		$adminName  = $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE);
		
		$senderInfo = [
		    'name' => $adminName,
		    'email' => $adminName,
		];
		$emailTemplateVariables = array();
		$emailTempVariables['name'] = $vendor->getPublicName();
		$emailTempVariables['email'] = $vendor->getEmail();
		$this->_objectManager->get('Ced\CsMultistepreg\Helper\Email')->sendEmail(
		      $emailTempVariables,
		      $senderInfo,
		      $receiverInfo
		  );
	 }
}