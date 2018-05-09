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
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\TeamMember\Block\Adminhtml\TeamMember\Renderer;
 
class Approve extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		$html = '';
		if($row->getId()!='' && $row->getStatus() != \Ced\TeamMember\Model\TeamMember::TEAMMEMBER_APPROVED_STATUS) {	
			$url =  $this->getUrl('*/*/massStatus', array('memberid' => $row->getId(), 'status'=>\Ced\TeamMember\Model\TeamMember::TEAMMEMBER_APPROVED_STATUS, 'inline'=>1));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Approve?').'\', \''. $url . '\');" >'.__('Approve').'</a>';  
		} 
				
		if($row->getId()!='' && $row->getStatus() != \Ced\TeamMember\Model\TeamMember::TEAMMEMBER_DISAPPROVED_STATUS) {
			if(strlen($html) > 0) $html .= ' | ';
			$url =  $this->getUrl('*/*/massStatus', array('memberid' => $row->getId(), 'status'=>\Ced\TeamMember\Model\TeamMember::TEAMMEMBER_DISAPPROVED_STATUS, 'inline'=>1));
			$html .= '<a href="javascript:void(0);" onclick="deleteConfirm(\''.__('Are you sure you want to Disapprove?').'\', \''. $url . '\');" >'.__('Disapprove')."</a>";
		}
		return $html;
	}
}