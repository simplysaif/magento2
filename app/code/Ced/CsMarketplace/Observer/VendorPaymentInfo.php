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
 * @package     Ced_CsMarketplace
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMarketplace\Observer;

use Magento\Framework\Event\ObserverInterface;

class VendorPaymentInfo implements ObserverInterface {

	protected $_objectManager;
	
	public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager) {
		$this->_objectManager = $objectManager;
	}
	
	public function execute(\Magento\Framework\Event\Observer $observer) {	
		$vendor= $observer->getEvent()->getCurrent();
		$vendorid=$vendor->getRequest()->getParam("vendor_id");

		if(!$vendorid) { return;
		}
		$section = 'payment';
		$groups = $vendor->getRequest()->getPost('groups', array());
		if(strlen($section) > 0 && $vendorid && count($groups)>0) {
			$vendor_id = $vendorid;
			try {
				foreach ($groups as $code => $values) {
					foreach ($values as $name => $value) {
						$serialized = 0;
						$key = strtolower($section.'/'.$code.'/'.$name);
						if (is_array($value)) {
							$value = serialize($value);
							$serialized = 1;
						}
						$setting=false;
						$setting = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings')->loadByField(array('key','vendor_id'), array($key,$vendor_id));
		
						if ($setting && $setting->getId()) {
							var_dump($setting->getId());
							$setting->setVendorId($vendor_id)
							->setGroup($section)
							->setKey($key)
							->setValue($value)
							->setSerialized($serialized)
							->save();
						} else {
		
							$setting = $this->_objectManager->create('Ced\CsMarketplace\Model\Vsettings');
							$setting->setVendorId($vendor_id)
							->setGroup($section)
							->setKey($key)
							->setValue($value)
							->setSerialized($serialized)
							->save();
						}
					}
				}
				return;
			} catch (\Exception $e) {
				return;
			}
		}
	}
}