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
namespace Ced\Vbadges\Block\Adminhtml;

class Images extends \Magento\Backend\Block\Template {

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		array $data = []
	) {
		parent::__construct ( $context, $data );
		$this->setTemplate('Ced_Vbadges::images.phtml');
	}

	public function getbadges() {

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
		$vendorid = $this->getRequest()->getParam('vendor_id');
		$badges = $objectManager->create ( 'Ced\Vbadges\Helper\Data' )->getbadges($vendorid);
		
		return $badges;
	}

}