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
namespace Ced\Vbadges\Observer;

use Magento\Framework\Event\ObserverInterface;

class Addbadge implements ObserverInterface {

	protected $_objectManager;

	public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager
	) {
		$this->_objectManager = $objectManager;
	}

	public function execute(\Magento\Framework\Event\Observer $observer) {
		$badge = $observer->getEvent ()->getCurrent ();
		$vendorid = $badge->getRequest ()->getParam ( "vendor_id" );
		$data = $badge->getRequest ()->getPostValue ();
		
		$model = $this->_objectManager->create ( 'Ced\Vbadges\Model\Vendorbadges' );
		$count = $model->getcollection ()->addFieldToFilter ( 'vendor_id', $vendorid )->getData ();
		if (sizeof ( $count ) == "1") {
			foreach ( $count as $key => $value ) {
				$model->load ( $value ["id"] );
			}
		}
		if (isset ( $data ["badges"] )) {
			$badgesTypes = implode ( ', ', $data ["badges"] );
			$model->setData ( 'vendor_id', $vendorid );
			$model->setData ( 'badge_id', $badgesTypes );
			$model->save ();
		} else {
			$model->setData ( 'vendor_id', $vendorid );
			$model->setData ( 'badge_id', '' );
			$model->save ();
		}

		if (isset ( $data ["reviewbadges"] )) {
			$reviewbadgesTypes = implode ( ', ', $data ["reviewbadges"] );
			$model->setData ( 'vendor_id', $vendorid );
			$model->setData ( 'review_badge_id', $reviewbadgesTypes );
			$model->save ();
		} else {
			$model->setData ( 'vendor_id', $vendorid );
			$model->setData ( 'review_badge_id', '' );
			$model->save ();
		}
		
		return;
	}
}