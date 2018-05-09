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
namespace Ced\Vbadges\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Catalog data helper
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper {
	
	const APPROVED = 1;
	const ENABLED = 1;
	const DISABLED = 2;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Ced\Vbadges\Model\Badges $orderBadges,
		\Ced\Vbadges\Model\Review $reviewBadges,
		\Ced\Vbadges\Model\Vendorbadges $vendorBadge
	) {
		$this->_objectManager = $objectManager;
		$this->_orderBadges = $orderBadges;
		$this->_reviewBadges = $reviewBadges;
		$this->_vendorBadge = $vendorBadge;
		parent::__construct ( $context );
	}
	
	
	public function getbadges($vendorId){

		$vendor_order_badges = [];
		$vendor_review_badges = [];

		$getvendorbadges = $this->getvendorbadges( $vendorId, $vendor_order_badges, $vendor_review_badges );
		
		$vendor_order_badges = $getvendorbadges["orderbadges"];
		$vendor_review_badges = $getvendorbadges["reviewbadges"];
	
		$accordingtoorder = $this->accordingtoorder ( $vendorId, $vendor_order_badges );

		if (sizeof ( $accordingtoorder ) > 0) {
			$vendor_order_badges = array_unique ( $accordingtoorder );
		}
	

		$accordingtoreview = $this->accordingtoreview ( $vendorId, $vendor_review_badges );
		if (sizeof ( $accordingtoreview ) > 0) {
			$vendor_review_badges = array_unique ( $accordingtoreview );
		}

		$sortingrank = $this->badgerank ( $vendorId, $vendor_order_badges, $vendor_review_badges );
		$badgeimages = $this->badgeimage ( $vendorId, $sortingrank);


		if(sizeof($badgeimages)==0){
			$badgeimages = [];
		}

		return $badgeimages;
	}
	
	public function getvendorbadges($vendorId, $vendor_order_badges, $vendor_review_badges) {

		$vendorbadges = [
				"orderbadges" => [ ],
				"reviewbadges" => [ ] 
		];

		$badgemodel = $this->_vendorBadge
							->getCollection()
							->addFieldToFilter( 'vendor_id', $vendorId )
							->getFirstItem ();

		if ($badgemodel && $badgemodel->getId()) {
			$orderbadges = $badgemodel->getBadgeId();
			$reviewbadges = $badgemodel->getReviewBadgeId();
			
			if ($orderbadges) {
				$badgeid = explode ( ',', $orderbadges );
				foreach ( $badgeid as $key => $value ) {
					$badgeview = $this->_orderBadges->load ( $value );
					if ($badgeview && $badgeview->getId() && $badgeview->getBadgeStatus()!=self::DISABLED) {
						$vendorbadges ["orderbadges"] [] = $badgeview->getBadgesId();
					}
				}
			}

			if ($reviewbadges) {
				$badgeid = explode ( ', ', $reviewbadges );
				foreach ( $badgeid as $key => $value ) {
					$badgeview = $this->_reviewBadges->load ( $value );
					if ($badgeview && $badgeview->getId() && $badgeview->getBadgeStatus()!=self::DISABLED) {
						$vendorbadges ["reviewbadges"] [] = $badgeview->getBadgesId();
					}
				}
			}
			return $vendorbadges;
		} else {
			return $vendorbadges;
		}
	}
	
	public function accordingtoorder($vendorId, $vendor_order_badges) {

		$ordermodel = $this->_objectManager->create('Magento\Sales\Model\Order')
					->getCollection();

		$ordermodel->getSelect()->joinLeft('me2_ced_csmarketplace_vendor_sales_order', 'main_table.increment_id=me2_ced_csmarketplace_vendor_sales_order.order_id', ['vendor_id']);

		$ordermodel->addFieldToFilter('vendor_id', $vendorId);
		$ordermodel->addFieldToFilter('status', 'complete')->getData();

		$orderplaced = sizeof ( $ordermodel );

		$allbadges = $this->_orderBadges->getCollection ()->getData ();

		foreach ( $allbadges as $key => $value ) {
			if ($value ['badge_status'] != self::DISABLED) {
				if ($orderplaced >= $value ["sales"]) {
					$vendor_order_badges [] = $value ["badges_id"];
				}
			}
		}

		return $vendor_order_badges;
	}
	
	public function accordingtoreview($vendorId, $vendor_review_badges) {
		$allbadges = $this->_reviewBadges
							->getCollection ()
							->getData ();
		$reviewcount = $this->reviewcalculation($vendorId);
		foreach ( $allbadges as $key => $value ) {
			if ($value ['badge_status'] != self::DISABLED) {
				if ($reviewcount >= $value ["points"]) {
					$vendor_review_badges [] = $value ["badges_id"];
				}
			}
		}
		return $vendor_review_badges;
	}
	
	public function badgerank($vendorId, $vendor_order_badges, $vendor_review_badges) {
		$orderbadgerank = [];
		foreach ( $vendor_order_badges as $key => $value ) {
			$allbadges = $this->_orderBadges
							->getCollection ()
							->addFieldToFilter ( 'badges_id', $value )
							->getData ();
			foreach ( $allbadges as $key1 => $value1 ) {
				$orderbadgerank [] = $value1 ["sales"];
			}
		}


		$reviewbadgerank = [];
		foreach ( $vendor_review_badges as $key => $value ) {
			$allbadges = $this->_reviewBadges
							->getCollection ()
							->addFieldToFilter ( 'badges_id', $value )
							->getData ();
			foreach ( $allbadges as $key1 => $value1 ) {
				$reviewbadgerank [] = $value1 ["points"];
			}
		}


		$orderbadge = "";
		$reviewbadge = "";
		if (sizeof ( $orderbadgerank ) > 0) {
			$orderbadge = $this->_orderBadges
								->getCollection ()
								->addFieldToFilter ( 'sales', max ( $orderbadgerank ) )
								->getFirstItem ();
		
			$orderbadge = $orderbadge->getBadgesId();
		}

		
		if (sizeof ( $reviewbadgerank ) > 0) {
			$reviewbadge = $this->_reviewBadges
								->getCollection ()
								->addFieldToFilter ( 'points', max ( $reviewbadgerank ) )
								->getFirstItem ();
			$reviewbadge = $reviewbadge->getBadgesId();
		}

		$rank = [
				"order" => $orderbadge,
				"review" => $reviewbadge 
			];
		return $rank;
	}
	
	public function badgeimage($vendorId, $sortingrank) {
		$finalbadges = ["order" => [], "review" => []];
		$orderbadge = $sortingrank ["order"];
		$reviewbadge = $sortingrank ["review"];
		$vendorbadge = $this->_vendorBadge->load($vendorId, 'vendor_id');
		
		$vendororder = '';
		if($vendorbadge && $vendorbadge->getId()){
			$vendororder = $vendorbadge->getId();
		}

		if($vendororder){
			$vendorbadge = $this->_vendorBadge->load($vendororder);
		}else{
			$vendorbadge = $this->_vendorBadge;
		}
		
		$vendorbadge->setData('vendor_id', $vendorId);
		$vendorbadge->setData('badge_id', $orderbadge);

		$vendorbadge->setData('review_badge_id', $reviewbadge);
		$vendorbadge->save();

		$storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
		$currentStore = $storeManager->getStore();
		$mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'badge/';

		$orderBadgeImage = $this->_orderBadges->load ( $orderbadge )->getBadgeImage ();
		$reviewBadgeImage = $this->_reviewBadges->load ( $reviewbadge )->getBadgeImage ();
		if($orderBadgeImage){
			$finalbadges ["order"][] = $mediaUrl.$orderBadgeImage;
		}
		
		if($reviewBadgeImage){
			$finalbadges ["review"][] = $mediaUrl.$reviewBadgeImage;
		}
		
		return $finalbadges;
	}
	
	
 	public function reviewcalculation($vendorId){
		$reviews = $this->_reviewBadges
						->getCollection()
						->addFieldToFilter('badge_status', self::APPROVED)
						->getData();

		$rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')
						->getCollection()
						->getData();
		$points = 0;
		foreach($reviews as $key=>$reviewvalue){

			$rating_sum = 0;
			$count_rating_code = 0;
			foreach($rating as $key=>$ratingvalue){
				if(isset($reviewvalue[$ratingvalue["rating_code"]])){
					$rating_sum+=$reviewvalue[$ratingvalue["rating_code"]];
					$count_rating_code++;
				}
			}
			if($count_rating_code>0){
				$average = ceil($rating_sum/$count_rating_code);
							$average_rating = intval(ceil($average/2)/10);
				$pointobj = $this->_objectManager->create('Ced\Vbadges\Model\Points')
								->getCollection()
								->addFieldToFilter('rating', $average_rating)
								->getData();
				foreach($pointobj as $key=>$value){
					$points+=$value["points"];
				}
			}
		
		}
		return $points;
	}
	
	
	
	public function countofreviews($vendorId){
		$review_data = $this->_reviewBadges
							->getCollection()
							->addFieldToFilter('badge_status', self::APPROVED);

		$rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')
						->getCollection()
						->addFieldToSelect('rating_code');
		$count = 0;
		$rating_sum = 0;
		$vendor_products = $this->_objectManager->create('Ced\CsVendorReview\Block\Vshops\View')->getVendorProducts($vendorId);
		if(count($vendor_products)>0)
		{
			$rating_sum_prod = $this->_objectManager->create('Ced\CsVendorReview\Block\Vshops\View')->getProductRating($vendor_products);
			if($rating_sum_prod>0)
			{
				$rating_sum = $rating_sum_prod;
				$count++;
			}
		}
		 
		foreach($review_data as $key => $value){
			foreach($rating as $k => $val){
				 
				if($value[$val['rating_code']] > 0){
					$rating_sum += $value[$val['rating_code']];
					$count++;
				}
			}
		}
		 
		if($count > 0 && $rating_sum > 0){
			$width = $rating_sum/$count;
			return ceil($width);
		}
		else{
			return false;
		}
		
	}
	
	public function countoforder($vendorId){
		$ordermodel = $this->_objectManager->get ( 'Ced\CsMarketplace\Model\Vorders' )
							->getCollection ()
							->addFieldToFilter ( 'vendor_id', $vendorId )
							->getAllIds ();
		$orderplaced = sizeof ( $ordermodel );
		return $orderplaced;
	}
}