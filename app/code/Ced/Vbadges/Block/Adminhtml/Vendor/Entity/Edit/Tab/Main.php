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
namespace Ced\Vbadges\Block\Adminhtml\Vendor\Entity\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {
	
	/**
	 * @param \Magento\Backend\Block\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory    	
	 * @param array $data        	
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Data\FormFactory $formFactory,
		array $data = []
	) {
		parent::__construct ( $context, $registry, $formFactory, $data );
	}
	
	protected function _prepareForm() {
		$isElementDisabled = false;
		
		/** @var \Magento\Framework\Data\Form $form */
		$form = $this->_formFactory->create ();
		
		$form->setHtmlIdPrefix ( 'page_' );
		
		$fieldset = $form->addFieldset ( 'base_fieldset', [ 
				'legend' => __ ( 'Badges' ) 
		] );
		
		if (isset ( $data )) {
			$fieldset->addField ( 'badges_id', 'hidden', [ 
					'name' => 'badges_id' 
			] );
		}
	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance ();
		$badgeobj = $objectManager->create ( 'Ced\Vbadges\Model\Badges' );
		$badges = $badgeobj->getCollection ()->addFieldToFilter ( 'badge_status', '0' )->addFieldToSelect ( "badge_name" )->addFieldToSelect ( "badges_id" )->getData ();
		$badges_name = [];
		foreach ( $badges as $key => $value ) {
			$badges_name [] = [
					'value' => $value ["badges_id"],
					'label' => $value ["badge_name"],
					'checked' => true 
			];
		}
		//assignning review based checkbox name
		$reviewobj=$objectManager->create ( 'Ced\Vbadges\Model\Review' );
		$reviewbadges = $reviewobj->getCollection ()->addFieldToFilter ( 'badge_status', '0' )->addFieldToSelect ( "badge_name" )->addFieldToSelect ( "badges_id" )->getData ();
		$review_badges_name = array ();
		foreach ( $reviewbadges as $key => $value ) {
			$review_badges_name [] = array (
					'value' => $value ["badges_id"],
					'label' => $value ["badge_name"],
					'checked' => true
			);
		}

		//getting vendor order badges
		$vendorid = $this->getRequest ()->getParam ( "vendor_id" );
		$badgemodel = $objectManager->create ( 'Ced\Vbadges\Model\Vendorbadges' )->getCollection ()->addFieldToFilter ( 'vendor_id', $vendorid )->getData ();
		foreach ( $badgemodel as $key => $value ) {
			$value_badge = $value ["badge_id"];
			$review_value_badge = $value ["review_badge_id"];
		}
		
		if (isset ( $value_badge )) {
			if ($value_badge != "") {
				$badgeid = explode ( ', ', $value_badge );
				$vendorbadges = [ ];
				foreach ( $badgeid as $key => $value ) {
					$badgeview = $badgeobj->load ( $value )->getData ();
					if ($badgeview ['badge_status'] != "1") {
						$vendorbadges [] = $badgeview ["badges_id"];
					}
				}
			}
		}
		if (isset ( $review_value_badge )) {
			if ($review_value_badge != "") {
				$review_badgeid = explode ( ', ', $review_value_badge );
				$vendorreviewbadges = [ ];
				foreach ( $review_badgeid as $key => $value ) {
					$reviewbadgeview = $reviewobj->load ( $value )->getData ();
					if ($reviewbadgeview ['badge_status'] != "1") {
						$vendorreviewbadges [] = $reviewbadgeview ["badges_id"];
					}
				}
			}
		}
		
		$checkbox = array (
				'label' => __ ( 'Assign Order Based Badge' ),
				'name' => 'badges[]',
				'values' => $badges_name,
				'onclick' => "",
				'onchange' => "",
				'value' => "",
				'disabled' => false,
				'tabindex' => 1 
		);
		$reviewcheckbox = array (
				'label' => __ ( 'Assign Review Based Badge' ),
				'name' => 'reviewbadges[]',
				'values' => $review_badges_name,
				'onclick' => "",
				'onchange' => "",
				'value' => "",
				'disabled' => false,
				'tabindex' => 1
		);
		if (isset ( $vendorbadges )) {
			$checkbox ["value"] = $vendorbadges;
		}
		
		if (isset ( $vendorreviewbadges )) {
			$reviewcheckbox ["value"] = $vendorreviewbadges;
		}
		$fieldset->addField ( 'badges', 'checkboxes', $checkbox );
		$fieldset->addField ( 'reviewbadges', 'checkboxes', $reviewcheckbox );
		$this->setForm ( $form );
		
		return parent::_prepareForm ();
	}
	
	public function getTabLabel() {
		return __ ( 'Badge Information' );
	}
	
	public function getTabTitle() {
		return __ ( 'Badge Information' );
	}
	
	public function canShowTab() {
		return true;
	}
	
	public function isHidden() {
		return false;
	}
	
	protected function _isAllowedAction($resourceId) {
		return $this->_authorization->isAllowed ( $resourceId );
	}
}