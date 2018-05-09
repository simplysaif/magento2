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
namespace Ced\Vbadges\Block\Vshops;

use Magento\Framework\View\Element\Template;

class Left extends \Magento\Framework\View\Element\Template {

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context, 
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Ced\Vbadges\Helper\Data $helper,
		array $data = []
	) {
		$this->_objectManager = $objectManager;
		$this->helper = $helper;
		parent::__construct ( $context, $data );
	}
	
	protected function _prepareLayout() {
		return parent::_prepareLayout ();
	}

	public function getshopbadges(){
		$shopurl = $this->getRequest()->getParam("shop_url");
		$model = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByAttribute('shop_url', $shopurl);
		if($model && $model->getId()){
			$vendorId = $model->getId();
		}
		
		$vendor_order_badges = [];
		$vendor_review_badges = [];

		$getvendorbadges = $this->helper->getvendorbadges ( $vendorId, $vendor_order_badges, $vendor_review_badges );
		
		$vendor_order_badges = $getvendorbadges["orderbadges"];
		$vendor_review_badges = $getvendorbadges["reviewbadges"];
		
		$accordingtoorder = $this->helper->accordingtoorder ( $vendorId, $vendor_order_badges );

		if (sizeof ( $accordingtoorder ) > 0) {
			$vendor_order_badges = array_unique ( $accordingtoorder );
		}
		
		$accordingtoreview = $this->helper->accordingtoreview ( $vendorId, $vendor_review_badges );

		if (sizeof ( $accordingtoreview ) > 0) {
			$vendor_review_badges = array_unique ( $accordingtoreview );
		}

		$sortingrank = $this->helper->badgerank ( $vendorId, $vendor_order_badges, $vendor_review_badges );

		$badgeimages = $this->helper->badgeimage ( $vendorId, $sortingrank);

		if(sizeof($badgeimages)==0){
			$badgeimages = [];
		}
		return $badgeimages;
	}
}