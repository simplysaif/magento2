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

namespace Ced\TeamMember\Block\Vendor\Chat\Renderer;
 
class Chat extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer {
 
	/**
	 * Render approval link in each vendor row
	 * @param Varien_Object $row
	 * @return String
	 */
	public function render(\Magento\Framework\DataObject $row) {
		$html = '';
			$html .= '<a href="javascript:void(0)" onclick ="callFancyBox(\''.$row->getId().'\',\''.$row->getEmail().'\');" >'.__('Send Message').'</a>';  
		
		return $html;
	}
}