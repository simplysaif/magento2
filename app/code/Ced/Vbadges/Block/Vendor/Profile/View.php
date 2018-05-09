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
 * @package     Ced_Vbadges
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Vbadges\Block\Vendor\Profile;

class View extends \Magento\Framework\View\Element\Template {
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Customer\Model\Session $session,
		\Ced\Vbadges\Helper\Data $helper,
		array $data = []
	) {
		$this->_objectManager = $objectManager;
		$this->helper = $helper;
		$this->session = $session;
		parent::__construct ( $context, $data );
	}
	
	protected function _prepareLayout() {
		return parent::_prepareLayout ();
	}
	
	public function getbadges() {
		$vendorid = $this->session->getVendorId ();
		$badges = $this->helper->getbadges($vendorid);
		return $badges;
	}

	public function countsales(){
		$vendorid = $this->session->getVendorId ();
		$badges = $this->helper->countoforder($vendorid);
		return $badges;
	}
	
	public function countofreviews(){
		$vendorid = $this->session->getVendorId ();
		$badges = $this->helper->countofreviews($vendorid);
		return $badges;
	}
}